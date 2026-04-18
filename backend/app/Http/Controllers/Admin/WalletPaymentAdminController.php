<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletPaymentAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = WalletPaymentRequest::with('user.profile')
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(20);

        return view('admin.payments.index', compact('requests'));
    }

    public function approve(Request $request, WalletPaymentRequest $paymentRequest)
    {
        if ($paymentRequest->status !== 'pending') {
            return back()->with('error', 'Payment request already processed');
        }

        $paymentRequest->approve(auth()->user()->id);

        return back()->with('success', 'Payment request approved. Wallet credited successfully.');
    }

    public function reject(Request $request, WalletPaymentRequest $paymentRequest)
    {
        if ($paymentRequest->status !== 'pending') {
            return back()->with('error', 'Payment request already processed');
        }

        $paymentRequest->reject($request->note);

        return back()->with('success', 'Payment request rejected');
    }

    public function getSettings()
    {
        $settings = DB::table('app_settings')
            ->where('key', 'payment_upi_id')
            ->first();

        return response()->json([
            'upi_id' => $settings?->value ?? 'sample@upi',
        ]);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'upi_id' => 'required|string|max:255',
        ]);

        DB::table('app_settings')->updateOrInsert(
            ['key' => 'payment_upi_id'],
            [
                'value' => $request->upi_id,
                'description' => 'UPI ID for wallet recharge payments',
                'updated_at' => now(),
            ]
        );

        return back()->with('success', 'UPI ID updated successfully');
    }
}