<?php
$page_title = "Order Details";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: my-orders.php");
    exit;
}

$stmt_items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt_items->execute([$order['id']]);
$items = $stmt_items->fetchAll();

$shipping = json_decode($order['shipping_address'], true);
?>

<section class="py-5">
    <div class="container">
        <div class="glass-card p-5" data-aos="fade-up">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
                <div>
                    <h3 class="font-serif text-white mb-1">Order #<?= htmlspecialchars($order['order_number']) ?></h3>
                    <span class="text-muted small">Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></span>
                </div>
                <button onclick="window.print()" class="btn btn-outline-gold btn-sm"><i class="fas fa-print me-1"></i> Print Invoice</button>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-dark border border-secondary rounded-3">
                        <h6 class="font-serif text-gold mb-2">Delivery Address</h6>
                        <p class="text-white small mb-1"><strong><?= htmlspecialchars($shipping['full_name']) ?></strong></p>
                        <p class="text-muted small mb-1"><?= htmlspecialchars($shipping['address_line1']) ?>, <?= htmlspecialchars($shipping['address_line2']) ?></p>
                        <p class="text-muted small mb-1"><?= htmlspecialchars($shipping['city']) ?>, <?= htmlspecialchars($shipping['state']) ?> - <?= htmlspecialchars($shipping['pincode']) ?></p>
                        <p class="text-muted small mb-0">Phone: <?= htmlspecialchars($shipping['phone']) ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-dark border border-secondary rounded-3">
                        <h6 class="font-serif text-gold mb-2">Payment Summary</h6>
                        <p class="text-white small mb-1">Method: <strong><?= strtoupper($order['payment_method']) ?></strong></p>
                        <p class="text-white small mb-1">Payment Status: <span class="badge bg-secondary"><?= strtoupper($order['payment_status']) ?></span></p>
                        <p class="text-white small mb-0">Order Status: <span class="badge bg-gold text-dark"><?= strtoupper($order['order_status']) ?></span></p>
                    </div>
                </div>
            </div>

            <h5 class="font-serif text-gold mb-3">Order Items</h5>
            <div class="table-responsive mb-4">
                <table class="table table-dark table-hover border-secondary align-middle small">
                    <thead>
                        <tr class="text-gold font-serif">
                            <th>Item</th>
                            <th>Unit Price</th>
                            <th>Qty</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $it): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= htmlspecialchars($it['product_image']) ?>" width="50" height="50" class="rounded object-fit-cover">
                                        <div>
                                            <div class="text-white font-serif"><?= htmlspecialchars($it['product_name']) ?></div>
                                            <?php if ($it['color'] || $it['size']): ?>
                                                <div class="extra-small text-muted"><?= $it['color'] ?> | <?= $it['size'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-gold font-serif"><?= format_price($it['price']) ?></td>
                                <td><?= $it['quantity'] ?></td>
                                <td class="text-end text-gold font-serif fw-bold"><?= format_price($it['total']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end text-light small">
                <div class="col-md-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span class="text-gold"><?= format_price($order['subtotal']) ?></span>
                    </div>
                    <?php if ($order['discount'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount:</span>
                            <span>-<?= format_price($order['discount']) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax:</span>
                        <span><?= format_price($order['tax']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span><?= format_price($order['shipping_fee']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between fs-5 font-serif text-white pt-2 border-top border-secondary">
                        <span>Grand Total:</span>
                        <span class="text-gold fw-bold"><?= format_price($order['grand_total']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
