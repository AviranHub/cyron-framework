<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\DiscountService;
use Cyron\Authentication\Auth;
use Cyron\Database\Db;
use Cyron\Http\Request;
use Cyron\Analytics\ActivityTracker;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = Subscription::orderBy('price', 'asc')->get();
        $activePlan = null;

        if (Auth::check()) {
            $activePlan = UserSubscription::where('user_id', Auth::id())
                ->where('status', 'active')
                ->where('end_date', '>=', date('Y-m-d H:i:s'))
                ->orderBy('end_date', 'desc')
                ->first();
        }

        return view('subscriptions', compact('plans', 'activePlan'));
    }

    public function purchase(Request $request, $id)
    {
        $plan = Subscription::find($id);
        $user = Auth::user();

        if (!$plan || !$user) {
            return redirect()->route('subscriptions.plans')->with('error', 'پلن اشتراک پیدا نشد.');
        }

        if ($request->input('method', 'wallet') !== 'wallet') {
            return redirect()->route('subscriptions.plans')->with('notice', 'پرداخت درگاهی اشتراک پس از تست روی سرور عمومی فعال می‌شود.');
        }

        $active = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', date('Y-m-d H:i:s'))
            ->first();
        if ($active) return redirect()->route('subscriptions.plans')->with('error', 'شما در حال حاضر یک اشتراک فعال دارید.');

        $couponCode = trim((string) $request->input('coupon_code', ''));
        $pricing = (new DiscountService())->calculate('subscription', $plan, $couponCode !== '' ? $couponCode : null);
        if ($couponCode !== '' && !($pricing['coupon_valid'] ?? false)) {
            return redirect()->route('subscriptions.plans')->with('error', 'کد تخفیف معتبر نیست یا شرایط استفاده را ندارد.');
        }
        $amount = (float) $pricing['final_amount'];
        $discountRule = $pricing['discount'];

        $wallet = Wallet::where('user_id', $user->id)->first();
        if (!$wallet) $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 0]);
        if ((float) $wallet->balance < $amount) {
            return redirect()->route('subscriptions.plans')->with('error', 'موجودی کیف پول برای خرید این اشتراک کافی نیست.');
        }

        Db::transaction(function () use ($wallet, $amount, $user, $plan, $discountRule) {
            if (!$wallet->update(['balance' => (float) $wallet->balance - $amount])) {
                throw new \RuntimeException('Wallet update failed.');
            }
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'subscription',
                'status' => 'success',
                'description' => 'خرید اشتراک ' . $plan->name,
                'payment_method' => 'wallet',
                'payable_type' => Subscription::class,
                'payable_id' => $plan->id,
                'paid_at' => date('Y-m-d H:i:s'),
            ]);
            if (!$transaction) throw new \RuntimeException('Transaction creation failed.');

            if (!UserSubscription::create([
                'user_id' => $user->id,
                'subscription_id' => $plan->id,
                'start_date' => date('Y-m-d H:i:s'),
                'end_date' => date('Y-m-d H:i:s', strtotime('+' . (int) $plan->duration . ' days')),
                'status' => 'active',
                'trial_used' => 0,
                'payment_method' => 'wallet',
                'transaction_id' => (string) $transaction->id,
            ])) throw new \RuntimeException('Subscription creation failed.');

            if (!WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'withdraw',
                'description' => 'خرید اشتراک ' . $plan->name,
                'transaction_id' => $transaction->id,
            ])) throw new \RuntimeException('Wallet transaction creation failed.');

            if ($discountRule) {
                if (!$discountRule->update(['usage_count' => (int) $discountRule->usage_count + 1])) {
                    throw new \RuntimeException('Discount usage update failed.');
                }
            }
        });

        ActivityTracker::record('subscription.purchased', [
            'subject_type' => Subscription::class,
            'subject_id' => (int) $plan->id,
            'amount' => $amount,
        ], (int) $user->id);

        return redirect()->route('subscriptions.plans')->with('notice', 'اشتراک «' . $plan->name . '» با موفقیت فعال شد.');
    }
}
