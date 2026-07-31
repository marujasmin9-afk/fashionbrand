<?php
include __DIR__ . '/includes/admin_header.php';

// Calculate Dashboard Stats
$todays_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$monthly_sales = $pdo->query("SELECT SUM(grand_total) FROM orders WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) AND order_status != 'cancelled'")->fetchColumn() ?: 0;
$total_revenue = $pdo->query("SELECT SUM(grand_total) FROM orders WHERE order_status != 'cancelled'")->fetchColumn() ?: 0;
$total_customers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

$pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'pending'")->fetchColumn();
$delivered_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'delivered'")->fetchColumn();
$cancelled_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'cancelled'")->fetchColumn();
$low_stock = $pdo->query("SELECT COUNT(*) FROM products WHERE stock < 5")->fetchColumn();

// Fetch Recent Orders
$recent_orders = $pdo->query("SELECT o.*, u.name as customer_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.id DESC LIMIT 5")->fetchAll();
?>

<!-- Stat Cards Grid -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <span class="text-muted small text-uppercase">Today's Orders</span>
            <div class="stat-value"><?= $todays_orders ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <span class="text-muted small text-uppercase">Monthly Revenue</span>
            <div class="stat-value"><?= format_price($monthly_sales) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <span class="text-muted small text-uppercase">Total Revenue</span>
            <div class="stat-value"><?= format_price($total_revenue) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <span class="text-muted small text-uppercase">Registered Collectors</span>
            <div class="stat-value"><?= $total_customers ?></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card border-warning">
            <span class="text-warning small text-uppercase">Pending Orders</span>
            <div class="stat-value text-warning"><?= $pending_orders ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-success">
            <span class="text-success small text-uppercase">Delivered</span>
            <div class="stat-value text-success"><?= $delivered_orders ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-danger">
            <span class="text-danger small text-uppercase">Cancelled</span>
            <div class="stat-value text-danger"><?= $cancelled_orders ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-danger">
            <span class="text-danger small text-uppercase">Low Stock (< 5)</span>
            <div class="stat-value text-danger"><?= $low_stock ?></div>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="card bg-dark border-secondary">
    <div class="card-header bg-dark border-secondary d-flex justify-content-between align-items-center">
        <h5 class="font-serif text-gold mb-0">Latest Orders</h5>
        <a href="orders.php" class="btn btn-outline-gold btn-sm">View All Orders</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover border-secondary align-middle mb-0 small">
                <thead>
                    <tr class="text-gold font-serif">
                        <th>Order #</th>
                        <th>Client</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $ord): ?>
                        <tr>
                            <td class="fw-bold text-white"><?= htmlspecialchars($ord['order_number']) ?></td>
                            <td><?= htmlspecialchars($ord['customer_name'] ?: 'Guest Collector') ?></td>
                            <td class="text-gold font-serif"><?= format_price($ord['grand_total']) ?></td>
                            <td><span class="badge bg-secondary"><?= strtoupper($ord['payment_method']) ?></span></td>
                            <td><span class="badge bg-gold text-dark"><?= strtoupper($ord['order_status']) ?></span></td>
                            <td>
                                <a href="order-details.php?id=<?= $ord['id'] ?>" class="btn btn-sm btn-outline-light">Manage</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
