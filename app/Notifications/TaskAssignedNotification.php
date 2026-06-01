<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    public function __construct(public Task $task) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'description' => $this->task->description,
            'assigned_by' => $this->task->assigner?->name ?? 'Supervisor',
            'deadline' => $this->task->deadline?->format('Y-m-d'),
            'message' => 'You have been assigned a new task: ' . $this->task->title,
            'type' => 'task_assigned',
        ];
    }
}
