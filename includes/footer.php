<?php
$settings = get_settings();
?>
<footer class="luxury-footer">
    <div class="container">
        <div class="row g-4">
            <!-- Brand Info & Newsletter -->
            <div class="col-lg-4 col-md-6">
                <h3 class="navbar-brand text-white mb-3">AURA <span class="text-gold">LUXE</span></h3>
                <p class="text-muted small mb-4">
                    BRAND FASHION is a premier international luxury maison bringing bespoke Haute Couture dresses, fine solitaire diamond jewelry, and handcrafted silk heritage wear to discerning collectors worldwide.
                </p>
                <h5 class="footer-title text-gold fs-6 mb-2">JOIN THE PRIVATE CLUB</h5>
                <form action="index.php" method="POST" class="d-flex gap-2">
                    <input type="email" name="newsletter_email" class="form-control form-control-luxury text-white" placeholder="Enter your VIP email..." required>
                    <button type="submit" name="subscribe_newsletter" class="btn btn-gold px-3"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>

            <!-- Boutique Categories -->
            <div class="col-lg-2 col-md-6 col-6">
                <h5 class="footer-title">Collections</h5>
                <ul class="footer-links">
                    <li><a href="shop.php?category=haute-couture-dresses">Haute Couture</a></li>
                    <li><a href="shop.php?category=diamond-fine-jewelry">Diamond Jewelry</a></li>
                    <li><a href="shop.php?category=solid-gold-pearls">Gold & Pearls</a></li>
                    <li><a href="shop.php?category=ethnic-wear-sarees">Silk Sarees</a></li>
                    <li><a href="shop.php?category=luxury-handbags">Leather Handbags</a></li>
                </ul>
            </div>

            <!-- Quick Customer Care -->
            <div class="col-lg-2 col-md-6 col-6">
                <h5 class="footer-title">Concierge</h5>
                <ul class="footer-links">
                    <li><a href="about.php">About Maison</a></li>
                    <li><a href="contact.php">Private Appointment</a></li>
                    <li><a href="faq.php">Shipping & Returns</a></li>
                    <li><a href="privacy.php">Privacy Policy</a></li>
                    <li><a href="terms.php">Terms of Service</a></li>
                </ul>
            </div>

            <!-- Contact & Social -->
            <div class="col-lg-4 col-md-6">
                <h5 class="footer-title">Flagship Boutique</h5>
                <p class="text-muted small mb-2"><i class="fas fa-map-marker-alt text-gold me-2"></i> <?= htmlspecialchars($settings['address']) ?></p>
                <p class="text-muted small mb-2"><i class="fas fa-phone-alt text-gold me-2"></i> Concierge: <?= htmlspecialchars($settings['contact_phone']) ?></p>
                <p class="text-muted small mb-4"><i class="fas fa-envelope text-gold me-2"></i> <?= htmlspecialchars($settings['contact_email']) ?></p>

                <div class="d-flex gap-3 fs-5 text-white">
                    <a href="<?= htmlspecialchars($settings['facebook_url']) ?>" target="_blank" class="text-white hover-gold"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?= htmlspecialchars($settings['instagram_url']) ?>" target="_blank" class="text-white hover-gold"><i class="fab fa-instagram"></i></a>
                    <a href="<?= htmlspecialchars($settings['pinterest_url']) ?>" target="_blank" class="text-white hover-gold"><i class="fab fa-pinterest-p"></i></a>
                    <a href="#" class="text-white hover-gold"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>

        <hr class="border-secondary my-5">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-muted">
            <p class="mb-2 mb-md-0">&copy; <?= date('Y') ?> BRAND FASHION Maison. All Rights Reserved. Crafted for Haute Couture & Fine Jewelry.</p>
            <div class="d-flex gap-3 fs-4 text-secondary">
                <i class="fab fa-cc-visa"></i>
                <i class="fab fa-cc-mastercard"></i>
                <i class="fab fa-cc-paypal"></i>
                <i class="fab fa-cc-stripe"></i>
            </div>
        </div>
    </div>
</footer>

<!-- Include Modals & Toasts -->
<?php include __DIR__ . '/toast.php'; ?>
<?php include __DIR__ . '/quick_view_modal.php'; ?>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- GSAP Animations -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<!-- AOS Scroll Animations JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Main JS File -->
<script src="assets/js/main.js"></script>
</body>
</html>
