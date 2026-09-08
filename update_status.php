<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = intval($_POST['id']);
    $verified_by = $_SESSION['fullname'] ?? 'Admin';

    // ================= GET PAYMENT =================
    $stmt = $conn->prepare("SELECT student_id, particular FROM payment_history WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment = $result->fetch_assoc();
    $stmt->close();

    if (!$payment) {
        echo "error";
        exit;
    }

    $student_id = $payment['student_id'];
    $particular = $payment['particular'];

    // ================= UPDATE STATUS =================
    $stmt = $conn->prepare("
        UPDATE payment_history
        SET gcash_status = 'verified',
            verified_by = ?
        WHERE id = ?
    ");
    $stmt->bind_param("si", $verified_by, $id);
    $success = $stmt->execute();
    $stmt->close();

    // ================= IF REGISTRATION FEE =================
    if ($success && $particular === 'Registration Fee') {

        // UPDATE student_accounts
        $stmt = $conn->prepare("
            UPDATE student_accounts
            SET registration_fee = 1
            WHERE student_id = ?
        ");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $stmt->close();

        // UPDATE students table -> OFFICIAL
        $stmt = $conn->prepare("
            UPDATE students
            SET status_type = 'official'
            WHERE student_id = ?
        ");

        
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $stmt->close();
    }

    echo $success ? "success" : "error";
}
?>