<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo '<div class="alert alert-warning text-white bg-dark">Product not found.</div>';
    exit;
}

$effective_price = ($product['discount_price'] && $product['discount_price'] < $product['price']) ? $product['discount_price'] : $product['price'];
?>
<div class="row g-4">
    <div class="col-md-6">
        <div class="rounded-3 overflow-hidden border border-secondary" style="height: 380px;">
            <img src="<?= htmlspecialchars($product['main_image']) ?>" class="w-100 h-100 object-fit-cover" alt="<?= htmlspecialchars($product['title']) ?>">
        </div>
    </div>
    <div class="col-md-6 text-start">
        <span class="badge bg-outline-gold border border-warning text-gold text-uppercase small mb-2"><?= htmlspecialchars($product['category_name']) ?></span>
        <h3 class="font-serif text-white mb-2"><?= htmlspecialchars($product['title']) ?></h3>
        
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="fs-4 fw-bold text-gold"><?= format_price($effective_price) ?></span>
            <?php if ($product['discount_price'] && $product['discount_price'] < $product['price']): ?>
                <span class="text-muted text-decoration-line-through small"><?= format_price($product['price']) ?></span>
            <?php endif; ?>
        </div>

        <p class="text-secondary small mb-4"><?= htmlspecialchars($product['short_description']) ?></p>

        <?php if ($product['color']): ?>
            <div class="mb-3">
                <label class="form-label text-gold small uppercase fw-bold">Color:</label>
                <div class="text-white small"><?= htmlspecialchars($product['color']) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($product['size']): ?>
            <div class="mb-4">
                <label class="form-label text-gold small uppercase fw-bold">Available Sizes:</label>
                <div class="d-flex gap-2">
                    <?php foreach(explode(',', $product['size']) as $sz): ?>
                        <span class="badge bg-dark border border-secondary px-3 py-2 text-white"><?= trim($sz) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="d-flex gap-2">
            <button class="btn btn-gold flex-grow-1 ajax-add-cart" data-id="<?= $product['id'] ?>">
                <i class="fas fa-shopping-bag me-2"></i> Add To Bag
            </button>
            <a href="product-details.php?id=<?= $product['id'] ?>" class="btn btn-outline-light">
                Full Details
            </a>
        </div>
    </div>
</div>
