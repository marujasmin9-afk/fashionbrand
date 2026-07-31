<?php
require_once __DIR__ . '/config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name, b.name as brand_name 
                       FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       LEFT JOIN brands b ON p.brand_id = b.id 
                       WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: shop.php");
    exit;
}

$page_title = $product['title'];
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

// Fetch Product Gallery Images
$stmt_img = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ?");
$stmt_img->execute([$id]);
$gallery_images = $stmt_img->fetchAll();

// Handle Review Submission
if (isset($_POST['submit_review'])) {
    if (is_logged_in()) {
        $rating = (int)$_POST['rating'];
        $comment = sanitize($_POST['comment']);
        $user_id = $_SESSION['user_id'];

        $stmt_rev = $pdo->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt_rev->execute([$user_id, $id, $rating, $comment]);
        set_flash_message('success', 'Thank you. Your luxury product review has been published.');
    } else {
        set_flash_message('info', 'Please sign in to leave a product review.');
    }
}

// Fetch Reviews
$stmt_reviews = $pdo->prepare("SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? AND r.status = 'approved' ORDER BY r.id DESC");
$stmt_reviews->execute([$id]);
$reviews = $stmt_reviews->fetchAll();

// Fetch Related Products
$stmt_related = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? LIMIT 4");
$stmt_related->execute([$product['category_id'], $id]);
$related_products = $stmt_related->fetchAll();

$effective_price = ($product['discount_price'] && $product['discount_price'] < $product['price']) ? $product['discount_price'] : $product['price'];
$flash = get_flash_message();
?>

<?php if ($flash): ?>
    <div class="alert alert-info alert-dismissible fade show rounded-0 mb-0 bg-dark text-gold border-gold" role="alert">
        <div class="container d-flex align-items-center">
            <i class="fas fa-crown me-2"></i> <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>

<!-- Breadcrumb -->
<div class="py-3 bg-dark border-bottom border-secondary">
    <div class="container small">
        <a href="index.php" class="text-muted text-decoration-none">Home</a> /
        <a href="shop.php" class="text-muted text-decoration-none">Shop</a> /
        <a href="shop.php?category=<?= $product['category_id'] ?>" class="text-muted text-decoration-none"><?= htmlspecialchars($product['category_name']) ?></a> /
        <span class="text-gold"><?= htmlspecialchars($product['title']) ?></span>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Left Column: Gallery -->
            <div class="col-lg-6" data-aos="fade-right">
                <div class="main-image-box rounded-3 overflow-hidden border border-gold mb-3" style="height: 480px;">
                    <img id="main-product-img" src="<?= htmlspecialchars($product['main_image']) ?>" class="w-100 h-100 object-fit-cover" alt="<?= htmlspecialchars($product['title']) ?>">
                </div>

                <div class="d-flex gap-3">
                    <img src="<?= htmlspecialchars($product['main_image']) ?>" class="gallery-thumb rounded border border-gold object-fit-cover active-thumb" width="80" height="80" style="cursor:pointer;" onclick="changeImage(this.src)">
                    <?php foreach ($gallery_images as $gimg): ?>
                        <img src="<?= htmlspecialchars($gimg['image_url']) ?>" class="gallery-thumb rounded border border-secondary object-fit-cover" width="80" height="80" style="cursor:pointer;" onclick="changeImage(this.src)">
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right Column: Product Info -->
            <div class="col-lg-6" data-aos="fade-left">
                <div class="glass-card p-4">
                    <span class="sub-title d-block mb-1"><?= htmlspecialchars($product['brand_name'] ?: 'MAISON AURA') ?></span>
                    <h1 class="font-serif text-white fs-2 mb-2"><?= htmlspecialchars($product['title']) ?></h1>

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="text-gold small">
                            <?php for($i=0; $i<floor($product['rating']); $i++): ?><i class="fas fa-star"></i><?php endfor; ?>
                            <span class="ms-1 text-muted">(<?= count($reviews) ?> client reviews)</span>
                        </div>
                        <span class="badge bg-dark border border-secondary text-light">SKU: <?= htmlspecialchars($product['sku']) ?></span>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="display-6 font-serif text-gold fw-bold"><?= format_price($effective_price) ?></span>
                        <?php if ($product['discount_price'] && $product['discount_price'] < $product['price']): ?>
                            <span class="fs-5 text-muted text-decoration-line-through"><?= format_price($product['price']) ?></span>
                            <span class="badge bg-warning text-dark text-uppercase fw-bold">SAVE <?= round((($product['price'] - $product['discount_price']) / $product['price']) * 100) ?>%</span>
                        <?php endif; ?>
                    </div>

                    <p class="text-secondary mb-4 small"><?= htmlspecialchars($product['short_description']) ?></p>

                    <hr class="border-secondary mb-4">

                    <!-- Options Form -->
                    <form id="add-to-cart-form">
                        <?php if ($product['color']): ?>
                            <div class="mb-3">
                                <label class="form-label text-gold small text-uppercase fw-bold">Color:</label>
                                <select id="selected-color" class="form-select bg-dark text-white border-secondary small" style="width: 200px;">
                                    <?php foreach(explode(',', $product['color']) as $c): ?>
                                        <option value="<?= trim($c) ?>"><?= trim($c) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if ($product['size']): ?>
                            <div class="mb-4">
                                <label class="form-label text-gold small text-uppercase fw-bold">Size:</label>
                                <select id="selected-size" class="form-select bg-dark text-white border-secondary small" style="width: 200px;">
                                    <?php foreach(explode(',', $product['size']) as $s): ?>
                                        <option value="<?= trim($s) ?>"><?= trim($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="input-group" style="width: 130px;">
                                <button type="button" class="btn btn-outline-secondary text-white" onclick="adjustQty(-1)">-</button>
                                <input type="number" id="buy-quantity" class="form-control bg-dark text-white text-center border-secondary" value="1" min="1" max="<?= $product['stock'] ?>">
                                <button type="button" class="btn btn-outline-secondary text-white" onclick="adjustQty(1)">+</button>
                            </div>
                            <span class="text-muted small"><i class="fas fa-check-circle text-gold me-1"></i> In Stock (<?= $product['stock'] ?> available)</span>
                        </div>

                        <div class="d-flex gap-3 mb-4">
                            <button type="button" id="main-add-cart-btn" class="btn btn-gold flex-grow-1 text-uppercase py-3">
                                <i class="fas fa-shopping-bag me-2"></i> Add To Shopping Bag
                            </button>
                            <button type="button" class="btn btn-outline-gold px-4 ajax-wishlist" data-id="<?= $product['id'] ?>">
                                <i class="far fa-heart fs-5"></i>
                            </button>
                        </div>
                    </form>

                    <div class="p-3 bg-dark border border-secondary rounded-3 small text-muted">
                        <div class="mb-1"><i class="fas fa-shield-alt text-gold me-2"></i> 100% Certified Authentic High Couture & Fine Jewelry</div>
                        <div class="mb-1"><i class="fas fa-truck text-gold me-2"></i> Complimentary Worldwide Express Shipping</div>
                        <div><i class="fas fa-undo text-gold me-2"></i> 30-Day Bespoke Guarantee & Return Privilege</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description & Reviews Tabs -->
        <div class="row mt-5">
            <div class="col-12" data-aos="fade-up">
                <div class="glass-card p-4">
                    <ul class="nav nav-tabs border-secondary mb-4" id="productTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active text-gold font-serif" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button">Detailed Heritage</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-gold font-serif" id="spec-tab" data-bs-toggle="tab" data-bs-target="#spec" type="button">Specifications</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-gold font-serif" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">Client Reviews (<?= count($reviews) ?>)</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="productTabsContent">
                        <!-- Heritage Description -->
                        <div class="tab-pane fade show active text-light small lh-lg" id="desc">
                            <?= nl2br(htmlspecialchars($product['description'])) ?>
                        </div>

                        <!-- Specifications -->
                        <div class="tab-pane fade text-light small" id="spec">
                            <table class="table table-dark table-striped border-secondary">
                                <tbody>
                                    <tr><td>Material</td><td><?= htmlspecialchars($product['material'] ?: 'Premium Luxury') ?></td></tr>
                                    <tr><td>Category</td><td><?= htmlspecialchars($product['category_name']) ?></td></tr>
                                    <tr><td>Color</td><td><?= htmlspecialchars($product['color'] ?: 'Standard') ?></td></tr>
                                    <tr><td>SKU</td><td><?= htmlspecialchars($product['sku']) ?></td></tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Client Reviews -->
                        <div class="tab-pane fade" id="reviews">
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <h5 class="font-serif text-white mb-3">Collector Feedback</h5>
                                    <?php if (empty($reviews)): ?>
                                        <p class="text-muted small">Be the first to review this piece.</p>
                                    <?php else: ?>
                                        <?php foreach ($reviews as $rev): ?>
                                            <div class="p-3 bg-dark border border-secondary rounded-3 mb-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <strong class="text-gold"><?= htmlspecialchars($rev['user_name']) ?></strong>
                                                    <span class="text-gold extra-small">
                                                        <?php for($i=0; $i<$rev['rating']; $i++): ?><i class="fas fa-star"></i><?php endfor; ?>
                                                    </span>
                                                </div>
                                                <p class="text-light small mb-0"><?= htmlspecialchars($rev['comment']) ?></p>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <div class="col-lg-6">
                                    <h5 class="font-serif text-white mb-3">Write A Review</h5>
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label text-gold small">Rating</label>
                                            <select name="rating" class="form-select bg-dark text-white border-secondary small" required>
                                                <option value="5">5 Stars - Outstanding</option>
                                                <option value="4">4 Stars - Excellent</option>
                                                <option value="3">3 Stars - Good</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-gold small">Comment</label>
                                            <textarea name="comment" class="form-control bg-dark text-white border-secondary small" rows="3" required></textarea>
                                        </div>
                                        <button type="submit" name="submit_review" class="btn btn-gold text-uppercase small">Submit Review</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
            <div class="mt-5" data-aos="fade-up">
                <h3 class="font-serif text-white fs-2 mb-4">You May Also Admire</h3>
                <div class="row g-4">
                    <?php foreach ($related_products as $rel): 
                        $rel_price = ($rel['discount_price'] && $rel['discount_price'] < $rel['price']) ? $rel['discount_price'] : $rel['price'];
                    ?>
                        <div class="col-lg-3 col-md-6 col-6">
                            <div class="product-card">
                                <div class="product-thumb">
                                    <img src="<?= htmlspecialchars($rel['main_image']) ?>" alt="<?= htmlspecialchars($rel['title']) ?>" loading="lazy">
                                </div>
                                <div class="product-details-content">
                                    <h3 class="product-title small">
                                        <a href="product-details.php?id=<?= $rel['id'] ?>"><?= htmlspecialchars($rel['title']) ?></a>
                                    </h3>
                                    <div class="product-price"><?= format_price($rel_price) ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function changeImage(src) {
    document.getElementById('main-product-img').src = src;
}

function adjustQty(amount) {
    const qtyInput = document.getElementById('buy-quantity');
    let current = parseInt(qtyInput.value) || 1;
    current += amount;
    if (current < 1) current = 1;
    qtyInput.value = current;
}

document.getElementById('main-add-cart-btn').addEventListener('click', function () {
    const qty = document.getElementById('buy-quantity').value;
    const color = document.getElementById('selected-color') ? document.getElementById('selected-color').value : '';
    const size = document.getElementById('selected-size') ? document.getElementById('selected-size').value : '';

    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', <?= $product['id'] ?>);
    formData.append('quantity', qty);
    formData.append('color', color);
    formData.append('size', size);

    fetch('ajax/cart.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast('Shopping Bag', data.message, 'success');
            updateCartBadge(data.cart_count);
        } else {
            showToast('Error', data.message, 'error');
        }
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
