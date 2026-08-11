<?php
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../config/db.php';
$settings = get_settings();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' | ' : '' ?><?= htmlspecialchars($settings['site_title']) ?></title>
    
    <!-- Meta Tags & SEO -->
    <meta name="description" content="<?= htmlspecialchars($settings['site_title']) ?> - Experience High Couture Dresses, Diamond Jewelry, and Luxury Accessories.">
    <meta property="og:title" content="<?= htmlspecialchars($settings['site_name']) ?>">
    <meta property="og:description" content="Luxury Women's Fashion & Fine Jewelry eCommerce">
    <meta property="og:type" content="website">

    <!-- Favicon -->
    <link rel="icon" href="https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=100&q=80" type="image/png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
   <link rel="stylesheet" href="assets/css/boostrap.css">
   <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- AOS Animations CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom Luxury CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
