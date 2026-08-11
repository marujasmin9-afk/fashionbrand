<?php
include __DIR__ . '/includes/admin_header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT o.*, u.name as customer_name, u.email as customer_email FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: orders.php");
    exit;
}

$stmt_items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt_items->execute([$order['id']]);
$items = $stmt_items->fetchAll();

$shipping = json_decode($order['shipping_address'], true);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-serif text-white mb-0">Order Details: <?= htmlspecialchars($order['order_number']) ?></h3>
    <div>
        <button onclick="window.print()" class="btn btn-outline-gold btn-sm"><i class="fas fa-print me-1"></i> Print Invoice</button>
        <a href="orders.php" class="btn btn-outline-secondary text-white btn-sm ms-2"><i class="fas fa-arrow-left me-1"></i> Back to Orders</a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card bg-dark border-secondary p-3">
            <h5 class="font-serif text-white mb-3">Shipping Address</h5>
            <p class="text-white small mb-1"><strong><?= htmlspecialchars($shipping['full_name']) ?></strong></p>
            <p class="text-white small mb-1"><?= htmlspecialchars($shipping['address_line1']) ?>, <?= htmlspecialchars($shipping['address_line2']) ?></p>
            <p class="text-white small mb-1"><?= htmlspecialchars($shipping['city']) ?>, <?= htmlspecialchars($shipping['state']) ?> - <?= htmlspecialchars($shipping['pincode']) ?></p>
            <p class="text-white small mb-0">Phone: <?= htmlspecialchars($shipping['phone']) ?></p>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card bg-dark border-secondary p-3">
            <h5 class="font-serif text-white mb-3">Order Information</h5>
            <p class="text-white small mb-1">Placed On: <strong><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></strong></p>
            <p class="text-white small mb-1">Payment Method: <strong><?= strtoupper($order['payment_method']) ?></strong></p>
            <p class="text-white small mb-0">Current Status: <span class="badge bg-white text-dark"><?= strtoupper($order['order_status']) ?></span></p>
        </div>
    </div>
</div>

<div class="card bg-dark border-secondary">
    <div class="card-header bg-dark border-secondary text-gold font-serif">Line Items</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover border-secondary align-middle mb-0 small">
                <thead>
                    <tr class="text-gold font-serif">
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $it): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= htmlspecialchars($it['product_image']) ?>" width="40" height="40" class="rounded object-fit-cover">
                                    <span class="text-white font-serif"><?= htmlspecialchars($it['product_name']) ?></span>
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
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
