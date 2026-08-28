<footer>
    <p>© 2025 What's Real Shall Prosper. All rights reserved.</p>
</footer>

<?php include __DIR__ . '/chatbot.php'; ?>


<?php include 'modals.php'; ?>

<!-- Quick View Modal -->
<div id="quick-view-modal" class="modal-overlay">
    <div class="modal-content quick-view-content">
        <button class="modal-close" style="position:absolute; top:10px; right:10px; z-index:10;">&times;</button>
        <div class="quick-view-grid">
            <div class="quick-view-image">
                <img src="" alt="Product Image" id="qv-image">
            </div>
            <div class="quick-view-details">
                <h2 id="qv-title">Product Title</h2>
                <div class="qv-price" id="qv-price">$0.00</div>
                <div class="qv-rating" id="qv-rating" style="margin-bottom:15px; color:#f39c12; font-size:14px;"></div>
                
                <p class="qv-description">Experience the premium quality of our signature collection. Designed for comfort and style, this piece is a must-have for your wardrobe.</p>
                
                <div class="qv-actions">
                    <div class="qty-selector">
                        <button class="qty-btn minus" onclick="var i=document.getElementById('qv-qty'); i.value=Math.max(1, parseInt(i.value)-1)">-</button>
                        <input type="number" id="qv-qty" value="1" min="1" readonly>
                        <button class="qty-btn plus" onclick="var i=document.getElementById('qv-qty'); i.value=parseInt(i.value)+1">+</button>
                    </div>
                    <button class="add-to-cart-btn-large" id="qv-add-btn">Add to Bag</button>
                </div>
                
                <div class="qv-meta" style="font-size:12px; color:#888; margin-top:20px;">
                    <div style="margin-bottom:5px;">Category: <strong id="qv-category" style="color:#000">-</strong></div>
                    <div>Condition: <strong id="qv-condition" style="color:#000">-</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Global JavaScript for Cart, Favorites & Chatbot -->
<script src="js/main.js" defer></script>
<script src="js/chatbot.js" defer></script>

</body>
</html>

