# Real-Time Notification System Setup Guide

This guide explains how to set up and configure the real-time notification system for work assignments and submissions in the ESP application.

## Overview

The real-time notification system includes:
- **Task Assignments**: Employees receive real-time notifications when supervisors assign work
- **Task Submissions**: Supervisors receive real-time notifications when employees submit completed work

## Architecture

### Database Layer
- **TaskSubmission Model**: Tracks when work is submitted for review
  - Fields: task_id, submitted_by, submission_notes, submission_status, submitted_at, reviewed_at
  - Relationships: belongs to Task and User

### Event System
- **TaskAssigned Event**: Fired when a supervisor assigns work to an employee
  - Broadcasts on private channel: `user.{employee_id}`
  - Contains: task_id, title, description, deadline, assigned_by, message

- **TaskSubmitted Event**: Fired when an employee submits work for evaluation
  - Broadcasts on private channel: `user.{supervisor_id}`
  - Contains: submission_id, task_id, task_title, submitted_by, submission_notes, message

### Notification Handlers
- **TaskAssignedNotification**: Stores notification in database
- **TaskSubmittedNotification**: Stores notification in database

### Frontend
- **NotificationManager Class**: Manages real-time listeners and notification display
- **Notification UI**: Toast-style notifications that auto-dismiss after 8 seconds

## Setup Instructions

### 1. Run Migrations
```bash
php artisan migrate
```

This will create:
- `task_submissions` table
- Add any new columns to existing tables

### 2. Install Laravel Echo (Recommended)

For real-time WebSocket support, install Laravel Echo:

```bash
npm install laravel-echo
```

Then install your WebSocket provider (choose one):

**Option A: Using Pusher (Cloud service)**
```bash
npm install pusher-js
```

Then configure in `.env`:
```
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
```

**Option B: Using Redis (Self-hosted)**
```bash
npm install redis
```

Then configure in `.env`:
```
BROADCAST_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

**Option C: Using database polling (Development)**
Keep `BROADCAST_DRIVER=redis` but disable actual Redis:
```
BROADCAST_DRIVER=log
```

### 3. Configure Laravel Echo in your frontend

Create `resources/js/echo.js`:
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// For Pusher
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// For Redis
window.Echo = new Echo({
    broadcaster: 'redis',
    host: window.location.hostname,
    port: 6379,
});
```

Then import in your blade view after loading:
```blade
<script src="https://js.pusher.com/8.0.0/pusher.min.js"></script>
```

### 4. Broadcasting Authorization

The system uses private channels that are secured by the `routes/channels.php` file. Users can only listen to notifications intended for them.

## Usage

### Assigning a Task

```php
$task = Task::find($id);
TaskAssigned::dispatch($task);
```

Or use the API endpoint:
```bash
POST /api/tasks/{task}/assign-notification
```

### Submitting Work

```php
$submission = TaskSubmission::create([
    'task_id' => $task->id,
    'submitted_by' => auth()->id(),
    'submission_notes' => 'Completed the task',
]);

TaskSubmitted::dispatch($submission);
```

Or use the API endpoint:
```bash
POST /api/tasks/{task}/submit
Body: {
    "submission_notes": "Completed the task"
}
```

### Checking Submission Status

```bash
GET /api/tasks/{task}/submission-status
```

## Frontend Notification System

The dashboard includes a `NotificationManager` class that:

1. **Listens for broadcasts** from Laravel Echo
2. **Displays notifications** as toast messages in the top-right corner
3. **Auto-dismisses** notifications after 8 seconds
4. **Allows manual dismissal** via close button
5. **Pauses auto-dismiss** on hover

### Notification Types

- **success**: Task assignments and confirmations (green/teal accent)
- **info**: Task submissions and updates (blue accent)
- **warning**: Pending actions (amber accent)
- **error**: Failures and errors (red accent)

## Database Schema

### task_submissions table
```sql
CREATE TABLE task_submissions (
    id BIGINT PRIMARY KEY,
    task_id BIGINT FOREIGN KEY,
    submitted_by BIGINT FOREIGN KEY (users),
    submission_notes TEXT,
    submission_data JSON,
    submission_status ENUM ('Submitted', 'Under Review', 'Approved', 'Rejected'),
    submitted_at TIMESTAMP,
    reviewed_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Testing

### Test Task Assignment
```php
// In controller or test
$task = Task::find(1);
TaskAssigned::dispatch($task);
```

### Test Task Submission
```php
$submission = TaskSubmission::create([
    'task_id' => 1,
    'submitted_by' => 2,
    'submission_notes' => 'Work completed',
]);
TaskSubmitted::dispatch($submission);
```

## Troubleshooting

### Notifications not appearing
1. **Check console errors**: Open browser DevTools > Console
2. **Verify Laravel Echo is loaded**: Check if `window.Echo` exists
3. **Check authentication**: Ensure user is authenticated
4. **Check broadcasting driver**: Verify `BROADCAST_DRIVER` in .env

### Redis not connecting
1. Ensure Redis server is running: `redis-server`
2. Check Redis host/port in .env
3. Try a different driver (Pusher) for testing

### Pusher not working
1. Verify API credentials in .env
2. Check Pusher dashboard for event logs
3. Ensure `pusher-js` is installed

## API Endpoints

### POST `/api/tasks/{task}/submit`
Submit work for evaluation
```json
{
    "submission_notes": "Completed all requirements"
}
```

### GET `/api/tasks/{task}/submission-status`
Check if task has been submitted
```json
{
    "submitted": true,
    "submission": { ... }
}
```

### POST `/api/tasks/{task}/assign-notification`
Trigger assignment notification
```json
{
    "success": true,
    "message": "Task assigned and notification sent"
}
```

## Security

- All notifications use **private channels** (user.{id})
- Only the intended recipient can access the channel
- Broadcasting authorization is handled by Laravel's authorization system
- All API endpoints require authentication via Sanctum

## Performance Considerations

- Notifications auto-dismiss after 8 seconds to prevent UI clutter
- Private channels ensure only relevant notifications are delivered
- Database polling fallback works without additional infrastructure
- Redis/Pusher provide true real-time with minimal latency

## Future Enhancements

- Email notifications in addition to in-app
- Notification history/archive
- Notification preferences per user
- Batch notifications for multiple events
- Notification sounds/desktop alerts

## Support

For issues or questions about the notification system:
1. Check Laravel Broadcasting documentation: https://laravel.com/docs/broadcasting
2. Review Laravel Echo documentation: https://laravel.com/docs/broadcasting#using-the-js-echo-library
3. Check BROADCAST_DRIVER configuration in config/broadcasting.php
