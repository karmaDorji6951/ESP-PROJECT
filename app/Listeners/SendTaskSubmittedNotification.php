<?php

namespace App\Listeners;

use App\Events\TaskSubmitted;

class SendTaskSubmittedNotification
{
    public function handle(TaskSubmitted $event): void
    {
        // Store notification in database for supervisor
        $supervisor = $event->submission->task->assigner;
        
        if ($supervisor) {
            $supervisor->notify(
                new \App\Notifications\TaskSubmittedNotification($event->submission)
            );
        }
    }
}
