<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Validator;

class TransactionController extends Controller
{
    public function getAllTransactions() {
        $user = auth('api')->user();
        // $transaction = Transaction::where('user_id', $user->uuid)->get();
        $transaction = Transaction::with('service')->where('user_id', $user->uuid)->get();

        $transactionData = $transaction->map(function ($transaction) {
            return [
                'uuid' => $transaction->uuid,
                'total_amount' => $transaction->total_amount,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
                'services' => $transaction->service->name
            ];
        });

        if ($user->role->name === "admin") {
            $transaction = Transaction::all();
        }

        return response()->json([
            'error' => false,
            'data' => [
                'message' => "Success get all transaction data",
                'transaction' => $transactionData
            ]
        ]);
    }
}
