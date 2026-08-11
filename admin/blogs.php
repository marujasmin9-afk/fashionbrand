<?php
include __DIR__ . '/includes/admin_header.php';

// Add Blog
if (isset($_POST['add_blog'])) {
    $title = sanitize($_POST['title']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $image = sanitize($_POST['image']);
    $content = $_POST['content'];
    $category = sanitize($_POST['category']);

    $stmt = $pdo->prepare("INSERT INTO blogs (title, slug, image, content, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $slug, $image, $content, $category]);
    header("Location: blogs.php");
    exit;
}

$blogs = $pdo->query("SELECT * FROM blogs ORDER BY id DESC")->fetchAll();
?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary text-light font-serif">Write Blog Article</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-light small">Title *</label>
                        <input type="text" name="title" class="form-control bg-dark text-white border-secondary small" required placeholder="Styling Solitaire Diamonds">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light small">Category</label>
                        <input type="text" name="category" class="form-control bg-dark text-white border-secondary small" value="Fine Jewelry Guide">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light small">Cover Image URL *</label>
                        <input type="url" name="image" class="form-control bg-dark text-white border-secondary small" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-light small">Content</label>
                        <textarea name="content" class="form-control bg-dark text-white border-secondary small" rows="4" required></textarea>
                    </div>
                    <button type="submit" name="add_blog" class="btn btn-gold w-100 text-uppercase small">Publish Post</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary text-white font-serif">Published Journal Posts</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle border-secondary mb-0 small">
                        <thead>
                            <tr class="text-gold font-serif">
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($blogs as $b): ?>
                                <tr>
                                    <td><img src="<?= htmlspecialchars($b['image']) ?>" width="60" height="40" class="rounded object-fit-cover"></td>
                                    <td class="fw-bold text-white"><?= htmlspecialchars($b['title']) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($b['category']) ?></span></td>
                                    <td class="text-white"><?= date('d M Y', strtotime($b['created_at'])) ?></td>
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
