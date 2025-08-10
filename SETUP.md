# Event Management System - Setup Guide

## Quick Start

### 1. Database Setup
1. Make sure XAMPP is running (Apache + MySQL)
2. Open your browser and go to: `http://localhost/ev/install.php`
3. This will create the database and all tables automatically
4. Delete `install.php` after successful installation

### 2. Test Database Connection
1. Visit: `http://localhost/ev/test_db.php`
2. You should see green checkmarks for successful connections
3. Delete `test_db.php` after confirming it works

### 3. Access the System
1. Main page: `http://localhost/ev/index.php`
2. Login page: `http://localhost/ev/login.php`

## Default Login Credentials

| User Type | Username | Password |
|-----------|----------|----------|
| Admin     | admin    | password |
| Planner   | planner1 | password |
| Client    | client1  | password |

## System Features

### For Clients:
- Register and create events
- Browse available planners
- Send messages to planners
- Track event progress
- Provide feedback

### For Planners:
- Create professional profiles
- View event requests
- Send quotes and proposals
- Manage event tasks
- Communicate with clients

### For Admins:
- Manage all users
- Approve planner registrations
- Monitor system activity
- View all events and statistics

## File Structure

```
ev/
├── index.php              # Main landing page
├── login.php              # Login system
├── install.php            # Database setup (delete after use)
├── test_db.php            # Database test (delete after use)
├── includes/
│   ├── db.php            # Database connection
│   └── functions.php     # Helper functions
├── user/                  # Client interface
├── planner/               # Planner interface
├── admin/                 # Admin interface
└── assets/                # CSS, JS, images
```

## Troubleshooting

### Database Connection Issues:
- Check if XAMPP MySQL service is running
- Verify database name is `event_management_system`
- Check username/password in `includes/db.php`

### Login Issues:
- Make sure you've run `install.php` first
- Check if the database tables exist
- Verify the default users were created

### Permission Issues:
- Ensure PHP has write permissions
- Check file ownership and permissions

## Next Steps

After successful setup:
1. Customize the system for your needs
2. Add more event categories and services
3. Customize the UI and branding
4. Add additional features as needed

## Support

If you encounter issues:
1. Check the browser console for errors
2. Verify database connection
3. Check PHP error logs
4. Ensure all required files are present 