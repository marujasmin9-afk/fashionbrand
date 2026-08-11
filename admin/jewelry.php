<?php
include __DIR__ . '/includes/admin_header.php';

$jewelry_items = $pdo->query("SELECT p.*, c.name as category_name 
                             FROM products p 
                             JOIN categories c ON p.category_id = c.id 
                             WHERE c.type = 'jewelry' 
                             ORDER BY p.id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-serif text-white mb-0">Fine Jewelry Collection Management</h3>
    <a href="product-add.php" class="btn btn-gold btn-sm"><i class="fas fa-plus me-1"></i> Add Jewelry Item</a>
</div>

<div class="card bg-dark border-secondary">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle border-secondary mb-0 small">
                <thead>
                    <tr class="text-gold font-serif">
                        <th>Jewel Image</th>
                        <th>Title / SKU</th>
                        <th>Material Accent</th>
                        <th>Jewelry Type</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jewelry_items as $j): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($j['main_image']) ?>" width="50" height="50" class="rounded object-fit-cover">
                            </td>
                            <td>
                                <strong class="text-white d-block"><?= htmlspecialchars($j['title']) ?></strong>
                                <small class="text-muted"><?= htmlspecialchars($j['sku']) ?></small>
                            </td>
                            <td><span class="badge bg-gold text-white"><?= htmlspecialchars($j['material'] ?: 'Gemstone') ?></span></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($j['jewelry_type'] ?: 'Jewel') ?></span></td>
                            <td class="text-gold font-serif fw-bold"><?= format_price($j['discount_price'] ?: $j['price']) ?></td>
                            <td><span class="badge bg-dark border border-secondary text-white"><?= $j['stock'] ?> left</span></td>
                            <td>
                                <a href="product-edit.php?id=<?= $j['id'] ?>" class="btn btn-sm btn-outline-warning me-1"><i class="fas fa-edit"></i> Edit</a>
                                <a href="product-delete.php?id=<?= $j['id'] ?>" onclick="return confirm('Delete item?')" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
