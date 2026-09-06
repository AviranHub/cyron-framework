<?php

namespace App\Http\Controllers\User;

use App\Http\Controller;
use App\Models\Book;
use App\Models\Library;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\DiscountService;
use App\Services\ZarinpalGateway;
use Cyron\Authentication\Auth;
use Cyron\Database\Db;
use Cyron\Http\Request;
use Cyron\Analytics\ActivityTracker;

class PaymentController extends Controller
{
    public function purchaseBook(Request $request, $slug)
    {
        $book = Book::where('slug', $slug)->first();
        $user = Auth::user();
        if (!$book || !$user) return redirect()->route('books')->with('error', 'کتاب پیدا نشد.');
        if ($request->input('method') !== 'wallet') {
            return redirect()->route('book.buy', ['slug' => $slug])->with('notice', 'پرداخت درگاهی پس از تنظیم کلید درگاه فعال می‌شود.');
        }

        $owned = Library::where('user_id', $user->id)->where('book_id', $book->id)->first();
        if ($owned) return redirect()->route('book', ['slug' => $slug])->with('notice', 'این کتاب قبلاً در کتابخانه شماست.');

        $couponCode = trim((string) $request->input('coupon_code', ''));
        $pricing = (new DiscountService())->calculate('book', $book, $couponCode !== '' ? $couponCode : null);
        if ($couponCode !== '' && !($pricing['coupon_valid'] ?? false)) {
            return redirect()->route('book.buy', ['slug' => $slug])->with('error', 'کد تخفیف معتبر نیست یا شرایط استفاده را ندارد.');
        }
        if ($couponCode === '' && $pricing['discount'] === null && (float) ($book->copen ?? 0) > 0) {
            $legacyDiscount = (float) $book->price * min(100, max(0, (float) $book->copen)) / 100;
            $pricing['discount_amount'] = round($legacyDiscount, 2);
            $pricing['final_amount'] = max(0, round((float) $book->price - $legacyDiscount, 2));
        }
        $amount = max(0, (int) round($pricing['final_amount']));
        $discountRule = $pricing['discount'];
        $wallet = Wallet::where('user_id', $user->id)->first();
        if (!$wallet) $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 0]);
        if ((float) $wallet->balance < $amount) {
            return redirect()->route('book.buy', ['slug' => $slug])->with('error', 'موجودی کیف پول کافی نیست.');
        }

        Db::transaction(function () use ($wallet, $amount, $user, $book, $discountRule) {
            if (!$wallet->update(['balance' => (float) $wallet->balance - $amount])) {
                throw new \RuntimeException('Wallet update failed.');
            }

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'purchase',
                'status' => 'success',
                'description' => 'خرید کتاب ' . $book->title,
                'payment_method' => 'wallet',
                'payable_type' => Book::class,
                'payable_id' => $book->id,
                'paid_at' => date('Y-m-d H:i:s'),
            ]);
            if (!$transaction) throw new \RuntimeException('Transaction creation failed.');

            if (!Library::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'purchased_at' => date('Y-m-d H:i:s'),
            ])) throw new \RuntimeException('Library entry creation failed.');

            if (!WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'withdraw',
                'description' => 'خرید کتاب ' . $book->title,
                'transaction_id' => $transaction->id,
            ])) throw new \RuntimeException('Wallet transaction creation failed.');

            if ($discountRule) {
                if (!$discountRule->update(['usage_count' => (int) $discountRule->usage_count + 1])) {
                    throw new \RuntimeException('Discount usage update failed.');
                }
            }
        });

        ActivityTracker::record('book.purchased', [
            'subject_type' => Book::class,
            'subject_id' => (int) $book->id,
            'amount' => $amount,
        ], (int) $user->id);

        return redirect()->route('book', ['slug' => $slug])->with('success', 'کتاب با موفقیت به کتابخانه شما اضافه شد.');
    }

    public function recharge(Request $request)
    {
        $errors = $request->validate(['amount' => 'required|integer|min:1000']);
        if ($errors && $errors->any()) return redirect()->back()->withErrors($errors)->withInput();

        $user = Auth::user();
        $amount = (int) $request->input('amount');
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'charge',
            'status' => 'pending',
            'description' => 'شارژ کیف پول',
            'payment_method' => 'zarinpal',
        ]);

        $gateway = new ZarinpalGateway();
        $result = $gateway->request($amount, 'شارژ کیف پول', route('payment.verify'));
        if (!($result['success'] ?? false)) {
            $transaction->update(['status' => 'failed', 'gateway_response' => json_encode($result, JSON_UNESCAPED_UNICODE)]);
            return redirect()->back()->with('error', $result['message'] ?? 'درخواست پرداخت ناموفق بود.');
        }

        $transaction->update([
            'authority' => $result['authority'],
            'reference_id' => $result['authority'],
        ]);

        return redirect($gateway->startUrl($result['authority']));
    }

    public function verify(Request $request)
    {
        $authority = trim((string) $request->input('Authority', ''));
        $status = strtoupper((string) $request->input('Status', ''));
        $transaction = $authority !== '' ? Transaction::where('authority', $authority)->first() : null;

        if (!$transaction) return redirect()->route('user.wallet')->with('error', 'تراکنش پیدا نشد.');
        if ($transaction->status === 'success') return redirect()->route('user.wallet')->with('success', 'این تراکنش قبلاً تایید شده است.');
        if ($transaction->status !== 'pending' || $status !== 'OK') {
            $transaction->update(['status' => 'failed']);
            return redirect()->route('user.wallet')->with('error', 'پرداخت انجام نشد.');
        }

        $result = (new ZarinpalGateway())->verify((int) $transaction->amount, $authority);
        if (!($result['success'] ?? false)) {
            $transaction->update(['status' => 'failed', 'gateway_response' => json_encode($result, JSON_UNESCAPED_UNICODE)]);
            return redirect()->route('user.wallet')->with('error', 'تایید پرداخت ناموفق بود.');
        }

        Db::transaction(function () use ($transaction, $result, $authority) {
            if (!$transaction->update([
                'status' => 'success',
                'tracking_code' => $result['ref_id'],
                'reference_id' => (string) ($result['ref_id'] ?? $authority),
                'paid_at' => date('Y-m-d H:i:s'),
                'gateway_response' => json_encode($result['response'] ?? [], JSON_UNESCAPED_UNICODE),
            ])) throw new \RuntimeException('Payment update failed.');

            $wallet = Wallet::where('user_id', $transaction->user_id)->first();
            if (!$wallet) $wallet = Wallet::create(['user_id' => $transaction->user_id, 'balance' => 0]);
            if (!$wallet || !$wallet->update(['balance' => (float) $wallet->balance + (float) $transaction->amount])) {
                throw new \RuntimeException('Wallet credit failed.');
            }
            if (!WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $transaction->user_id,
                'amount' => $transaction->amount,
                'type' => 'deposit',
                'description' => 'شارژ کیف پول از طریق درگاه',
                'transaction_id' => $transaction->id,
            ])) throw new \RuntimeException('Wallet transaction creation failed.');
        });

        ActivityTracker::record('wallet.recharged', [
            'subject_type' => Transaction::class,
            'subject_id' => (int) $transaction->id,
            'amount' => (float) $transaction->amount,
        ], (int) $transaction->user_id);

        return redirect()->route('user.wallet')->with('success', 'کیف پول با موفقیت شارژ شد.');
    }
}