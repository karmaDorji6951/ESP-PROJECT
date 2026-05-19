<?php

namespace App\Helpers;

use App\Models\LeaveRequest;
use App\Models\Task;

class NotificationHelper
{
    public static function getNotificationUrl($notification)
    {
        $data = $notification->data;
        $type = $data['type'] ?? null;
        $userRole = auth()->user()?->role?->slug;
        
        switch ($type) {
            case 'task_assigned':
                if (isset($data['task_id'])) {
                    if ($userRole === 'staff') {
                        return route('staff.tasks.show', $data['task_id']);
                    }
                    if ($userRole === 'supervisor') {
                        return route('supervisor.tasks.show', $data['task_id']);
                    }
                }
                break;

            case 'task_submitted':
                if (isset($data['task_id'])) {
                    if ($userRole === 'supervisor') {
                        return route('supervisor.tasks.evaluation.create', $data['task_id']);
                    }
                    // Staff can't evaluate; send them to task details if available.
                    if ($userRole === 'staff') {
                        return route('staff.tasks.show', $data['task_id']);
                    }
                }
                break;

            case 'task_completed':
                // Route to task details for supervisor/admin
                if (isset($data['task_id'])) {
                    if ($userRole === 'supervisor') {
                        return route('supervisor.tasks.show', $data['task_id']);
                    } elseif ($userRole === 'staff') {
                        return route('staff.tasks.show', $data['task_id']);
                    } elseif ($userRole === 'admin') {
                        // Admin might not have task show route, redirect to task index
                        return route('admin.dashboard');
                    }
                }
                break;
                
            case 'leave_request':
                // Route to leave request details for supervisor
                if (isset($data['leave_request_id'])) {
                    $userRole = auth()->user()?->role?->slug;
                    if ($userRole === 'supervisor') {
                        return route('supervisor.leaves.show', $data['leave_request_id']);
                    } elseif ($userRole === 'admin') {
                        return route('admin.leaves.show', $data['leave_request_id']);
                    }
                }
                break;
                
            case 'leave_decision':
                // Route to leave request details for staff
                if (isset($data['leave_request_id'])) {
                    return route('staff.leaves.show', $data['leave_request_id']);
                }
                break;
                
            default:
                // Fallback: if it has a task_id, try to route to task details.
                if (isset($data['task_id'])) {
                    if ($userRole === 'supervisor') {
                        return route('supervisor.tasks.show', $data['task_id']);
                    }
                    if ($userRole === 'staff') {
                        return route('staff.tasks.show', $data['task_id']);
                    }
                }

                // Otherwise go to notifications page
                return route('notifications.index');
        }
        
        return route('notifications.index');
    }
    
    public static function getNotificationUrlWithMarkAsRead($notification)
    {
        // Mark as read first, then return the URL
        if (!$notification->read_at) {
            $notification->markAsRead();
        }
        return self::getNotificationUrl($notification);
    }
    
    public static function getNotificationIcon($notification)
    {
        $data = $notification->data;
        $type = $data['type'] ?? null;
        
        switch ($type) {
            case 'task_assigned':
                return '🆕';
            case 'task_submitted':
                return '📩';
            case 'task_completed':
                return '✅';
            case 'leave_request':
                return '📅';
            case 'leave_decision':
                return $data['status'] === 'Approved' ? '✅' : '❌';
            default:
                return '🔔';
        }
    }
    
    public static function getNotificationColor($notification)
    {
        $data = $notification->data;
        $type = $data['type'] ?? null;
        
        switch ($type) {
            case 'task_assigned':
                return 'primary';
            case 'task_submitted':
                return 'info';
            case 'task_completed':
                return 'success';
            case 'leave_request':
                return 'info';
            case 'leave_decision':
                return $data['status'] === 'Approved' ? 'success' : 'danger';
            default:
                return 'primary';
        }
    }
}
