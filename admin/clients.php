<?php
require_once '../auth.php';
checkLogin('admin');

require_once '../db.php'; // Database connection

ob_start(); // Start capturing content

// Handle client addition
if (isset($_POST['add_client'])) {
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $phone   = $_POST['phone'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO clients (name, email, phone, address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $address);

    if ($stmt->execute()) {
        header("Location: clients.php"); // refresh page
        exit();
    } else {
        echo "<div class='alert alert-danger'>Failed to add client.</div>";
    }
}

// Edit client logic
if (isset($_POST['edit_client'])) {
    $id = $_POST['client_id'];
    $name = $_POST['edit_name'];
    $email = $_POST['edit_email'];
    $phone = $_POST['edit_phone'];
    $address = $_POST['edit_address'];

    $stmt = $conn->prepare("UPDATE clients SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $name, $email, $phone, $address, $id);

    if ($stmt->execute()) {
        header("Location: clients.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Failed to update client.</div>";
    }
}

// Delete client logic
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: clients.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Failed to delete client.</div>";
    }
}

?>

<!-- Content for admin/clients.php -->
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Clients</h2>
        <!-- Add Client Button -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addClientModal">
          + Add Client
        </button>
    </div>

    <!-- Static Clients Table -->
    <table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Phone</th>
      <th>Address</th>
      <th>Created At</th>
    </tr>
  </thead>
  <tbody>
        <?php
        $query = "SELECT * FROM clients";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0):
        ?>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['phone']) ?></td>
          <td><?= htmlspecialchars($row['address']) ?></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
          <td>
          <!-- View Statement Button -->
          <a href="client_statement.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">View Statement</a>

          <!-- Edit Button -->
          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editClientModal<?= $row['id'] ?>">Edit</button>

          <!-- Delete Button -->
          <a href="clients.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this client?');">Delete</a>
          </td>
        </tr>
        <!-- Edit Modal -->
        <div class="modal fade" id="editClientModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editClientModalLabel<?= $row['id'] ?>" aria-hidden="true">
          <div class="modal-dialog">
            <form method="POST" action="">
              <input type="hidden" name="client_id" value="<?= $row['id'] ?>">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Client</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="edit_name" value="<?= $row['name'] ?>" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="edit_email" value="<?= $row['email'] ?>">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="edit_phone" value="<?= $row['phone'] ?>">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="edit_address"><?= $row['address'] ?></textarea>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" name="edit_client" class="btn btn-success">Update</button>
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <?php endwhile; ?>

        <?php else: ?>  
      <tr><td colspan="6">No clients found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
</div>

<!-- Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Client</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">Client Name</label>
            <input type="text" class="form-control" name="name" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" name="email">
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone">
          </div>
          <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" name="address" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="add_client" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean(); // End capturing

include '../base.php'; // Include layout
?>
