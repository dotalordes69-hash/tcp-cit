<?php
/* ADD STUDENT */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {

    $student_id    = trim($_POST['student_id'] ?? '');
    $fullname      = trim($_POST['fullname'] ?? '');
    $contact_no    = trim($_POST['contact_no'] ?? '');
    $email_address = trim($_POST['email_address'] ?? '');
    $semester      = trim($_POST['semester'] ?? '');
    $school_year   = trim($_POST['school_year'] ?? '');
    $section       = trim($_POST['section'] ?? '');
    $total_amount  = floatval($_POST['total_amount'] ?? 0);
  

    // SAFE DEFAULT EMAIL FIX
    if ($email_address === '') {
        $email_address = 'N/A';
    }

    if ($student_id && $fullname) {

$status_type = "Unenrolled";

$stmt = mysqli_prepare($conn, "
    INSERT INTO students
    (student_id, fullname, contact_no, email_address, semester, school_year, section, status_type)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

mysqli_stmt_bind_param(
    $stmt,
    "ssssssss",
    $student_id,
    $fullname,
    $contact_no,
    $email_address,
    $semester,
    $school_year,
    $section,
    $status_type
);

mysqli_stmt_execute($stmt);


            // CREATE ACCOUNT
            $balance = $total_amount;

            $stmt_acc = mysqli_prepare($conn, "
                INSERT INTO student_accounts
                (student_id, total_amount, total_paid, balance)
                VALUES (?, ?, 0, ?)
            ");

            mysqli_stmt_bind_param(
                $stmt_acc,
                "sdd",
                $student_id,
                $total_amount,
                $balance
            );

mysqli_stmt_execute($stmt_acc);
mysqli_stmt_close($stmt_acc);

$_SESSION['student_saved'] = true;

header("Location: student_list.php");
exit();


   

        } else {
            die("Student insert failed: " . mysqli_error($conn));
        }
    }


?>
