<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

$q = isset($_GET['q']) ? sanitize($_GET['q']) : '';

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, title, main_image, price, discount_price FROM products WHERE title LIKE ? OR color LIKE ? OR material LIKE ? LIMIT 6");
$searchTerm = '%' . $q . '%';
$stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
$products = $stmt->fetchAll();

$results = [];
foreach ($products as $p) {
    $finalPrice = ($p['discount_price'] && $p['discount_price'] < $p['price']) ? $p['discount_price'] : $p['price'];
    $results[] = [
        'id' => $p['id'],
        'title' => $p['title'],
        'main_image' => $p['main_image'],
        'price_formatted' => format_price($finalPrice)
    ];
}

echo json_encode($results);
