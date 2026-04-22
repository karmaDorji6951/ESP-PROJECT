# PMS for ESP (Elementary Service Personnel)

Laravel-based Personnel Management System for Elementary Service Personnel.

## Stack

- Laravel 10.x
- MySQL (XAMPP)
- Blade + Bootstrap 5
- Eloquent ORM

> Note: The project uses Laravel 10 because the available PHP version in this workspace does not satisfy Laravel 13's PHP 8.3 requirement.

## Features

- Authentication and role-based access
- Dashboard with staff, attendance, task, and leave summaries
- Employee management with profile photo upload
- Attendance marking and filtered reports
- Task assignment and status tracking
- Leave application and approval workflow
- CSV-based report export for attendance and performance summaries
- Database notifications for leave activity

## Default Credentials

After seeding, use one of these accounts:

- Admin: admin@esp.local / password
- Supervisor: supervisor@esp.local / password
- Staff: staff@esp.local / password

## Database

The app is configured for a MySQL database named `ESP`.

## Setup Instructions

1. Start XAMPP MySQL.
2. Create the database if needed:
   - `ESP`
3. Install dependencies if required:
   - `composer install`
   - `npm install`
4. Configure environment variables in `.env`.
5. Run migrations and seed data:
   - `php artisan migrate --force`
   - `php artisan db:seed --force`
6. Create the storage symlink for employee photos:
   - `php artisan storage:link`
7. Build frontend assets:
   - `npm run build`
8. Start the app:
   - `php artisan serve --host=127.0.0.1 --port=8000`

## Main Modules

- Dashboard
- Employees
- Attendance
- Tasks
- Leaves
- Reports

## Project Structure

- `app/Http/Controllers` - PMS controllers
- `app/Models` - Eloquent models
- `app/Http/Middleware` - role-based middleware
- `database/migrations` - schema definitions
- `database/seeders` - sample data
- `resources/views` - Blade templates
- `routes/web.php` - web routes

## Notes

- Uploaded employee photos are stored in `storage/app/public/employees`.
- The app includes database notifications for leave requests and leave approvals.
- Reports currently export as CSV for easy Excel use.
