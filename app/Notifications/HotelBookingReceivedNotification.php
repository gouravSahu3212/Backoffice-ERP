<?php

namespace App\Notifications;

use App\Models\HotelBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HotelBookingReceivedNotification extends Notification
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
        $roomType = $this->booking->roomSlot->room_type_name ?? 'Room';
        $customer = $this->booking->customer_name;
        $price = number_format($this->booking->total_price, 2).' '.$this->booking->currency;

        return (new MailMessage)
            ->subject("New Hotel Room Booking Request [{$ref}]")
            ->greeting('Hello Admin,')
            ->line("A new room booking request [{$ref}] has been submitted.")
            ->line("Customer: {$customer}")
            ->line("Hotel: {$hotelName}")
            ->line("Room Category: {$roomType}")
            ->line("Check-in: {$this->booking->check_in?->format('Y-m-d')} | Check-out: {$this->booking->check_out?->format('Y-m-d')}")
            ->line("Total Price: {$price}")
            ->line('Please review and update the booking status in the backoffice.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Room Booking Received',
            'message' => "New hotel room booking request {$this->booking->booking_reference} received for {$this->booking->customer_name}.",
            'booking_id' => $this->booking->id,
            'booking_reference' => $this->booking->booking_reference,
            'type' => 'hotel_booking_received',
        ];
    }
}
