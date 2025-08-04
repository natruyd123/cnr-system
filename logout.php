<?php
session_start();
session_unset();
session_destroy();
header("Location: /cnr-system/login.php");
exit();
