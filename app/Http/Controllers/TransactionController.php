<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Validator;

class TransactionController extends Controller
{
    public function getAllTransactions() {
        $user = auth('api')->user();
        $transaction = Transaction::where('user_id', $user->id)->get();

        if ($user->role->name === "admin") {
            $transaction = Transaction::all();
        }

        return response()->json([
            'error' => false,
            'data' => [
                'message' => "Success get all transaction data",
                'transaction' => $transaction
            ]
        ]);
    }
}
