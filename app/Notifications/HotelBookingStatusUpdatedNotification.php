<?php

namespace App\Notifications;

use App\Models\HotelBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HotelBookingStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected HotelBooking $booking
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ref = $this->booking->booking_reference;
        $hotelName = $this->booking->hotel->name ?? 'Hotel';
        $status = ucfirst($this->booking->status);

        return (new MailMessage)
            ->subject("Hotel Booking Status Updated [{$ref}] - {$status}")
            ->greeting('Hello '.$notifiable->name.',')
            ->line("The status of your hotel room booking [{$ref}] for {$hotelName} has been changed.")
            ->line("Booking Reference: {$ref}")
            ->line("Hotel: {$hotelName}")
            ->line("New Status: {$status}")
            ->line('Please log in to your agent dashboard to view full details.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Hotel Booking Status Updated',
            'message' => "Your hotel booking {$this->booking->booking_reference} status has been updated to {$this->booking->status}.",
            'booking_id' => $this->booking->id,
            'booking_reference' => $this->booking->booking_reference,
            'status' => $this->booking->status,
            'type' => 'hotel_booking_status_updated',
        ];
    }
}
