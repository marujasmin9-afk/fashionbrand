<?php
include __DIR__ . '/includes/admin_header.php';

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$brands = $pdo->query("SELECT * FROM brands ORDER BY name ASC")->fetchAll();

$message = '';
if (isset($_POST['save_product'])) {
    $title = sanitize($_POST['title']);
    $slug = sanitize($_POST['slug']) ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $sku = sanitize($_POST['sku']);
    $category_id = (int)$_POST['category_id'];
    $brand_id = !empty($_POST['brand_id']) ? (int)$_POST['brand_id'] : null;
    $price = (float)$_POST['price'];
    $discount_price = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
    $stock = (int)$_POST['stock'];
    $main_image = sanitize($_POST['main_image']);
    $short_desc = sanitize($_POST['short_description']);
    $description = sanitize($_POST['description']);
    $color = sanitize($_POST['color']);
    $size = sanitize($_POST['size']);
    $material = sanitize($_POST['material']);
    $jewelry_type = sanitize($_POST['jewelry_type']);
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $is_best_seller = isset($_POST['is_best_seller']) ? 1 : 0;
    $is_flash_sale = isset($_POST['is_flash_sale']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("INSERT INTO products (title, slug, sku, category_id, brand_id, price, discount_price, stock, main_image, short_description, description, color, size, material, jewelry_type, is_new, is_best_seller, is_flash_sale) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $title, $slug, $sku, $category_id, $brand_id, $price, $discount_price, $stock, $main_image, $short_desc, $description, $color, $size, $material, $jewelry_type, $is_new, $is_best_seller, $is_flash_sale
        ]);
        header("Location: products.php");
        exit;
    } catch (PDOException $e) {
        $message = "Error adding product: " . $e->getMessage();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-serif text-white mb-0">Add New Product</h3>
    <a href="products.php" class="btn btn-outline-secondary text-white btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to Inventory</a>
</div>

<?php if ($message): ?>
    <div class="alert alert-danger bg-dark text-white border-danger mb-4"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="card bg-dark border-secondary">
    <div class="card-body p-4">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-light small">Product Title *</label>
                    <input type="text" name="title" class="form-control bg-dark text-white border-secondary small" required placeholder="e.g. Diamond Pendant Necklace">
                </div>

                <div class="col-md-3">
                    <label class="form-label text-light small">SKU *</label>
                    <input type="text" name="sku" class="form-control bg-dark text-white border-secondary small" required placeholder="SKU-JWL-999">
                </div>

                <div class="col-md-3">
                    <label class="form-label text-light small">URL Slug (Optional)</label>
                    <input type="text" name="slug" class="form-control bg-dark text-white border-secondary small" placeholder="diamond-pendant-necklace">
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Category *</label>
                    <select name="category_id" class="form-select bg-dark text-white border-secondary small" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Brand / Maison</label>
                    <select name="brand_id" class="form-select bg-dark text-white border-secondary small">
                        <option value="">Select Brand</option>
                        <?php foreach ($brands as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Jewelry Type (If Applicable)</label>
                    <select name="jewelry_type" class="form-select bg-dark text-white border-secondary small">
                        <option value="">None / Clothing</option>
                        <option value="Rings">Rings</option>
                        <option value="Earrings">Earrings</option>
                        <option value="Necklaces">Necklaces</option>
                        <option value="Bracelets">Bracelets</option>
                        <option value="Bangles">Bangles</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Regular Price ($) *</label>
                    <input type="number" step="0.01" name="price" class="form-control bg-dark text-white border-secondary small" required placeholder="1250.00">
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Discount Sale Price ($)</label>
                    <input type="number" step="0.01" name="discount_price" class="form-control bg-dark text-white border-secondary small" placeholder="980.00">
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Stock Quantity *</label>
                    <input type="number" name="stock" class="form-control bg-dark text-white border-secondary small" value="10" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label text-light small">Main Product Image URL *</label>
                    <input type="url" name="main_image" class="form-control bg-dark text-white border-secondary small" required placeholder="https://images.unsplash.com/photo-...">
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Material (Gold, Diamond, Silk, Velvet)</label>
                    <input type="text" name="material" class="form-control bg-dark text-white border-secondary small" placeholder="18K Gold & Solitaire">
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Available Colors (Comma separated)</label>
                    <input type="text" name="color" class="form-control bg-dark text-white border-secondary small" placeholder="Black, Gold, Silver">
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Available Sizes (Comma separated)</label>
                    <input type="text" name="size" class="form-control bg-dark text-white border-secondary small" placeholder="S, M, L, Free Size">
                </div>

                <div class="col-md-12">
                    <label class="form-label text-light small">Short Summary</label>
                    <textarea name="short_description" class="form-control bg-dark text-white border-secondary small" rows="2"></textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label text-light small">Detailed Description & Specifications</label>
                    <textarea name="description" class="form-control bg-dark text-white border-secondary small" rows="4"></textarea>
                </div>

                <div class="col-md-12 d-flex gap-4">
                    <div class="form-check">
                        <input class="form-check-input bg-dark border-gold" type="checkbox" name="is_new" value="1" id="chkNew">
                        <label class="form-check-label text-white small" for="chkNew">New Arrival</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input bg-dark border-gold" type="checkbox" name="is_best_seller" value="1" id="chkBest">
                        <label class="form-check-label text-white small" for="chkBest">Best Seller</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input bg-dark border-gold" type="checkbox" name="is_flash_sale" value="1" id="chkSale">
                        <label class="form-check-label text-white small" for="chkSale">Flash Sale</label>
                    </div>
                </div>

                <div class="col-md-12 mt-4">
                    <button type="submit" name="save_product" class="btn btn-gold text-uppercase py-2 px-4">Save Product To Catalog</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
