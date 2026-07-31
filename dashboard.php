<?php
$page_title = "User Dashboard";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch Latest 3 Orders
$stmt_ord = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT 3");
$stmt_ord->execute([$user_id]);
$recent_orders = $stmt_ord->fetchAll();

$wishlist_cnt = get_wishlist_count();
$cart_cnt = get_cart_count();
?>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3">
                <div class="glass-card p-4">
                    <div class="text-center mb-4 pb-3 border-bottom border-secondary">
                        <div class="rounded-circle bg-gold text-dark font-serif fs-2 d-inline-flex align-items-center justify-content-center mb-2" style="width: 70px; height: 70px;">
                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                        </div>
                        <h6 class="font-serif text-white mb-0"><?= htmlspecialchars($user['name']) ?></h6>
                        <span class="extra-small text-gold">VIP Member</span>
                    </div>

                    <ul class="nav flex-column gap-2 small">
                        <li class="nav-item"><a href="dashboard.php" class="nav-link active text-gold px-0"><i class="fas fa-chart-line me-2"></i> Dashboard Overview</a></li>
                        <li class="nav-item"><a href="my-orders.php" class="nav-link text-light px-0"><i class="fas fa-box text-gold me-2"></i> Order History</a></li>
                        <li class="nav-item"><a href="wishlist.php" class="nav-link text-light px-0"><i class="fas fa-heart text-gold me-2"></i> Saved Wishlist (<?= $wishlist_cnt ?>)</a></li>
                        <li class="nav-item"><a href="profile.php" class="nav-link text-light px-0"><i class="fas fa-user-edit text-gold me-2"></i> Personal Profile</a></li>
                        <li class="nav-item"><a href="change-password.php" class="nav-link text-light px-0"><i class="fas fa-lock text-gold me-2"></i> Security Credentials</a></li>
                        <li class="nav-item border-top border-secondary pt-2"><a href="logout.php" class="nav-link text-danger px-0"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-lg-9">
                <!-- Stats Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="glass-card p-4 text-center">
                            <i class="fas fa-box text-gold fs-2 mb-2"></i>
                            <h3 class="font-serif text-white mb-1"><?= count($recent_orders) ?></h3>
                            <span class="extra-small text-muted text-uppercase">Total Orders</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="glass-card p-4 text-center">
                            <i class="fas fa-heart text-gold fs-2 mb-2"></i>
                            <h3 class="font-serif text-white mb-1"><?= $wishlist_cnt ?></h3>
                            <span class="extra-small text-muted text-uppercase">Wishlist Items</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="glass-card p-4 text-center">
                            <i class="fas fa-shopping-bag text-gold fs-2 mb-2"></i>
                            <h3 class="font-serif text-white mb-1"><?= $cart_cnt ?></h3>
                            <span class="extra-small text-muted text-uppercase">Items in Bag</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders Preview -->
                <div class="glass-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-serif text-white mb-0">Recent Orders</h5>
                        <a href="my-orders.php" class="text-gold small text-decoration-none">View All &rarr;</a>
                    </div>

                    <?php if (empty($recent_orders)): ?>
                        <p class="text-muted small">No orders recorded yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark align-middle border-secondary mb-0 small">
                                <thead>
                                    <tr class="text-gold font-serif">
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $ro): ?>
                                        <tr>
                                            <td class="text-white"><?= htmlspecialchars($ro['order_number']) ?></td>
                                            <td class="text-muted"><?= date('d M Y', strtotime($ro['created_at'])) ?></td>
                                            <td class="text-gold font-serif"><?= format_price($ro['grand_total']) ?></td>
                                            <td><span class="badge bg-gold text-dark"><?= strtoupper($ro['order_status']) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
