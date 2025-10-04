<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Traits\CommonQueryScopes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EventController extends Controller
{
    use CommonQueryScopes;

    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'events_' . md5(serialize($request->all()));
        
        $events = Cache::remember($cacheKey, 3600, function () use ($request) {
            $query = Event::with('user', 'tickets');

            if ($request->has('search')) {
                $query = $this->searchByTitle($query, $request->search);
            }

            if ($request->has('date')) {
                $query = $this->filterByDate($query, $request->date);
            }

            if ($request->has('location')) {
                $query->where('location', 'like', '%' . $request->location . '%');
            }

            return $query->paginate(10);
        });

        return response()->json([
            'message' => 'Events retrieved successfully',
            'data' => $events
        ]);
    }

    public function show(Event $event): JsonResponse
    {
        $event->load('user', 'tickets');

        return response()->json([
            'message' => 'Event retrieved successfully',
            'data' => $event
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
        ]);

        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'location' => $request->location,
            'created_by' => $request->user()->id,
        ]);

        Cache::forget('events_*');

        return response()->json([
            'message' => 'Event created successfully',
            'data' => $event
        ], 201);
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        if ($event->created_by !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized to update this event'
            ], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'date' => 'sometimes|date|after:now',
            'location' => 'sometimes|string|max:255',
        ]);

        $event->update($request->all());

        Cache::forget('events_*');

        return response()->json([
            'message' => 'Event updated successfully',
            'data' => $event
        ]);
    }

    public function destroy(Request $request, Event $event): JsonResponse
    {
        if ($event->created_by !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized to delete this event'
            ], 403);
        }

        $event->delete();

        Cache::forget('events_*');

        return response()->json([
            'message' => 'Event deleted successfully'
        ]);
    }
}