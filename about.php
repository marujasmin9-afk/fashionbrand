<?php
$page_title = "About Maison AURA";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';
?>

<section class="py-5 bg-dark border-bottom border-secondary text-center">
    <div class="container" data-aos="fade-up">
        <span class="sub-title">Maison Heritage</span>
        <h1 class="font-serif text-white gold-gradient-text display-4">The Story of BRAND FASHION</h1>
        <p class="text-muted small">Crafting haute couture gowns and fine solitaire jewelry for royalty and private collectors worldwide.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5 mb-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="rounded-3 overflow-hidden border border-gold">
                    <img src="https://images.unsplash.com/photo-1509631179647-0177331693ae?w=800&q=80" class="w-100 object-fit-cover" style="height: 450px;">
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <span class="sub-title">Atelier Heritage</span>
                <h2 class="font-serif text-white fs-1 mb-3">Unrivaled Artistry & Pure Elegance</h2>
                <p class="text-secondary small lh-lg mb-3">
                    Founded in Paris and operating across luxury capitals worldwide, BRAND FASHION embodies the pinnacle of high fashion and bespoke gemology. Each piece in our boutique is individually handcrafted by master artisans who have spent decades perfecting French embroidery and Solitaire setting.
                </p>
                <p class="text-secondary small lh-lg mb-4">
                    From rare VVS1 Columbian emeralds to hand-spun Banarasi silk sarees, BRAND FASHION is dedicated to serving collectors who settle for nothing less than perfection.
                </p>
                <a href="shop.php" class="btn btn-gold text-uppercase">Explore Private Catalog</a>
            </div>
        </div>

        <div class="row g-4 text-center mt-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="glass-card p-4">
                    <i class="fas fa-crown text-gold display-5 mb-3"></i>
                    <h5 class="font-serif text-white mb-2">Haute Couture Standard</h5>
                    <p class="text-muted small">Tailored by master couturiers with French velvet and Italian silk finishes.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="glass-card p-4">
                    <i class="fas fa-gem text-gold display-5 mb-3"></i>
                    <h5 class="font-serif text-white mb-2">Certified Gemology</h5>
                    <p class="text-muted small">Every solitaire diamond and South Sea pearl is GIA & IGI ethically certified.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="glass-card p-4">
                    <i class="fas fa-concierge-bell text-gold display-5 mb-3"></i>
                    <h5 class="font-serif text-white mb-2">Private Concierge</h5>
                    <p class="text-muted small">Discreet worldwide courier delivery and personalized styling consultations.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
