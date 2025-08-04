<?php
require_once '../auth.php';
require_once '../db.php';
checkLogin('admin');

ob_start(); // Start capturing content

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $name = $_POST['service_name'];
    $desc = $_POST['description'];
    $amount = $_POST['amount'];

    $stmt = $conn->prepare("INSERT INTO billing_templates (service_name, description, amount) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $name, $desc, $amount);
    $stmt->execute();
    header("Location: billing_template.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM billing_templates WHERE id = $id");
    header("Location: billing_template.php");
    exit();
}

// Fetch services
$result = $conn->query("SELECT * FROM billing_templates ORDER BY created_at DESC");
?>

<h2>Billing Templates</h2>

<!-- Button trigger modal -->
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Add Service</button>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Service</th>
            <th>Description</th>
            <th>Amount (₱)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['service_name']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><?= number_format($row['amount'], 2) ?></td>
            <td>
                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this service?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Billing Service</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Service Name</label>
          <input type="text" name="service_name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Description</label>
          <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
          <label>Amount (₱)</label>
          <input type="number" name="amount" class="form-control" step="0.01" required>
        </div>
        <input type="hidden" name="add_service" value="1">
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary">Add Service</button>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean(); // End capturing

include '../base.php'; // Include layout
?>
