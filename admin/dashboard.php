<?php
require_once '../auth.php';
require_once '../db.php';
checkLogin('admin');

ob_start(); // Start capturing content
?>


<div class="container">
  <h2 class="mb-4">Admin Dashboard</h2>

  <div class="row text-center mb-4">
    <?php
      // Total Clients
      $clients = $conn->query("SELECT COUNT(*) AS total FROM clients")->fetch_assoc()['total'];

      // Total Billings
      $billings = $conn->query("SELECT COUNT(*) AS total FROM billings")->fetch_assoc()['total'];

      // Total Billed
      $billed = $conn->query("SELECT SUM(amount) AS total FROM billings")->fetch_assoc()['total'] ?? 0;

      // Total Paid
      $paid = $conn->query("SELECT SUM(amount) AS total FROM billings WHERE status = 'Paid'")->fetch_assoc()['total'] ?? 0;
    ?>
    <div class="col-md-3">
      <div class="card shadow-sm p-3">
        <h5>Total Clients</h5>
        <p class="fs-4"><?= $clients ?></p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm p-3">
        <h5>Total Billings</h5>
        <p class="fs-4"><?= $billings ?></p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm p-3">
        <h5>Total Billed</h5>
        <p class="fs-4">₱<?= number_format($billed, 2) ?></p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm p-3">
        <h5>Total Paid</h5>
        <p class="fs-4 text-success">₱<?= number_format($paid, 2) ?></p>
      </div>
    </div>
  </div>

  <div class="card mt-4">
    <div class="card-header">
      Recent Billing Activity
    </div>
    <div class="card-body">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>Date</th>
            <th>Client</th>
            <th>Amount</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $activities = $conn->query("
              SELECT b.date, c.name AS client, b.amount, b.status
              FROM billings b
              JOIN clients c ON b.client_id = c.id
              ORDER BY b.date DESC
              LIMIT 5
            ");
            while ($row = $activities->fetch_assoc()):
          ?>
          <tr>
            <td><?= $row['date'] ?></td>
            <td><?= $row['client'] ?></td>
            <td>₱<?= number_format($row['amount'], 2) ?></td>
            <td>
              <?php if ($row['status'] === 'Paid'): ?>
                <span class="badge bg-success">Paid</span>
              <?php else: ?>
                <span class="badge bg-warning text-dark">Unpaid</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>


<?php
$content = ob_get_clean(); // End capturing content

include '../base.php'; // Include layout
?>
