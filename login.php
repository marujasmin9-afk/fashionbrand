<?php
$page_title = "Sign In";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

if (is_logged_in()) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
if (isset($_POST['login'])) {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        // Assign current cart items to logged in user
        $session_id = session_id();
        $stmt_cart = $pdo->prepare("UPDATE cart SET user_id = ? WHERE session_id = ?");
        $stmt_cart->execute([$user['id'], $session_id]);

        header("Location: dashboard.php");
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>

<section class="py-5">
    <div class="container">
        <div class="glass-card p-5 mx-auto" style="max-width: 480px;" data-aos="zoom-in">
            <div class="text-center mb-4">
                <span class="sub-title">VIP Access</span>
                <h2 class="font-serif text-white fs-1 gold-gradient-text">Member Sign In</h2>
                <p class="text-muted small">Sign in to manage your collection and exclusive offers.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger bg-dark text-white border-danger mb-4"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label text-light small">Email Address</label>
                    <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required placeholder="vip@auraluxe.com">
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label class="form-label text-light small">Password</label>
                        <a href="forgot-password.php" class="text-gold extra-small text-decoration-none">Forgot Password?</a>
                    </div>
                    <input type="password" name="password" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <button type="submit" name="login" class="btn btn-gold w-100 text-uppercase py-3 mb-3">Sign In To Maison</button>
            </form>

            <div class="text-center text-muted small">
                Don't have a private account? <a href="register.php" class="text-gold text-decoration-none">Create Account</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
