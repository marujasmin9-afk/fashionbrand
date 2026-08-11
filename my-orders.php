<?php
$page_title = "My Orders";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3">
                <div class="glass-card p-4">
                    <h5 class="font-serif text-gold mb-3">Client Portal</h5>
                   <ul class="nav flex-column gap-2 small">
                        <li class="nav-item"><a href="dashboard.php" class="nav-link active text-gold px-0" style="color: #ffffff !important;"><i class="fas fa-chart-line me-2"></i> Dashboard Overview</a></li>
                        <li class="nav-item"><a href="my-orders.php" class="nav-link text-light px-0" style="color: #ffffff !important;"><i class="fas fa-box text-gold me-2"></i> Order History</a></li>
                        <li class="nav-item"><a href="wishlist.php" class="nav-link text-light px-0" style="color: #ffffff !important;"><i class="fas fa-heart text-gold me-2"></i> Saved Wishlist</a></li>
                        <li class="nav-item"><a href="profile.php" class="nav-link text-light px-0" style="color: #ffffff !important;"><i class="fas fa-user-edit text-gold me-2"></i> Personal Profile</a></li>
                       <li class="nav-item"><a href="change-password.php" class="nav-link text-light px-0" style="color: #ffffff !important;"><i class="fas fa-lock text-gold me-2"></i> Security Credentials</a></li>
                       <li class="nav-item border-top border-secondary pt-2"><a href="logout.php" class="nav-link text-danger px-0" style="color: #ffffff !important;"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="col-lg-9">
                <div class="glass-card p-4">
                    <h3 class="font-serif text-white fs-3 mb-4">My Order History</h3>

                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-box-open text-gold display-1 mb-3 opacity-50"></i>
                            <h4 class="font-serif text-white">No Orders Placed Yet</h4>
                            <p class="text-muted small">Your purchases will be listed here.</p>
                            <a href="shop.php" class="btn btn-gold btn-sm text-uppercase">Shop Collections</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle border-secondary mb-0 small">
                                <thead>
                                    <tr class="text-gold font-serif">
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $ord): ?>
                                        <tr>
                                            <td class="fw-bold text-white"><?= htmlspecialchars($ord['order_number']) ?></td>
                                            <td class="text-muted"><?= date('d M Y', strtotime($ord['created_at'])) ?></td>
                                            <td class="text-gold font-serif"><?= format_price($ord['grand_total']) ?></td>
                                            <td><span class="badge bg-dark border border-secondary"><?= strtoupper($ord['payment_method']) ?></span></td>
                                            <td>
                                                <?php
                                                    $bg = 'bg-warning text-dark';
                                                    if ($ord['order_status'] === 'delivered') $bg = 'bg-success';
                                                    if ($ord['order_status'] === 'shipped') $bg = 'bg-info text-dark';
                                                    if ($ord['order_status'] === 'cancelled') $bg = 'bg-danger';
                                                ?>
                                                <span class="badge <?= $bg ?>"><?= strtoupper($ord['order_status']) ?></span>
                                            </td>
                                            <td>
                                                <a href="order-details.php?id=<?= $ord['id'] ?>" class="btn btn-sm btn-outline-gold">
                                                    Details & Invoice
                                                </a>
                                            </td>
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
