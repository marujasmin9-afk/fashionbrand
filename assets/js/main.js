/* ==========================================================================
   AURA LUXE - JavaScript & AJAX Logic
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
    
    // Initialize AOS Animations
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 900,
            once: true,
            easing: 'ease-out-quad'
        });
    }

    // Light / Dark Theme Switcher
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('aura_theme', newTheme);
            themeToggle.querySelector('i').className = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
        });

        // Restore theme
        const savedTheme = localStorage.getItem('aura_theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
            if (themeToggle.querySelector('i')) {
                themeToggle.querySelector('i').className = savedTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            }
        }
    }

    // Flash Sale Countdown Timer
    const countdownEl = document.getElementById('flash-sale-timer');
    if (countdownEl) {
        const targetDate = new Date().getTime() + (3 * 24 * 60 * 60 * 1000); // 3 days from now
        
        setInterval(function () {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) return;

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            const daysEl = document.getElementById('timer-days');
            const hoursEl = document.getElementById('timer-hours');
            const minsEl = document.getElementById('timer-mins');
            const secsEl = document.getElementById('timer-secs');

            if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
            if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
            if (minsEl) minsEl.textContent = String(minutes).padStart(2, '0');
            if (secsEl) secsEl.textContent = String(seconds).padStart(2, '0');
        }, 1000);
    }

    // Live Search Suggestions
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results-dropdown');

    if (searchInput && searchResults) {
        let debounceTimer;
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            debounceTimer = setTimeout(function () {
                fetch(`ajax/search_suggest.php?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) {
                            let html = '<ul class="list-group list-group-flush bg-dark text-white rounded-3 shadow-lg">';
                            data.forEach(item => {
                                html += `
                                    <li class="list-group-item bg-dark border-secondary">
                                        <a href="product-details.php?id=${item.id}" class="d-flex align-items-center text-decoration-none text-white">
                                            <img src="${item.main_image}" width="40" height="40" class="rounded me-3 object-fit-cover" />
                                            <div>
                                                <div class="fw-semibold small">${item.title}</div>
                                                <div class="text-gold extra-small">${item.price_formatted}</div>
                                            </div>
                                        </a>
                                    </li>
                                `;
                            });
                            html += '</ul>';
                            searchResults.innerHTML = html;
                            searchResults.style.display = 'block';
                        } else {
                            searchResults.innerHTML = '<div class="p-3 bg-dark text-muted small">No luxury items found</div>';
                            searchResults.style.display = 'block';
                        }
                    });
            }, 300);
        });

        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    }

    // AJAX Add to Cart Handler
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.ajax-add-cart');
        if (btn) {
            e.preventDefault();
            const productId = btn.getAttribute('data-id');
            const qty = btn.getAttribute('data-qty') || 1;
            const color = btn.getAttribute('data-color') || '';
            const size = btn.getAttribute('data-size') || '';

            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('product_id', productId);
            formData.append('quantity', qty);
            formData.append('color', color);
            formData.append('size', size);

            fetch('ajax/cart.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast('Shopping Bag', data.message, 'success');
                    updateCartBadge(data.cart_count);
                } else {
                    showToast('Error', data.message, 'error');
                }
            })
            .catch(err => {
                showToast('Error', 'Unable to process cart operation.', 'error');
            });
        }
    });

    // AJAX Wishlist Toggle Handler
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.ajax-wishlist');
        if (btn) {
            e.preventDefault();
            const productId = btn.getAttribute('data-id');

            const formData = new FormData();
            formData.append('product_id', productId);

            fetch('ajax/wishlist.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast('Wishlist', data.message, 'success');
                    updateWishlistBadge(data.wishlist_count);
                    if (data.action === 'added') {
                        btn.classList.add('active');
                        btn.querySelector('i').className = 'fas fa-heart text-danger';
                    } else {
                        btn.classList.remove('active');
                        btn.querySelector('i').className = 'far fa-heart';
                    }
                } else {
                    showToast('Wishlist', data.message, 'warning');
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                }
            });
        }
    });

    // AJAX Quick View Modal Handler
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.ajax-quick-view');
        if (btn) {
            e.preventDefault();
            const productId = btn.getAttribute('data-id');
            
            fetch(`ajax/quick_view.php?id=${productId}`)
                .then(res => res.text())
                .then(html => {
                    const modalBody = document.getElementById('quickViewModalBody');
                    if (modalBody) {
                        modalBody.innerHTML = html;
                        const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));
                        modal.show();
                    }
                });
        }
    });

    // Helper functions for badge updating & toast notifications
    window.updateCartBadge = function (count) {
        const badges = document.querySelectorAll('.cart-count-badge');
        badges.forEach(b => b.textContent = count);
    };

    window.updateWishlistBadge = function (count) {
        const badges = document.querySelectorAll('.wishlist-count-badge');
        badges.forEach(b => b.textContent = count);
    };

    window.showToast = function (title, message, type = 'info') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast luxury-toast show align-items-center mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong class="text-gold me-2"><i class="fas fa-crown"></i> ${title}:</strong> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', toastHtml);

        setTimeout(() => {
            const el = document.getElementById(toastId);
            if (el) el.remove();
        }, 4000);
    };

});
