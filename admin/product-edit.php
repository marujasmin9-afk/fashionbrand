<?php
ob_start();
include __DIR__ . '/includes/admin_header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: products.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$brands = $pdo->query("SELECT * FROM brands ORDER BY name ASC")->fetchAll();

$message = '';
if (isset($_POST['update_product'])) {
    $title = sanitize($_POST['title']);
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
        $stmt_up = $pdo->prepare("UPDATE products SET title = ?, sku = ?, category_id = ?, brand_id = ?, price = ?, discount_price = ?, stock = ?, main_image = ?, short_description = ?, description = ?, color = ?, size = ?, material = ?, jewelry_type = ?, is_new = ?, is_best_seller = ?, is_flash_sale = ? WHERE id = ?");
        $stmt_up->execute([
            $title, $sku, $category_id, $brand_id, $price, $discount_price, $stock, $main_image, $short_desc, $description, $color, $size, $material, $jewelry_type, $is_new, $is_best_seller, $is_flash_sale, $id
        ]);
        header("Location: products.php");
        exit;
    } catch (PDOException $e) {
        $message = "Error updating product: " . $e->getMessage();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-serif text-white mb-0">Edit Product: <?= htmlspecialchars($product['title']) ?></h3>
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
                    <input type="text" name="title" class="form-control bg-dark text-white border-secondary small" value="<?= htmlspecialchars($product['title']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-light small">SKU *</label>
                    <input type="text" name="sku" class="form-control bg-dark text-white border-secondary small" value="<?= htmlspecialchars($product['sku']) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Category *</label>
                    <select name="category_id" class="form-select bg-dark text-white border-secondary small" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Brand</label>
                    <select name="brand_id" class="form-select bg-dark text-white border-secondary small">
                        <option value="">None</option>
                        <?php foreach ($brands as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= $product['brand_id'] == $b['id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Jewelry Type</label>
                    <select name="jewelry_type" class="form-select bg-dark text-white border-secondary small">
                        <option value="">None</option>
                        <option value="Rings" <?= $product['jewelry_type'] == 'Rings' ? 'selected' : '' ?>>Rings</option>
                        <option value="Earrings" <?= $product['jewelry_type'] == 'Earrings' ? 'selected' : '' ?>>Earrings</option>
                        <option value="Necklaces" <?= $product['jewelry_type'] == 'Necklaces' ? 'selected' : '' ?>>Necklaces</option>
                        <option value="Bangles" <?= $product['jewelry_type'] == 'Bangles' ? 'selected' : '' ?>>Bangles</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Price ($) *</label>
                    <input type="number" step="0.01" name="price" class="form-control bg-dark text-white border-secondary small" value="<?= $product['price'] ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Discount Price ($)</label>
                    <input type="number" step="0.01" name="discount_price" class="form-control bg-dark text-white border-secondary small" value="<?= $product['discount_price'] ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Stock *</label>
                    <input type="number" name="stock" class="form-control bg-dark text-white border-secondary small" value="<?= $product['stock'] ?>" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label text-light small">Main Image URL *</label>
                    <input type="url" name="main_image" class="form-control bg-dark text-white border-secondary small" value="<?= htmlspecialchars($product['main_image']) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Material</label>
                    <input type="text" name="material" class="form-control bg-dark text-white border-secondary small" value="<?= htmlspecialchars($product['material']) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Color</label>
                    <input type="text" name="color" class="form-control bg-dark text-white border-secondary small" value="<?= htmlspecialchars($product['color']) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label text-light small">Size</label>
                    <input type="text" name="size" class="form-control bg-dark text-white border-secondary small" value="<?= htmlspecialchars($product['size']) ?>">
                </div>

                <div class="col-md-12">
                    <label class="form-label text-light small">Short Description</label>
                    <textarea name="short_description" class="form-control bg-dark text-white border-secondary small" rows="2"><?= htmlspecialchars($product['short_description']) ?></textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label text-light small">Description</label>
                    <textarea name="description" class="form-control bg-dark text-white border-secondary small" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>

                <div class="col-md-12 d-flex gap-4">
                    <div class="form-check">
                        <input class="form-check-input bg-dark border-gold" type="checkbox" name="is_new" value="1" id="chkNew" <?= $product['is_new'] ? 'checked' : '' ?>>
                        <label class="form-check-label text-white small" for="chkNew">New Arrival</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input bg-dark border-gold" type="checkbox" name="is_best_seller" value="1" id="chkBest" <?= $product['is_best_seller'] ? 'checked' : '' ?>>
                        <label class="form-check-label text-white small" for="chkBest">Best Seller</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input bg-dark border-gold" type="checkbox" name="is_flash_sale" value="1" id="chkSale" <?= $product['is_flash_sale'] ? 'checked' : '' ?>>
                        <label class="form-check-label text-white small" for="chkSale">Flash Sale</label>
                    </div>
                </div>

                <div class="col-md-12 mt-4">
                    <button type="submit" name="update_product" class="btn btn-gold text-uppercase py-2 px-4">Update Product Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
