<?php
$page_title = "Collections Directory";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<section class="py-5">
    <div class="container text-center mb-5" data-aos="fade-up">
        <span class="sub-title">Maison Directory</span>
        <h1 class="font-serif text-white display-4 gold-gradient-text">All Collections</h1>
        <p class="text-muted small">Explore tailored evening wear, solitaire gems, silk heritage sarees, and leather goods.</p>
    </div>

    <div class="container">
        <div class="row g-4">
            <?php foreach ($categories as $cat): 
                $sub_stmt = $pdo->prepare("SELECT * FROM subcategories WHERE category_id = ?");
                $sub_stmt->execute([$cat['id']]);
                $subs = $sub_stmt->fetchAll();
            ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up">
                    <div class="glass-card p-4 h-100">
                        <div class="rounded-3 overflow-hidden mb-3" style="height: 200px;">
                            <img src="<?= htmlspecialchars($cat['image']) ?>" class="w-100 h-100 object-fit-cover">
                        </div>
                        <h4 class="font-serif text-white mb-2"><?= htmlspecialchars($cat['name']) ?></h4>
                        <p class="text-muted small mb-3"><?= htmlspecialchars($cat['description']) ?></p>
                        
                        <?php if (!empty($subs)): ?>
                            <div class="mb-3">
                                <span class="extra-small text-gold uppercase tracking-wider d-block mb-1">Subcategories:</span>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php foreach ($subs as $s): ?>
                                        <span class="badge bg-dark border border-secondary text-light"><?= htmlspecialchars($s['name']) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <a href="shop.php?category=<?= $cat['slug'] ?>" class="btn btn-outline-gold btn-sm text-uppercase w-100">Browse Atelier</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
