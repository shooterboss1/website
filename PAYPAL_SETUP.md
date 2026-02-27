# PayPal Integration Setup Guide for NEXTGEN FDM

## Overview
Your website now has PayPal payment integration! When customers purchase items, payments will go directly to your PayPal account: **@nextgenfdm_payments**

## 🚀 Quick Start

### Step 1: Get Your PayPal Client ID

1. **Go to PayPal Developer Portal**
   - Visit: https://developer.paypal.com/
   - Sign in with your PayPal account (@nextgenfdm_payments)

2. **Create an App**
   - Click on "Dashboard" in the top menu
   - Go to "My Apps & Credentials"
   - Under "REST API apps", click "Create App"
   - Give it a name (e.g., "NEXTGEN FDM Store")
   - Click "Create App"

3. **Get Your Client ID**
   - You'll see two Client IDs:
     - **Sandbox Client ID** (for testing)
     - **Live Client ID** (for real payments)
   - Copy the appropriate one

### Step 2: Add Client ID to Your Website

1. **Open** `checkout.php`
2. **Find** line that says: `script.src = 'https://www.paypal.com/sdk/js?client-id=YOUR_CLIENT_ID&currency=USD';`
3. **Replace** `YOUR_CLIENT_ID` with your actual PayPal Client ID

Example:
```javascript
script.src = 'https://www.paypal.com/sdk/js?client-id=AeB1234567890XYZ&currency=USD';
```

### Step 3: Testing vs Live Mode

**For Testing (Sandbox Mode):**
- Use the Sandbox Client ID
- Create test buyer accounts at: https://developer.paypal.com/developer/accounts/
- Use test credit cards provided by PayPal

**For Live Payments:**
- Use the Live Client ID
- Make sure your PayPal account is verified and can accept payments
- Real money will be transferred!

## 📁 Files Created

1. **checkout.php** - The checkout page with PayPal integration
2. **process_order.php** - Backend script to save orders to database
3. **PAYPAL_SETUP.md** - This guide

## 💳 How It Works

1. Customer adds items to cart
2. Customer clicks "Checkout" button
3. Redirects to `checkout.php`
4. Customer sees order summary
5. Customer clicks PayPal button
6. PayPal processes payment
7. Money goes to @nextgenfdm_payments
8. Order is saved to database
9. Customer sees success message
10. Cart is cleared

## 🗄️ Database Tables

Two new tables are automatically created:

### `orders` table:
- `id` - Unique order ID
- `paypal_order_id` - PayPal transaction ID
- `customer_email` - Customer's email
- `customer_name` - Customer's name
- `total_amount` - Total payment amount
- `order_date` - When order was placed
- `status` - Order status (completed, pending, etc.)

### `order_items` table:
- `id` - Unique item ID
- `order_id` - Links to orders table
- `product_id` - Product purchased
- `product_name` - Name of product
- `quantity` - How many purchased
- `price` - Price per item

## 🎨 Features

✅ Shopping cart integration
✅ Real-time order summary
✅ Automatic tax calculation (8%)
✅ Free shipping over $100
✅ PayPal secure payment
✅ Order history saved to database
✅ Success confirmation page
✅ Automatic cart clearing after purchase
✅ Mobile responsive design

## 📊 Viewing Orders

You can view all orders in the database by creating an admin page or running:

```sql
SELECT * FROM orders;
SELECT * FROM order_items;
```

## 🔒 Security Tips

1. Never commit your Client ID to public repositories
2. Use HTTPS in production
3. Validate all payments server-side
4. Keep your PayPal credentials secure
5. Monitor your PayPal account for suspicious activity

## 🆘 Troubleshooting

**PayPal button doesn't show:**
- Check browser console for errors
- Verify Client ID is correct
- Make sure PayPal SDK loaded properly

**Payment not going through:**
- Check your PayPal account status
- Verify email @nextgenfdm_payments is correct
- Check if account can receive payments

**Orders not saving:**
- Check database connection in `config.php`
- Verify tables were created
- Check PHP error logs

## 🎯 Next Steps

1. Get your PayPal Client ID
2. Update `checkout.php` with your Client ID
3. Test with Sandbox mode first
4. Switch to Live mode when ready
5. Start accepting payments! 💰

## 📧 Support

For PayPal support: https://www.paypal.com/us/smarthelp/home
For developer docs: https://developer.paypal.com/docs/

---

**Your PayPal Account:** @nextgenfdm_payments  
**Ready to receive payments!** 🚀
