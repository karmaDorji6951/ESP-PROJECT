<?php

namespace App\Listeners;

use App\Events\TaskSubmitted;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTaskSubmittedNotification implements ShouldQueue
{
    use InteractsWithQueue;

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
