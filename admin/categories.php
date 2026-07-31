<?php
include __DIR__ . '/includes/admin_header.php';

$message = '';

// Handle Add Category
if (isset($_POST['add_category'])) {
    $name = sanitize($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $type = sanitize($_POST['type']);
    $image = sanitize($_POST['image']);
    $description = sanitize($_POST['description']);

    $stmt = $pdo->prepare("INSERT INTO categories (name, slug, type, image, description, is_featured) VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->execute([$name, $slug, $type, $image, $description]);
    header("Location: categories.php");
    exit;
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$del_id]);
    header("Location: categories.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
?>

<div class="row g-4">
    <!-- Form to Add Category -->
    <div class="col-md-4">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary text-gold font-serif">Add New Category</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-light small">Category Name *</label>
                        <input type="text" name="name" class="form-control bg-dark text-white border-secondary small" required placeholder="e.g. Silk Sarees">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light small">Department Type</label>
                        <select name="type" class="form-select bg-dark text-white border-secondary small">
                            <option value="clothing">Clothing</option>
                            <option value="jewelry">Jewelry</option>
                            <option value="accessories">Accessories</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light small">Image URL</label>
                        <input type="url" name="image" class="form-control bg-dark text-white border-secondary small" placeholder="https://images.unsplash.com/...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light small">Description</label>
                        <textarea name="description" class="form-control bg-dark text-white border-secondary small" rows="2"></textarea>
                    </div>
                    <button type="submit" name="add_category" class="btn btn-gold w-100 text-uppercase small">Create Category</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Category List Table -->
    <div class="col-md-8">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary text-gold font-serif">Categories List</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle border-secondary mb-0 small">
                        <thead>
                            <tr class="text-gold font-serif">
                                <th>Image</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Slug</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td>
                                        <img src="<?= htmlspecialchars($cat['image']) ?>" width="40" height="40" class="rounded object-fit-cover">
                                    </td>
                                    <td class="fw-bold text-white"><?= htmlspecialchars($cat['name']) ?></td>
                                    <td><span class="badge bg-secondary"><?= strtoupper($cat['type']) ?></span></td>
                                    <td class="text-muted"><?= htmlspecialchars($cat['slug']) ?></td>
                                    <td>
                                        <a href="categories.php?delete=<?= $cat['id'] ?>" onclick="return confirm('Delete this category?')" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
