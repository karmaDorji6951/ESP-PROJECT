# Role-Based Dashboards - Complete Guide

## 📊 Overview

The ESP application now includes **three distinct dashboards**, each tailored for a specific user role with unique features, metrics, and functionality.

## 🎯 Dashboard Types

### 1. **Admin Dashboard** (System Administration)
**For Users with Admin Role**: System-wide management and oversight

#### Features:
- **⚙️ System Overview**: Complete system metrics and statistics
- **👥 Staff Directory**: Manage all employees and their profiles
- **📋 Attendance Tracking**: View attendance across entire organization
- **✓ Task Management**: Oversee all assigned tasks
- **📅 Leave Approvals**: Approve/reject all leave requests
- **📈 Reports**: Generate comprehensive system reports

#### Key Metrics:
- **Total Staff**: Count of all employees
- **Present Today**: Real-time attendance count
- **Total Tasks**: All tasks across the system
- **Pending Approvals**: Leave requests awaiting action

#### Task Statistics:
- Pending Tasks: Not yet started
- In Progress: Currently being worked on
- Completed: Successfully finished

#### Color Scheme:
- Accent Color: **Purple** (#7c3aed)
- Primary Action: Leave approvals and system controls
- Tables: Recent tasks, pending leaves, today's attendance

---

### 2. **Supervisor Dashboard** (Team Management)
**For Users with Supervisor Role**: Team and task oversight

#### Features:
- **👔 Team Overview**: Manage assigned staff
- **✓ My Tasks**: Tasks assigned by this supervisor
- **📋 Team Attendance**: View team's daily attendance
- **📅 Leave Reviews**: Approve createdyn task approvals
- **👥 Staff Directory**: Access to employee information
- **📈 Reports**: Team-level reports and analytics

#### Key Metrics:
- **Staff Under Supervision**: Team size
- **Present Today**: Team attendance count
- **Tasks Assigned**: Total tasks given to team
- **Pending Approvals**: Leave requests to review

#### Task Statistics:
- Pending: Awaiting team action
- In Progress: Team is working
- Completed: Successfully finished by team

#### Color Scheme:
- Accent Color: **Blue** (#2563eb)
- Primary Action: Leave approvals and task assignment
- Tables: My assigned tasks, team attendance, pending leaves

---

### 3. **Employee Dashboard** (Personal Workspace)
**For Users with Staff/Employee Role**: Personal task and attendance management

#### Features:
- **👤 Personal Workspace**: My tasks and assignments
- **📋 My Tasks**: Tasks assigned to me
- **📋 Attendance**: Personal attendance history
- **📅 Leave Requests**: Submit and track leave applications
- **✓ Task Tracking**: Monitor task progress and deadlines

#### Key Metrics:
- **Total Tasks**: All tasks assigned to me
- **Pending Tasks**: Tasks not yet completed
- **Completed Tasks**: Successfully finished work
- **Leave Pending**: Requests awaiting approval

#### Features in Employee Dashboard:
- Task deadline tracking with overdue indicators
- Attendance history with dates and status
- Leave request submission form
- Quick action buttons for common tasks

#### Color Scheme:
- Accent Color: **Teal** (#14b8a6)
- Primary Action: Task submission and leave requests
- Empty states with encouraging messages

---

## 🔀 Role-Based Routing

The dashboard automatically route users based on their role:

```
User Login
    ↓
DashboardController checks role
    ↓
┌───────────────────────────────────────────┐
│ Role: admin    → Admin Dashboard          │
│ Role: supervisor → Supervisor Dashboard   │
│ Role: staff/employee → Employee Dashboard │
└───────────────────────────────────────────┘
```

## 📁 File Structure

```
resources/views/dashboards/
├── admin-dashboard.blade.php          # Admin system-wide view
├── supervisor-dashboard.blade.php     # Supervisor team view  
└── employee-dashboard.blade.php       # Employee personal view

app/Http/Controllers/
└── DashboardController.php            # Route logic
    ├── index()                        # Main router method
    ├── adminDashboard()              # Admin data gathering
    ├── supervisorDashboard()         # Supervisor data gathering
    └── employeeDashboard()           # Employee data gathering
```

## 🎨 Design Features

### Consistent Elements Across All Dashboards:
- **Responsive Grid Layout**: Works on desktop, tablet, mobile
- **Sidebar Navigation**: Quick access to key features
- **Top Bar**: User greeting and quick actions
- **Color-Coded Metrics**: KPI cards with role-specific colors
- **Status Badges**: Visual indicators (Pending, In Progress, Completed)
- **Action Buttons**: Quick actions for approvals and submissions

### Responsive Breakpoints:
- **Desktop**: Full layout with 4-column grids (> 1200px)
- **Tablet**: 2-column grids (768px - 1200px)
- **Mobile**: 1-column stacked layout (< 768px)
- **Small Phone**: Single column with reduced font sizes (< 480px)

### Mobile Navigation:
- Sidebar slides in from left on click
- Overlay closes sidebar when clicked
- Menu items close sidebar automatically
- Touch-friendly button sizes

## 🗄️ Database Queries Optimized for Each Role

### Admin Dashboard Queries:
```php
// Wide-reaching queries
Employee::count()
Attendance::whereDate('attendance_date', today())
Task::all()
LeaveRequest::where('status', 'Pending')
Task::where('status', 'Completed').count()
```

### Supervisor Dashboard Queries:
```php
// Filtered by assigned_by (supervisor's ID)
Task::where('assigned_by', $user->id)
Employee::count()
Attendance::whereDate('attendance_date', today())
LeaveRequest::where('status', 'Pending')
```

### Employee Dashboard Queries:
```php
// Filtered by employee's own ID
Task::where('employee_id', $employee->id)
Attendance::where('employee_id', $employee->id)
LeaveRequest::where('user_id', $user->id)
```

## 🔐 Security

### Role-Based Access Control:
- Routes middleware checks user role
- Each dashboard only shows relevant data
- Leave approval buttons only for admin/supervisor
- Employee cannot see other employee's tasks

### Middleware Protection:
```php
Route::middleware(['auth', 'role:admin,supervisor,staff'])
    ->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');
```

## 📊 Key Features by Dashboard

| Feature | Admin | Supervisor | Employee |
|---------|-------|-----------|----------|
| View all staff | ✅ | ✅ | ❌ |
| View all tasks | ✅ | ✅ (own) | ✅ (own) |
| Approve leaves | ✅ | ✅ | ❌ |
| Assign tasks | ✅ | ✅ | ❌ |
| View attendance | ✅ | ✅ | ✅ (own) |
| Submit leaves | ✅ | ✅ | ✅ |
| Generate reports | ✅ | ✅ | ❌ |
| System settings | ✅ | ❌ | ❌ |

## 🚀 How to Use

### Login Flow:

1. **Open Application**: Visit `/login`
2. **Enter Credentials**:
   - **Admin**: admin@esp.local / password
   - **Supervisor**: supervisor@esp.local / password
   - **Staff**: staff@esp.local / password
3. **Automatic Redirect**: You're directed to your role's dashboard
4. **Navigate**: Use sidebar menu to access features

### Example Workflows:

**Admin Workflow:**
```
Login as admin@esp.local
    ↓
Land on Admin Dashboard
    ↓
View pending leave requests
    ↓
Click "Approve" or "Reject"
    ↓
Automatically updated in database
    ↓
Notifications sent to employee
```

**Supervisor Workflow:**
```
Login as supervisor@esp.local
    ↓
Land on Supervisor Dashboard
    ↓
Review assigned tasks
    ↓
Navigate to "My Tasks"
    ↓
Assign new task to employee
    ↓
Employee receives notification
```

**Employee Workflow:**
```
Login as staff@esp.local
    ↓
Land on Employee Dashboard
    ↓
See assigned tasks
    ↓
Check task deadline and details
    ↓
Submit work when complete
    ↓
Supervisor gets notification
```

## 🎯 Navigation by Role

### Admin Sidebar:
- 📊 Dashboard
- 👥 Staff Directory (with staff count)
- 📋 Attendance
- ✓ All Tasks (with total count)
- 📅 Leave Requests (with pending count)
- 📈 Reports
- 🚪 Logout

### Supervisor Sidebar:
- 📊 Dashboard
- ✓ My Tasks (with count)
- 📋 Attendance
- 📅 Leave Reviews (with pending count)
- 👥 Staff Directory
- 📈 Reports
- 🚪 Logout

### Employee Sidebar:
- 📊 Dashboard
- ✓ My Tasks (with count)
- 📋 Attendance
- 📅 Leave Requests (with pending count)
- 🚪 Logout

## 💡 Customization Options

### Change Colors:
Edit the CSS `:root` variables in each dashboard:
```css
:root {
    --admin-accent: #7c3aed;      /* Change admin purple */
    --supervisor-accent: #2563eb; /* Change supervisor blue */
    --employee-accent: #14b8a6;   /* Change employee teal */
}
```

### Modify Metrics:
Update the `DashboardController` methods:
```php
private function adminDashboard($user) {
    $summary = [
        'custom_metric' => Model::query()->count(),
        // Add your custom metrics here
    ];
}
```

### Add New Dashboard:
1. Create new blade file in `resources/views/dashboards/`
2. Create new role and assign to users
3. Add new case in `index()` method:
```php
return match ($role) {
    'admin' => $this->adminDashboard($user),
    'supervisor' => $this->supervisorDashboard($user),
    'new-role' => $this->newRoleDashboard($user),
    default => $this->employeeDashboard($user),
};
```

## 🐛 Troubleshooting

### Dashboard Not Loading?
- Check user has assigned role in database
- Verify `role_id` on user record points to valid role
- Check middleware in `routes/web.php`

### Wrong Dashboard Showing?
- Verify user's role in database: `SELECT * FROM users WHERE id = X;`
- Check role slug in roles table
- Reload page and clear browser cache

### Permission Errors?
- Ensure user has proper role_id
- Check middleware allows the role
- Verify blade conditions use correct role slug

## 📈 Performance Notes

- Each dashboard queries only relevant data
- Indexes on `assigned_by`, `employee_id`, `user_id` for fast filtering
- Limit queries to recent 10 records per table
- Use `with()` for eager loading relationships
- Pagination recommended for views with many records

## 🔄 Future Enhancements

- Custom dashboard widgets per role
- Drag-and-drop task status updates
- Real-time dashboard updates with WebSockets
- Custom reporting per role
- Dashboard preferences (saved layouts)
- Dark mode support
- PDF report generation

## 📞 Support

For issues with role-based dashboards:
1. Check user role assignment
2. Verify database migrations ran
3. Check laravel.log for errors
4. Clear cache: `php artisan cache:clear`
5. Re-login to refresh session

---

✨ **Your role-based dashboard system is now live!** Each user sees exactly what they need. Enjoy! 🎉
