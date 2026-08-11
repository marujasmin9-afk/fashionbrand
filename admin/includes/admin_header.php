<?php
require_once __DIR__ . '/../../config/db.php';

if (!is_admin()) {
    header("Location: login.php");
    exit;
}

$page_name = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRAND FASHION | Admin Management Console</title>
    
   <link rel="stylesheet" href=https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #0b0b0b; color: #ffffff; font-family: 'Poppins', sans-serif; min-height: 100vh; }
        .admin-sidebar { background: #121214; border-right: 1px solid rgba(212, 175, 55, 0.2); min-height: 100vh; padding: 20px 0; }
        .admin-brand { font-family: 'Playfair Display', serif; color: #D4AF37; font-size: 1.5rem; letter-spacing: 2px; padding: 0 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .admin-nav-link { color: #A0A0A0; padding: 12px 20px; text-decoration: none; display: flex; align-items: center; gap: 12px; font-size: 0.9rem; transition: all 0.3s ease; }
        .admin-nav-link:hover, .admin-nav-link.active { color: #D4AF37; background: rgba(212, 175, 55, 0.08); border-left: 3px solid #D4AF37; }
        .stat-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(212,175,55,0.2); border-radius: 12px; padding: 20px; }
        .stat-value { font-family: 'Playfair Display', serif; font-size: 2rem; color: #D4AF37; }
        .btn-gold { background: linear-gradient(135deg, #D4AF37, #AA7C11); color: #000; font-weight: 600; border: none; }
        .btn-gold:hover { background: linear-gradient(135deg, #FFF, #D4AF37); color: #000; }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="admin-sidebar" style="width: 250px; flex-shrink: 0;">
        <div class="admin-brand d-flex align-items-center justify-content-between">
            <span>AURA <small class="fs-6 text-white">ADMIN</small></span>
        </div>

        <nav class="nav flex-column">
            <a href="index.php" class="admin-nav-link <?= $page_name == 'index.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="products.php" class="admin-nav-link <?= strpos($page_name, 'product') !== false ? 'active' : '' ?>"><i class="fas fa-tshirt"></i> Products</a>
            <a href="categories.php" class="admin-nav-link <?= $page_name == 'categories.php' ? 'active' : '' ?>"><i class="fas fa-list"></i> Categories</a>
            <a href="jewelry.php" class="admin-nav-link <?= $page_name == 'jewelry.php' ? 'active' : '' ?>"><i class="fas fa-gem"></i> Jewelry Collections</a>
            <a href="orders.php" class="admin-nav-link <?= strpos($page_name, 'order') !== false ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="customers.php" class="admin-nav-link <?= $page_name == 'customers.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> Customers</a>
            <a href="coupons.php" class="admin-nav-link <?= $page_name == 'coupons.php' ? 'active' : '' ?>"><i class="fas fa-ticket-alt"></i> Coupons</a>
            <a href="banners.php" class="admin-nav-link <?= $page_name == 'banners.php' ? 'active' : '' ?>"><i class="fas fa-images"></i> Sliders & Banners</a>
            <a href="blogs.php" class="admin-nav-link <?= $page_name == 'blogs.php' ? 'active' : '' ?>"><i class="fas fa-blog"></i> Blogs</a>
            <div class="border-top border-secondary mt-3 pt-3">
                <a href="../index.php" target="_blank" class="admin-nav-link"><i class="fas fa-external-link-alt"></i> View Main Site</a>
                <a href="logout.php" class="admin-nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </nav>
    </div>

    <!-- Main Content wrapper -->
    <div class="flex-grow-1 p-4" style="overflow-x: hidden;">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
            <div>
                <h4 class="font-serif text-white mb-0">Concierge Administration</h4>
                <small class="text-white">Welcome back, <?= htmlspecialchars($_SESSION['admin_name']) ?></small>
            </div>
            <a href="logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-power-off"></i> Logout</a>
        </div>
