<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function showTransactions()
    {
        $userId = Auth::id();
        // Retrieve user and transactions
        $user = User::with('transactions')->find($userId);
        // Return data
        return response()->json([
            'user' => $user,
        ]);
    }

    public function showDeposits()
    {
        $userId = Auth::id();
        $deposits = Transaction::where('user_id', $userId)
            ->where('transaction_type', 'deposit')
            ->get();

        return response()->json([
            'deposits' => $deposits,
        ]);
    }

    public function deposit(Request $request)
    {
        $userId = Auth::id();
        $user = User::findOrFail($userId);
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);
        $amount = $request->input('amount');
        // Update user balance
        $user->balance += $amount;
        $user->save();
        // Create deposit transaction
        Transaction::create([
            'user_id' => $userId,
            'transaction_type' => 'deposit',
            'amount' => $amount,
            'fee' => 0,
            'date' => now(),
        ]);

        return response()->json(['message' => 'Deposit successful']);
    }

    public function showWithdrawals()
    {
        $userId = Auth::id();
        $withdrawals = Transaction::where('user_id', $userId)
            ->where('transaction_type', 'withdrawal')
            ->get();

        return response()->json([
            'withdrawals' => $withdrawals,
        ]);
    }

    public function withdrawal(Request $request)
    {
        $userId = Auth::id();
        $user = User::findOrFail($userId);

        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $amount = $request->input('amount');

        if ($user->balance < $amount){
            return response()->json(['message' => 'Insufficient Balance']);
        }

        $accountType = $user->account_type;

        // Apply withdrawal conditions
        $freeWithdrawalLimitPerTransaction = 1000;
        $freeWithdrawalLimitPerMonth = 5000;
        $individualWithdrawalRate = 0.015;
        $businessWithdrawalRate = 0.025;
        $businessWithdrawalLimit = 50000;

        $fee = 0;

        // Check if it's Friday
        if (now()->dayOfWeek == Carbon::FRIDAY) {
            $fee = 0;
        } else {
            if ($amount > $freeWithdrawalLimitPerTransaction) {
                $fee = ($amount - $freeWithdrawalLimitPerTransaction) * ($accountType == 'Individual' ? $individualWithdrawalRate : $businessWithdrawalRate);
            }

            $totalWithdrawalsThisMonth = Transaction::where('user_id', $userId)
                ->where('transaction_type', 'withdrawal')
                ->whereMonth('date', now()->month)
                ->sum('amount');

            if ($totalWithdrawalsThisMonth + $amount > $freeWithdrawalLimitPerMonth) {
                $fee = ($totalWithdrawalsThisMonth + $amount - $freeWithdrawalLimitPerMonth) * ($accountType == 'Individual' ? $individualWithdrawalRate : $businessWithdrawalRate);
            }

            // Check if the user is a Business account and has exceeded the withdrawal threshold
            if ($accountType == 'Business' && $totalWithdrawalsThisMonth > $businessWithdrawalLimit) {
                $fee = $fee * $individualWithdrawalRate / $businessWithdrawalRate;
            }
        }
        // Update user balance
        $user->balance -= $amount;
        $user->save();

        // Create withdrawal transaction
        Transaction::create([
            'user_id' => $userId,
            'transaction_type' => 'withdrawal',
            'amount' => $amount,
            'fee' => $fee,
            'date' => now(),
        ]);

        return response()->json(['message' => 'Withdrawal successful']);
    }
}
