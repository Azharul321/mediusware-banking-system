<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{

    public function index(){
    $user = Auth::user();
    $transactions = $user->withdrawals; 
    $currentBalance = $user->balance; 

    return view('transactions.show', compact('transactions', 'currentBalance'));
    }

    // Example deposit method in a controller
    public function deposit(Request $request)
    {
        // Validate input
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $user = auth()->user();
        $amount = $request->input('amount');

        // Update user's account balance
        $user->balance += $amount;
        $user->save();

        // Record the transaction
        Transaction::create([
            'user_id' => $user->id,
            'transaction_type' => 'deposit',
            'amount' => $amount,
        ]);

        return back()->with('success', 'Deposit successful.');
    }


    public function withdraw(Request $request)
    {
        $user = auth()->user();
        $currentBalance = $user->balance;

            // Validate the request data
    $validatedData = $request->validate([
        'amount' => [
            'required',
            'numeric',
            'min:0.01', // Adjust this minimum value as needed
        ],
    ]);

    // Check if the withdrawal amount exceeds the current balance
    if ($validatedData['amount'] > $currentBalance) {
        throw ValidationException::withMessages([
            'message1' => 'Withdrawal amount cannot exceed your current balance.',
        ]);
    }
        $amount = $request->input('amount');

        // Check account type and apply the appropriate withdrawal rate
        $withdrawalRate = ($user->account_type === 'individual') ? 0.015 : 0.025;

        // Check if it's a Friday and apply the free withdrawal condition
        $today = now();
        if ($today->dayOfWeek === Carbon::FRIDAY) {
            // Friday withdrawal is free of charge
            $withdrawalRate = 0;
        }

        // Check the total monthly withdrawal for individual accounts
        if ($user->account_type === 'individual') {
            $monthlyWithdrawals = $user->withdrawals()
                ->whereMonth('created_at', '=', $today->month)
                ->sum('amount');

            // The first 5K withdrawal each month is free
            if ($monthlyWithdrawals <= 5000) {
                $withdrawalRate = 0;
            }
        }

        // Apply the first 1K withdrawal per transaction free condition
        if ($amount <= 1000) {
            $withdrawalRate = 0;
        }

        // Decrease withdrawal fee for Business accounts after a total withdrawal of 50K
        if ($user->account_type === 'Business') {
            $totalWithdrawals = $user->withdrawals()->sum('amount');

            if ($totalWithdrawals >= 50000) {
                $withdrawalRate = 0.015;
            }
        }

        // Calculate withdrawal fee
        $withdrawalFee = $amount * $withdrawalRate;

        $totalToWithdraw = ($amount + $withdrawalFee);
        if ($totalToWithdraw > $currentBalance) {
            throw ValidationException::withMessages([
                'message1' => 'Withdrawal amount cannot exceed your current balance.',
            ]);
        }

        // Update user's balance
        $user->balance -= $totalToWithdraw;
        $user->save();

        // Record the transaction
        Transaction::create([
            'user_id' => $user->id,
            'transaction_type' => 'withdrawal',
            'amount' => $amount,
            'fee' => $withdrawalFee,
        ]);

        return back()->with('success', 'Withdrawal successful.');
    }
}
