<?php
$page_title = "Forgot Password";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

$message = '';
if (isset($_POST['reset_request'])) {
    $email = sanitize($_POST['email']);
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $token = bin2hex(random_bytes(16));
        $stmt_up = $pdo->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $stmt_up->execute([$token, $email]);
        $message = "A password reset link has been dispatched to your email address.";
    } else {
        $message = "If an account exists for this email, reset instructions have been sent.";
    }
}
?>

<section class="py-5">
    <div class="container">
        <div class="glass-card p-5 mx-auto" style="max-width: 480px;" data-aos="zoom-in">
            <div class="text-center mb-4">
                <span class="sub-title">Account Recovery</span>
                <h2 class="font-serif text-white fs-1 gold-gradient-text">Reset Password</h2>
                <p class="text-muted small">Enter your registered email to receive access credentials.</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-info bg-dark text-white border-gold mb-4"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="form-label text-light small">Email Address</label>
                    <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required placeholder="vip@auraluxe.com">
                </div>
                <button type="submit" name="reset_request" class="btn btn-gold w-100 text-uppercase py-3 mb-3">Send Reset Instructions</button>
            </form>

            <div class="text-center text-muted small">
                Remember your password? <a href="login.php" class="text-gold text-decoration-none">Return to Sign In</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
