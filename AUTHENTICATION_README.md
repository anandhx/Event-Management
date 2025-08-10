# Event Management System - Authentication System

## Overview
This system provides a complete authentication solution with separate registration forms for clients and event planners, along with a unified login system.

## Features

### 🔐 Login System
- **Unified Login**: Single login form for all user types (admin, planner, client)
- **Secure Authentication**: Password hashing using PHP's `password_hash()` and `password_verify()`
- **Session Management**: Proper session handling with security measures
- **Role-based Redirects**: Automatic redirection based on user type after login

### 📝 Registration System
- **Dual Registration Forms**: Separate forms for clients and event planners
- **User Type Selection**: Initial screen to choose account type
- **Comprehensive Validation**: Client-side and server-side validation
- **Database Integration**: Automatic user creation with proper relationships

## User Types

### 👤 Client Registration
**Fields:**
- Full Name
- Username (3-20 characters, alphanumeric + underscore)
- Email Address
- Phone Number (10-15 digits)
- Address
- Password (minimum 8 characters)
- Confirm Password

### 🎯 Event Planner Registration
**Fields:**
- Full Name
- Username (3-20 characters, alphanumeric + underscore)
- Email Address
- Phone Number (10-15 digits)
- Company Name
- Specialization
- Years of Experience
- Location
- Bio
- Password (minimum 8 characters)
- Confirm Password

## Technical Implementation

### Frontend
- **HTML Forms**: Bootstrap-styled responsive forms
- **JavaScript Validation**: Real-time client-side validation
- **CSS Styling**: Dark theme with custom animations
- **Responsive Design**: Mobile-friendly interface

### Backend
- **PHP Processing**: Server-side validation and processing
- **Database Integration**: MySQL with prepared statements
- **Transaction Support**: Atomic operations for data integrity
- **Security**: Input sanitization and password hashing

### Database Structure
- **Users Table**: Core user information
- **Planners Table**: Additional planner-specific data
- **Foreign Key Relationships**: Proper database normalization

## File Structure

```
├── index.php                 # Main page with authentication modal
├── login.php                 # Dedicated login page
├── logout.php                # Logout handler
├── register_backend.php      # Registration processing
├── assets/
│   ├── css/
│   │   └── custom-style.css # Custom styling
│   └── js/
│       └── custom.js        # Form validation and interactions
└── includes/
    ├── db.php               # Database connection
    └── functions.php        # Helper functions
```

## Usage

### For Users
1. **Login**: Click "Sign In" tab and enter credentials
2. **Register as Client**: Click "Sign Up" → "Client" → Fill form
3. **Register as Planner**: Click "Sign Up" → "Event Planner" → Fill form

### For Developers
1. **Customization**: Modify validation rules in `custom.js`
2. **Styling**: Update CSS in `custom-style.css`
3. **Backend Logic**: Modify `register_backend.php` for business logic changes

## Security Features

- ✅ Password hashing with bcrypt
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS protection (input sanitization)
- ✅ Session security
- ✅ Input validation (client + server-side)
- ✅ CSRF protection (form tokens)

## Demo Credentials

**Admin:**
- Username: `admin`
- Password: `admin123`

**Planner:**
- Username: `planner1`
- Password: `admin123`

**Client:**
- Username: `client1`
- Password: `admin123`

## Browser Compatibility

- ✅ Chrome (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Edge (latest)
- ✅ Mobile browsers

## Dependencies

- PHP 7.4+
- MySQL 5.7+
- Bootstrap 5.3.0
- Font Awesome 6.0.0
- Modern JavaScript (ES6+)

## Installation

1. **Database Setup**: Run `database_setup.sql`
2. **File Upload**: Upload all files to web server
3. **Configuration**: Update database settings in `includes/db.php`
4. **Permissions**: Ensure web server can write to session directory

## Troubleshooting

### Common Issues
- **Registration Fails**: Check database connection and table structure
- **Login Issues**: Verify password hashing and user status
- **Styling Problems**: Ensure CSS files are properly linked

### Debug Mode
Enable error reporting in PHP for development:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Future Enhancements

- [ ] Email verification
- [ ] Password reset functionality
- [ ] Two-factor authentication
- [ ] Social media login
- [ ] Profile picture upload
- [ ] Account activation workflow

## Support

For technical support or feature requests, please refer to the main project documentation or contact the development team. 