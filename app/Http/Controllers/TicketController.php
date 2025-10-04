<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function store(Request $request, Event $event): JsonResponse
    {
        if ($event->created_by !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized to add tickets to this event'
            ], 403);
        }

        $request->validate([
            'type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        $ticket = Ticket::create([
            'type' => $request->type,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'event_id' => $event->id,
        ]);

        return response()->json([
            'message' => 'Ticket created successfully',
            'data' => $ticket
        ], 201);
    }

    public function update(Request $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->event->created_by !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized to update this ticket'
            ], 403);
        }

        $request->validate([
            'type' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $ticket->update($request->all());

        return response()->json([
            'message' => 'Ticket updated successfully',
            'data' => $ticket
        ]);
    }

    public function destroy(Request $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->event->created_by !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized to delete this ticket'
            ], 403);
        }

        $ticket->delete();

        return response()->json([
            'message' => 'Ticket deleted successfully'
        ]);
    }
}