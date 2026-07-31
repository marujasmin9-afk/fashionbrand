<?php
// Automated Installer script for AURA LUXE
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'clothing_db';

$message = '';
$success = false;

if (isset($_POST['install']) || isset($_GET['auto'])) {
    try {
        // First connect without dbname to create database
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = file_get_contents(__DIR__ . '/schema.sql');
        $pdo->exec($sql);
        
        $message = "Database 'clothing_db' and 22 tables with luxury seed data created successfully!";
        $success = true;
    } catch (PDOException $e) {
        $message = "Installation Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AURA LUXE | Database Installer</title>
   <link rel="stylesheet" href="assets/css/boostrap.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #0b0b0b;
            color: #ffffff;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .luxury-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(212, 175, 55, 0.3);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            padding: 40px;
            max-width: 550px;
            width: 100%;
            box-shadow: 0 20px 50px rgba(0,0,0,0.8);
        }
        .gold-title {
            font-family: 'Playfair Display', serif;
            color: #D4AF37;
            font-size: 2.2rem;
            letter-spacing: 2px;
        }
        .btn-gold {
            background: linear-gradient(135deg, #D4AF37, #AA7C11);
            color: #000;
            font-weight: 600;
            letter-spacing: 1px;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            transition: all 0.3s ease;
        }
        .btn-gold:hover {
            background: linear-gradient(135deg, #FFF, #D4AF37);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.6);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="luxury-card text-center">
        <h1 class="gold-title mb-2">AURA LUXE</h1>
        <p class="text-secondary mb-4">Haute Couture & High Jewelry Installer</p>

        <?php if ($message): ?>
            <div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?> bg-dark border-0 text-white mb-4">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="text-start mb-4 text-secondary small">
                <p class="mb-1 text-gold"><strong>Credentials Initialized:</strong></p>
                <p class="mb-1">🔑 Admin Login: <code>admin@auraluxe.com</code> / <code>Admin@123</code></p>
                <p class="mb-1">✨ Sample Products: 8 Luxury Dresses & Jewelry Items Seeded</p>
            </div>
            <a href="index.php" class="btn btn-gold text-uppercase">Enter Storefront</a>
            <a href="admin/login.php" class="btn btn-outline-warning text-uppercase ms-2">Admin Portal</a>
        <?php else: ?>
            <p class="text-light mb-4">Click below to initialize database <strong>clothing_db</strong> with tables, relations, and seed collections.</p>
            <form method="POST">
                <button type="submit" name="install" class="btn btn-gold text-uppercase">Run One-Click Setup</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
