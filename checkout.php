<?php
$page_title = "Boutique Checkout";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

$user_id = is_logged_in() ? $_SESSION['user_id'] : null;
$session_id = session_id();

// Fetch Cart items
if ($user_id) {
    $stmt = $pdo->prepare("SELECT c.*, p.title, p.main_image, p.price, p.discount_price 
                           FROM cart c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
} else {
    $stmt = $pdo->prepare("SELECT c.*, p.title, p.main_image, p.price, p.discount_price 
                           FROM cart c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.session_id = ?");
    $stmt->execute([$session_id]);
}
$cart_items = $stmt->fetchAll();

if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $unit_price = ($item['discount_price'] && $item['discount_price'] < $item['price']) ? $item['discount_price'] : $item['price'];
    $subtotal += ($unit_price * $item['quantity']);
}

$applied_coupon = $_SESSION['applied_coupon'] ?? null;
$discount_amount = 0.00;
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
$shipping_fee = ($subtotal >= $settings['free_shipping_threshold']) ? 0.00 : $settings['shipping_fee'];
$grand_total = ($subtotal - $discount_amount) + $tax_amount + $shipping_fee;

// Handle Order Placement
$error = '';
if (isset($_POST['place_order'])) {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address_line1 = sanitize($_POST['address_line1']);
    $address_line2 = sanitize($_POST['address_line2']);
    $city = sanitize($_POST['city']);
    $state = sanitize($_POST['state']);
    $country = sanitize($_POST['country']);
    $pincode = sanitize($_POST['pincode']);
    $payment_method = sanitize($_POST['payment_method']);
    $notes = sanitize($_POST['notes']);

    if (empty($full_name) || empty($email) || empty($phone) || empty($address_line1) || empty($city) || empty($pincode)) {
        $error = 'Please complete all required shipping fields.';
    } else {
        try {
            $pdo->beginTransaction();

            // Create User if guest
            $effective_user_id = $user_id;
            if (!$user_id) {
                // Check if user exists by email
                $stmt_u = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt_u->execute([$email]);
                $existing_u = $stmt_u->fetch();
                if ($existing_u) {
                    $effective_user_id = $existing_u['id'];
                } else {
                    $random_pw = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
                    $stmt_ins_u = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
                    $stmt_ins_u->execute([$full_name, $email, $phone, $random_pw]);
                    $effective_user_id = $pdo->lastInsertId();
                }
            }

            $order_number = 'AURA-' . strtoupper(bin2hex(random_bytes(4)));
            $shipping_address_json = json_encode([
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
                'address_line1' => $address_line1,
                'address_line2' => $address_line2,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'pincode' => $pincode
            ]);

            // Save Order
            $stmt_ord = $pdo->prepare("INSERT INTO orders (order_number, user_id, subtotal, discount, tax, shipping_fee, grand_total, payment_method, payment_status, order_status, shipping_address, notes) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed', ?, ?)");
            $stmt_ord->execute([
                $order_number,
                $effective_user_id,
                $subtotal,
                $discount_amount,
                $tax_amount,
                $shipping_fee,
                $grand_total,
                $payment_method,
                $payment_method === 'cod' ? 'pending' : 'paid',
                $shipping_address_json,
                $notes
            ]);
            $order_id = $pdo->lastInsertId();

            // Save Order Items & Update Stock
            foreach ($cart_items as $item) {
                $unit_price = ($item['discount_price'] && $item['discount_price'] < $item['price']) ? $item['discount_price'] : $item['price'];
                $item_total = $unit_price * $item['quantity'];

                $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, price, quantity, total, color, size) 
                                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt_item->execute([
                    $order_id,
                    $item['product_id'],
                    $item['title'],
                    $item['main_image'],
                    $unit_price,
                    $item['quantity'],
                    $item_total,
                    $item['color'],
                    $item['size']
                ]);

                // Reduce stock
                $stmt_stk = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stmt_stk->execute([$item['quantity'], $item['product_id']]);
            }

            // Save Payment Transaction
            $stmt_pay = $pdo->prepare("INSERT INTO payments (order_id, transaction_id, payment_method, amount, status) VALUES (?, ?, ?, ?, ?)");
            $stmt_pay->execute([
                $order_id,
                'TXN-' . strtoupper(bin2hex(random_bytes(5))),
                $payment_method,
                $grand_total,
                $payment_method === 'cod' ? 'pending' : 'completed'
            ]);

            // Clear Cart
            if ($user_id) {
                $stmt_clr = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
                $stmt_clr->execute([$user_id]);
            } else {
                $stmt_clr = $pdo->prepare("DELETE FROM cart WHERE session_id = ?");
                $stmt_clr->execute([$session_id]);
            }

            unset($_SESSION['applied_coupon']);
            $pdo->commit();

            header("Location: order-success.php?order=" . $order_number);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Failed to process order: ' . $e->getMessage();
        }
    }
}
?>

<section class="py-5">
    <div class="container">
        <h1 class="font-serif text-white display-5 mb-4" data-aos="fade-up">Checkout</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger bg-dark text-white border-danger mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="row g-4">
                <!-- Shipping Details -->
                <div class="col-lg-7" data-aos="fade-right">
                    <div class="glass-card p-4 mb-4">
                        <h4 class="font-serif text-gold mb-4 pb-2 border-bottom border-secondary">Boutique Delivery Address</h4>
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label text-light small">Full Name *</label>
                                <input type="text" name="full_name" class="form-control bg-dark text-white border-secondary small" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-light small">Email Address *</label>
                                <input type="email" name="email" class="form-control bg-dark text-white border-secondary small" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-light small">Telephone *</label>
                                <input type="tel" name="phone" class="form-control bg-dark text-white border-secondary small" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label text-light small">Address Line 1 *</label>
                                <input type="text" name="address_line1" class="form-control bg-dark text-white border-secondary small" placeholder="Street name, suite, villa" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label text-light small">Address Line 2 (Optional)</label>
                                <input type="text" name="address_line2" class="form-control bg-dark text-white border-secondary small">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-light small">City *</label>
                                <input type="text" name="city" class="form-control bg-dark text-white border-secondary small" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-light small">State *</label>
                                <input type="text" name="state" class="form-control bg-dark text-white border-secondary small" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-light small">PIN / Postal Code *</label>
                                <input type="text" name="pincode" class="form-control bg-dark text-white border-secondary small" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label text-light small">Country *</label>
                                <input type="text" name="country" class="form-control bg-dark text-white border-secondary small" value="United States" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label text-light small">Special Delivery Notes</label>
                                <textarea name="notes" class="form-control bg-dark text-white border-secondary small" rows="2" placeholder="Gift packaging instructions, discreet delivery, etc."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary & Payment -->
                <div class="col-lg-5" data-aos="fade-left">
                    <div class="glass-card p-4 mb-4">
                        <h4 class="font-serif text-gold mb-4 pb-2 border-bottom border-secondary">Order Summary</h4>

                        <?php foreach ($cart_items as $ci): 
                            $price = ($ci['discount_price'] && $ci['discount_price'] < $ci['price']) ? $ci['discount_price'] : $ci['price'];
                        ?>
                            <div class="d-flex align-items-center justify-content-between mb-3 text-light small">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= htmlspecialchars($ci['main_image']) ?>" class="rounded object-fit-cover" width="40" height="40">
                                    <div>
                                        <div class="fw-semibold text-white"><?= htmlspecialchars($ci['title']) ?></div>
                                        <div class="extra-small text-muted">Qty: <?= $ci['quantity'] ?></div>
                                    </div>
                                </div>
                                <div class="text-gold font-serif"><?= format_price($price * $ci['quantity']) ?></div>
                            </div>
                        <?php endforeach; ?>

                        <hr class="border-secondary my-3">

                        <div class="d-flex justify-content-between text-white font-serif fs-5 mb-4">
                            <span>Total Payable</span>
                            <span class="text-gold fw-bold"><?= format_price($grand_total) ?></span>
                        </div>

                        <h5 class="font-serif text-gold mb-3">Select Payment Gateway</h5>

                        <div class="form-check p-3 bg-dark border border-secondary rounded-3 mb-2">
                            <input class="form-check-input bg-dark border-gold" type="radio" name="payment_method" value="cod" id="pmCod" checked>
                            <label class="form-check-label text-white small" for="pmCod">
                                <i class="fas fa-hand-holding-usd me-2 text-gold"></i> Cash On Delivery (COD) / Concierge Pay
                            </label>
                        </div>

                        <div class="form-check p-3 bg-dark border border-secondary rounded-3 mb-2">
                            <input class="form-check-input bg-dark border-gold" type="radio" name="payment_method" value="razorpay" id="pmRazorpay">
                            <label class="form-check-label text-white small" for="pmRazorpay">
                                <i class="fas fa-credit-card me-2 text-gold"></i> Razorpay (Credit/Debit/UPI)
                            </label>
                        </div>

                        <div class="form-check p-3 bg-dark border border-secondary rounded-3 mb-2">
                            <input class="form-check-input bg-dark border-gold" type="radio" name="payment_method" value="stripe" id="pmStripe">
                            <label class="form-check-label text-white small" for="pmStripe">
                                <i class="fab fa-stripe me-2 text-gold fs-5"></i> Stripe International Card
                            </label>
                        </div>

                        <div class="form-check p-3 bg-dark border border-secondary rounded-3 mb-4">
                            <input class="form-check-input bg-dark border-gold" type="radio" name="payment_method" value="paypal" id="pmPaypal">
                            <label class="form-check-label text-white small" for="pmPaypal">
                                <i class="fab fa-paypal me-2 text-gold"></i> PayPal Express
                            </label>
                        </div>

                        <button type="submit" name="place_order" class="btn btn-gold w-100 text-uppercase py-3">
                            Confirm & Place Order <i class="fas fa-check-circle ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
