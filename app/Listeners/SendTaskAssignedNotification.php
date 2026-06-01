<?php

namespace App\Listeners;

use App\Events\TaskAssigned;

class SendTaskAssignedNotification
{
    public function handle(TaskAssigned $event): void
    {
        // Store notification in database
        $employee = $event->task->employee;
        $user = $employee?->user;

        if ($user) {
            $user->notify(new \App\Notifications\TaskAssignedNotification($event->task));
        }
    }
}
