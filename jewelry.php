<?php
$page_title = "Fine Jewelry Collection";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

$material_filter = isset($_GET['material']) ? sanitize($_GET['material']) : '';
$type_filter = isset($_GET['type']) ? sanitize($_GET['type']) : '';

$where = ["c.type = 'jewelry'"];
$params = [];

if ($material_filter) {
    $where[] = "p.material LIKE ?";
    $params[] = '%' . $material_filter . '%';
}
if ($type_filter) {
    $where[] = "p.jewelry_type = ?";
    $params[] = $type_filter;
}

$sql = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE " . implode(" AND ", $where) . " ORDER BY p.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jewelry_list = $stmt->fetchAll();
?>

<section class="py-5 bg-dark border-bottom border-secondary text-center">
    <div class="container" data-aos="fade-up">
        <span class="sub-title"><i class="fas fa-gem me-1 text-gold"></i> High Gemology & Solitaires</span>
        <h1 class="font-serif text-white gold-gradient-text display-4">High Jewelry Collection</h1>
        <p class="text-muted small">Handcrafted 18K solid gold bangles, VVS1 solitaire diamond rings, and South Sea golden pearls.</p>
        
        <!-- Quick Material & Type Buttons -->
        <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
            <a href="jewelry.php" class="btn btn-sm <?= empty($material_filter) && empty($type_filter) ? 'btn-gold' : 'btn-outline-gold' ?>">All Jewels</a>
            <a href="jewelry.php?type=Rings" class="btn btn-sm <?= $type_filter === 'Rings' ? 'btn-gold' : 'btn-outline-gold' ?>">Rings</a>
            <a href="jewelry.php?type=Earrings" class="btn btn-sm <?= $type_filter === 'Earrings' ? 'btn-gold' : 'btn-outline-gold' ?>">Earrings</a>
            <a href="jewelry.php?type=Necklaces" class="btn btn-sm <?= $type_filter === 'Necklaces' ? 'btn-gold' : 'btn-outline-gold' ?>">Necklaces</a>
            <a href="jewelry.php?type=Bangles" class="btn btn-sm <?= $type_filter === 'Bangles' ? 'btn-gold' : 'btn-outline-gold' ?>">Bangles</a>
            <a href="jewelry.php?material=Diamond" class="btn btn-sm <?= $material_filter === 'Diamond' ? 'btn-gold' : 'btn-outline-gold' ?>">Solitaire Diamond</a>
            <a href="jewelry.php?material=Gold" class="btn btn-sm <?= $material_filter === 'Gold' ? 'btn-gold' : 'btn-outline-gold' ?>">Solid 18K/22K Gold</a>
            <a href="jewelry.php?material=Pearl" class="btn btn-sm <?= $material_filter === 'Pearl' ? 'btn-gold' : 'btn-outline-gold' ?>">South Sea Pearl</a>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php if (empty($jewelry_list)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-gem fs-1 text-gold mb-3 opacity-50"></i>
                    <h4 class="font-serif text-white mb-2">No Jewelry Items Found</h4>
                    <p class="text-secondary small">Try clearing your material or type filters.</p>
                </div>
            <?php else: ?>
                <?php foreach ($jewelry_list as $product): 
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
                                <span class="extra-small text-gold uppercase tracking-wider d-block mb-1"><?= htmlspecialchars($product['material'] ?: 'Fine Jewelry') ?></span>
                                <h3 class="product-title"><a href="product-details.php?id=<?= $product['id'] ?>"><?= htmlspecialchars($product['title']) ?></a></h3>
                                <div class="product-price"><?= format_price($effective_price) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
