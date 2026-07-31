<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode([
        'status' => 'warning',
        'message' => 'Please login to save items to your Wishlist.',
        'redirect' => 'login.php'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product id.']);
    exit;
}

// Check existing
$stmt = $pdo->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
$existing = $stmt->fetch();

if ($existing) {
    $stmt_del = $pdo->prepare("DELETE FROM wishlist WHERE id = ?");
    $stmt_del->execute([$existing['id']]);
    $action = 'removed';
    $msg = 'Item removed from your Wishlist.';
} else {
    $stmt_in = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
    $stmt_in->execute([$user_id, $product_id]);
    $action = 'added';
    $msg = 'Item added to your Wishlist.';
}

echo json_encode([
    'status' => 'success',
    'action' => $action,
    'message' => $msg,
    'wishlist_count' => get_wishlist_count()
]);
