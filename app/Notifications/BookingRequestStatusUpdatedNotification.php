<?php

namespace App\Notifications;

use App\Models\TourRequest;
use App\Models\TransferRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingRequestStatusUpdatedNotification extends Notification
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
        $status = ucfirst($this->request->status);

        return (new MailMessage)
            ->subject("Booking Request Status Updated [{$this->request->request_reference}]")
            ->greeting('Hello '.$notifiable->name.',')
            ->line("The status of your {$type} booking request has been updated.")
            ->line("Reference: {$this->request->request_reference}")
            ->line("Title: {$title}")
            ->line("New Status: {$status}")
            ->line('Please visit the dashboard to view more details.');
    }
}
