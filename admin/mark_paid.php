<?php
require_once '../db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "UPDATE billings SET status = 'Paid' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back to the page you came from
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        echo "Error updating status.";
    }
} else {
    echo "Invalid ID.";
}
?>
