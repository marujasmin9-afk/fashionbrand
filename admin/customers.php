<?php
include __DIR__ . '/includes/admin_header.php';

$customers = $pdo->query("SELECT u.*, COUNT(o.id) as total_orders, SUM(o.grand_total) as total_spent 
                          FROM users u 
                          LEFT JOIN orders o ON u.id = o.user_id 
                          GROUP BY u.id 
                          ORDER BY u.id DESC")->fetchAll();
?>

<h3 class="font-serif text-white mb-4">Customer Base</h3>

<div class="card bg-dark border-secondary">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle border-secondary mb-0 small">
                <thead>
                    <tr class="text-gold font-serif">
                        <th>Collector Name</th>
                        <th>Email Address</th>
                        <th>Phone</th>
                        <th>Total Orders</th>
                        <th>Total Spent</th>
                        <th>Joined Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $c): ?>
                        <tr>
                            <td class="fw-bold text-white"><?= htmlspecialchars($c['name']) ?></td>
                            <td><?= htmlspecialchars($c['email']) ?></td>
                            <td><?= htmlspecialchars($c['phone'] ?: 'N/A') ?></td>
                            <td><span class="badge bg-secondary"><?= $c['total_orders'] ?> orders</span></td>
                            <td class="text-gold font-serif fw-bold"><?= format_price($c['total_spent'] ?: 0) ?></td>
                            <td class="text-gold"><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
