<?php
include __DIR__ . '/includes/admin_header.php';

$products = $pdo->query("SELECT p.*, c.name as category_name, b.name as brand_name 
                        FROM products p 
                        LEFT JOIN categories c ON p.category_id = c.id 
                        LEFT JOIN brands b ON p.brand_id = b.id 
                        ORDER BY p.id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-serif text-white mb-0">Product Inventory</h3>
    <a href="product-add.php" class="btn btn-gold"><i class="fas fa-plus me-1"></i> Add New Product</a>
</div>

<div class="card bg-dark border-secondary">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle border-secondary mb-0 small">
                <thead>
                    <tr class="text-gold font-serif">
                        <th>Image</th>
                        <th>Title / SKU</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Flags</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($p['main_image']) ?>" width="50" height="50" class="rounded object-fit-cover">
                            </td>
                            <td>
                                <strong class="text-white d-block"><?= htmlspecialchars($p['title']) ?></strong>
                                <small class="text-muted"><?= htmlspecialchars($p['sku']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($p['category_name']) ?></td>
                            <td class="text-gold font-serif fw-bold">
                                <?= format_price($p['discount_price'] ?: $p['price']) ?>
                                <?php if ($p['discount_price']): ?>
                                    <small class="text-muted text-decoration-line-through d-block"><?= format_price($p['price']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?= $p['stock'] < 5 ? 'bg-danger' : 'bg-success' ?>"><?= $p['stock'] ?> units</span>
                            </td>
                            <td>
                                <?= $p['is_new'] ? '<span class="badge bg-info text-dark">New</span> ' : '' ?>
                                <?= $p['is_best_seller'] ? '<span class="badge bg-warning text-dark">Best</span> ' : '' ?>
                                <?= $p['is_flash_sale'] ? '<span class="badge bg-danger">Sale</span>' : '' ?>
                            </td>
                            <td>
                                <a href="product-edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-warning me-1"><i class="fas fa-edit"></i> Edit</a>
                                <a href="product-delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?')" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
