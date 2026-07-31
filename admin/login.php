<?php
require_once __DIR__ . '/../config/db.php';

if (is_admin()) {
    header("Location: index.php");
    exit;
}

$error = '';
if (isset($_POST['admin_login'])) {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && $password === $admin['password']) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_email'] = $admin['email'];
        header("Location: index.php");
        exit;
    } else {
        $error = 'Invalid admin credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AURA LUXE | Admin Portal Sign In</title>
   <link rel="stylesheet" href="assets/css/boostrap.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body { background: #0b0b0b; color: #fff; font-family: 'Poppins', sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .admin-card { background: rgba(22, 22, 24, 0.9); border: 1px solid rgba(212, 175, 55, 0.4); backdrop-filter: blur(15px); border-radius: 16px; padding: 40px; width: 100%; max-width: 440px; }
        .btn-gold { background: linear-gradient(135deg, #D4AF37, #AA7C11); color: #000; font-weight: 600; border: none; }
        .btn-gold:hover { background: linear-gradient(135deg, #FFF, #D4AF37); }
    </style>
</head>
<body>
    <div class="admin-card text-center">
        <h2 class="font-serif text-warning mb-1">AURA LUXE</h2>
        <p class="text-muted small mb-4">Maison Administration Console</p>

        <?php if ($error): ?>
            <div class="alert alert-danger bg-dark text-white border-danger small mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="text-start">
            <div class="mb-3">
                <label class="form-label small text-light">Admin Email</label>
                <input type="email" name="email" class="form-control bg-dark text-white border-secondary small" value="admin@auraluxe.com" required>
            </div>
            <div class="mb-4">
                <label class="form-label small text-light">Password</label>
                <input type="password" name="password" class="form-control bg-dark text-white border-secondary small" value="1111" required>
            </div>
            <button type="submit" name="admin_login" class="btn btn-gold w-100 py-2 text-uppercase small">Access Console</button>
        </form>
    </div>
</body>
</html>
