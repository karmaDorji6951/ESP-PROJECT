# Real-Time Notifications - Quick Start Guide

## What's New

Your ESP application now includes a complete real-time notification system for:
- ✅ Instant notifications when employees are assigned work
- ✅ Instant notifications when supervisors receive work submissions
- ✅ Beautiful toast-style notifications in the dashboard
- ✅ Secure private channel communication

## Quick Setup (5 minutes)

### Step 1: Run Migrations
```bash
php artisan migrate
```

### Step 2: Choose Your Broadcasting Method

Choose ONE option below:

#### Option A: Redis (Best for Development/Self-Hosted)
```bash
# Redis is usually already installed. Just set:
BROADCAST_DRIVER=redis
```
Then verify Redis is running:
```bash
redis-server
```

#### Option B: Pusher (Best for Cloud/Easy Setup)
```bash
npm install pusher-js
```
Then update `.env`:
```
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=xxxxx
PUSHER_APP_KEY=xxxxx
PUSHER_APP_SECRET=xxxxx
PUSHER_APP_CLUSTER=mt1
```
Get credentials from pusher.com

#### Option C: Database (Polling - No WebSocket)
Already configured, just works! (Slower but no additional setup)
```
BROADCAST_DRIVER=log
```

### Step 3: Install Laravel Echo (Recommended)

```bash
npm install laravel-echo
```

### Step 4: Create Echo Configuration

Create `resources/js/echo.js`:

```javascript
import Echo from 'laravel-echo';

// For Redis
window.Echo = new Echo({
    broadcaster: 'redis',
    host: window.location.hostname,
    port: 6379,
    encrypted: false,
});

// For Pusher (uncomment if using Pusher)
/*
import Pusher from 'pusher-js';
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});
*/
```

### Step 5: Load Echo in Your Blade View

Add this to `resources/views/welcome.blade.php` (or your layout) in the HEAD section:

```blade
@production
    <script src="https://js.pusher.com/8.0.0/pusher.min.js"></script>
@endproduction
@if(app('config.broadcasting.default') === 'redis')
    <script>
        // Redis connection info - customize as needed
    </script>
@endif

<script type="module">
    import './echo.js';
</script>
```

## Testing It Works

### Test 1: Verify Migrations
```bash
php artisan tinker
>>> DB::table('task_submissions')->count()
=> 0
```

### Test 2: Send Test Event
```bash
php artisan tinker
>>> 
$task = App\Models\Task::first();
event(new App\Events\TaskAssigned($task));
```

You should see a notification appear in the top-right corner!

### Test 3: Test Submission
```bash
php artisan tinker
>>>
$task = App\Models\Task::first();
$submission = App\Models\TaskSubmission::create([
    'task_id' => $task->id,
    'submitted_by' => 1,
    'submission_notes' => 'Testing notification',
]);
$submission->load('task.assigner', 'submitter');
event(new App\Events\TaskSubmitted($submission));
```

## API Endpoints

All endpoints require authentication (Bearer token via Sanctum):

### 1. Get Unread Notifications
```bash
GET /api/notifications
```

### 2. Mark Notification as Read
```bash
POST /api/notifications/{id}/read
```

### 3. Submit Work
```bash
POST /api/tasks/{task}/submit
Content-Type: application/json

{
    "submission_notes": "Completed the task"
}
```

### 4. Check Submission Status
```bash
GET /api/tasks/{task}/submission-status
```

### 5. Assign Task with Notification
```bash
POST /api/tasks/{task}/assign-notification
```

## How It Works

```
1. Supervisor assigns task → PHP Event fired
   ↓
2. Event Listener broadcasts to WebSocket server
   ↓
3. Employee's browser receives via Laravel Echo
   ↓
4. Frontend shows notification toast
   ↓
5. Same flow for submissions (reversed roles)
```

## Troubleshooting

### Q: Notifications not showing?
A: Check browser console (F12) for errors. Look for:
- `window.Echo is undefined` → Echo not loaded properly
- Network errors → Broadcasting server not running
- CORS errors → Check broadcasting configuration

### Q: "Laravel Echo not initialized" warning?
A: This is a warning if you haven't installed Echo yet. Either:
1. Install it: `npm install laravel-echo pusher-js`
2. Or use database polling (already works, just slower)

### Q: Redis connection refused?
A: Start Redis:
```bash
# Windows
redis-server

# macOS (if installed via brew)
redis-server

# Linux (if installed via apt)
sudo service redis-server start
```

### Q: How to enable test/demo notifications?
Edit `resources/views/welcome.blade.php` and uncomment the example in the JavaScript:
```javascript
// Show example notifications for testing
window.notificationManager?.showNotification(
    'Test Task Assigned! ✓',
    'This is a test notification',
    'success',
    '📋'
);
```

## Production Deployment

### Before Going Live:

1. **Set env to production**:
   ```
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Use Pusher or dedicated WebSocket server**:
   - Redis works but needs specific infrastructure
   - Pusher is recommended for reliability

3. **Enable encryption**:
   ```
   BROADCAST_DRIVER=pusher
   PUSHER_APP_SECURE=true
   ```

4. **Setup queue worker**:
   ```bash
   php artisan queue:work
   ```

5. **Configure notifications email**:
   ```
   MAIL_FROM_ADDRESS=notifications@yourcompany.com
   ```

## Files Modified

Core notification system files:
- `app/Models/TaskSubmission.php` - Tracks submissions
- `app/Events/TaskAssigned.php` - Assignment event
- `app/Events/TaskSubmitted.php` - Submission event
- `app/Listeners/SendTaskAssignedNotification.php` - Handles assignments
- `app/Listeners/SendTaskSubmittedNotification.php` - Handles submissions
- `routes/api.php` - Notification API endpoints
- `resources/views/welcome.blade.php` - Notification UI + listeners
- `database/migrations/2026_04_22_000011_create_task_submissions_table.php` - Schema

## Next Steps

1. ✅ Run migrations: `php artisan migrate`
2. ✅ Choose broadcasting method and update `.env`
3. ✅ Install Laravel Echo: `npm install laravel-echo pusher-js`
4. ✅ Create `echo.js` configuration
5. ✅ Load Echo in your Blade view
6. ✅ Test using the methods above
7. ✅ Deploy to production with proper configuration

## Support Docs

- [Laravel Broadcasting](https://laravel.com/docs/11.x/broadcasting)
- [Laravel Echo](https://laravel.com/docs/11.x/broadcasting#using-the-js-echo-library)
- [Pusher Setup](https://pusher.com/docs)
- [Redis Pub/Sub](https://redis.io/topics/pubsub)

Enjoy your real-time notification system! 🚀
