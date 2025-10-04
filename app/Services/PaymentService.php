<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Notifications\BookingConfirmed;
use Illuminate\Support\Facades\Notification;

class PaymentService
{
    public function processPayment(Booking $booking): Payment
    {
        $amount = $booking->total_amount;

        $status = $this->simulatePayment($amount) ? 'success' : 'failed';

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $amount,
            'status' => $status,
        ]);

        if ($status === 'success') {
            Notification::send($booking->user, new BookingConfirmed($booking));
        }

        return $payment;
    }

    private function simulatePayment(float $amount): bool
    {
        return rand(0, 100) > 20;
    }
}