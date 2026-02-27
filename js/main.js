
(function () {
    // helpers
    const qs = (s, el = document) => el.querySelector(s);
    const qsa = (s, el = document) => Array.from(el.querySelectorAll(s));

    // Common State (Cart & Favs)
    let cart = JSON.parse(localStorage.getItem('nextgenfdm_cart') || '{}');
    let favs = JSON.parse(localStorage.getItem('nextgenfdm_favs') || '{}');

    // AJAX State
    let isLoading = false;
    let hasMore = true;
    let currentOffset = 0;
    const loadLimit = 12;

    // --- State Management ---
    function updateFavIcons() {
        // Only if there are cards on the page
        const cards = qsa('.card');
        if (cards.length) {
            cards.forEach(card => {
                const id = card.dataset.id;
                const btn = card.querySelector('.fav-toggle');
                if (btn) {
                    if (favs[id]) btn.classList.add('active');
                    else btn.classList.remove('active');
                }
            });
        }

        const badge = qs('#fav-badge');
        if (badge) {
            const count = Object.keys(favs).length;
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }

    function updateCartUI() {
        const count = Object.values(cart).reduce((a, b) => a + b.qty, 0);
        const badge = qs('#cart-badge');
        if (badge) badge.textContent = count;
    }

    function saveCart() {
        localStorage.setItem('nextgenfdm_cart', JSON.stringify(cart));
        updateCartUI();
    }
    function saveFavs() {
        localStorage.setItem('nextgenfdm_favs', JSON.stringify(favs));
        updateFavIcons();
    }

    // --- Global Actions (exposed or used by event delegation) ---
    window.toggleFav = function (card) {
        const id = card.dataset.id;
        if (favs[id]) {
            delete favs[id];
        } else {
            favs[id] = {
                id: id,
                title: card.dataset.title || card.dataset.name,
                price: card.dataset.price,
                img: card.dataset.img || card.querySelector('img').src
            };
        }
        saveFavs();
        renderFavItems(); // Ensure fav items are rendered when favs update
    }

    window.addToCart = function (cardOrData) {
        // Support passing an element (card) or direct data object
        let id, title, price, img, qty = 1;

        if (cardOrData instanceof HTMLElement) {
            id = cardOrData.dataset.id;
            title = cardOrData.dataset.title || cardOrData.dataset.name;
            price = parseFloat(cardOrData.dataset.price);
            img = cardOrData.dataset.img || cardOrData.querySelector('img').src;
            // Check if there is a qty input inside the card
            const qtyInput = cardOrData.querySelector('input[name="qty"]');
            if (qtyInput) qty = parseInt(qtyInput.value) || 1;
        } else {
            // direct object
            id = cardOrData.id;
            title = cardOrData.title;
            price = parseFloat(cardOrData.price);
            img = cardOrData.img;
            qty = cardOrData.qty || 1;
        }

        if (!cart[id]) {
            cart[id] = {
                id: id,
                title: title,
                price: price,
                img: img,
                qty: qty
            };
        } else {
            cart[id].qty += qty;
        }
        saveCart();

        // Show feedback if button exists
        if (cardOrData instanceof HTMLElement) {
            const btn = cardOrData.querySelector('.quick-add-btn') || cardOrData.querySelector('button[type="submit"]');
            if (btn) {
                const originalText = btn.textContent;
                btn.textContent = 'Added!';
                const originalBg = btn.style.background;
                const originalColor = btn.style.color;

                btn.style.background = '#000';
                btn.style.color = '#fff';
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.style.background = originalBg;
                    btn.style.color = originalColor;
                }, 1500);
            }
        } else {
            alert('Added to cart!');
        }
    }

    // --- Modals ---
    function openModal(id) {
        const modal = qs('#' + id);
        if (!modal) return;
        modal.classList.add('open');
        if (id === 'cart-modal') renderCartItems();
        if (id === 'fav-modal') renderFavItems();
    }
    function closeModal(el) {
        el.closest('.modal-overlay').classList.remove('open');
    }

    function renderCartItems() {
        const container = qs('#cart-items-container');
        if (!container) return;
        const totalEl = qs('#cart-total-display');
        container.innerHTML = '';
        let total = 0;
        const ids = Object.keys(cart);

        if (ids.length === 0) {
            container.innerHTML = '<p style="text-align:center; color:#888; margin-top:50px;">Your bag is empty.</p>';
        } else {
            ids.forEach(id => {
                const item = cart[id];
                const row = document.createElement('div');
                row.className = 'cart-item';
                row.innerHTML = `
                    <img src="${item.img}" alt="${item.title}">
                    <div style="flex:1">
                        <div style="font-weight:600; font-size:13px;">${item.title}</div>
                        <div style="color:#888; font-size:12px;">$${item.price.toFixed(2)} x ${item.qty}</div>
                    </div>
                    <div>
                        <button class="remove-cart-item" data-id="${id}" style="border:none; background:none; color:red; cursor:pointer;">&times;</button>
                    </div>
                `;
                container.appendChild(row);
                total += (item.price * item.qty);
            });
        }
        if (totalEl) totalEl.textContent = '$' + total.toFixed(2);

        // wire remove buttons
        container.querySelectorAll('.remove-cart-item').forEach(btn => {
            btn.addEventListener('click', e => {
                delete cart[e.target.dataset.id];
                saveCart();
                renderCartItems();
            });
        });
    }

    function renderFavItems() {
        const container = qs('#fav-items-container');
        if (!container) return;
        container.innerHTML = '';
        const ids = Object.keys(favs);

        if (ids.length === 0) {
            container.innerHTML = '<p style="text-align:center; color:#888; margin-top:50px;">No favorites yet.</p>';
        } else {
            ids.forEach(id => {
                const item = favs[id];
                const row = document.createElement('div');
                row.className = 'fav-item';
                row.innerHTML = `
                    <img src="${item.img}" alt="${item.title}">
                    <div style="flex:1">
                        <div style="font-weight:600; font-size:13px;">${item.title}</div>
                        <div style="color:#888; font-size:12px;">$${item.price}</div>
                    </div>
                    <button class="remove-fav-item" data-id="${id}" style="border:none; background:none; cursor:pointer; font-size:12px; text-decoration:underline;">Remove</button>
                `;
                container.appendChild(row);
            });
        }
        // wire remove buttons
        container.querySelectorAll('.remove-fav-item').forEach(btn => {
            btn.addEventListener('click', e => {
                delete favs[e.target.dataset.id];
                saveFavs();
                renderFavItems();
                updateFavIcons();
            });
        });
    }

    // --- Search & Filtering (AJAX Based) ---
    const productList = qs('#product-list');
    const resultsCount = qs('#results-count');
    const loadMoreBtn = qs('#load-more-btn');

    function getCurrentFilters() {
        const catEl = qs('#search-category');
        const searchEl = qs('#site-search');
        const sortEl = qs('#sort-by');
        const condChecked = qsa('.cond-filter:checked').map(i => i.value);
        const priceEl = qs('.price-filter:checked');
        const ratingActive = qs('.rating-filter.active');

        return {
            category: catEl ? catEl.value : '',
            search: searchEl ? searchEl.value : '',
            condition: condChecked.length ? condChecked[0] : '', // API currently supports one condition
            sort: sortEl ? sortEl.value : 'newest',
            priceRange: priceEl ? priceEl.value : '',
            minRating: ratingActive ? ratingActive.dataset.min : 0
        };
    }

    function createProductCard(product) {
        const card = document.createElement('li');
        card.className = 'card';
        card.dataset.id = product.id;
        card.dataset.cat = product.category;
        card.dataset.price = product.price;
        card.dataset.title = product.name;
        card.dataset.cond = product.condition;
        card.dataset.rating = product.rating;
        card.dataset.img = product.image;

        let conditionBadge = '';
        if (product.condition === 'new') {
            conditionBadge = '<div style="font-size: 11px; color: #28a745; font-weight: 600; margin-top: 5px;">NEW ARRIVAL</div>';
        } else if (product.condition === 'sale') {
            conditionBadge = '<div style="font-size: 11px; color: #dc3545; font-weight: 600; margin-top: 5px;">ON SALE</div>';
        }

        card.innerHTML = `
            <div style="position:relative; overflow:hidden;">
                <img src="${product.image}" loading="lazy" class="product-image" alt="${product.name}">
                <button type="button" class="fav-toggle"><i class="fa fa-heart"></i></button>
                <button type="button" class="quick-add-btn">Add to Bag</button>
            </div>
            <div class="product-info">
                <h3 class="product-title">${product.name}</h3>
                <div class="product-price">$${product.price.toFixed(2)}</div>
                ${conditionBadge}
            </div>
        `;
        return card;
    }

    function loadMoreProducts(isFilter = false) {
        if (!productList) return;
        if (isLoading || (!hasMore && !isFilter)) return;

        isLoading = true;
        if (loadMoreBtn) {
            loadMoreBtn.disabled = true;
            loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i>' + (isFilter ? 'Filtering...' : 'Loading...');
        }

        const filters = getCurrentFilters();
        const params = new URLSearchParams({
            offset: currentOffset,
            limit: loadLimit,
            category: filters.category,
            search: filters.search,
            condition: filters.condition,
            sort: filters.sort,
            price_range: filters.priceRange,
            min_rating: filters.minRating
        });

        fetch(`load_more.php?${params}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const existingTeaser = productList.parentNode.querySelector('.end-of-archives');
                    if (existingTeaser) existingTeaser.remove();

                    if (isFilter) productList.innerHTML = '';

                    data.products.forEach(product => {
                        const card = createProductCard(product);
                        productList.appendChild(card);
                    });

                    hasMore = data.hasMore;
                    currentOffset += data.loaded;
                    updateFavIcons();

                    if (loadMoreBtn) {
                        if (!hasMore) {
                            loadMoreBtn.style.display = 'none';
                            const teaser = document.createElement('div');
                            teaser.className = 'end-of-archives';
                            teaser.style.textAlign = 'center';
                            teaser.style.marginTop = '40px';
                            teaser.style.marginBottom = '60px';
                            teaser.style.width = '100%';
                            teaser.innerHTML = `
                                <h3 style="font-size:16px; letter-spacing:2px; text-transform:uppercase; margin-bottom:15px; color:#888;">End of Archives</h3>
                                <a href="coming_soon.php" style="display:inline-block; padding:15px 30px; background:#000; color:#fff; text-decoration:none; text-transform:uppercase; font-size:12px; font-weight:700; letter-spacing:1px; transition:all 0.3s;">
                                    View Unreleased Collection &rarr;
                                </a>
                            `;
                            productList.after(teaser);
                        } else {
                            loadMoreBtn.style.display = 'inline-block';
                            loadMoreBtn.disabled = false;
                            loadMoreBtn.innerHTML = 'Load More Products';
                        }
                    }

                    if (resultsCount) resultsCount.textContent = data.total;

                    // Animations
                    const newCards = Array.from(productList.children).slice(-data.loaded);
                    newCards.forEach((card, index) => {
                        if (card.nodeType === 1) {
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(20px)';
                            setTimeout(() => {
                                card.style.transition = 'all 0.5s ease-out';
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, index * 30);
                        }
                    });
                } else {
                    console.error('Server returned error:', data.message);
                    if (loadMoreBtn) {
                        loadMoreBtn.disabled = false;
                        loadMoreBtn.innerHTML = 'Load More Products';
                    }
                }
            })
            .catch(err => {
                console.error('Error loading products:', err);
                if (loadMoreBtn) {
                    loadMoreBtn.disabled = false;
                    loadMoreBtn.innerHTML = 'Load More Products';
                    // Show a temporary error message near the button
                    const errEl = document.createElement('div');
                    errEl.style.color = '#d90000';
                    errEl.style.fontSize = '12px';
                    errEl.style.marginTop = '10px';
                    errEl.textContent = 'Failed to load products. Please check your connection.';
                    loadMoreBtn.parentNode.appendChild(errEl);
                    setTimeout(() => errEl.remove(), 3000);
                }
            })
            .finally(() => {
                isLoading = false;
            });
    }

    function filterAndSort() {
        currentOffset = 0;
        hasMore = true;
        loadMoreProducts(true);
    }

    // Expose to window for external access (e.g. from shop_now.php)
    window.filterAndSort = filterAndSort;
    window.loadMoreProducts = loadMoreProducts;

    if (productList) {
        // Wire Filtering Controls
        const catInput = qs('#search-category');
        if (catInput) catInput.addEventListener('change', filterAndSort);

        const searchInput = qs('#site-search') || qs('#q');
        if (searchInput) searchInput.addEventListener('input', filterAndSort);

        const sortSelect = qs('#sort-by') || qs('#sort');
        if (sortSelect) sortSelect.addEventListener('change', filterAndSort);

        qsa('.cat-filter').forEach(b => b.addEventListener('click', e => {
            const catIn = qs('#search-category');
            if (catIn) { catIn.value = e.target.dataset.cat; filterAndSort(); }
        }));

        qsa('.cond-filter').forEach(c => c.addEventListener('change', filterAndSort));

        qsa('.price-filter').forEach(r => r.addEventListener('change', filterAndSort));

        qsa('.rating-filter').forEach(b => b.addEventListener('click', function () {
            if (this.classList.contains('active')) {
                this.classList.remove('active');
            } else {
                qsa('.rating-filter').forEach(x => x.classList.remove('active'));
                this.classList.add('active');
            }
            filterAndSort();
        }));

        if (loadMoreBtn) loadMoreBtn.addEventListener('click', () => loadMoreProducts());

        // --- Shop Now Page Unique Logic ---

        // Category quick filter cards
        qsa('.category-card').forEach(card => {
            card.addEventListener('click', function () {
                qsa('.category-card').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                const catIn = qs('#search-category');
                if (catIn) {
                    catIn.value = this.dataset.category;
                    filterAndSort();
                }
            });
        });

        // View toggle
        const viewGridBtn = qs('#view-grid');
        const viewListBtn = qs('#view-list');
        if (viewGridBtn && viewListBtn) {
            viewGridBtn.addEventListener('click', () => {
                productList.className = 'products grid';
                viewGridBtn.classList.add('active');
                viewListBtn.classList.remove('active');
            });
            viewListBtn.addEventListener('click', () => {
                productList.className = 'products list';
                viewListBtn.classList.add('active');
                viewGridBtn.classList.remove('active');
            });
        }

        // Clear all filters
        const clearBtn = qs('#clear-all-filters');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                qsa('.cond-filter, .price-filter').forEach(cb => cb.checked = false);
                const catIn = qs('#search-category');
                if (catIn) catIn.value = '';
                qsa('.category-card').forEach(c => c.classList.remove('active'));
                const allCatCard = qs('.category-card[data-category=""]');
                if (allCatCard) allCatCard.classList.add('active');
                const sortSelect = qs('#sort-by');
                if (sortSelect) sortSelect.value = 'relevance';
                const searchInput = qs('#site-search');
                if (searchInput) searchInput.value = '';
                filterAndSort();
            });
        }

        // Infinite Scroll
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                const scrollPos = window.innerHeight + window.scrollY;
                const threshold = document.documentElement.scrollHeight - 600;
                if (scrollPos >= threshold && !isLoading && hasMore) loadMoreProducts();
            }, 100);
        });

        // Initial Load or Filter
        const urlParams = new URLSearchParams(window.location.search);
        const q = urlParams.get('q');
        const catURL = urlParams.get('cat');
        if (q && searchInput) searchInput.value = q;
        if (catURL && catInput) catInput.value = catURL;

        // Only trigger if we are on a page that needs initial dynamic load
        currentOffset = qsa('#product-list .card').length;

        // Initial hasMore check - hide button if we already show everything
        const totalEl = qs('#results-count');
        if (totalEl && loadMoreBtn) {
            const total = parseInt(totalEl.textContent) || 0;
            if (currentOffset >= total) {
                loadMoreBtn.style.display = 'none';
                hasMore = false;
            }
            console.log(`Initial State: offset=${currentOffset}, total=${total}, hasMore=${hasMore}`);
        }

        if (currentOffset === 0 || q || catURL) {
            filterAndSort();
        }
    }

    // --- Quick View Logic ---
    function openQuickView(card) {
        const d = card.dataset;
        const titleEl = qs('#qv-title'), priceEl = qs('#qv-price'), imgEl = qs('#qv-image'),
            ratingEl = qs('#qv-rating'), catEl = qs('#qv-category'), condEl = qs('#qv-condition');

        if (titleEl) titleEl.textContent = d.title;
        if (priceEl) priceEl.textContent = '$' + parseFloat(d.price).toFixed(2);
        if (imgEl) imgEl.src = d.img;
        if (ratingEl) ratingEl.textContent = '⭐'.repeat(Math.round(d.rating)) + ' (' + d.rating + ')';
        if (catEl) catEl.textContent = d.cat || 'General';
        if (condEl) condEl.textContent = d.cond ? (d.cond.charAt(0).toUpperCase() + d.cond.slice(1)) : 'Standard';

        const qtyEl = qs('#qv-qty'); if (qtyEl) qtyEl.value = 1;
        const btn = qs('#qv-add-btn');
        if (btn) {
            btn.dataset.id = d.id; btn.dataset.title = d.title;
            btn.dataset.price = d.price; btn.dataset.img = d.img;
        }
        openModal('quick-view-modal');
    }

    // --- Global Event Delegation (Clicks) ---
    document.addEventListener('click', e => {
        const target = e.target;
        // Fav
        if (target.closest('.fav-toggle')) {
            e.preventDefault();
            const card = target.closest('.card') || target.closest('.product-page');
            if (card) window.toggleFav(card);
            return;
        }

        // Quick View (Clicking Card Image or Title)
        if (target.closest('.card') && !target.closest('button') && !target.closest('.fav-toggle') && !target.closest('.quick-add-btn')) {
            const card = target.closest('.card');
            openQuickView(card);
            return;
        }

        // Add to Cart
        if (target.closest('.quick-add-btn')) {
            e.preventDefault();
            const card = target.closest('.card') || target.closest('.product-page');
            if (card) window.addToCart(card);
            return;
        }

        // Modal Triggers
        if (target.closest('#cart-btn')) { e.preventDefault(); openModal('cart-modal'); }
        if (target.closest('#fav-btn')) { e.preventDefault(); openModal('fav-modal'); }
        if (target.closest('.modal-close') || target.closest('.modal-overlay') === target) {
            const overlay = target.closest('.modal-overlay');
            if (overlay) closeModal(overlay);
        }

        // Quick View Add to Cart Button
        if (target.closest('#qv-add-btn')) {
            const btn = target.closest('#qv-add-btn');
            const qtyInput = qs('#qv-qty');
            window.addToCart({ id: btn.dataset.id, title: btn.dataset.title, price: parseFloat(btn.dataset.price), img: btn.dataset.img, qty: parseInt(qtyInput.value) || 1 });
            closeModal(btn.closest('.modal-overlay'));
        }
    });

    // initial run - global
    updateCartUI();
    updateFavIcons(); // in case static Fav icons exist on other pages

})();
