<?php
$page_title = "Boutique Catalog & Shop";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

// Fetch Categories & Brands for Filters
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$brands = $pdo->query("SELECT * FROM brands ORDER BY name ASC")->fetchAll();

$current_cat = isset($_GET['category']) ? sanitize($_GET['category']) : '';
?>

<!-- Header Banner -->
<section class="py-5 bg-dark border-bottom border-secondary text-center">
    <div class="container" data-aos="fade-up">
        <span class="sub-title">Haute Couture & High Gemology</span>
        <h1 class="font-serif text-white gold-gradient-text display-5">AURA Catalog</h1>
        <p class="text-muted small">Discover luxury evening wear, bespoke gowns, fine solitaire diamonds, and handcrafted silk.</p>
    </div>
</section>

<!-- Shop Body -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar Filters -->
            <div class="col-lg-3">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary">
                        <h5 class="font-serif text-gold mb-0"><i class="fas fa-sliders-h me-2"></i> Refine Catalog</h5>
                        <button id="reset-filters" class="btn btn-link text-muted small p-0 text-decoration-none">Reset</button>
                    </div>

                    <form id="filter-form">
                        <!-- Category Filter -->
                        <div class="mb-4">
                            <label class="form-label text-white small fw-semibold text-uppercase tracking-wider">Category</label>
                            <select name="category" class="form-select bg-dark text-white border-secondary small">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['slug'] ?>" <?= $current_cat === $cat['slug'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Brand Filter -->
                        <div class="mb-4">
                            <label class="form-label text-white small fw-semibold text-uppercase tracking-wider">Maison / Brand</label>
                            <select name="brand_id" class="form-select bg-dark text-white border-secondary small">
                                <option value="">All Brands</option>
                                <?php foreach ($brands as $b): ?>
                                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Price Range Filter -->
                        <div class="mb-4">
                            <label class="form-label text-white small fw-semibold text-uppercase tracking-wider">Price Range ($)</label>
                            <div class="d-flex gap-2">
                                <input type="number" name="min_price" class="form-control bg-dark text-white border-secondary small" placeholder="Min">
                                <input type="number" name="max_price" class="form-control bg-dark text-white border-secondary small" placeholder="Max">
                            </div>
                        </div>

                        <!-- Material Filter -->
                        <div class="mb-4">
                            <label class="form-label text-white small fw-semibold text-uppercase tracking-wider">Material</label>
                            <select name="material" class="form-select bg-dark text-white border-secondary small">
                                <option value="">All Materials</option>
                                <option value="Gold">18K / 22K Gold</option>
                                <option value="Diamond">Solitaire Diamond</option>
                                <option value="Pearl">South Sea Pearl</option>
                                <option value="Velvet">French Velvet</option>
                                <option value="Silk">Katan Pure Silk</option>
                                <option value="Leather">Calfskin Leather</option>
                            </select>
                        </div>

                        <!-- Color Filter -->
                        <div class="mb-4">
                            <label class="form-label text-white small fw-semibold text-uppercase tracking-wider">Color Accent</label>
                            <select name="color" class="form-select bg-dark text-white border-secondary small">
                                <option value="">All Colors</option>
                                <option value="Black">Midnight Black</option>
                                <option value="Gold">Champagne Gold</option>
                                <option value="Crimson">Crimson Red</option>
                                <option value="Silver">Silver / White</option>
                            </select>
                        </div>

                        <!-- Size Filter -->
                        <div class="mb-4">
                            <label class="form-label text-white small fw-semibold text-uppercase tracking-wider">Size</label>
                            <select name="size" class="form-select bg-dark text-white border-secondary small">
                                <option value="">All Sizes</option>
                                <option value="S">Small (S)</option>
                                <option value="M">Medium (M)</option>
                                <option value="L">Large (L)</option>
                                <option value="Free Size">Free Size / One Size</option>
                            </select>
                        </div>

                        <!-- In Stock Only -->
                        <div class="form-check mb-4">
                            <input class="form-check-input bg-dark border-secondary" type="checkbox" name="in_stock" value="1" id="inStockCheck">
                            <label class="form-check-label text-muted small" for="inStockCheck">
                                In Stock Treasures Only
                            </label>
                        </div>

                        <button type="button" id="apply-filters-btn" class="btn btn-gold w-100 text-uppercase small">
                            Apply Filters
                        </button>
                    </form>
                </div>
            </div>

            <!-- Product Grid Area -->
            <div class="col-lg-9">
                <!-- Sorting & Top Bar -->
                <div class="glass-card p-3 mb-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                    <span class="text-muted small">Showing curated luxury pieces</span>
                    
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-muted small mb-0 text-nowrap">Sort By:</label>
                        <select id="sort-select" class="form-select bg-dark text-white border-secondary small py-1" style="width: 180px;">
                            <option value="newest">Newest Arrivals</option>
                            <option value="price_low">Price: Low to High</option>
                            <option value="price_high">Price: High to Low</option>
                            <option value="popularity">Most Popular</option>
                            <option value="rating">Top Rated</option>
                        </select>
                    </div>
                </div>

                <!-- AJAX Product Grid Container -->
                <div id="product-grid-container" class="row g-4">
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-gold" role="status">
                            <span class="visually-hidden">Loading products...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('filter-form');
    const applyBtn = document.getElementById('apply-filters-btn');
    const resetBtn = document.getElementById('reset-filters');
    const sortSelect = document.getElementById('sort-select');
    const container = document.getElementById('product-grid-container');

    function fetchFilteredProducts() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        params.append('sort', sortSelect.value);

        fetch(`ajax/filter_products.php?${params.toString()}`)
            .then(res => res.text())
            .then(html => {
                container.innerHTML = html;
                if (typeof AOS !== 'undefined') AOS.refresh();
            });
    }

    applyBtn.addEventListener('click', fetchFilteredProducts);
    sortSelect.addEventListener('change', fetchFilteredProducts);
    resetBtn.addEventListener('click', function () {
        filterForm.reset();
        fetchFilteredProducts();
    });

    // Initial load
    fetchFilteredProducts();
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
