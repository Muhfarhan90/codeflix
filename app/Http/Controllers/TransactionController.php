<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class TransactionController extends Controller
{
    public function __construct()
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('midtrans.is_3ds');
    }

    public function checkout(Request $request)
    {
        Log::info('Checkout Request:', $request->all());

        try {
            $user = Auth::user();
            Log::info('User authenticated:', ['user_id' => $user->id]);

            $validated = $request->validate([
                'plan_id' => 'required|exists:plans,id',
                'total_amount' => 'required|numeric|min:1',
            ]);
            Log::info('Validation passed:', $validated);

            $transaction_number = 'ORDER-' . time() . '-' . $user->id;

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'plan_id' => $validated['plan_id'],
                'transaction_number' => $transaction_number,
                'total_amount' => $validated['total_amount'],
                'payment_status' => 'pending'
            ]);
            Log::info('Transaction created:', ['id' => $transaction->id, 'total_amount' => $transaction->total_amount]);

            $payload = [
                'transaction_details' => [
                    'order_id' => $transaction->transaction_number,
                    'gross_amount' => (int) round($transaction->total_amount),
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => '08123456789',
                ],
                'item_details' => [
                    [
                        'id' => $transaction->plan_id,
                        'price' => (int) round($transaction->total_amount),
                        'quantity' => 1,
                        'name' => $transaction->plan->title,
                    ]
                ]
            ];
            Log::info('Payload created:', $payload);

            $snap_token = Snap::getSnapToken($payload);
            Log::info('Snap token received:', ['token' => substr($snap_token, 0, 20) . '...']);

            $transaction->update(['midtrans_snap_token' => $snap_token]);

            return response()->json([
                'status' => 'success',
                'snap_token' => $snap_token
            ]);
        } catch (Exception $e) {
            Log::error('Checkout error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        Log::info('Midtrans Callback Request:', $request->all());

        // Handle the callback logic here
        $serverKey = config('midtrans.server_key');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        if ($hashed == $request->signature_key) {
            $transaction = Transaction::with(['user', 'plan'])->where('transaction_number', $request->order_id)->first();

            if ($transaction) {
                $paymentStatus = 'pending';

                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $paymentStatus = 'success';

                    $user = $transaction->user;
                    $plan = $transaction->plan;

                    try {
                        DB::beginTransaction();

                        $user->memberships()->create([
                            'plan_id' => $plan->id,
                            'start_date' => now(),
                            'end_date' => now()->addDays($plan->duration),
                        ]);

                        $transaction->update([
                            'payment_status' => $paymentStatus,
                            'midtrans_transaction_id' => $request->transaction_id,
                        ]);

                        DB::commit();
                    } catch (Exception $e) {
                        Log::error('Failed to process successful payment:' . $e->getMessage());
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Failed to prcess membership'
                        ], 500);
                    }
                } elseif ($request->transaction_status == 'deny' || $request->transaction_status == 'cancel') {
                    $paymentStatus = 'failed';
                    $transaction->update([
                        'payment_status' => $paymentStatus,
                        'midtrans_transaction_id' => $request->transaction_id,
                    ]);
                } elseif ($request->transaction_status == 'expire') {
                    $paymentStatus = 'expired';
                    $transaction->update([
                        'payment_status' => $paymentStatus,
                        'midtrans_transaction_id' => $request->transaction_id,
                    ]);
                }
                Log::info('Transaction updated with payment status:', ['payment_status' => $paymentStatus]);
                return response()->json(['status' => 'success']);
            }
        }
        Log::error('Invalid signature in Midtrans callback:', $request->all());
        return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
    }
}
