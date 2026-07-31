<?php
$page_title = "Create VIP Account";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';

if (is_logged_in()) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
if (isset($_POST['register'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill out all mandatory fields.';
    } else {
        // Check existing email
        $stmt_c = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_c->execute([$email]);
        if ($stmt_c->fetch()) {
            $error = 'An account with this email already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt_in = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt_in->execute([$name, $email, $phone, $hashed]);
            $new_id = $pdo->lastInsertId();

            $_SESSION['user_id'] = $new_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;

            header("Location: dashboard.php");
            exit;
        }
    }
}
?>

<section class="py-5">
    <div class="container">
        <div class="glass-card p-5 mx-auto" style="max-width: 520px;" data-aos="zoom-in">
            <div class="text-center mb-4">
                <span class="sub-title">VIP Membership</span>
                <h2 class="font-serif text-white fs-1 gold-gradient-text">Register Account</h2>
                <p class="text-muted small">Join AURA LUXE for personalized concierge services and private previews.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger bg-dark text-white border-danger mb-4"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label text-light small">Full Name *</label>
                    <input type="text" name="name" class="form-control bg-dark text-white border-secondary" required placeholder="Lady Eleanor Vance">
                </div>
                <div class="mb-3">
                    <label class="form-label text-light small">Email Address *</label>
                    <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required placeholder="eleanor@vance.com">
                </div>
                <div class="mb-3">
                    <label class="form-label text-light small">Phone / Mobile</label>
                    <input type="tel" name="phone" class="form-control bg-dark text-white border-secondary" placeholder="+1 555-0192">
                </div>
                <div class="mb-4">
                    <label class="form-label text-light small">Password *</label>
                    <input type="password" name="password" class="form-control bg-dark text-white border-secondary" required minlength="6">
                </div>
                <button type="submit" name="register" class="btn btn-gold w-100 text-uppercase py-3 mb-3">Create Membership</button>
            </form>

            <div class="text-center text-muted small">
                Already registered? <a href="login.php" class="text-gold text-decoration-none">Sign In</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
