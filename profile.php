<?php
$page_title = "Personal Profile";
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

$message = '';
if (isset($_POST['update_profile'])) {
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);

    $stmt_up = $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
    $stmt_up->execute([$name, $phone, $user_id]);
    $_SESSION['user_name'] = $name;
    set_flash_message('success', 'Profile updated successfully.');
    header("Location: profile.php");
    exit;
}

$flash = get_flash_message();
?>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                <div class="glass-card p-4">
                    <ul class="nav flex-column gap-2 small">
                        <li class="nav-item"><a href="dashboard.php" class="nav-link text-light px-0"><i class="fas fa-chart-line text-gold me-2"></i> Dashboard Overview</a></li>
                        <li class="nav-item"><a href="my-orders.php" class="nav-link text-light px-0"><i class="fas fa-box text-gold me-2"></i> Order History</a></li>
                        <li class="nav-item"><a href="wishlist.php" class="nav-link text-light px-0"><i class="fas fa-heart text-gold me-2"></i> Saved Wishlist</a></li>
                        <li class="nav-item"><a href="profile.php" class="nav-link active text-gold px-0"><i class="fas fa-user-edit me-2"></i> Personal Profile</a></li>
                        <li class="nav-item"><a href="change-password.php" class="nav-link text-light px-0"><i class="fas fa-lock text-gold me-2"></i> Security Credentials</a></li>
                        <li class="nav-item border-top border-secondary pt-2"><a href="logout.php" class="nav-link text-danger px-0"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="glass-card p-4">
                    <h4 class="font-serif text-gold mb-4 pb-2 border-bottom border-secondary">Edit Personal Profile</h4>

                    <?php if ($flash): ?>
                        <div class="alert alert-success bg-dark text-white border-success mb-4"><?= htmlspecialchars($flash['message']) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label text-light small">Full Name</label>
                            <input type="text" name="name" class="form-control bg-dark text-white border-secondary small" value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-light small">Email Address (Read-only)</label>
                            <input type="email" class="form-control bg-dark text-muted border-secondary small" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-light small">Phone / Mobile</label>
                            <input type="text" name="phone" class="form-control bg-dark text-white border-secondary small" value="<?= htmlspecialchars($user['phone']) ?>">
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-gold text-uppercase small">Save Profile Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
