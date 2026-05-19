<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LeaveDecisionNotification extends Notification implements ShouldQueue
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
            'status' => $this->data['status'],
            'message' => $this->data['message'],
            'reviewed_by' => $this->data['reviewed_by'],
            'type' => $this->data['type'],
        ];
    }
}
