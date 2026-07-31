<?php
// Configuration & Database connection for AURA LUXE
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'clothing_db');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // If DB missing, try auto install
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        header("Location: install.php?auto=1");
        exit();
    }
    die("Database Connection Error: " . $e->getMessage());
}

// Global Helper Functions

/**
 * Generate CSRF Token
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Input Sanitization
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Format Price with Currency Symbol
 */
function format_price($amount) {
    global $pdo;
    static $currency = null;
    if ($currency === null) {
        $stmt = $pdo->query("SELECT currency FROM settings WHERE id = 1 LIMIT 1");
        $row = $stmt->fetch();
        $currency = $row ? $row['currency'] : '$';
    }
    return $currency . number_format((float)$amount, 2);
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if admin is logged in
 */
function is_admin() {
    return isset($_SESSION['admin_id']);
}

/**
 * Fetch Store Settings
 */
function get_settings() {
    global $pdo;
    static $settings = null;
    if ($settings === null) {
        $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1 LIMIT 1");
        $settings = $stmt->fetch() ?: [
            'site_name' => 'AURA LUXE',
            'site_title' => 'AURA LUXE | High Fashion & Fine Jewelry',
            'currency' => '$',
            'tax_rate' => 18.00,
            'shipping_fee' => 25.00,
            'free_shipping_threshold' => 500.00
        ];
    }
    return $settings;
}

/**
 * Get Cart Count for current session/user
 */
function get_cart_count() {
    global $pdo;
    if (is_logged_in()) {
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } else {
        $session_id = session_id();
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE session_id = ?");
        $stmt->execute([$session_id]);
    }
    $res = $stmt->fetch();
    return $res && $res['total'] ? (int)$res['total'] : 0;
}

/**
 * Get Wishlist Count for logged in user
 */
function get_wishlist_count() {
    global $pdo;
    if (!is_logged_in()) return 0;
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM wishlist WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $res = $stmt->fetch();
    return $res ? (int)$res['total'] : 0;
}

/**
 * Flash Message setter / getter
 */
function set_flash_message($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $msg];
}

function get_flash_message() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
