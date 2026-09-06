<?php

namespace App\Http\Controllers\User;

use App\Http\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Cyron\Authentication\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);
        }

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('user/wallet', compact('user', 'wallet', 'transactions'));
    }
}