<?php
include __DIR__ . '/includes/admin_header.php';

// Add Slider
if (isset($_POST['add_slider'])) {
    $title = sanitize($_POST['title']);
    $subtitle = sanitize($_POST['subtitle']);
    $image = sanitize($_POST['image']);
    $link = sanitize($_POST['link']);

    $stmt = $pdo->prepare("INSERT INTO sliders (title, subtitle, image, button_link) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $subtitle, $image, $link]);
    header("Location: banners.php");
    exit;
}

// Delete Slider
if (isset($_GET['del_slider'])) {
    $id = (int)$_GET['del_slider'];
    $stmt = $pdo->prepare("DELETE FROM sliders WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: banners.php");
    exit;
}

$sliders = $pdo->query("SELECT * FROM sliders ORDER BY id DESC")->fetchAll();
?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary text-light font-serif">Add Hero Slider</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-light small">Title *</label>
                        <input type="text" name="title" class="form-control bg-dark text-white border-secondary small" required placeholder="The Royal Gold Affair">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light small">Subtitle</label>
                        <input type="text" name="subtitle" class="form-control bg-dark text-white border-secondary small" placeholder="Discover 2026 Collection">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light small">Image URL *</label>
                        <input type="url" name="image" class="form-control bg-dark text-white border-secondary small" required placeholder="https://images.unsplash.com/...">
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-light small">Button Link</label>
                        <input type="text" name="link" class="form-control bg-dark text-white border-secondary small" value="shop.php">
                    </div>
                    <button type="submit" name="add_slider" class="btn btn-gold w-100 text-uppercase small">Add Hero Slide</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary text-light font-serif">Current Hero Sliders</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle border-secondary mb-0 small">
                        <thead>
                            <tr class="text-gold font-serif">
                                <th>Image</th>
                                <th>Title</th>
                                <th>Link</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sliders as $s): ?>
                                <tr>
                                    <td><img src="<?= htmlspecialchars($s['image']) ?>" width="70" height="40" class="rounded object-fit-cover"></td>
                                    <td class="fw-bold text-white"><?= htmlspecialchars($s['title']) ?></td>
                                    <td class="text-white"><?= htmlspecialchars($s['button_link']) ?></td>
                                    <td>
                                        <a href="banners.php?del_slider=<?= $s['id'] ?>" onclick="return confirm('Delete slide?')" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Delete</a>
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
