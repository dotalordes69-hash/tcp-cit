<?php
include __DIR__ . '/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = intval($_POST['student_id']);
    $charges = floatval($_POST['charges']);

    if ($charges <= 0) {
        echo "Invalid charge.";
        exit;
    }

    // Update total_amount in student_accounts
    $check = mysqli_query($conn, "SELECT total_amount FROM student_accounts WHERE student_id = $student_id");
    $account = mysqli_fetch_assoc($check);

    if ($account) {
        $new_total = floatval($account['total_amount']) + $charges;
        mysqli_query($conn, "UPDATE student_accounts SET total_amount = $new_total WHERE student_id = $student_id");
        echo "Updated total_amount to $new_total";
    } else {
        // If no account exists yet, create one
        $new_total = 10800 + $charges;
        mysqli_query($conn, "INSERT INTO student_accounts (student_id, total_amount, total_paid) VALUES ($student_id, $new_total, 0)");
        echo "Created new account with total_amount $new_total";
    }
}
?>