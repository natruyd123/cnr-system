<?php
require_once '../auth.php';
require_once '../db.php';
checkLogin('admin');

ob_start(); // Start capturing content

$client_id = $_GET['id'] ?? null;

if (!$client_id) {
    die("Client ID missing.");
}

// Get client details
$clientQuery = $conn->prepare("SELECT * FROM clients WHERE id = ?");
$clientQuery->bind_param("i", $client_id);
$clientQuery->execute();
$clientResult = $clientQuery->get_result();
$client = $clientResult->fetch_assoc();

if (!$client) {
    die("Client not found.");
}

// Get billing records
$billingQuery = $conn->prepare("
    SELECT b.date, t.service_name, b.amount, b.status, b.notes
    FROM billings b
    JOIN billing_templates t ON b.service_id = t.id
    WHERE b.client_id = ?
    ORDER BY b.date DESC
");
$billingQuery->bind_param("i", $client_id);
$billingQuery->execute();
$billings = $billingQuery->get_result();

// Calculate totals
$total_billed = 0;
$total_paid = 0;

$billing_rows = [];

while ($row = $billings->fetch_assoc()) {
    $total_billed += $row['amount'];
    if ($row['status'] === 'Paid') {
        $total_paid += $row['amount'];
    }
    $billing_rows[] = $row;
}

$outstanding = $total_billed - $total_paid;
?>

<h2>Client Statement - <?= htmlspecialchars($client['name']) ?></h2>

<div class="mt-4">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="border p-3 bg-light">
                <strong>Total Billed:</strong><br>
                ₱<?= number_format($total_billed, 2) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border p-3 bg-light">
                <strong>Total Paid:</strong><br>
                ₱<?= number_format($total_paid, 2) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border p-3 bg-light">
                <strong>Outstanding Balance:</strong><br>
                ₱<?= number_format($outstanding, 2) ?>
            </div>
        </div>
    </div>

    <h5>Billing History</h5>
    <table class="table table-bordered table-hover">
        <thead class="table-secondary">
            <tr>
                <th>Date</th>
                <th>Service</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($billing_rows as $bill): ?>
                <tr>
                    <td><?= $bill['date'] ?></td>
                    <td><?= htmlspecialchars($bill['service_name']) ?></td>
                    <td>₱<?= number_format($bill['amount'], 2) ?></td>
                    <td>
                        <span class="badge bg-<?= $bill['status'] === 'Paid' ? 'success' : 'danger' ?>">
                            <?= $bill['status'] ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($bill['notes']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean(); // End capturing content and assign to $content   

include '../base.php'; // Include layout
?>
