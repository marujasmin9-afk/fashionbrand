<?php
$page_title = "My Saved Wishlist";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT w.id as wishlist_id, p.*, c.name as category_name 
                       FROM wishlist w 
                       JOIN products p ON w.product_id = p.id 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       WHERE w.user_id = ? ORDER BY w.id DESC");
$stmt->execute([$user_id]);
$wishlist_items = $stmt->fetchAll();
?>

<section class="py-5">
    <div class="container">
        <h1 class="font-serif text-white display-5 mb-4" data-aos="fade-up">My Saved Wishlist</h1>

        <?php if (empty($wishlist_items)): ?>
            <div class="glass-card p-5 text-center" data-aos="fade-up">
                <i class="far fa-heart text-gold display-1 mb-3 opacity-50"></i>
                <h3 class="font-serif text-white mb-2">Your Wishlist is Empty</h3>
                <p class="text-secondary small mb-4">Explore our haute couture dresses and solitaire diamond jewels.</p>
                <a href="shop.php" class="btn btn-gold text-uppercase">Explore Collection</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($wishlist_items as $product): 
                    $effective_price = ($product['discount_price'] && $product['discount_price'] < $product['price']) ? $product['discount_price'] : $product['price'];
                ?>
                    <div class="col-lg-3 col-md-6 col-6" data-aos="fade-up">
                        <div class="product-card">
                            <div class="product-thumb">
                                <img src="<?= htmlspecialchars($product['main_image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                                <div class="product-actions">
                                    <button class="action-btn ajax-add-cart" data-id="<?= $product['id'] ?>" title="Add to Bag">
                                        <i class="fas fa-shopping-bag"></i>
                                    </button>
                                    <button class="action-btn ajax-wishlist active" data-id="<?= $product['id'] ?>" title="Remove">
                                        <i class="fas fa-heart text-danger"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="product-details-content">
                                <span class="extra-small text-gold uppercase tracking-wider d-block mb-1"><?= htmlspecialchars($product['category_name']) ?></span>
                                <h3 class="product-title">
                                    <a href="product-details.php?id=<?= $product['id'] ?>"><?= htmlspecialchars($product['title']) ?></a>
                                </h3>
                                <div class="product-price"><?= format_price($effective_price) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
