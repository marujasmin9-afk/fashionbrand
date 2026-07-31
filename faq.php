<?php
$page_title = "Frequently Asked Questions";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';
?>

<section class="py-5 bg-dark border-bottom border-secondary text-center">
    <div class="container" data-aos="fade-up">
        <span class="sub-title">Concierge Help</span>
        <h1 class="font-serif text-white gold-gradient-text display-4">Frequently Asked Questions</h1>
        <p class="text-muted small">Learn about custom sizing, gem certifications, and global courier dispatch.</p>
    </div>
</section>

<section class="py-5">
    <div class="container" style="max-width: 850px;">
        <div class="accordion accordion-flush" id="faqAccordion">
            <div class="accordion-item bg-dark border border-secondary rounded mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button bg-dark text-gold font-serif collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        Are all solitaire diamonds and gold pieces certified?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-light small">
                        Yes, every fine jewelry creation comes with an authentic GIA / IGI certificate verifying stone clarity, cut, carat weight, and 18K/22K gold hallmark stamps.
                    </div>
                </div>
            </div>

            <div class="accordion-item bg-dark border border-secondary rounded mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button bg-dark text-gold font-serif collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        How long does international express courier shipping take?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-light small">
                        In-stock items are dispatched within 24 hours and delivered via insured DHL Express within 2 to 4 business days globally.
                    </div>
                </div>
            </div>

            <div class="accordion-item bg-dark border border-secondary rounded mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button bg-dark text-gold font-serif collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        Can I request custom alterations for Haute Couture evening gowns?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-light small">
                        Certainly. Our Maison atelier offers complimentary bespoke fitting for VIP clients. Simply specify your measurements during checkout.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
