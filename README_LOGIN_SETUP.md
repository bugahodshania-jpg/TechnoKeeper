# TechnoKeeper Login System Setup

## Overview
This login system provides secure authentication for the TechnoKeeper application using PHP and MySQL.

## Files Created/Modified

### Backend Files
- `server/login.php` - Main login processing script
- `server/auth_check.php` - Authentication validation for protected pages
- `server/logout.php` - User logout functionality
- `server/generate_password.php` - Utility to generate password hashes

### Frontend Files
- `views/login.php` - Updated with AJAX login functionality
- `views/admin_dashboard.php` - Added authentication and user data display

## Database Setup

1. Import the database schema:
   ```sql
   mysql -u root -p < database/technokeeper_database.sql
   ```

2. Generate a secure password hash for the admin user:
   ```bash
   php server/generate_password.php
   ```

   This will update the admin user with:
   - Username: `john.doe`
   - Password: `admin123`

## Login Credentials

**Default Admin Account:**
- Username: `john.doe`
- Password: `admin123`
- Role: `admin`

## Security Features

1. **Password Hashing**: Uses PHP's `password_hash()` with bcrypt
2. **SQL Injection Protection**: Prepared statements for all database queries
3. **Session Management**: Secure session tokens stored in database
4. **Session Expiration**: Sessions expire after 24 hours
5. **Account Lockout**: Failed attempt tracking (framework ready)
6. **Input Validation**: Server-side validation and sanitization

## How It Works

### Login Process
1. User submits login form via AJAX
2. Backend validates credentials against database
3. If successful, creates session token and stores in database
4. Sets PHP session variables
5. Redirects to appropriate dashboard based on user role

### Session Validation
1. Each protected page includes `auth_check.php`
2. Validates session token exists and is active
3. Checks session expiration
4. Redirects to login if validation fails

### Logout Process
1. Deactivates session token in database
2. Destroys PHP session
3. Clears session cookies
4. Redirects to login page

## Usage

### Adding Authentication to New Pages
```php
<?php
require_once '../server/auth_check.php';

// Your page content here
?>
```

### Checking User Role
```php
<?php
if (isAdmin()) {
    // Admin-only content
} else {
    // Regular user content
}
?>
```

### Getting Current User Data
```php
<?php
$user = getCurrentUser();
echo "Welcome, " . $user['first_name'];
?>
```

## Configuration

### Database Connection
Edit `server/db.php` to match your database settings:
```php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'technokeeper_database';
```

### Session Settings
Default session duration is 24 hours. To change this, edit `server/login.php`:
```php
$expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
```

## Testing

1. Navigate to `views/login.php`
2. Enter admin credentials
3. Verify redirect to admin dashboard
4. Check that user information is displayed correctly
5. Test logout functionality

## Security Notes

- Change the default admin password immediately after setup
- Use HTTPS in production
- Implement rate limiting for login attempts
- Regularly update PHP and MySQL versions
- Consider implementing two-factor authentication

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check MySQL server is running
   - Verify database credentials in `db.php`
   - Ensure database exists and schema is imported

2. **Login Fails with Correct Credentials**
   - Run `generate_password.php` to update password hash
   - Check if user account is active (`is_active = 1`)

3. **Session Expired Immediately**
   - Check server timezone settings
   - Verify PHP session configuration

4. **Access Denied on Dashboard**
   - Ensure user has correct role (`admin` for admin dashboard)
   - Check if `auth_check.php` is properly included

## Next Steps

1. Implement user management interface
2. Add password reset functionality
3. Implement two-factor authentication
4. Add audit logging for admin actions
5. Create user dashboard for regular users
