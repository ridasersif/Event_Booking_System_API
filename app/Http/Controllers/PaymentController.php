<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    public function store(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to pay for this booking'
            ], 403);
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'message' => 'Payment can only be made for pending bookings'
            ], 422);
        }

        $paymentResult = DB::transaction(function () use ($booking) {
            $payment = $this->paymentService->processPayment($booking);

            if ($payment->status === 'success') {
                $booking->update(['status' => 'confirmed']);
            }

            return $payment;
        });

        return response()->json([
            'message' => $paymentResult->status === 'success' 
                ? 'Payment processed successfully' 
                : 'Payment failed',
            'data' => $paymentResult
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $payment = \App\Models\Payment::with('booking')->findOrFail($id);

        if ($payment->booking->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized to view this payment'
            ], 403);
        }

        return response()->json([
            'message' => 'Payment retrieved successfully',
            'data' => $payment
        ]);
    }
}