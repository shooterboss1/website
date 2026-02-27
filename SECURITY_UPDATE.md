# 🔒 CRITICAL SECURITY UPDATE

## ✅ Actions Taken
1. **Secured Admin Dashboard**: Added code to `admin.php` to prevent unauthorized access.
2. **Updated Login Logic**: `signin.php` now checks for User Roles (Admin vs User).
3. **Refactored Code**: Cleaned up duplicated HTML across all pages.

## ⚠️ ACTION REQUIRED
You **MUST** run the database update script to add the "role" column to your users table. Without this, **LOGIN WILL FAIL**.

### How to Run:
Since the automatic attempt failed (likely due to WAMP environment specifics), please open your browser and visit:

**`http://localhost/ClothingBrandwebsite/update_users_table.php`**

You should see a success message:
> ✅ Column 'role' added successfully.
> ✅ Admin account created.

### Default Admin Login
Once the script runs, you can log in with:
- **Email**: `admin@nextgenfdm.com`
- **Password**: `admin123`

### Troubleshooting
If the link above doesn't work, you can manually run the SQL in phpMyAdmin:
```sql
ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user' AFTER password;
INSERT INTO users (username, email, password, role) VALUES ('Admin', 'admin@nextgenfdm.com', '$2y$10$YourHashHere', 'admin');
```
