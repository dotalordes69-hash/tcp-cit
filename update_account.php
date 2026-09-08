<?php
include __DIR__ . '/db.php';
session_start();
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['fullname'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $student_id = trim($_POST['student_id'] ?? '');
    $student_id_safe = mysqli_real_escape_string($conn, $student_id);

    $amount_paid = floatval($_POST['amount_paid'] ?? 0);
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

    // Manual inputs
    $payment_date = !empty($_POST['payment_date']) ? $_POST['payment_date'] : null;
    $or_number = !empty($_POST['or_number']) ? trim($_POST['or_number']) : null;
    $gcash_ref = !empty($_POST['gcash_ref']) ? trim($_POST['gcash_ref']) : null;
    $particular = !empty($_POST['particular']) ? trim($_POST['particular']) : null;

    // Added charges
    $added_charges = floatval($_POST['added_charges'] ?? 0);

    $received_by = $_SESSION['fullname'];

    // Validation
    if (empty($student_id)) {
        die("Student ID is required.");
    }

    if ($amount_paid < 0) {
        die("Invalid payment amount.");
    }

    if (!$particular) {
        die("Please select a Particular.");
    }

    if ($amount_paid > 0 && (!$payment_date || !$or_number)) {
        die("Payment date and OR number are required.");
    }

    // Fetch student account
    $check = mysqli_query(
        $conn,
        "SELECT * FROM student_accounts WHERE student_id = '$student_id_safe' LIMIT 1"
    );

    $account = mysqli_fetch_assoc($check);

    if ($account) {

        $current_total = floatval($account['total_amount']);
        $current_paid  = floatval($account['total_paid']);

        $new_total_amount = $current_total + $added_charges;
        $new_total_paid   = $current_paid + $amount_paid;
        $new_balance      = $new_total_amount - $new_total_paid;

        $sql = "
            UPDATE student_accounts
            SET
                total_amount = '$new_total_amount',
                total_paid   = '$new_total_paid',
                balance      = '$new_balance',
                due_date     = " . ($due_date ? "'$due_date'" : "NULL") . "
            WHERE student_id = '$student_id_safe'
        ";

        mysqli_query($conn, $sql);

    } else {

        $new_total_amount = 10800 + $added_charges;
        $new_total_paid   = $amount_paid;
        $new_balance      = $new_total_amount - $new_total_paid;

        $sql = "
            INSERT INTO student_accounts
            (
                student_id,
                total_amount,
                total_paid,
                balance,
                due_date
            )
            VALUES
            (
                '$student_id_safe',
                '$new_total_amount',
                '$new_total_paid',
                '$new_balance',
                " . ($due_date ? "'$due_date'" : "NULL") . "
            )
        ";

        mysqli_query($conn, $sql);
    }

    // Insert payment history
    if ($amount_paid > 0) {

        $status = "Validating";
        $extra_fee = $added_charges;
        $verified_by = '';

        $stmt = mysqli_prepare(
            $conn,
            "
            INSERT INTO payment_history
            (
                student_id,
                payment_date,
                or_number,
                gcash_ref,
                particular,
                amount_paid,
                extra_fee,
                received_by,
                gcash_status,
                verified_by
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            "
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sssssddsss",
            $student_id,
            $payment_date,
            $or_number,
            $gcash_ref,
            $particular,
            $amount_paid,
            $extra_fee,
            $received_by,
            $status,
            $verified_by
        );

        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_error($stmt)) {
            die("Payment History Error: " . mysqli_stmt_error($stmt));
        }           

        mysqli_stmt_close($stmt);
    }

    // Get students.id for redirect
   $getStudent = mysqli_query(
    $conn,
    "SELECT id, student_id
     FROM students
     WHERE student_id = '$student_id_safe'
     LIMIT 1"
);

if (!$getStudent) {
    die(mysqli_error($conn));
}

if (mysqli_num_rows($getStudent) == 0) {
    die("Student not found. student_id = " . $student_id_safe);
}


    header("Location: view_student.php?id=" . $student_pk);
    exit();
}
?>
