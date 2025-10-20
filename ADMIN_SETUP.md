# RaceNex Admin Panel Setup

This guide will help you set up the admin panel for the RaceNex project.

## Database Setup

1. **Run the database setup script:**
   ```sql
   -- Execute the contents of admin_setup.sql in your MySQL database
   ```

2. **The setup script will:**
   - Add a `role` column to the `users` table
   - Create a default admin user
   - Add additional fields to the `products` table (status, category, updated_at)

## Default Admin Credentials

- **Email:** admin@racenex.com
- **Password:** admin123

## Admin Panel Features

### 1. Dashboard (`admin_dashboard.php`)
- Overview of system statistics
- Recent orders display
- Product statistics
- Quick action buttons

### 2. Product Management (`admin_products.php`)
- Add new products
- Edit existing products
- Delete products
- Manage product categories and status
- View product inventory

### 3. Order Management (`admin_orders.php`)
- View all orders
- Update order status
- Filter orders by status
- View order details

### 4. User Management (`admin_users.php`)
- View all users
- Change user roles (user/admin)
- Filter users by role

## Accessing the Admin Panel

1. Navigate to `admin_login.php` in your browser
2. Use the default admin credentials
3. You'll be redirected to the admin dashboard

## Security Notes

- Change the default admin password immediately
- The admin panel is protected by session management
- Only users with `role = 'admin'` can access admin functions
- Regular users cannot access admin pages

## File Structure

```
racenex/
├── admin_login.php          # Admin login page
├── admin_dashboard.php      # Main admin dashboard
├── admin_products.php       # Product management
├── admin_orders.php         # Order management
├── admin_users.php          # User management
├── admin_logout.php         # Admin logout
├── admin_setup.sql          # Database setup script
├── classes/
│   ├── Admin.php            # Admin functionality
│   ├── AdminSessionManager.php # Admin session management
│   └── Order.php            # Order management
└── ADMINSETUP.md           # This file
```

## Customization

### Adding New Admin Features
1. Create new admin pages following the existing pattern
2. Use `AdminSessionManager::requireLogin()` for authentication
3. Follow the existing UI design patterns

### Modifying Product Categories
Edit the category options in `admin_products.php`:
```php
<option value="your-category">Your Category</option>
```

### Styling
The admin panel uses Bootstrap 5 with a dark theme. Main colors:
- Primary: #ff3b3b (red)
- Background: #071025 (dark blue)
- Cards: #1a1a2e (darker blue)

## Troubleshooting

### Common Issues

1. **Cannot access admin panel:**
   - Ensure the database setup script has been run
   - Check that the admin user was created successfully

2. **Products not saving:**
   - Verify the database schema includes the new fields
   - Check for PHP errors in the error log

3. **Session issues:**
   - Ensure PHP sessions are enabled
   - Check file permissions on the project directory

### Database Verification

Run this query to verify the setup:
```sql
SELECT * FROM users WHERE role = 'admin';
SELECT * FROM products LIMIT 1;
```

Both queries should return results if the setup was successful.
