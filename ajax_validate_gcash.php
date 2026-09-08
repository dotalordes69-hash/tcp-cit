<?php
include __DIR__ . '/db.php';
session_start();

if (!isset($_SESSION['fullname'])) {
    exit('error');
}

if (isset($_POST['id'])) {
    $payment_id = intval($_POST['id']);

    $stmt = mysqli_prepare($conn, "UPDATE student_payments SET gcash_status = 'Validated' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $payment_id);
    $stmt->execute();
    $stmt->close();

    echo 'success';
}
?>