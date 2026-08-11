<?php
    ob_start();
$page_title = "Shopping Bag";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

$user_id = is_logged_in() ? $_SESSION['user_id'] : null;
$session_id = session_id();

// Fetch Cart items
if ($user_id) {
    $stmt = $pdo->prepare("SELECT c.*, p.title, p.main_image, p.price, p.discount_price, p.stock 
                           FROM cart c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
} else {
    $stmt = $pdo->prepare("SELECT c.*, p.title, p.main_image, p.price, p.discount_price, p.stock 
                           FROM cart c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.session_id = ?");
    $stmt->execute([$session_id]);
}
$cart_items = $stmt->fetchAll();

// Coupon check
$discount_amount = 0.00;
$applied_coupon = $_SESSION['applied_coupon'] ?? null;

$subtotal = 0;
foreach ($cart_items as $item) {
    $unit_price = ($item['discount_price'] && $item['discount_price'] < $item['price']) ? $item['discount_price'] : $item['price'];
    $subtotal += ($unit_price * $item['quantity']);
}

// Handle Coupon Application
if (isset($_POST['apply_coupon'])) {
    $code = sanitize($_POST['coupon_code']);
    $stmt_c = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active' AND expiry_date >= CURDATE()");
    $stmt_c->execute([$code]);
    $coupon = $stmt_c->fetch();

    if ($coupon) {
        if ($subtotal >= $coupon['min_order_amount']) {
            $_SESSION['applied_coupon'] = $coupon;
            set_flash_message('success', 'Coupon code ' . $coupon['code'] . ' applied!');
            header("Location: cart.php");
            exit;
        } else {
            set_flash_message('info', 'Minimum order amount for this coupon is ' . format_price($coupon['min_order_amount']));
        }
    } else {
        set_flash_message('danger', 'Invalid or expired promo coupon code.');
    }
}

if ($applied_coupon) {
    if ($applied_coupon['type'] === 'percentage') {
        $discount_amount = ($subtotal * $applied_coupon['value']) / 100;
        if ($applied_coupon['max_discount'] && $discount_amount > $applied_coupon['max_discount']) {
            $discount_amount = $applied_coupon['max_discount'];
        }
    } else {
        $discount_amount = $applied_coupon['value'];
    }
}

$tax_rate = $settings['tax_rate'];
$tax_amount = (($subtotal - $discount_amount) * $tax_rate) / 100;
$shipping_fee = ($subtotal >= $settings['free_shipping_threshold'] || $subtotal == 0) ? 0.00 : $settings['shipping_fee'];
$grand_total = ($subtotal - $discount_amount) + $tax_amount + $shipping_fee;

$flash = get_flash_message();
?>

<?php if ($flash): ?>
    <div class="alert alert-info alert-dismissible fade show rounded-0 mb-0 bg-dark text-gold border-gold" role="alert">
        <div class="container d-flex align-items-center">
            <i class="fas fa-crown me-2"></i> <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>

<section class="py-5">
    <div class="container">
        <h1 class="font-serif text-white display-5 mb-4" data-aos="fade-up">Shopping Bag</h1>

        <?php if (empty($cart_items)): ?>
            <div class="glass-card p-5 text-center" data-aos="fade-up">
                <i class="fas fa-shopping-bag text-gold display-1 mb-3 opacity-50"></i>
                <h3 class="font-serif text-white mb-2">Your Shopping Bag is Empty</h3>
                <p class="text-secondary small mb-4">Discover our high couture collection and fine solitaire jewelry.</p>
                <a href="shop.php" class="btn btn-gold text-uppercase">Explore Collection</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <!-- Cart Items List -->
                <div class="col-lg-8" data-aos="fade-right">
                    <div class="glass-card p-4 mb-4">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle border-secondary mb-0">
                                <thead>
                                    <tr class="text-gold uppercase small font-serif">
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): 
                                        $unit_price = ($item['discount_price'] && $item['discount_price'] < $item['price']) ? $item['discount_price'] : $item['price'];
                                        $item_total = $unit_price * $item['quantity'];
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?= htmlspecialchars($item['main_image']) ?>" class="rounded object-fit-cover" width="60" height="60" alt="<?= htmlspecialchars($item['title']) ?>">
                                                    <div>
                                                        <h6 class="font-serif text-white mb-1 small"><?= htmlspecialchars($item['title']) ?></h6>
                                                        <?php if ($item['color'] || $item['size']): ?>
                                                            <div class="extra-small text-muted">
                                                                <?= $item['color'] ? 'Color: ' . htmlspecialchars($item['color']) : '' ?>
                                                                <?= $item['size'] ? ' | Size: ' . htmlspecialchars($item['size']) : '' ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-gold font-serif"><?= format_price($unit_price) ?></td>
                                            <td>
                                                <div class="input-group input-group-sm" style="width: 100px;">
                                                    <input type="number" class="form-control bg-dark text-white text-center border-secondary cart-qty-input" data-id="<?= $item['id'] ?>" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>">
                                                </div>
                                            </td>
                                            <td class="text-gold font-serif fw-bold"><?= format_price($item_total) ?></td>
                                            <td>
                                                <button class="btn btn-link text-danger p-0 cart-remove-btn" data-id="<?= $item['id'] ?>" title="Remove">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="shop.php" class="btn btn-outline-gold text-uppercase small"><i class="fas fa-arrow-left me-2"></i> Continue Shopping</a>
                    </div>
                </div>

                <!-- Summary Column -->
                <div class="col-lg-4" data-aos="fade-left">
                    <div class="glass-card p-4">
                        <h4 class="font-serif text-gold mb-3 pb-2 border-bottom border-secondary">Order Summary</h4>

                        <!-- Coupon Form -->
                        <form method="POST" class="mb-4">
                            <label class="form-label text-white small">Promo / VIP Coupon Code</label>
                            <div class="input-group">
                                <input type="text" name="coupon_code" class="form-control bg-dark text-white border-secondary small" placeholder="e.g. AURA10" value="<?= $applied_coupon ? htmlspecialchars($applied_coupon['code']) : '' ?>">
                                <button type="submit" name="apply_coupon" class="btn btn-outline-gold small">Apply</button>
                            </div>
                        </form>

                        <div class="d-flex justify-content-between text-light small mb-2">
                            <span>Subtotal</span>
                            <span class="text-gold"><?= format_price($subtotal) ?></span>
                        </div>

                        <?php if ($discount_amount > 0): ?>
                            <div class="d-flex justify-content-between text-success small mb-2">
                                <span>Discount (<?= htmlspecialchars($applied_coupon['code']) ?>)</span>
                                <span>-<?= format_price($discount_amount) ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between text-light small mb-2">
                            <span>Estimated Tax (<?= $tax_rate ?>%)</span>
                            <span><?= format_price($tax_amount) ?></span>
                        </div>

                        <div class="d-flex justify-content-between text-light small mb-3">
                            <span>Express Shipping</span>
                            <span><?= $shipping_fee == 0 ? '<span class="text-success">COMPLIMENTARY</span>' : format_price($shipping_fee) ?></span>
                        </div>

                        <hr class="border-secondary mb-3">

                        <div class="d-flex justify-content-between text-white font-serif fs-5 mb-4">
                            <span>Grand Total</span>
                            <span class="text-gold fw-bold"><?= format_price($grand_total) ?></span>
                        </div>

                        <a href="checkout.php" class="btn btn-gold w-100 text-uppercase py-3">
                            Proceed To Checkout <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
