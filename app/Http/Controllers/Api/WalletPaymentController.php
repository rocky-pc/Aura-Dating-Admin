<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WalletPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletPaymentController extends Controller
{
    public function getUpiId()
    {
        $upiId = DB::table('app_settings')
            ->where('key', 'payment_upi_id')
            ->value('value');

        return response()->json([
            'upi_id' => $upiId ?? 'sample@upi',
        ]);
    }

    public function createRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:10',
            'utr_number' => 'required|string|unique:wallet_payment_requests,utr_number',
            'screenshot' => 'sometimes|file|image|max:2048',
        ]);

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')->store('payment-screenshots', 'public');
        }

        $paymentRequest = WalletPaymentRequest::create([
            'user_id' => auth()->user()->id,
            'amount' => $request->amount,
            'utr_number' => $request->utr_number,
            'screenshot_path' => $screenshotPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Payment request submitted successfully',
            'request' => $paymentRequest,
        ], 201);
    }

    public function myRequests()
    {
        $requests = WalletPaymentRequest::where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($requests);
    }
}
