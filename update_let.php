<?php

include 'db.php';


// =========================================================
// VALIDATE REQUEST
// =========================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: student_list.php");
    exit();
}


// =========================================================
// GET DATA
// =========================================================

$student_pk = (int)($_POST['student_pk'] ?? 0);

$student_id = trim($_POST['student_id'] ?? '');


// Checkbox:
// Checked   = 1
// Unchecked = 0
$let_exam = isset($_POST['let_exam']) ? 1 : 0;


// Examination Result:
// Passer     = 1
// Not Passer = 0
$let_status = isset($_POST['let_status'])
    ? (int)$_POST['let_status']
    : 0;


// Month and Year
$let_month = trim($_POST['let_month'] ?? '');

$let_year = trim($_POST['let_year'] ?? '');


// =========================================================
// VALIDATE STUDENT
// =========================================================

if ($student_pk <= 0) {
    die("Invalid student.");
}


if ($student_id === '') {
    die("Invalid student ID.");
}


// =========================================================
// VALIDATE LET STATUS
// =========================================================

// Only 1 or 0 is allowed
if ($let_status !== 0 && $let_status !== 1) {
    die("Invalid examination result.");
}


// =========================================================
// MONTH AND YEAR
// =========================================================

// If PASSER
// Month and Year are required.

if ($let_status === 1) {

    if (!in_array($let_month, ['March', 'September'], true)) {
        die("Invalid examination month.");
    }

    if ($let_year === '') {
        die("Please select examination year.");
    }

}


// If NOT PASSER
// No examination period is required.

else {

    $let_month = null;
    $let_year = null;

}


// =========================================================
// UPDATE LET INFORMATION
// =========================================================

$stmt = $conn->prepare("
    UPDATE students
    SET
        let_exam = ?,
        let_status = ?,
        let_month = ?,
        let_year = ?
    WHERE id = ?
    LIMIT 1
");


if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}


// =========================================================
// BIND PARAMETERS
// =========================================================

$stmt->bind_param(
    "iissi",
    $let_exam,
    $let_status,
    $let_month,
    $let_year,
    $student_pk
);


// =========================================================
// EXECUTE
// =========================================================

if (!$stmt->execute()) {

    die("Update failed: " . $stmt->error);

}


$stmt->close();


// =========================================================
// REDIRECT
// =========================================================

header(
    "Location: view_student.php?id=" .
    urlencode($student_pk)
);

exit();

?>