<!-- Cart Modal -->
<div id="cart-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Shopping Bag</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="cart-items" id="cart-items-container">
            <p style="text-align:center; color:#888; margin-top:50px;">Your bag is empty.</p>
        </div>
        <div class="cart-actions">
            <div style="display:flex; justify-content:space-between; margin-bottom:20px; font-weight:bold;">
                <span>Total</span>
                <span id="cart-total-display">$0.00</span>
            </div>
            <button class="checkout-btn" onclick="window.location.href='checkout.php';">Checkout</button>
        </div>
    </div>
</div>

<!-- Favorites Modal -->
<div id="fav-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Favorites</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="fav-items" id="fav-items-container">
             <p style="text-align:center; color:#888; margin-top:50px;">No favorites yet.</p>
        </div>
    </div>
</div>
