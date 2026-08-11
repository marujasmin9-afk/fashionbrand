<?php
$page_title = "Change Password";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!password_verify($current_password, $user['password'])) {
        $error = 'Current password is incorrect.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'New password and confirmation do not match.';
    } elseif (strlen($new_password) < 6) {
        $error = 'New password must be at least 6 characters long.';
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt_up = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt_up->execute([$hashed, $user_id]);
        $success = 'Password changed successfully.';
    }
}
?>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                <div class="glass-card p-4">
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

            <div class="col-lg-9">
                <div class="glass-card p-4">
                    <h4 class="font-serif text-gold mb-4 pb-2 border-bottom border-secondary">Change Account Password</h4>

                    <?php if ($error): ?>
                        <div class="alert alert-danger bg-dark text-white border-danger mb-4"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success bg-dark text-white border-success mb-4"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label text-light small">Current Password</label>
                            <input type="password" name="current_password" class="form-control bg-dark text-white border-secondary small" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-light small">New Password</label>
                            <input type="password" name="new_password" class="form-control bg-dark text-white border-secondary small" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-light small">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control bg-dark text-white border-secondary small" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-gold text-uppercase small">Update Security Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
