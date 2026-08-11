<?php
include __DIR__ . '/includes/admin_header.php';

// Add Coupon
if (isset($_POST['add_coupon'])) {
    $code = strtoupper(sanitize($_POST['code']));
    $type = sanitize($_POST['type']);
    $value = (float)$_POST['value'];
    $min_order_amount = (float)$_POST['min_order_amount'];
    $expiry_date = sanitize($_POST['expiry_date']);

    $stmt = $pdo->prepare("INSERT INTO coupons (code, type, value, min_order_amount, expiry_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$code, $type, $value, $min_order_amount, $expiry_date]);
    header("Location: coupons.php");
    exit;
}

// Delete Coupon
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM coupons WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: coupons.php");
    exit;
}

$coupons = $pdo->query("SELECT * FROM coupons ORDER BY id DESC")->fetchAll();
?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary text-white font-serif">Create VIP Coupon</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-light small">Coupon Code *</label>
                        <input type="text" name="code" class="form-control bg-dark text-white border-secondary small text-uppercase" required placeholder="e.g. VIP20">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light small">Discount Type</label>
                        <select name="type" class="form-select bg-dark text-white border-secondary small">
                            <option value="percentage">Percentage (%)</option>
                            <option value="flat">Flat Amount ($)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light small">Discount Value *</label>
                        <input type="number" step="0.01" name="value" class="form-control bg-dark text-white border-secondary small" required placeholder="10.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light small">Min Order Amount ($)</label>
                        <input type="number" step="0.01" name="min_order_amount" class="form-control bg-dark text-white border-secondary small" value="500.00">
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-light small">Expiry Date *</label>
                        <input type="date" name="expiry_date" class="form-control bg-dark text-white border-secondary small" required value="2027-12-31">
                    </div>
                    <button type="submit" name="add_coupon" class="btn btn-gold w-100 text-uppercase small">Create Coupon</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary text-white font-serif">Active Promo Coupons</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle border-secondary mb-0 small">
                        <thead>
                            <tr class="text-gold font-serif">
                                <th>Code</th>
                                <th>Discount</th>
                                <th>Min Spend</th>
                                <th>Expiry</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coupons as $cp): ?>
                                <tr>
                                    <td class="fw-bold text-gold"><?= htmlspecialchars($cp['code']) ?></td>
                                    <td><?= $cp['type'] === 'percentage' ? $cp['value'] . '%' : format_price($cp['value']) ?></td>
                                    <td><?= format_price($cp['min_order_amount']) ?></td>
                                    <td class="text-light"><?= date('d M Y', strtotime($cp['expiry_date'])) ?></td>
                                    <td>
                                        <a href="coupons.php?delete=<?= $cp['id'] ?>" onclick="return confirm('Delete coupon?')" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Delete</a>
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
