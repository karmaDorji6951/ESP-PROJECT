<?php

namespace App\Notifications;

use App\Models\TaskSubmission;
use Illuminate\Notifications\Notification;

class TaskSubmittedNotification extends Notification
{
    public function __construct(public TaskSubmission $submission) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'submission_id' => $this->submission->id,
            'task_id' => $this->submission->task->id,
            'task_title' => $this->submission->task->title,
            'submitted_by' => $this->submission->submitter?->name ?? 'Employee',
            'submission_notes' => $this->submission->submission_notes,
            'message' => $this->submission->submitter?->name . ' has submitted their work for evaluation',
            'type' => 'task_submitted',
        ];
    }
}
