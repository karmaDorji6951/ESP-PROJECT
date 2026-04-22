# Real-Time Notifications - Configuration Reference

## Environment Variables (.env)

### Broadcasting Configuration
```ini
# Broadcasting driver: log, redis, pusher, database
BROADCAST_DRIVER=redis

# Redis Connection (if using Redis)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Pusher Configuration (if using Pusher)
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=api-mt1.pusher.com
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

# Frontend Vite Variables
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# Queue Configuration (for background listeners)
QUEUE_CONNECTION=sync  # or redis, database
```

## Laravel Configuration Files

### config/broadcasting.php
Already configured by Laravel. Key settings:

```php
'default' => env('BROADCAST_DRIVER', 'log'),

'connections' => [
    'pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true,
        ],
    ],

    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
    ],

    'log' => [
        'driver' => 'log',
    ],

    'database' => [
        'driver' => 'database',
        'connection' => null,
        'table' => 'broadcasts',
    ],
],
```

### routes/channels.php
Broadcasting channel authorization:

```php
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private user notification channels
// Access pattern: user.{user_id}
// Example: user.1, user.2, user.3
```

## Application Structure

### Event Classes

**app/Events/TaskAssigned.php**
- Implements: ShouldBroadcast
- Broadcasts to: `PrivateChannel('user.' . $employee_id)`
- Data: task_id, title, description, deadline, assigned_by, message

**app/Events/TaskSubmitted.php**
- Implements: ShouldBroadcast
- Broadcasts to: `PrivateChannel('user.' . $supervisor_id)`
- Data: submission_id, task_id, task_title, submitted_by, submission_notes, message

### Event Listeners

**app/Listeners/SendTaskAssignedNotification.php**
- Triggers: TaskAssignedNotification
- Stores: Database notification record

**app/Listeners/SendTaskSubmittedNotification.php**
- Triggers: TaskSubmittedNotification
- Stores: Database notification record

### Notification Classes

**app/Notifications/TaskAssignedNotification.php**
- Channel: 'database'
- Type: 'task_assigned'
- Recipients: Assigned employee

**app/Notifications/TaskSubmittedNotification.php**
- Channel: 'database'
- Type: 'task_submitted'
- Recipients: Task assigner (supervisor)

## Database Tables

### notifications (Laravel built-in)
```sql
CREATE TABLE notifications (
    id UUID PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255),
    notifiable_id BIGINT,
    data JSON,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### task_submissions (Custom)
```sql
CREATE TABLE task_submissions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    task_id BIGINT NOT NULL,
    submitted_by BIGINT NOT NULL,
    submission_notes TEXT,
    submission_data JSON,
    submission_status ENUM('Submitted', 'Under Review', 'Approved', 'Rejected'),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id),
    FOREIGN KEY (submitted_by) REFERENCES users(id)
);
```

## Frontend Integration

### JavaScript Classes

**NotificationManager** (defined in welcome.blade.php)
```javascript
Properties:
- container: DOM element for notifications
- notifications: Array of notification IDs

Methods:
- showNotification(title, message, type, icon)
- removeNotification(id)
- initializeEcho()
- listenForTaskAssignments()
- listenForTaskSubmissions()
```

### Echo Listeners

```javascript
// Listen for task assignments
Echo.private(`user.${userId}`)
    .listen('TaskAssigned', (data) => {
        // Show notification
    });

// Listen for task submissions  
Echo.private(`user.${userId}`)
    .listen('TaskSubmitted', (data) => {
        // Show notification
    });
```

### CSS Classes

```css
.notifications-container    /* Main container */
.notification              /* Notification toast */
.notification.success      /* Success styled notification */
.notification.info         /* Info styled notification */
.notification.warning      /* Warning styled notification */
.notification.error        /* Error styled notification */
.notification-icon         /* Emoji icon */
.notification-title        /* Notification title */
.notification-message      /* Notification message */
.notification-close        /* Close button */
```

## API Endpoints

### Task Submission
```
POST /api/tasks/{task}/submit
Headers: Authorization: Bearer {token}
Body: {
    "submission_notes": "string (min:10)"
}
Response: {
    "success": true,
    "message": "Work submitted for evaluation",
    "submission": {...}
}
```

### Check Submission Status
```
GET /api/tasks/{task}/submission-status
Headers: Authorization: Bearer {token}
Response: {
    "submitted": boolean,
    "submission": {...} | null
}
```

### Assign with Notification
```
POST /api/tasks/{task}/assign-notification
Headers: Authorization: Bearer {token}
Response: {
    "success": true,
    "message": "Task assigned and notification sent",
    "task": {...}
}
```

## Event Dispatch Flow

### Task Assignment Flow
```
1. POST /api/tasks/{id}/assign-notification
   ↓
2. TaskController.assignTaskWithNotification()
   ↓
3. TaskAssigned::dispatch($task)
   ↓
4. Event broadcast to Redis/Pusher
   ↓
5. SendTaskAssignedNotification listener
   ↓
6. TaskAssignedNotification sent to user
   ↓
7. Stored in notifications table
   ↓
8. Echo client receives broadcast
   ↓
9. NotificationManager displays toast
```

### Task Submission Flow
```
1. POST /api/tasks/{id}/submit
   ↓
2. TaskController.submitTask()
   ↓
3. TaskSubmission record created
   ↓
4. TaskSubmitted::dispatch($submission)
   ↓
5. Event broadcast to Redis/Pusher
   ↓
6. SendTaskSubmittedNotification listener
   ↓
7. TaskSubmittedNotification sent to supervisor
   ↓
8. Stored in notifications table
   ↓
9. Echo client receives broadcast
   ↓
10. NotificationManager displays toast
```

## Performance Tuning

### Broadcasting Options by Load

**Low Load (< 100 concurrent users)**
- Use: Database or Log driver
- Setup: None required
- Latency: 1-10 seconds

**Medium Load (100-1000 concurrent users)**
- Use: Redis
- Setup: Install redis-server, configure connection
- Latency: 100-500ms

**High Load (> 1000 concurrent users)**
- Use: Pusher or dedicated server
- Setup: External service or dedicated instance
- Latency: < 100ms

## Debugging

### Enable Debug Mode
```ini
APP_DEBUG=true
LOG_CHANNEL=stack
LOG_LEVEL=debug
```

### Monitor Broadcasting
```bash
# Watch Redis events
redis-cli subscribe '*'

# Watch Laravel logs
tail -f storage/logs/laravel.log

# Test broadcasting
php artisan tinker
>>> event(new App\Events\TaskAssigned($task));
```

## Security Considerations

1. **Private Channels**: Only authenticated users on private channels
2. **Sanctum Tokens**: API endpoints use Sanctum token authentication
3. **Model Authorization**: Events include user authorization checks
4. **Database Isolation**: Notifications filtered by user
5. **CORS**: Properly configured for WebSocket origins
6. **Encryption**: Use HTTPS/WSS in production

## Monitoring

### Health Checks
```php
// Check if broadcasting is configured
config('broadcasting.default');

// Check if event listener is registered
app('events');

// Verify database notifications table
DB::table('notifications')->count();
```

### Metrics to Track
- Notification delivery latency
- Failed broadcasts
- Listener errors
- Redis/Pusher connections
- Queue backlog (if using queue)

## Common Configuration Mistakes

1. ❌ BROADCAST_DRIVER=log (no real-time)
   ✅ Set to: redis, pusher, or database

2. ❌ Forget to run migrations
   ✅ Run: php artisan migrate

3. ❌ Echo not loaded in blade
   ✅ Add: <script type="module" src="echo.js"></script>

4. ❌ Wrong Redis port/host
   ✅ Verify in .env: REDIS_HOST=127.0.0.1 REDIS_PORT=6379

5. ❌ Missing Pusher credentials
   ✅ Add all PUSHER_* variables to .env

## Environment-Specific Configs

### Development
```ini
BROADCAST_DRIVER=redis
APP_DEBUG=true
```

### Staging
```ini
BROADCAST_DRIVER=pusher
APP_DEBUG=false
```

### Production
```ini
BROADCAST_DRIVER=pusher
APP_DEBUG=false
PUSHER_APP_SECURE=true
```

## Testing

### Unit Test Example
```php
public function test_task_assigned_event_broadcasts()
{
    Queue::fake();
    Event::fake();
    
    $task = Task::factory()->create();
    
    TaskAssigned::dispatch($task);
    
    Event::assertDispatched(TaskAssigned::class);
}
```

### Feature Test Example
```php
public function test_notification_received_in_real_time()
{
    $user = User::factory()->create();
    
    $this->actingAs($user);
    
    // Simulate broadcast reception
    $this->assertNotNull($user->notifications);
}
```

## Version Compatibility

- Laravel: 11.x
- PHP: 8.1+
- MySQL: 5.7+
- Redis: 6.0+ (if using Redis)
- Node.js: 16+ (for frontend tools)
