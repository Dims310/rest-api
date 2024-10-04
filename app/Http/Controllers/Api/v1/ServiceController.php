<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServicePrice;
use App\Models\Transaction;
use DB;
use Exception;
use Illuminate\Http\Request;
use Str;
use Validator;

class ServiceController extends Controller
{
    public function buyServices(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'service_id' => 'required|integer',
            'bank' => 'required|in:bni,bca'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'data' => [
                    'message' => $validator->messages()->first()
                ]
            ], 422);
        }

        // get services
        $services = Service::where('id', $request->service_id)->first();

        if (!$services) {
            return response()->json([
                'error' => true,
                'data' => [
                    'message' => "Service not found, https://kegiatan.upnvj.ac.id/."
                ]
            ], 422);
        }
        
        // get current user
        $user = auth('api')->user();

        if ($request->user_id != $user->uuid) {
            return response()->json([
                'error' => true,
                'data' => [
                    'message' => 'Forbidden access.'
                ]
            ], 401);
        }

        try {
            DB::beginTransaction();

            $orderId = Str::uuid()->toString();
            $grossAmount = ServicePrice::where('role_id', $user->role_id)
                ->where('service_id', $request->service_id)
                ->get('price')->first();

            $transaction_details = [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount->price
            ];

            $customer_details = [
                'email'            => $user->email,
                'phone'            => $user->phone,
            ];

            $transaction_data = [
                'payment_type' => 'bank_transfer',
                'transaction_details' => $transaction_details,
                'bank_transfer' => [
                    'bank' => $request->bank
                ],
                'customer_details' => $customer_details
            ];

            $response = \Midtrans\CoreApi::charge($transaction_data);

            // NEED EXCEPTION! MIDTRANS DOESN'T HAS AN FAILED METHOD
            // if ($response->failed()) {
            //     throw new Exception("Simulated API failure");
            // }

            Transaction::insert([
                'uuid' => $orderId,
                'total_amount' => $grossAmount->price,
                'user_id' => $request->user_id,
                'service_id' => $request->service_id,
                'created_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'error' => false,
                'data' => [
                    'message' => $response->status_message,
                    'order_id' => $response->order_id,
                    'gross_amount' => (int)$response->gross_amount,
                    'transaction_status' => $response->transaction_status,
                    'va_number' => $response->va_numbers[0]->va_number,
                    'bank' => $response->va_numbers[0]->bank
                ]
            ], 201);
                
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => true,
                'data' => [
                    'message' => $e->getMessage(),
                ]
            ], 500);
        }
    }

    public function getServices() {
        $services = Service::all();

        return response()->json([
            'error' => false,
            'data' => [
                'message' => 'Success get all services.',
                'services' => $services
            ]
        ]);
    }

}
