<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Booking $booking)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Confirmed - ' . $this->booking->ticket->event->title)
            ->line('Your booking has been confirmed!')
            ->line('Event: ' . $this->booking->ticket->event->title)
            ->line('Ticket Type: ' . $this->booking->ticket->type)
            ->line('Quantity: ' . $this->booking->quantity)
            ->line('Total Amount: $' . number_format($this->booking->total_amount, 2))
            ->action('View Event', url('/events/' . $this->booking->ticket->event->id))
            ->line('Thank you for using our service!');
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'event_title' => $this->booking->ticket->event->title,
            'amount' => $this->booking->total_amount,
        ];
    }
}