<?php
include __DIR__ . '/db.php';
session_start();

if (!isset($_SESSION['fullname'])) exit('error');

if(isset($_POST['student_id'], $_POST['charges'])) {
    $student_id = intval($_POST['student_id']);
    $charges = floatval($_POST['charges']);

    // Update total_amount and balance in student_accounts
    $sql = "UPDATE student_accounts 
            SET total_amount = total_amount + ?, 
                balance = balance + ? 
            WHERE student_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ddi", $charges, $charges, $student_id);
    if(mysqli_stmt_execute($stmt)) {
        echo 'success';
    } else {
        echo 'error';
    }
    mysqli_stmt_close($stmt);
}
?>