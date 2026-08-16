<?php

namespace App\Notifications;

use App\Models\TourRequest;
use App\Models\TransferRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingRequestNotification extends Notification
{
    use Queueable;

    public function __construct(protected TourRequest|TransferRequest $request)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isTour = $this->request instanceof TourRequest;
        $type = $isTour ? 'Tour' : 'Transfer';
        $title = $isTour ? ($this->request->tour->title ?? 'N/A') : $this->request->title;
        $agentName = $this->request->agent->name ?? 'N/A';
        $customerName = $this->request->customer_name;
        $price = $this->request->total_price;
        $currency = $this->request->currency;

        $email = (new MailMessage)
            ->subject("New {$type} Booking Request [{$this->request->request_reference}]")
            ->greeting('Hello Admin,')
            ->line("A new {$type} booking request has been submitted by agent: {$agentName}.")
            ->line("Reference: {$this->request->request_reference}")
            ->line("Customer Name: {$customerName}")
            ->line("Price: {$price} {$currency}");

        if ($isTour) {
            $departureDate = $this->request->departure_date ? $this->request->departure_date->format('M d, Y') : 'N/A';
            $email->line("Tour Title: {$title}")
                ->line("Departure Date: {$departureDate}")
                ->line("Pax: {$this->request->pax}");
        } else {
            $pickupDate = $this->request->pickup_date ? $this->request->pickup_date->format('M d, Y') : 'N/A';
            $pickupTime = $this->request->pickup_time ?? 'N/A';
            $email->line("Transfer Title: {$title}")
                ->line("Route: {$this->request->route_label}")
                ->line("Pickup Date/Time: {$pickupDate} at {$pickupTime}");
        }

        return $email;
    }
}
