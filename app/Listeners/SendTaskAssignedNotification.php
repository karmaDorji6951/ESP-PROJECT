<?php

namespace App\Listeners;

use App\Events\TaskAssigned;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTaskAssignedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TaskAssigned $event): void
    {
        // Store notification in database
        $event->task->employee->notify(
            new \App\Notifications\TaskAssignedNotification($event->task)
        );
    }
}
