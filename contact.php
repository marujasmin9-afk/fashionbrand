<?php
$page_title = "Private Appointment & Contact";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/navbar.php';

$message = '';
if (isset($_POST['send_message'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $msg = sanitize($_POST['message']);

    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $msg]);
    $message = "Your private inquiry has been received by our Maison concierge. We will contact you within 12 hours.";
}
?>

<section class="py-5 bg-dark border-bottom border-secondary text-center">
    <div class="container" data-aos="fade-up">
        <span class="sub-title">Boutique Concierge</span>
        <h1 class="font-serif text-white gold-gradient-text display-4">Contact Maison AURA</h1>
        <p class="text-muted small">Schedule a private jewelry preview or request custom tailoring guidance.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <div class="glass-card p-4">
                    <h4 class="font-serif text-gold mb-4">Flagship Salon</h4>
                    <p class="text-light small mb-3"><i class="fas fa-map-marker-alt text-gold me-3 fs-5"></i> 740 Fifth Avenue, New York, NY 10019</p>
                    <p class="text-light small mb-3"><i class="fas fa-phone-alt text-gold me-3 fs-5"></i> Concierge: +1 (800) 888-AURA</p>
                    <p class="text-light small mb-4"><i class="fas fa-envelope text-gold me-3 fs-5"></i> concierge@auraluxe.com</p>

                    <hr class="border-secondary mb-4">

                    <h5 class="font-serif text-white fs-6 mb-2">Salon Hours</h5>
                    <p class="text-muted small mb-1">Monday - Saturday: 10:00 AM - 8:00 PM EST</p>
                    <p class="text-muted small">Sunday: By Private VIP Appointment Only</p>
                </div>
            </div>

            <div class="col-lg-7" data-aos="fade-left">
                <div class="glass-card p-4">
                    <h4 class="font-serif text-white mb-4">Send A Private Inquiry</h4>

                    <?php if ($message): ?>
                        <div class="alert alert-success bg-dark text-white border-gold mb-4"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-light small">Your Full Name *</label>
                                <input type="text" name="name" class="form-control bg-dark text-white border-secondary small" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-light small">Email Address *</label>
                                <input type="email" name="email" class="form-control bg-dark text-white border-secondary small" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-light small">Subject</label>
                                <input type="text" name="subject" class="form-control bg-dark text-white border-secondary small" placeholder="e.g. Bespoke Solitaire Consultation">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-light small">Inquiry Details *</label>
                                <textarea name="message" class="form-control bg-dark text-white border-secondary small" rows="4" required></textarea>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" name="send_message" class="btn btn-gold text-uppercase py-3 w-100">Transmit Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
