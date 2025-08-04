<?php
require_once '../auth.php';
require_once '../db.php';
checkLogin('admin');

ob_start(); // Start capturing content

// Fetch all billing records with JOINs
$query = "
SELECT 
    b.id,
    c.name AS client_name,
    t.service_name,
    b.date,
    b.amount,
    b.status,
    b.notes
FROM billings b
JOIN clients c ON b.client_id = c.id
JOIN billing_templates t ON b.service_id = t.id
ORDER BY b.date DESC
";

$result = $conn->query($query);
?>


<h2>Billing Records</h2>

<table class="table table-bordered table-striped mt-4">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Client</th>
            <th>Date</th>
            <th>Service</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Notes</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['client_name']) ?></td>
                    <td><?= $row['date'] ?></td>
                    <td><?= htmlspecialchars($row['service_name']) ?></td>
                    <td>₱<?= number_format($row['amount'], 2) ?></td>
                    <td>
                        <span class="badge bg-<?= $row['status'] === 'Paid' ? 'success' : 'danger' ?>">
                            <?= $row['status'] ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($row['notes']) ?></td>
                    <td>
                        <?php if ($row['status'] === 'Unpaid'): ?>
                            <a href="mark_paid.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Mark this invoice as paid?')">Mark as Paid</a>
                        <?php else: ?>
                            <span class="badge bg-success">Paid</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7" class="text-center">No billing records found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean(); // End capturing content

include '../base.php'; // Include layout
?>
