<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$user_id = is_logged_in() ? $_SESSION['user_id'] : null;
$session_id = session_id();

if ($action === 'add') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $color = isset($_POST['color']) ? sanitize($_POST['color']) : '';
    $size = isset($_POST['size']) ? sanitize($_POST['size']) : '';

    if ($product_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product selected.']);
        exit;
    }

    // Check product existence
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
        exit;
    }

    // Check existing item in cart
    if ($user_id) {
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND color = ? AND size = ?");
        $stmt->execute([$user_id, $product_id, $color, $size]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE session_id = ? AND product_id = ? AND color = ? AND size = ?");
        $stmt->execute([$session_id, $product_id, $color, $size]);
    }

    $existing = $stmt->fetch();

    if ($existing) {
        $new_qty = $existing['quantity'] + $quantity;
        $stmt_up = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt_up->execute([$new_qty, $existing['id']]);
    } else {
        $stmt_in = $pdo->prepare("INSERT INTO cart (user_id, session_id, product_id, quantity, color, size) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_in->execute([$user_id, $session_id, $product_id, $quantity, $color, $size]);
    }

    echo json_encode([
        'status' => 'success',
        'message' => htmlspecialchars($product['title']) . ' added to your shopping bag.',
        'cart_count' => get_cart_count()
    ]);
    exit;
}

if ($action === 'update') {
    $cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if ($cart_id <= 0 || $quantity <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid quantity.']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->execute([$quantity, $cart_id]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Cart updated.',
        'cart_count' => get_cart_count()
    ]);
    exit;
}

if ($action === 'remove') {
    $cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;

    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
    $stmt->execute([$cart_id]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Item removed from bag.',
        'cart_count' => get_cart_count()
    ]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
