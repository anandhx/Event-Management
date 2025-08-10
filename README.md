# Event Management System (EMS)

## Overview

The Event Management System (EMS) is a comprehensive web-based platform designed to connect event clients with professional event planners. This system has been transformed from a Vehicle Management System (VMS) to provide a complete event management solution.

## Features

### For Event Clients
- **User Registration & Authentication**: Secure registration and login system
- **Event Creation**: Create detailed event requests with specifications
- **Event Management**: View, edit, and track event status
- **Planner Communication**: Direct messaging with assigned planners
- **Event History**: Track past events and feedback

### For Event Planners
- **Professional Profile**: Create detailed planner profiles with expertise areas
- **Event Dashboard**: View assigned events and available opportunities
- **Event Management**: Manage event details, timelines, and client communication
- **Quote System**: Provide detailed quotes for event services
- **Portfolio Management**: Showcase past events and achievements

### For Administrators
- **User Management**: Manage clients and planners
- **Event Oversight**: Monitor all events and planner assignments
- **System Analytics**: View system statistics and performance metrics
- **Content Management**: Manage event categories and services

## System Architecture

### Database Structure
The system uses a MySQL database with the following main tables:

- **users**: User accounts (clients, planners, admins)
- **event_categories**: Event types (weddings, corporate, etc.)
- **event_planners**: Planner profiles and expertise
- **events**: Event details and specifications
- **event_quotes**: Pricing and service quotes
- **messages**: Communication between users
- **payments**: Payment tracking
- **feedback**: Event reviews and ratings

### User Types
1. **Client**: Users who want to organize events
2. **Event Planner**: Professionals who provide event services
3. **Admin**: System administrators with full access

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP/MAMP (for local development)

### Installation Steps

1. **Clone/Download the Project**
   ```bash
   git clone [repository-url]
   cd event
   ```

2. **Database Setup**
   - Create a new MySQL database named `ems`
   - Import the database schema from `includes/ems.sql`
   - Update database credentials in `includes/db.php`

3. **Web Server Configuration**
   - Place the project in your web server directory
   - Ensure PHP has write permissions for uploads and logs
   - Configure your web server to serve the project

4. **Initial Setup**
   - Access the system through your web browser
   - Register as an admin user
   - Configure system settings and categories

## Key Files & Directories

```
event/
├── index.php                 # Main landing page
├── header.php               # Common header with navigation
├── footer.php               # Common footer
├── includes/
│   ├── db.php              # Database connection
│   └── ems.sql             # Database schema
├── user/                    # Client user interface
│   ├── register.php         # User registration
│   ├── create_event.php     # Event creation form
│   ├── my_events.php        # User's event dashboard
│   └── ...
├── planner/                 # Event planner interface
│   ├── dashboard.php        # Planner dashboard
│   ├── profile.php          # Planner profile management
│   └── ...
├── admin/                   # Administrative interface
│   ├── index.php            # Admin dashboard
│   ├── user_management.php  # User management
│   └── ...
└── assets/                  # Static assets (CSS, JS, images)
    ├── css/
    ├── js/
    └── img/
```

## Usage Guide

### For Event Clients

1. **Registration**
   - Visit the main page and click "Sign Up"
   - Choose "Event Client" as your user type
   - Fill in your details and create account

2. **Creating an Event**
   - Log in to your account
   - Click "Create New Event"
   - Fill in event details (title, date, venue, budget, etc.)
   - Submit the event request

3. **Managing Events**
   - View your events in "My Events"
   - Track event status and planner assignments
   - Communicate with assigned planners
   - Provide feedback after events

### For Event Planners

1. **Registration**
   - Register as an "Event Planner"
   - Provide company details and expertise areas
   - Wait for admin approval

2. **Dashboard Access**
   - View assigned events and statistics
   - Browse available event opportunities
   - Manage event details and client communication

3. **Event Management**
   - Accept or decline event assignments
   - Provide detailed quotes and proposals
   - Update event progress and status
   - Communicate with clients

### For Administrators

1. **User Management**
   - Approve/reject planner registrations
   - Manage user accounts and permissions
   - Monitor system activity

2. **Event Oversight**
   - View all events in the system
   - Assign planners to events
   - Monitor event progress and completion

3. **System Configuration**
   - Manage event categories
   - Configure system settings
   - Generate reports and analytics

## Security Features

- **Password Hashing**: All passwords are securely hashed using PHP's password_hash()
- **SQL Injection Prevention**: Prepared statements for all database queries
- **Session Management**: Secure session handling and validation
- **Input Validation**: Server-side validation for all user inputs
- **Access Control**: Role-based access control for different user types

## Customization

### Adding New Event Categories
1. Add category to `event_categories` table
2. Update the category selection in event creation forms
3. Add corresponding services in `event_services` table

### Modifying User Interface
- Edit CSS files in `assets/css/`
- Update HTML templates in respective PHP files
- Modify JavaScript functionality in `assets/js/`

### Extending Functionality
- Add new database tables as needed
- Create new PHP files for additional features
- Update navigation and routing accordingly

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `includes/db.php`
   - Ensure MySQL service is running
   - Verify database name exists

2. **Upload Issues**
   - Check file permissions for uploads directory
   - Verify PHP upload settings in php.ini
   - Ensure sufficient disk space

3. **Session Problems**
   - Check PHP session configuration
   - Verify session storage permissions
   - Clear browser cookies if needed

### Performance Optimization

1. **Database Optimization**
   - Add appropriate indexes to frequently queried columns
   - Optimize complex queries
   - Use database connection pooling

2. **Caching**
   - Implement page caching for static content
   - Cache database query results
   - Use CDN for static assets

## Future Enhancements

- **Mobile Application**: Native mobile apps for iOS and Android
- **Payment Integration**: Stripe/PayPal payment processing
- **Calendar Integration**: Google Calendar, Outlook integration
- **Advanced Analytics**: Detailed reporting and insights
- **Multi-language Support**: Internationalization features
- **API Development**: RESTful API for third-party integrations

## Support

For technical support or feature requests, please contact the development team or create an issue in the project repository.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

---

**Note**: This system has been transformed from a Vehicle Management System to an Event Management System. All vehicle-related functionality has been replaced with event management features while maintaining the same robust architecture and security measures. 