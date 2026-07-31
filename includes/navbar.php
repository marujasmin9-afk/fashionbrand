<?php
$cart_count = get_cart_count();
$wishlist_count = get_wishlist_count();
?>
<header class="sticky-top">
    <nav class="navbar navbar-expand-lg luxury-navbar py-3">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                Brand<span>Fashion</span>
            </a>

            <button class="navbar-toggler border-secondary text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Navigation Links & Mega Menu -->
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'shop.php' ? 'active' : '' ?>" href="shop.php">Shop All</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'jewelry.php' ? 'active' : '' ?>" href="jewelry.php">Jewelry</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'clothing.php' ? 'active' : '' ?>" href="clothing.php">Clothing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'new-arrivals.php' ? 'active' : '' ?>" href="new-arrivals.php">New Arrivals</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'best-sellers.php' ? 'active' : '' ?>" href="best-sellers.php">Best Sellers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-gold <?= basename($_SERVER['PHP_SELF']) == 'sale.php' ? 'active' : '' ?>" href="sale.php"><i class="fas fa-bolt me-1"></i> Sale</a>
                    </li>
                </ul>

                <!-- Right Actions: Search, Wishlist, Cart, Theme Toggle -->
                <div class="d-flex align-items-center gap-3">
                    <!-- Live Search Bar -->
                    <div class="position-relative d-none d-xl-block" style="width: 220px;">
                        <input type="text" id="search-input" class="form-control form-control-luxury text-white rounded-pill px-3 py-1 small" placeholder="Search luxury..." autocomplete="off">
                        <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-gold extra-small"></i>
                        <div id="search-results-dropdown" class="position-absolute start-0 end-0 top-100 mt-2 z-3" style="display: none;"></div>
                    </div>

                    <!-- Wishlist Link -->
                    <a href="wishlist.php" class="text-white position-relative fs-5 ms-1">
                        <i class="far fa-heart"></i>
                        <span class="icon-badge wishlist-count-badge"><?= $wishlist_count ?></span>
                    </a>

                    <!-- Cart Link -->
                    <a href="cart.php" class="text-white position-relative fs-5 ms-2">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="icon-badge cart-count-badge"><?= $cart_count ?></span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>
