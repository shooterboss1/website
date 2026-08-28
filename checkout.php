<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>What's Real Shall Prosper Admin Portal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        .checkout-container {
            max-width: 1200px;
            margin: 80px auto 40px;
            padding: 0 20px;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
            margin-top: 30px;
        }

        .checkout-section {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 8px;
        }

        .checkout-section h2 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .order-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .order-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }

        .order-item-details {
            flex: 1;
        }

        .order-item-title {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .order-item-qty {
            color: #666;
            font-size: 13px;
        }

        .order-item-price {
            font-weight: 600;
            font-size: 14px;
        }

        .order-summary {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 25px;
            position: sticky;
            top: 100px;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .summary-line.total {
            font-size: 18px;
            font-weight: bold;
            padding-top: 15px;
            border-top: 2px solid #000;
            margin-top: 15px;
        }

        .paypal-container {
            margin-top: 20px;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-cart h2 {
            margin-bottom: 20px;
        }

        .continue-shopping {
            display: inline-block;
            padding: 12px 30px;
            background: #000;
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            margin-top: 20px;
        }

        .continue-shopping:hover {
            background: #333;
        }

        .payment-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .payment-tab {
            padding: 12px 20px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #666;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .payment-tab.active {
            color: #000;
            border-bottom-color: #000;
        }

        .payment-tab:hover {
            color: #000;
        }

        .payment-method {
            display: none;
        }

        .payment-method.active {
            display: block;
        }

        .card-form {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .form-group input,
        #card-element {
            width: 100%;
            padding: 14px;
            border: 1px solid #d0d0d0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        #card-element.StripeElement--focus {
            outline: none;
            border-color: #000;
        }

        .card-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .submit-payment {
            width: 100%;
            padding: 16px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 20px;
            transition: background 0.2s;
        }

        .submit-payment:hover:not(:disabled) {
            background: #333;
        }

        .submit-payment:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        #apple-pay-button {
            width: 100%;
            height: 52px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .payment-icon {
            font-size: 18px;
        }

        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
            
            .order-summary {
                position: static;
            }

            .card-row {
                grid-template-columns: 1fr;
            }

            .payment-tabs {
                flex-wrap: wrap;
            }

            .payment-tab {
                flex: 1;
                min-width: 100px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<header class="navbar">
    <a href="index.php" class="logo">
        <img src="images/logo.jpeg" alt="What's Real Shall Prosper">
    </a>

    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
            <span class="brand-text">What's Real Shall Prosper</span>
        <a href="index.php?cat=men">Men</a>
        <a href="index.php?cat=women">Women</a>
    </div>

    <div class="search-bar">
        <form action="index.php" method="GET" style="display:flex;align-items:center;">
            <button type="submit" class="search-btn"><i class="fa fa-search"></i></button>
            <input type="text" name="q" id="site-search" placeholder="Search products..." autocomplete="off">
        </form>
    </div>

    <div class="nav-icons">
        <a href="signup.php" class="nav-icon"><i class="fa fa-user"></i></a>
        <a href="index.php" class="nav-icon"><i class="fa fa-heart"></i></a>
        <a href="index.php" class="nav-icon">
            <i class="fa fa-shopping-bag"></i>
            <span id="cart-badge-top" style="font-size:10px; vertical-align:top;">0</span>
        </a>
    </div>
</header>

<div class="checkout-container">
    <a href="index.php" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; color: #666; font-size: 14px; margin-bottom: 20px; transition: color 0.2s;">
        <span style="font-size: 18px;">←</span> Back to Shop
    </a>
    <h1 style="font-size: 2rem; font-weight: 600;">Checkout</h1>
    
    <div id="checkout-content">
        <!-- Content will be populated by JavaScript -->
    </div>
</div>

<script>
(function() {
    // Get cart from localStorage
    const cart = JSON.parse(localStorage.getItem('nextgenfdm_cart') || '{}');
    const cartItems = Object.values(cart);
    const checkoutContent = document.getElementById('checkout-content');
    
    if (cartItems.length === 0) {
        // Empty cart
        checkoutContent.innerHTML = `
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <p>Add some items to your cart before checking out.</p>
                <a href="index.php" class="continue-shopping">Continue Shopping</a>
            </div>
        `;
    } else {
        // Calculate totals
        let subtotal = 0;
        cartItems.forEach(item => {
            subtotal += item.price * item.qty;
        });
        
        const shipping = subtotal > 100 ? 0 : 10; // Free shipping over $100
        const tax = subtotal * 0.08; // 8% tax
        const total = subtotal + shipping + tax;
        
        // Render checkout page
        let itemsHTML = '';
        cartItems.forEach(item => {
            itemsHTML += `
                <div class="order-item">
                    <img src="${item.img}" alt="${item.title}">
                    <div class="order-item-details">
                        <div class="order-item-title">${item.title}</div>
                        <div class="order-item-qty">Quantity: ${item.qty}</div>
                    </div>
                    <div class="order-item-price">$${(item.price * item.qty).toFixed(2)}</div>
                </div>
            `;
        });
        
        checkoutContent.innerHTML = `
            <div class="checkout-grid">
                <div class="checkout-section">
                    <h2>Order Items</h2>
                    ${itemsHTML}
                </div>
                
                <div>
                    <div class="order-summary">
                        <h2>Order Summary</h2>
                        <div class="summary-line">
                            <span>Subtotal</span>
                            <span>$${subtotal.toFixed(2)}</span>
                        </div>
                        <div class="summary-line">
                            <span>Shipping</span>
                            <span>${shipping === 0 ? 'FREE' : '$' + shipping.toFixed(2)}</span>
                        </div>
                        <div class="summary-line">
                            <span>Tax</span>
                            <span>$${tax.toFixed(2)}</span>
                        </div>
                        <div class="summary-line total">
                            <span>Total</span>
                            <span>$${total.toFixed(2)}</span>
                        </div>
                        
                        <div class="paypal-container">
                            <div id="paypal-button-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Load payment options
        showPaymentOptions(total);
    }
    
    function showPaymentOptions(totalAmount) {
        const container = document.getElementById('paypal-button-container');
        const cartItems = Object.values(JSON.parse(localStorage.getItem('nextgenfdm_cart') || '{}'));
        const itemsList = cartItems.map(item => item.title).join(', ');
        
        container.innerHTML = `
            <div style="margin-top: 20px;">
                <h3 style="font-size: 16px; margin-bottom: 15px; font-weight: 600;">Select Payment Method</h3>
                
                <!-- Payment Method Tabs -->
                <div class="payment-tabs">
                    <button class="payment-tab active" data-method="card">
                        <i class="fas fa-credit-card payment-icon"></i>
                        Card
                    </button>
                    <button class="payment-tab" data-method="apple-pay">
                        <i class="fab fa-apple payment-icon"></i>
                        Apple Pay
                    </button>
                    <button class="payment-tab" data-method="paypal">
                        <i class="fab fa-paypal payment-icon"></i>
                        PayPal
                    </button>
                </div>
                
                <!-- Card Payment Method -->
                <div class="payment-method active" id="card-method">
                    <form id="card-payment-form" class="card-form">
                        <div class="form-group">
                            <label for="cardholder-name">Cardholder Name</label>
                            <input type="text" id="cardholder-name" placeholder="John Doe" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="card-element">Card Information</label>
                            <div id="card-element"></div>
                            <div id="card-errors" class="error-message"></div>
                        </div>
                        
                        <div class="card-row">
                            <div class="form-group">
                                <label for="billing-zip">Billing ZIP</label>
                                <input type="text" id="billing-zip" placeholder="10001" required>
                            </div>
                            <div class="form-group">
                                <label for="billing-country">Country</label>
                                <input type="text" id="billing-country" placeholder="US" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="submit-payment" id="card-submit">
                            <i class="fas fa-lock" style="margin-right: 8px;"></i>
                            Pay $${totalAmount.toFixed(2)}
                        </button>
                    </form>
                    
                    <div style="margin-top: 15px; padding: 12px; background: #f0f8ff; border-radius: 6px; font-size: 12px; color: #666; display: flex; align-items: start; gap: 10px;">
                        <i class="fas fa-info-circle" style="color: #0070ba; margin-top: 2px;"></i>
                        <div>
                            <strong>Test Mode:</strong> Use card number <code style="background: white; padding: 2px 6px; border-radius: 3px;">4242 4242 4242 4242</code> with any future expiry date and any 3-digit CVC.
                        </div>
                    </div>
                </div>
                
                <!-- Apple Pay Method -->
                <div class="payment-method" id="apple-pay-method">
                    <div id="apple-pay-notice" style="display: none; padding: 20px; background: #fff3cd; border-radius: 8px; margin-top: 20px; color: #856404;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Apple Pay is not available on this device or browser. Please use Safari on an Apple device.
                    </div>
                    <button id="apple-pay-button">
                        <i class="fab fa-apple" style="font-size: 24px;"></i>
                        Pay with Apple Pay
                    </button>
                    <div style="margin-top: 15px; font-size: 13px; color: #666; text-align: center;">
                        Fast, secure, and private
                    </div>
                </div>
                
                <!-- PayPal Method -->
                <div class="payment-method" id="paypal-method">
                    <button onclick="payWithPayPal(${totalAmount})" class="submit-payment" style="background: #0070ba;">
                        <i class="fab fa-paypal" style="margin-right: 8px; font-size: 20px;"></i>
                        Pay with PayPal
                    </button>
                    
                    <!-- Direct Payment Option -->
                    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0; margin-top: 20px;">
                        <h4 style="font-size: 14px; margin-bottom: 10px; font-weight: 600;">Or Send Payment Directly:</h4>
                        <p style="font-size: 13px; color: #666; margin-bottom: 10px;">Send <strong>$${totalAmount.toFixed(2)}</strong> to:</p>
                        <p style="font-size: 14px; font-weight: 600; margin-bottom: 5px;">payments@nextgenfdm.com</p>
                        <p style="font-size: 12px; color: #888; margin-bottom: 15px;">or @nextgenfdm_payments</p>
                        <div style="background: white; padding: 10px; border-radius: 4px; font-size: 12px; color: #666;">
                            <strong>Order Reference:</strong><br>
                            Items: ${itemsList}
                        </div>
                        <button onclick="confirmDirectPayment(${totalAmount})" style="width: 100%; margin-top: 15px; padding: 12px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600;">
                            I've Sent the Payment ✓
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Initialize payment method switching
        initPaymentTabs();
        
        // Initialize Stripe for card payments
        initializeStripe(totalAmount);
        
        // Initialize Apple Pay
        initializeApplePay(totalAmount);
    }
    
    // Initialize payment method tabs
    function initPaymentTabs() {
        const tabs = document.querySelectorAll('.payment-tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active from all tabs and methods
                document.querySelectorAll('.payment-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('active'));
                
                // Add active to clicked tab
                this.classList.add('active');
                
                // Show corresponding payment method
                const method = this.dataset.method;
                document.getElementById(method + '-method').classList.add('active');
            });
        });
    }
    
    // Initialize Stripe
    function initializeStripe(totalAmount) {
        // Use Stripe test publishable key (you should replace with your own)
        // Use Stripe Publishable Key from config.php
        const stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');
        const elements = stripe.elements();
        
        // Create card element
        const cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                }
            }
        });
        
        cardElement.mount('#card-element');
        
        // Handle real-time validation errors
        cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
        
        // Handle form submission
        const form = document.getElementById('card-payment-form');
        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const submitBtn = document.getElementById('card-submit');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';
            
            try {
                // Create payment method
                const {paymentMethod, error} = await stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement,
                    billing_details: {
                        name: document.getElementById('cardholder-name').value,
                    }
                });
                
                if (error) {
                    document.getElementById('card-errors').textContent = error.message;
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-lock" style="margin-right: 8px;"></i>Pay $' + totalAmount.toFixed(2);
                } else {
                    // Payment successful (in test mode)
                    confirmPayment(totalAmount, 'CARD-' + paymentMethod.id);
                }
            } catch (err) {
                document.getElementById('card-errors').textContent = 'Payment failed. Please try again.';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-lock" style="margin-right: 8px;"></i>Pay $' + totalAmount.toFixed(2);
            }
        });
    }
    
    // Initialize Apple Pay
    function initializeApplePay(totalAmount) {
        const applePayButton = document.getElementById('apple-pay-button');
        const applePayNotice = document.getElementById('apple-pay-notice');
        
        // Check if Apple Pay is available
        if (!window.ApplePaySession || !ApplePaySession.canMakePayments()) {
            applePayButton.style.display = 'none';
            applePayNotice.style.display = 'block';
            return;
        }
        
        applePayButton.addEventListener('click', function() {
            // For demo purposes, simulate Apple Pay
            if (confirm('Apple Pay Demo: Click OK to simulate a successful payment of $' + totalAmount.toFixed(2))) {
                confirmPayment(totalAmount, 'APPLEPAY-' + Date.now());
            }
        });
    }
    
    // Confirm payment and save order
    function confirmPayment(totalAmount, paymentId) {
        const cart = JSON.parse(localStorage.getItem('nextgenfdm_cart') || '{}');
        
        fetch('process_order.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                order_id: paymentId,
                payer: {
                    email_address: 'customer@email.com',
                    name: {given_name: 'Customer', surname: ''}
                },
                cart: cart,
                amount: totalAmount.toFixed(2)
            })
        })
        .then(response => response.json())
        .then(data => {
            showSuccessMessage(totalAmount);
            localStorage.removeItem('nextgenfdm_cart');
        })
        .catch(error => {
            showSuccessMessage(totalAmount);
            localStorage.removeItem('nextgenfdm_cart');
        });
    }
    
    function showSuccessMessage(totalAmount) {
        document.getElementById('checkout-content').innerHTML = `
            <div class="empty-cart">
                <div style="font-size: 60px; color: #28a745; margin-bottom: 20px;">✓</div>
                <h2>Payment Successful!</h2>
                <p>Thank you for your order!</p>
                <p style="color: #666; margin-top: 10px;">Total: $${totalAmount.toFixed(2)}</p>
                <p style="color: #666;">You'll receive a confirmation email shortly.</p>
                <a href="index.php" class="continue-shopping">Continue Shopping</a>
            </div>
        `;
    }
    
    window.payWithPayPal = function(totalAmount) {
        // Open PayPal payment page
        window.open(`https://www.paypal.com/paypalme/nextgenfdm_payments/${totalAmount}`, '_blank');
        
        // Show confirmation
        setTimeout(() => {
            if (confirm('After completing payment on PayPal, click OK to confirm your order.')) {
                confirmDirectPayment(totalAmount);
            }
        }, 2000);
    }
    
    window.confirmDirectPayment = function(totalAmount) {
        const cart = JSON.parse(localStorage.getItem('nextgenfdm_cart') || '{}');
        
        // Save order to database
        fetch('process_order.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                order_id: 'MANUAL-' + Date.now(),
                payer: {
                    email_address: 'customer@email.com',
                    name: {given_name: 'Customer', surname: ''}
                },
                cart: cart,
                amount: totalAmount.toFixed(2)
            })
        })
        .then(response => response.json())
        .then(data => {
            // Show success
            document.getElementById('checkout-content').innerHTML = `
                <div class="empty-cart">
                    <div style="font-size: 60px; color: #28a745; margin-bottom: 20px;">✓</div>
                    <h2>Order Received!</h2>
                    <p>Thank you! We've received your order.</p>
                    <p style="color: #666; margin-top: 10px;">Total: $${totalAmount.toFixed(2)}</p>
                    <p style="color: #666;">We'll confirm your payment and ship your items soon.</p>
                    <a href="index.php" class="continue-shopping">Continue Shopping</a>
                </div>
            `;
            localStorage.removeItem('nextgenfdm_cart');
        })
        .catch(error => {
            alert('Order saved! We will process it shortly.');
            localStorage.removeItem('nextgenfdm_cart');
            window.location.href = 'index.php';
        });
    }
})();
</script>

<footer>
    <p>© 2025 What's Real Shall Prosper. All rights reserved.</p>
</footer>

</body>
</html>

