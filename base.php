<!-- base.php -->
<?php
if (!isset($_SESSION)) session_start();
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CNR System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <link rel="stylesheet" href="/cnr_system/assets/custom.css">
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-3 col-lg-2 bg-light">
        <?php
          if ($_SESSION['role'] === 'superadmin') {
              include 'includes/sidebar_superadmin.php';
          } elseif ($_SESSION['role'] === 'admin') {
              include 'includes/sidebar_admin.php';
          } elseif ($_SESSION['role'] === 'client') {
              include 'includes/sidebar_client.php';
          }
        ?>
      </div>

      <!-- Main Content -->
      <div class="col-md-9 col-lg-10 p-4">
        <?php include 'includes/header.php'; ?>
      </div> <!-- end of main content -->
    </div>
  </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>
</html>
