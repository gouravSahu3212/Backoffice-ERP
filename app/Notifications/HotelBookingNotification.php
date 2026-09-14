<?php

namespace App\Notifications;

use App\Models\HotelBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HotelBookingNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected HotelBooking $booking,
        protected string $recipientType = 'admin'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $hotelName = $this->booking->hotel->name ?? 'Hotel';
        $slotName = $this->booking->slot->name ?? 'Room';
        $checkIn = $this->booking->check_in ? $this->booking->check_in->format('M d, Y') : 'N/A';
        $checkOut = $this->booking->check_out ? $this->booking->check_out->format('M d, Y') : 'N/A';
        $ref = $this->booking->booking_reference;
        $price = number_format($this->booking->total_price, 2);
        $currency = $this->booking->currency;

        if ($this->recipientType === 'admin') {
            return (new MailMessage)
                ->subject("New Hotel Room Booking [{$ref}]")
                ->greeting('Hello Admin,')
                ->line('A new hotel room booking has been placed.')
                ->line("Booking Ref: {$ref}")
                ->line("Hotel: {$hotelName}")
                ->line("Room: {$slotName}")
                ->line("Check-in: {$checkIn} | Check-out: {$checkOut}")
                ->line("Guests: {$this->booking->guests}")
                ->line("Customer: {$this->booking->customer_name} ({$this->booking->customer_email})")
                ->line("Total Price: {$price} {$currency}");
        }

        return (new MailMessage)
            ->subject("Hotel Booking Confirmation [{$ref}] - {$hotelName}")
            ->greeting("Hello {$this->booking->customer_name},")
            ->line('Your hotel room booking has been successfully confirmed!')
            ->line("Booking Reference: {$ref}")
            ->line("Hotel: {$hotelName}")
            ->line("Room Category: {$slotName}")
            ->line("Check-in Date: {$checkIn}")
            ->line("Check-out Date: {$checkOut}")
            ->line("Guests: {$this->booking->guests}")
            ->line("Total Amount Paid: {$price} {$currency}")
            ->line('Thank you for booking with us!');
    }
}
