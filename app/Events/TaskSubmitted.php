<?php

namespace App\Events;

use App\Models\TaskSubmission;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskSubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public TaskSubmission $submission,
        public string $message = ''
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->submission->task->assigned_by),
        ];
    }

    public function broadcastAs(): string
    {
        return 'task.submitted';
    }

    public function broadcastWith(): array
    {
        return [
            'submission_id' => $this->submission->id,
            'task_id' => $this->submission->task->id,
            'task_title' => $this->submission->task->title,
            'submitted_by' => $this->submission->submitter?->name ?? 'Employee',
            'submission_notes' => $this->submission->submission_notes,
            'message' => $this->message ?: $this->submission->submitter?->name . ' has submitted their work for evaluation',
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
