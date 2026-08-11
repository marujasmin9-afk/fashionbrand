<?php
$page_title = "Brand Fashion";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

// Handle Newsletter Subscription POST
if (isset($_POST['subscribe_newsletter'])) {
    $email = sanitize($_POST['newsletter_email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO newsletter (email) VALUES (?)");
            $stmt->execute([$email]);
            set_flash_message('success', 'Thank you for joining the BRAND FASHION VIP Concierge list.');
        } catch (PDOException $e) {
            set_flash_message('info', 'You are already registered on our VIP list.');
        }
    }
}

// Fetch Hero Sliders
$sliders = $pdo->query("SELECT * FROM sliders WHERE status = 'active' ORDER BY sort_order ASC")->fetchAll();

// Fetch Featured Categories
$categories = $pdo->query("SELECT * FROM categories WHERE is_featured = 1 LIMIT 6")->fetchAll();

// Fetch New Arrivals
$new_arrivals = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_new = 1 ORDER BY p.id DESC LIMIT 8")->fetchAll();

// Fetch Best Sellers
$best_sellers = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_best_seller = 1 ORDER BY p.id DESC LIMIT 8")->fetchAll();

// Fetch Flash Sale Products
$flash_sale = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_flash_sale = 1 LIMIT 4")->fetchAll();

// Fetch Jewelry Collection
$jewelry_items = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE c.type = 'jewelry' LIMIT 4")->fetchAll();

// Fetch Testimonials
$testimonials = $pdo->query("SELECT * FROM testimonials WHERE status = 'active' LIMIT 3")->fetchAll();

$flash = get_flash_message();
?>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'warning' : 'info' ?> alert-dismissible fade show rounded-0 mb-0 bg-dark text-gold border-gold" role="alert">
        <div class="container d-flex align-items-center">
            <i class="fas fa-crown me-2"></i> <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>

<!-- ========================================================
     1. HERO SLIDER SECTION
     ======================================================== -->
<section class="hero-section">
    <div id="heroCarousel" class="carousel slide carousel-fade hero-slider" data-bs-ride="carousel" data-bs-interval="6000">
        <div class="carousel-inner">
            <?php foreach ($sliders as $index => $slide): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" style="background-image: url('<?= htmlspecialchars($slide['image']) ?>');">
                    <div class="hero-overlay">
                        <div class="container" data-aos="fade-up">
                            <div class="row">
                                <div class="col-lg-7">
                                    <span class="sub-title d-block mb-2"><i class="fas fa-gem me-2"></i> Haute Couture & Fine Gemology</span>
                                    <h1 class="hero-title text-white font-serif mb-3"><?= htmlspecialchars($slide['title']) ?></h1>
                                    <p class="text-light fs-5 fw-light mb-4"><?= htmlspecialchars($slide['subtitle']) ?></p>
                                    <div class="d-flex gap-3">
                                        <a href="<?= htmlspecialchars($slide['button_link']) ?>" class="btn btn-gold"><?= htmlspecialchars($slide['button_text']) ?></a>
                                        <a href="shop.php" class="btn btn-outline-gold">View Collections</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>


<!-- ========================================================
     2. FEATURED CATEGORIES SECTION
     ======================================================== -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="sub-title">Explore By Atelier</span>
            <h2 class="font-serif text-white fs-1 gold-gradient-text">Featured Categories</h2>
            <p class="text-muted small">Handcrafted gowns, rare solitaire diamonds, and Italian leather accessories.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($categories as $cat): ?>
                <div class="col-lg-2 col-md-4 col-6" data-aos="zoom-in">
                    <a href="shop.php?category=<?= $cat['slug'] ?>" class="category-card">
                        <div class="category-img">
                            <img src="<?= htmlspecialchars($cat['image']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" loading="lazy">
                        </div>
                        <h5 class="fs-6 font-serif mb-1"><?= htmlspecialchars($cat['name']) ?></h5>
                        <span class="extra-small text-gold">Explore Atelier &rarr;</span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ========================================================
     3. NEW ARRIVALS
     ======================================================== -->
<section class="py-5 bg-dark border-top border-bottom border-secondary">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4" data-aos="fade-up">
            <div>
                <span class="sub-title">Fresh Runway Pieces</span>
                <h2 class="font-serif text-white fs-2 mb-0">New Arrivals</h2>
            </div>
            <a href="new-arrivals.php" class="btn btn-outline-gold btn-sm">View All New In</a>
        </div>

        <div class="row g-4">
            <?php foreach ($new_arrivals as $product): 
                $effective_price = ($product['discount_price'] && $product['discount_price'] < $product['price']) ? $product['discount_price'] : $product['price'];
            ?>
                <div class="col-lg-3 col-md-6 col-6" data-aos="fade-up">
                    <div class="product-card">
                        <span class="badge-tag">NEW IN</span>
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
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ========================================================
     4. FLASH SALE & COUNTDOWN TIMER
     ======================================================== -->
<section class="py-5" style="background: linear-gradient(135deg, #121212 0%, #050505 100%);">
    <div class="container">
        <div class="glass-card p-5 border-gold">
            <div class="row align-items-center g-4">
                <div class="col-lg-5" data-aos="fade-right">
                    <span class="sub-title text-warning"><i class="fas fa-bolt me-1"></i> Private Salon Sale</span>
                    <h2 class="font-serif text-white fs-1 mb-3">Limited High Jewelry Flash Sale</h2>
                    <p class="text-secondary small mb-4">Enjoy up to 30% off selected 18K solid gold bangles, solitaire diamond rings, and silk gowns. Offer expires soon.</p>
                    
                    <div id="flash-sale-timer" class="d-flex gap-3 mb-4">
                        <div class="countdown-box">
                            <div id="timer-days" class="countdown-num">03</div>
                            <div class="countdown-label">Days</div>
                        </div>
                        <div class="countdown-box">
                            <div id="timer-hours" class="countdown-num">14</div>
                            <div class="countdown-label">Hours</div>
                        </div>
                        <div class="countdown-box">
                            <div id="timer-mins" class="countdown-num">45</div>
                            <div class="countdown-label">Mins</div>
                        </div>
                        <div class="countdown-box">
                            <div id="timer-secs" class="countdown-num">20</div>
                            <div class="countdown-label">Secs</div>
                        </div>
                    </div>

                    <a href="sale.php" class="btn btn-gold text-uppercase">Shop Sale Collection</a>
                </div>

                <div class="col-lg-7" data-aos="fade-left">
                    <div class="row g-3">
                        <?php foreach ($flash_sale as $item): 
                            $effective_price = ($item['discount_price'] && $item['discount_price'] < $item['price']) ? $item['discount_price'] : $item['price'];
                        ?>
                            <div class="col-md-6 col-6">
                                <div class="product-card">
                                    <span class="badge-tag text-warning border-warning">SALE</span>
                                    <div class="product-thumb">
                                        <img src="<?= htmlspecialchars($item['main_image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" loading="lazy">
                                    </div>
                                    <div class="product-details-content">
                                        <h3 class="product-title small mb-1">
                                            <a href="product-details.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['title']) ?></a>
                                        </h3>
                                        <div class="product-price">
                                            <?= format_price($effective_price) ?>
                                            <span class="old-price"><?= format_price($item['price']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ========================================================
     5. FEATURED HIGH JEWELRY COLLECTION
     ======================================================== -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="sub-title"><i class="fas fa-gem me-1"></i> Rare Gemology</span>
            <h2 class="font-serif text-white fs-1 gold-gradient-text">Fine Jewelry Showcase</h2>
            <p class="text-muted small">Solitaires, emerald cascades, and South Sea golden pearls.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($jewelry_items as $jw): 
                $effective_price = ($jw['discount_price'] && $jw['discount_price'] < $jw['price']) ? $jw['discount_price'] : $jw['price'];
            ?>
                <div class="col-lg-3 col-md-6 col-6" data-aos="fade-up">
                    <div class="product-card">
                        <div class="product-thumb">
                            <img src="<?= htmlspecialchars($jw['main_image']) ?>" alt="<?= htmlspecialchars($jw['title']) ?>" loading="lazy">
                            <div class="product-actions">
                                <button class="action-btn ajax-add-cart" data-id="<?= $jw['id'] ?>"><i class="fas fa-shopping-bag"></i></button>
                                <button class="action-btn ajax-wishlist" data-id="<?= $jw['id'] ?>"><i class="far fa-heart"></i></button>
                                <button class="action-btn ajax-quick-view" data-id="<?= $jw['id'] ?>"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="product-details-content">
                            <span class="extra-small text-gold uppercase tracking-wider d-block mb-1"><?= htmlspecialchars($jw['material'] ?: 'Fine Jewelry') ?></span>
                            <h3 class="product-title">
                                <a href="product-details.php?id=<?= $jw['id'] ?>"><?= htmlspecialchars($jw['title']) ?></a>
                            </h3>
                            <div class="product-price"><?= format_price($effective_price) ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ========================================================
     6. CUSTOMER REVIEWS & TESTIMONIALS
     ======================================================== -->
<section class="py-5 bg-dark border-top border-secondary">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="sub-title">Client Satisfaction</span>
            <h2 class="font-serif text-white fs-1">Collector Reviews</h2>
        </div>

        <div class="row g-4">
            <?php foreach ($testimonials as $t): ?>
                <div class="col-lg-6" data-aos="fade-up">
                    <div class="glass-card p-4 d-flex gap-3 align-items-center">
                        <img src="<?= htmlspecialchars($t['avatar']) ?>" class="rounded-circle border border-gold object-fit-cover" width="70" height="70" alt="<?= htmlspecialchars($t['name']) ?>">
                        <div>
                            <div class="text-gold small mb-1">
                                <?php for($i=0; $i<$t['rating']; $i++): ?><i class="fas fa-star"></i><?php endfor; ?>
                            </div>
                            <p class="fst-italic text-light small mb-2">"<?= htmlspecialchars($t['review']) ?>"</p>
                            <h6 class="font-serif text-gold mb-0"><?= htmlspecialchars($t['name']) ?> <span class="text-muted fw-normal fs-7">- <?= htmlspecialchars($t['role']) ?></span></h6>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
