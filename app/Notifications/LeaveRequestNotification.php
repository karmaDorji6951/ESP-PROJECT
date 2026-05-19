<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LeaveRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $data) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'leave_request_id' => $this->data['leave_request_id'],
            'leave_type' => $this->data['leave_type'],
            'start_date' => $this->data['start_date'],
            'end_date' => $this->data['end_date'],
            'requested_by' => $this->data['requested_by'],
            'message' => $this->data['message'],
            'type' => $this->data['type'],
        ];
    }
}
