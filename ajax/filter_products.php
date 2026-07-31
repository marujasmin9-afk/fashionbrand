<?php
require_once __DIR__ . '/../config/db.php';

$where = ["1=1"];
$params = [];

// Category filter
if (!empty($_GET['category'])) {
    $where[] = "(c.slug = ? OR c.id = ?)";
    $params[] = $_GET['category'];
    $params[] = $_GET['category'];
}

// Brand filter
if (!empty($_GET['brand_id'])) {
    $where[] = "p.brand_id = ?";
    $params[] = (int)$_GET['brand_id'];
}

// Price filter
if (!empty($_GET['min_price'])) {
    $where[] = "COALESCE(p.discount_price, p.price) >= ?";
    $params[] = (float)$_GET['min_price'];
}
if (!empty($_GET['max_price'])) {
    $where[] = "COALESCE(p.discount_price, p.price) <= ?";
    $params[] = (float)$_GET['max_price'];
}

// Color filter
if (!empty($_GET['color'])) {
    $where[] = "p.color LIKE ?";
    $params[] = '%' . $_GET['color'] . '%';
}

// Size filter
if (!empty($_GET['size'])) {
    $where[] = "p.size LIKE ?";
    $params[] = '%' . $_GET['size'] . '%';
}

// Material filter
if (!empty($_GET['material'])) {
    $where[] = "p.material LIKE ?";
    $params[] = '%' . $_GET['material'] . '%';
}

// Rating filter
if (!empty($_GET['rating'])) {
    $where[] = "p.rating >= ?";
    $params[] = (float)$_GET['rating'];
}

// Availability filter
if (!empty($_GET['in_stock']) && $_GET['in_stock'] == 1) {
    $where[] = "p.stock > 0";
}

// Sort By
$orderBy = "p.id DESC";
if (!empty($_GET['sort'])) {
    switch($_GET['sort']) {
        case 'price_low':
            $orderBy = "COALESCE(p.discount_price, p.price) ASC";
            break;
        case 'price_high':
            $orderBy = "COALESCE(p.discount_price, p.price) DESC";
            break;
        case 'popularity':
            $orderBy = "p.is_best_seller DESC, p.rating DESC";
            break;
        case 'rating':
            $orderBy = "p.rating DESC";
            break;
        case 'newest':
        default:
            $orderBy = "p.id DESC";
            break;
    }
}

$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE " . implode(" AND ", $where) . " 
        ORDER BY " . $orderBy;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

if (empty($products)):
?>
    <div class="col-12 text-center py-5">
        <i class="fas fa-gem fs-1 text-gold mb-3 opacity-50"></i>
        <h4 class="font-serif text-white mb-2">No Luxury Treasures Match Your Filter</h4>
        <p class="text-secondary small">Try adjusting your filters or search criteria.</p>
    </div>
<?php else: 
    foreach ($products as $product):
        $effective_price = ($product['discount_price'] && $product['discount_price'] < $product['price']) ? $product['discount_price'] : $product['price'];
?>
    <div class="col-lg-4 col-md-6 col-6 mb-4" data-aos="fade-up">
        <div class="product-card">
            <?php if ($product['is_new']): ?>
                <span class="badge-tag">NEW IN</span>
            <?php elseif ($product['is_flash_sale']): ?>
                <span class="badge-tag text-warning border-warning">FLASH SALE</span>
            <?php endif; ?>

            <div class="product-thumb">
                <img src="<?= htmlspecialchars($product['main_image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" loading="lazy">
                <div class="product-actions">
                    <button class="action-btn ajax-add-cart" data-id="<?= $product['id'] ?>" title="Add to Bag">
                        <i class="fas fa-shopping-bag"></i>
                    </button>
                    <button class="action-btn ajax-wishlist" data-id="<?= $product['id'] ?>" title="Wishlist">
                        <i class="far fa-heart"></i>
                    </button>
                    <button class="action-btn ajax-quick-view" data-id="<?= $product['id'] ?>" title="Quick View">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="product-details-content">
                <span class="extra-small text-gold uppercase tracking-wider d-block mb-1"><?= htmlspecialchars($product['category_name']) ?></span>
                <h3 class="product-title">
                    <a href="product-details.php?id=<?= $product['id'] ?>"><?= htmlspecialchars($product['title']) ?></a>
                </h3>
                <div class="product-price">
                    <?= format_price($effective_price) ?>
                    <?php if ($product['discount_price'] && $product['discount_price'] < $product['price']): ?>
                        <span class="old-price"><?= format_price($product['price']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php 
    endforeach;
endif; 
?>
