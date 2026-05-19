<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskCompletedNotification extends Notification implements ShouldQueue
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
            'task_id' => $this->data['task_id'],
            'title' => $this->data['title'],
            'description' => $this->data['description'],
            'completed_by' => $this->data['completed_by'],
            'completed_at' => $this->data['completed_at'],
            'message' => $this->data['message'],
            'type' => $this->data['type'],
        ];
    }
}
