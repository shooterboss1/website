# 🚀 WORLDWIDE LAUNCH GUIDE

This guide walks you through the steps to take your **What's Real Shall Prosper** website from your computer (Localhost) to the internet (Production).

---

## 🏗️ STEP 1: Buy Domain & Hosting
You need a "house" (Hosting) and an "address" (Domain) for your website.
Recommended hosting providers for PHP/MySQL:
- **Bluehost, SiteGround, or HostGator** (Beginner friendly)
- **DigitalOcean or Vultr** (Advanced)

**Requirements:**
- PHP 7.4 or higher
- MySQL 5.7 or higher
- **SSL Certificate** (Required for payments - usually free with hosting)

---

## ⚙️ STEP 2: Configure for Production

### 1. Update `config.php`
Open `config.php` locally. You will see a "Live Production Settings" section. You do NOT need to edit this file before uploading if you fill in the details **on the server**, but you can prepare it now.

### 2. Get Live Stripe Keys
1. Log in to [dashboard.stripe.com](https://dashboard.stripe.com)
2. Toggle "Test Mode" to **OFF** (top right)
3. Go to **Developers > API Keys**
4. Copy your **Publishable Key** (`pk_live_...`)
5. Copy your **Secret Key** (`sk_live_...`)
6. Paste these into the `config.php` "Live Production Settings" section.

---

## 📤 STEP 3: Uploading Your Site

1. **Export Database**:
   - Open `http://localhost/phpmyadmin`
   -  Select the `clothing` database.
   - Click **Export** > **Go**.
   - Save the `.sql` file.

2. **Upload Files**:
   - Log in to your hosting Control Panel (cPanel).
   - Go to **File Manager** > `public_html`.
   - Upload all files from your `ClothingBrandwebsite` folder.
   - **IMPORTANT**: Do not upload the `.git` folder if you have one.

3. **Import Database**:
   - In your hosting panel, find **MySQL Databases**.
   - Create a new database (e.g., `nextgenfdm_shop`).
   - Create a new user (e.g., `nextgenfdm_admin`) and password. **SAVE THIS PASSWORD!**
   - Add the user to the database with **All Privileges**.
   - Go to **phpMyAdmin** on your host.
   - Select your new database and **Import** the `.sql` file you saved earlier.

4. **Final Config Edit**:
   - In File Manager on your host, edit `config.php`.
   - Update `DB_NAME`, `DB_USER`, and `DB_PASSWORD` with the new details you just created.
   - Remove the `pk_test` keys and ensure `pk_live` keys are present.

---

## 🔒 STEP 4: Security Checklist (CRITICAL)

1. **Change Admin Password**:
   - The default is `admin123`.
   - Go to your hosted database's `users` table.
   - You must change the password hash. Since building a reset tool is complex, use this [Bcrypt Generator](https://bcrypt-generator.com/) to generate a hash for a new strong password.
   - Paste that hash into the `password` column for the admin user.

2. **Force HTTPS**:
   - Ensure your site loads with `https://`. If not, ask your host to "Force HTTPS".

---

## 🧪 STEP 5: Final Testing

1. **Test Payment**: Allow a friend to buy a $1 item using a real card (you can refund it later in Stripe).
2. **Check Emails**: Make sure you receive the "Order Confirmation" email. If not, ask your host if "PHP Mail" is enabled.
3. **Admin Check**: Log in to `admin.php` online and ensure you see the new order.

---

## 🎉 CONGRATULATIONS!
Your brand is now live for the world to see. 

