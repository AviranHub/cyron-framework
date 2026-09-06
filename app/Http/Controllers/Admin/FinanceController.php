<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\WalletTransaction;

class FinanceController extends Controller
{
    public function overview()
    {
        $successful = Transaction::where('status', 'success')->get();
        $failedCount = Transaction::where('status', 'failed')->count();
        $activeSubscriptions = UserSubscription::where('status', 'active')
            ->where('end_date', '>=', date('Y-m-d H:i:s'))
            ->count();
        $totalRevenue = 0;
        $bookRevenue = 0;
        $subscriptionRevenue = 0;
        $walletCharges = 0;

        foreach ($successful as $transaction) {
            $amount = (float) ($transaction->amount ?? 0);
            $totalRevenue += $amount;
            if ($transaction->type === 'purchase') $bookRevenue += $amount;
            if ($transaction->type === 'subscription') $subscriptionRevenue += $amount;
            if ($transaction->type === 'charge') $walletCharges += $amount;
        }

        return view('admin/finance-dashboard', compact(
            'totalRevenue', 'bookRevenue', 'subscriptionRevenue',
            'walletCharges', 'failedCount', 'activeSubscriptions'
        ));
    }

    public function transactions()
    {
        $query = Transaction::query();
        $this->applyFilters($query, ['status', 'type']);
        return $this->render($query->orderBy('created_at', 'desc')->paginate(30), 'transactions', 'تراکنش‌های مالی');
    }

    public function walletTransactions()
    {
        $query = WalletTransaction::query();
        $this->applyFilters($query, ['type']);
        return $this->render($query->orderBy('created_at', 'desc')->paginate(30), 'wallet-transactions', 'گردش کیف پول');
    }

    public function subscriptions()
    {
        $query = UserSubscription::query();
        $this->applyFilters($query, ['status']);
        return $this->render($query->orderBy('created_at', 'desc')->paginate(30), 'user-subscriptions', 'اشتراک‌های کاربران');
    }

    private function render($items, string $kind, string $title)
    {
        $users = [];
        foreach ($items as $item) {
            $userId = (int) ($item->user_id ?? 0);
            if ($userId && !isset($users[$userId])) $users[$userId] = User::find($userId);
        }

        return view('admin/finance', compact('items', 'kind', 'title', 'users'));
    }

    private function applyFilters($query, array $fields): void
    {
        foreach ($fields as $field) {
            $value = trim((string) request()->input($field, ''));
            if ($value !== '') $query->where($field, $value);
        }

        $from = trim((string) request()->input('date_from', ''));
        $to = trim((string) request()->input('date_to', ''));
        if ($from !== '') $query->where('created_at', '>=', $from . ' 00:00:00');
        if ($to !== '') $query->where('created_at', '<=', $to . ' 23:59:59');
    }
}