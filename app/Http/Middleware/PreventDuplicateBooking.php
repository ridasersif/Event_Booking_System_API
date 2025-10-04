<?php

namespace App\Http\Middleware;

use App\Models\Booking;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventDuplicateBooking
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $ticketId = $request->route('id');

        $existingBooking = Booking::where('user_id', $user->id)
            ->where('ticket_id', $ticketId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingBooking) {
            return response()->json([
                'message' => 'You already have an active booking for this ticket.'
            ], 409);
        }

        return $next($request);
    }
}