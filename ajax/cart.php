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

    // Stock check
    if ($product['stock'] <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Sorry, "' . htmlspecialchars($product['title']) . '" is currently Out of Stock.']);
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
    $requested_total_qty = $existing ? ($existing['quantity'] + $quantity) : $quantity;

    // Check requested total quantity against stock
    if ($requested_total_qty > $product['stock']) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Only ' . $product['stock'] . ' unit(s) of "' . htmlspecialchars($product['title']) . '" available. You already have ' . ($existing ? $existing['quantity'] : 0) . ' in your bag.'
        ]);
        exit;
    }

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

    // Get product stock via cart item
    $stmt_stock = $pdo->prepare("SELECT c.id, p.title, p.stock 
                                  FROM cart c 
                                  JOIN products p ON c.product_id = p.id 
                                  WHERE c.id = ?");
    $stmt_stock->execute([$cart_id]);
    $cart_product = $stmt_stock->fetch();

    if (!$cart_product) {
        echo json_encode(['status' => 'error', 'message' => 'Item not found in bag.']);
        exit;
    }

    if ($cart_product['stock'] <= 0) {
        echo json_encode(['status' => 'error', 'message' => '"' . htmlspecialchars($cart_product['title']) . '" is now Out of Stock.', 'cart_count' => get_cart_count()]);
        exit;
    }

    // Cap quantity to available stock
    $final_qty = $quantity;
    $adjusted = false;
    if ($quantity > $cart_product['stock']) {
        $final_qty = $cart_product['stock'];
        $adjusted = true;
    }

    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->execute([$final_qty, $cart_id]);

    echo json_encode([
        'status' => $adjusted ? 'warning' : 'success',
        'message' => $adjusted 
            ? 'Only ' . $cart_product['stock'] . ' unit(s) of "' . htmlspecialchars($cart_product['title']) . '" available. Quantity adjusted.' 
            : 'Cart updated.',
        'final_qty' => $final_qty,
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
