<?php
$settings = get_settings();
?>
<div class="top-bar">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-none d-md-flex align-items-center gap-3">
            <span><i class="fas fa-phone-alt text-gold me-1"></i> <?= htmlspecialchars($settings['contact_phone']) ?></span>
            <span><i class="fas fa-envelope text-gold me-1"></i> <?= htmlspecialchars($settings['contact_email']) ?></span>
        </div>
        <div class="text-center flex-grow-1 flex-md-grow-0">
            <span class="text-gold fw-semibold">COMPLIMENTARY WORLDWIDE EXPRESS SHIPPING ON ORDERS OVER <?= format_price($settings['free_shipping_threshold']) ?></span>
        </div>
        <div class="d-flex align-items-center gap-3">

            </div>
            <?php if (is_logged_in()): ?>
                <a href="dashboard.php" class="text-white text-decoration-none small"><i class="fas fa-user-circle text-gold me-1"></i> Account</a>
                <a href="logout.php" class="text-muted text-decoration-none small">Logout</a>
            <?php else: ?>
                <a href="login.php" class="text-white text-decoration-none small"><i class="fas fa-sign-in-alt text-gold me-1"></i> Login</a>
                <span class="text-muted">|</span>
                <a href="register.php" class="text-white text-decoration-none small">Register</a>
            <?php endif; ?>
        </div>
    </div>
</div>
