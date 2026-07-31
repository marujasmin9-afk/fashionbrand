<?php
include __DIR__ . '/includes/admin_header.php';

// Update Status POST
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = sanitize($_POST['order_status']);
    $stmt_up = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt_up->execute([$status, $order_id]);
    header("Location: orders.php");
    exit;
}

$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$where = "1=1";
$params = [];
if ($status_filter) {
    $where = "o.order_status = ?";
    $params[] = $status_filter;
}

$sql = "SELECT o.*, u.name as customer_name, u.email as customer_email 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE $where 
        ORDER BY o.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-serif text-white mb-0">Order Management</h3>
    
    <!-- Status Filter Buttons -->
    <div class="btn-group btn-group-sm">
        <a href="orders.php" class="btn <?= empty($status_filter) ? 'btn-gold' : 'btn-outline-gold' ?>">All</a>
        <a href="orders.php?status=pending" class="btn <?= $status_filter == 'pending' ? 'btn-gold' : 'btn-outline-gold' ?>">Pending</a>
        <a href="orders.php?status=confirmed" class="btn <?= $status_filter == 'confirmed' ? 'btn-gold' : 'btn-outline-gold' ?>">Confirmed</a>
        <a href="orders.php?status=packed" class="btn <?= $status_filter == 'packed' ? 'btn-gold' : 'btn-outline-gold' ?>">Packed</a>
        <a href="orders.php?status=shipped" class="btn <?= $status_filter == 'shipped' ? 'btn-gold' : 'btn-outline-gold' ?>">Shipped</a>
        <a href="orders.php?status=delivered" class="btn <?= $status_filter == 'delivered' ? 'btn-gold' : 'btn-outline-gold' ?>">Delivered</a>
    </div>
</div>

<div class="card bg-dark border-secondary">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle border-secondary mb-0 small">
                <thead>
                    <tr class="text-gold font-serif">
                        <th>Order #</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $ord): ?>
                        <tr>
                            <td class="fw-bold text-white"><?= htmlspecialchars($ord['order_number']) ?></td>
                            <td>
                                <div><?= htmlspecialchars($ord['customer_name'] ?: 'Guest') ?></div>
                                <small class="text-muted"><?= htmlspecialchars($ord['customer_email']) ?></small>
                            </td>
                            <td class="text-muted"><?= date('d M Y, h:i A', strtotime($ord['created_at'])) ?></td>
                            <td class="text-gold font-serif fw-bold"><?= format_price($ord['grand_total']) ?></td>
                            <td><span class="badge bg-secondary"><?= strtoupper($ord['payment_method']) ?></span></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                    <select name="order_status" class="form-select form-select-sm bg-dark text-white border-secondary" onchange="this.form.submit()">
                                        <option value="pending" <?= $ord['order_status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="confirmed" <?= $ord['order_status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                        <option value="packed" <?= $ord['order_status'] == 'packed' ? 'selected' : '' ?>>Packed</option>
                                        <option value="shipped" <?= $ord['order_status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                        <option value="delivered" <?= $ord['order_status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                        <option value="cancelled" <?= $ord['order_status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                            <td>
                                <a href="order-details.php?id=<?= $ord['id'] ?>" class="btn btn-sm btn-outline-light"><i class="fas fa-eye"></i> Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
