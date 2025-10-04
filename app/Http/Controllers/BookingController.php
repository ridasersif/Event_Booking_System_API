<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Ticket;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $bookings = Booking::with('ticket.event', 'payment')
            ->where('user_id', $request->user()->id)
            ->paginate(10);

        return response()->json([
            'message' => 'Bookings retrieved successfully',
            'data' => $bookings
        ]);
    }

    public function store(Request $request, Ticket $ticket): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        if ($request->quantity > $ticket->available_quantity) {
            return response()->json([
                'message' => 'Not enough tickets available. Only ' . $ticket->available_quantity . ' left.'
            ], 422);
        }

        $booking = DB::transaction(function () use ($request, $ticket) {
            $booking = Booking::create([
                'user_id' => $request->user()->id,
                'ticket_id' => $ticket->id,
                'quantity' => $request->quantity,
                'status' => 'pending',
            ]);

            return $booking;
        });

        return response()->json([
            'message' => 'Booking created successfully. Please proceed to payment.',
            'data' => $booking->load('ticket.event')
        ], 201);
    }

    public function cancel(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to cancel this booking'
            ], 403);
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending bookings can be cancelled'
            ], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Booking cancelled successfully'
        ]);
    }
}