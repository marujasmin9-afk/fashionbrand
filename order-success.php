<?php
$page_title = "Order Confirmed";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

$order_number = isset($_GET['order']) ? sanitize($_GET['order']) : '';

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ?");
$stmt->execute([$order_number]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit;
}

$stmt_items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt_items->execute([$order['id']]);
$items = $stmt_items->fetchAll();

$shipping = json_decode($order['shipping_address'], true);
?>

<section class="py-5">
    <div class="container text-center">
        <div class="glass-card p-5 mx-auto" style="max-width: 800px;" data-aos="zoom-in">
            <div class="mb-4 text-gold">
                <i class="fas fa-check-circle display-1"></i>
            </div>

            <span class="sub-title">Order Confirmed</span>
            <h1 class="font-serif text-white display-5 mb-3">Thank You For Your Order</h1>
            <p class="text-secondary small mb-4">Your order <strong><?= htmlspecialchars($order['order_number']) ?></strong> has been received by our Maison concierge team.</p>

            <div class="p-4 bg-dark border border-secondary rounded-3 text-start mb-4">
                <h5 class="font-serif text-gold mb-3">Order Details</h5>
                <div class="row g-3 small text-light">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Order Number:</strong> <?= htmlspecialchars($order['order_number']) ?></p>
                        <p class="mb-1"><strong>Date:</strong> <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></p>
                        <p class="mb-1"><strong>Payment Method:</strong> <?= strtoupper($order['payment_method']) ?></p>
                        <p class="mb-0"><strong>Status:</strong> <span class="badge bg-success"><?= strtoupper($order['order_status']) ?></span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Recipient:</strong> <?= htmlspecialchars($shipping['full_name']) ?></p>
                        <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($shipping['phone']) ?></p>
                        <p class="mb-0"><strong>Address:</strong> <?= htmlspecialchars($shipping['address_line1']) ?>, <?= htmlspecialchars($shipping['city']) ?>, <?= htmlspecialchars($shipping['country']) ?></p>
                    </div>
                </div>

                <hr class="border-secondary my-3">

                <h6 class="font-serif text-gold mb-2">Purchased Items</h6>
                <div class="table-responsive">
                    <table class="table table-dark table-sm border-secondary mb-0">
                        <thead>
                            <tr class="text-muted extra-small">
                                <th>Item</th>
                                <th>Qty</th>
                                <th class="text-end">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $it): ?>
                                <tr>
                                    <td><?= htmlspecialchars($it['product_name']) ?></td>
                                    <td><?= $it['quantity'] ?></td>
                                    <td class="text-end text-gold"><?= format_price($it['total']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between text-white font-serif fs-5 mt-3 pt-2 border-top border-secondary">
                    <span>Grand Total Paid</span>
                    <span class="text-gold fw-bold"><?= format_price($order['grand_total']) ?></span>
                </div>
            </div>

            <div class="d-flex justify-content-center gap-3">
                <button onclick="window.print()" class="btn btn-outline-gold text-uppercase"><i class="fas fa-print me-2"></i> Print Invoice</button>
                <a href="shop.php" class="btn btn-gold text-uppercase">Continue Shopping</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
