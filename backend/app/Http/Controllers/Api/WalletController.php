<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function balance(Request $request)
    {
        $wallet = Wallet::where('user_id', auth()->id())->first();

        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => auth()->id(),
                'balance' => 0,
                'points' => 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $wallet
        ]);
    }

    public function topup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:10000|max:10000000',
            'payment_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $wallet = Wallet::firstOrCreate(
                ['user_id' => auth()->id()],
                ['balance' => 0, 'points' => 0]
            );

            // Here you would integrate with payment gateway
            // For now, we'll create a pending transaction
            
            $transaction = WalletTransaction::create([
                'user_id' => auth()->id(),
                'type' => 'credit',
                'amount' => $request->amount,
                'balance_before' => $wallet->balance,
                'balance_after' => $wallet->balance, // Will be updated after payment
                'description' => "Top-up saldo via {$request->payment_method}",
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Top-up request created',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'amount' => $request->amount,
                    'payment_method' => $request->payment_method,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create top-up: ' . $e->getMessage()
            ], 500);
        }
    }

    public function transactions(Request $request)
    {
        $transactions = WalletTransaction::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }
}