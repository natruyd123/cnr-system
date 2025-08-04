<?php
if (!isset($_SESSION)) session_start();

if (!function_exists('checkLogin')) {
    function checkLogin($requiredRole = null) {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            header("Location: ../login.php");
            exit();
        }

        if ($requiredRole && $_SESSION['role'] !== $requiredRole) {
            switch ($_SESSION['role']) {
                case 'superadmin':
                    header("Location: /cnr_system/superadmin/dashboard.php");
                    break;
                case 'admin':
                    header("Location: /cnr_system/admin/dashboard.php");
                    break;
                case 'client':
                    header("Location: /cnr_system/client/dashboard.php");
                    break;
            }
            exit();
        }
    }
}
