<?php
require_once '../auth.php';
require_once '../db.php';
checkLogin('admin');

ob_start(); // Start capturing content

// Fetch clients
$clients = $conn->query("SELECT id, name FROM clients");

// Fetch billing templates (services)
$services = $conn->query("SELECT id, service_name, amount FROM billing_templates");

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_POST['client_id'];
    $date = $_POST['billing_date'];
    $service_ids = $_POST['services'];
    $notes = $_POST['notes'];

    foreach ($service_ids as $sid) {
        $stmt = $conn->prepare("SELECT amount FROM billing_templates WHERE id = ?");
        $stmt->bind_param("i", $sid);
        $stmt->execute();
        $stmt->bind_result($amount);
        $stmt->fetch();
        $stmt->close();

        $insert = $conn->prepare("INSERT INTO billings (client_id, service_id, date, amount, notes) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("iisds", $client_id, $sid, $date, $amount, $notes);
        $insert->execute();
    }

    header("Location: billing_input.php?success=1");
    exit();
}
?>

<h2>Generate Billing</h2>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success">Billing saved successfully!</div>
<?php endif; ?>

<form method="POST" class="border p-4 bg-light rounded">
    <div class="mb-3">
        <label for="client">Select Client</label>
        <select name="client_id" class="form-select" required>
            <option value="">-- Choose Client --</option>
            <?php while ($c = $clients->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="date">Billing Date</label>
        <input type="date" name="billing_date" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Select Services</label>
        <?php while ($s = $services->fetch_assoc()): ?>
            <div class="form-check">
                <input type="checkbox" name="services[]" value="<?= $s['id'] ?>" class="form-check-input" id="service<?= $s['id'] ?>">
                <label for="service<?= $s['id'] ?>" class="form-check-label">
                    <?= htmlspecialchars($s['service_name']) ?> – ₱<?= number_format($s['amount'], 2) ?>
                </label>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="mb-3">
        <label>Notes</label>
        <textarea name="notes" class="form-control"></textarea>
    </div>

    <button class="btn btn-primary">Save Billing</button>
</form>

<?php
$content = ob_get_clean(); // End capturing

include '../base.php'; // Include layout
?>