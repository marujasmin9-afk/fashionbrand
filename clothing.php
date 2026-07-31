<?php
$page_title = "Haute Couture Clothing Collection";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

$sql = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE c.type = 'clothing' ORDER BY p.id DESC";
$clothing_list = $pdo->query($sql)->fetchAll();
?>

<section class="py-5 bg-dark border-bottom border-secondary text-center">
    <div class="container" data-aos="fade-up">
        <span class="sub-title">Haute Couture & Heritage Wear</span>
        <h1 class="font-serif text-white gold-gradient-text display-4">Clothing Collection</h1>
        <p class="text-muted small">French velvet evening gowns, Banarasi Katan silk sarees, and bespoke runway dresses.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($clothing_list as $product): 
                $effective_price = ($product['discount_price'] && $product['discount_price'] < $product['price']) ? $product['discount_price'] : $product['price'];
            ?>
                <div class="col-lg-3 col-md-6 col-6" data-aos="fade-up">
                    <div class="product-card">
                        <div class="product-thumb">
                            <img src="<?= htmlspecialchars($product['main_image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                            <div class="product-actions">
                                <button class="action-btn ajax-add-cart" data-id="<?= $product['id'] ?>"><i class="fas fa-shopping-bag"></i></button>
                                <button class="action-btn ajax-wishlist" data-id="<?= $product['id'] ?>"><i class="far fa-heart"></i></button>
                                <button class="action-btn ajax-quick-view" data-id="<?= $product['id'] ?>"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="product-details-content">
                            <span class="extra-small text-gold uppercase tracking-wider d-block mb-1"><?= htmlspecialchars($product['category_name']) ?></span>
                            <h3 class="product-title"><a href="product-details.php?id=<?= $product['id'] ?>"><?= htmlspecialchars($product['title']) ?></a></h3>
                            <div class="product-price"><?= format_price($effective_price) ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
