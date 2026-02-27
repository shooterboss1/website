# 👕 NEXTGEN FDM Clothing Brand Website

A fully functional, premium e-commerce website built with PHP, MySQL, JavaScript, and CSS.

## ✅ Project Status: COMPLETE
**Last Updated:** December 17, 2025

---

## 🚀 Key Features

### 🛒 Shopping Experience
- **Dynamic Homepage**: Featured products, hero banners, and "Load More" pagination.
- **Shop All Page (`shop_now.php`)**: Advanced filtering (Price, Category, Rating), Grid/List views.
- **Category Pages**: Dedicated pages for **Men** (`men.php`) and **Women** (`women.php`) with unique designs and stats.
- **Unreleased Collection (`coming_soon.php`)**: A "hype" page for upcoming drops with countdowns and exclusive access requests.
- **Advanced Search (`search.php`)**: Full-text search with instant filtering.

### 💳 Checkout & Payments
- **Multi-Payment Support**:
  - 💳 **Credit/Debit Card** (Stripe Integration)
  - 🍎 **Apple Pay** (Device detection included)
  - 💙 **PayPal** (Direct links)
- **Shopping Cart**: Persistent cart using LocalStorage (works across all pages).
- **Order Processing**: Database recording of customer details and items.

### 🎨 Design & UI
- **Premium Aesthetic**: Minimalist, "NEXTGEN FDM" design with modern typography.
- **Responsive**: Fully optimized for mobile, tablet, and desktop.
- **Animations**: Smooth fade-ins, hover effects, and interactive elements.
- **Dark Mode Areas**: Specialized "Unreleased" section with dark theme.

---

## 📂 Project Structure

```
/ClothingBrandwebsite
│
├── index.php               # Homepage
├── shop_now.php            # Main Shop Page
├── men.php                 # Men's Collection
├── women.php               # Women's Collection
├── coming_soon.php         # Unreleased Collection page
├── search.php              # Search Results page
├── checkout.php            # Checkout & Payment page
├── product-cap.php         # Single Product Template
│
├── /includes
│   ├── header.php          # Global Navigation & Search Bar
│   └── footer.php          # Global Footer
│
├── /js
│   └── main.js             # Core Logic (Cart, Favs, Load More, Filters)
│
├── /images                 # Product & Asset Images
├── style.css               # Main Stylesheet
│
├── config.php              # Database Connection
├── load_more.php           # AJAX Backend for products
└── process_order.php       # Order saving logic
```

---

## 🛠️ Setup & Management

### Database Support
- **`config.php`**: Holds your database credentials (default: root/no password).
- **`setup_database.php`**: Run this to reset/create the database tables.

### Helper Scripts (Admin Tools)
- **`add_women_products.php`**: Easily add products to the Women's category.
- **`add_men_products.php`**: Easily add products to the Men's category.
- **`add_new_items.php`**: General product addition tool.
- **`check_db_status.php`**: Diagnostic tool for database connection.

---

## 🧪 Testing The Site

1. **Purchasing**: Use the stripe test card `4242 4242 4242 4242` on checkout.
2. **Search**: Try searching for "shirt" or "cap" in the header.
3. **Load More**: Scroll to the bottom of the home/shop page and click "Load More".
4. **Unreleased**: Click "Load More" until products run out to see the prompt for the Unreleased Collection.

---

## 📝 Notes for Deployment
If you move this to a live server (e.g., GoDaddy, AWS, etc.):
1. Upload all files to `public_html`.
2. Import your database using `setup_database.php`.
3. Update `config.php` with your **live** database password/username.
4. Replace the **Stripe Public Key** in `checkout.php` with your live key.

---
**Developed by:** User & AI Pair Programmer
