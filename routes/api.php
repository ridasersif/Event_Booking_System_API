<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Events
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    
    Route::middleware('organizer')->group(function () {
        Route::post('/events', [EventController::class, 'store']);
        Route::put('/events/{event}', [EventController::class, 'update']);
        Route::delete('/events/{event}', [EventController::class, 'destroy']);
        
        // Tickets
        Route::post('/events/{event}/tickets', [TicketController::class, 'store']);
        Route::put('/tickets/{ticket}', [TicketController::class, 'update']);
        Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy']);
    });

    // Bookings
    Route::middleware('customer')->group(function () {
        Route::get('/bookings', [BookingController::class, 'index']);
        Route::post('/tickets/{ticket}/bookings', [BookingController::class, 'store'])
            ->middleware('prevent.duplicate.booking');
        Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
        
        // Payments
        Route::post('/bookings/{booking}/payment', [PaymentController::class, 'store']);
        Route::get('/payments/{id}', [PaymentController::class, 'show']);
    });
});