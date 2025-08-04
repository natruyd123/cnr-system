<?php
require_once '../auth.php';  // Session & role check
checkLogin('admin');
require_once '../base.php';  // Base layout (includes sidebar and header)
?>

<div class="container mt-4">
  <h2>Admin Dashboard</h2>
  <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
</div>
