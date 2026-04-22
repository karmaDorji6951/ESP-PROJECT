<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Task $task,
        public string $message = ''
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->task->employee_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'task.assigned';
    }

    public function broadcastWith(): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'description' => $this->task->description,
            'deadline' => $this->task->deadline,
            'assigned_by' => $this->task->assigner?->name ?? 'Supervisor',
            'message' => $this->message ?: 'You have been assigned a new task: ' . $this->task->title,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
