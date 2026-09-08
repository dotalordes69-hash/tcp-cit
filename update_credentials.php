<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

$student_id = $_POST['student_id'] ?? '';
$checked_by = $_POST['checked_by'] ?? 'Unknown User';
$is_superadmin = ($_SESSION['role'] ?? '') === 'superadmin';

if (empty($student_id)) {
    die("Invalid student.");
}

$credentials = $_POST['credentials'] ?? [];
$submitted_dates = $_POST['submitted_date'] ?? [];

/*
|--------------------------------------------------------------------------
| Ensure credentials row exists
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("SELECT * FROM student_credentials WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    $insert = $conn->prepare("INSERT INTO student_credentials (student_id) VALUES (?)");
    $insert->bind_param("s", $student_id);
    $insert->execute();
    $insert->close();

    $stmt = $conn->prepare("SELECT * FROM student_credentials WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
}

/*
|--------------------------------------------------------------------------
| Update credentials
|--------------------------------------------------------------------------
*/
foreach ($row as $column => $value) {

    if (
        $column === 'id' ||
        $column === 'student_id' ||
        str_ends_with($column, '_date') ||
        str_ends_with($column, '_by')
    ) {
        continue;
    }

    $date_col = $column . '_date';
    $by_col   = $column . '_by';

    if (in_array($column, $credentials)) {

        $date_value = !empty($submitted_dates[$column])
            ? $submitted_dates[$column]
            : date('Y-m-d');

        $stmt2 = $conn->prepare("
            UPDATE student_credentials
            SET `$column` = 1,
                `$date_col` = ?,
                `$by_col` = ?
            WHERE student_id = ?
        ");

        $stmt2->bind_param(
            "sss",
            $date_value,
            $checked_by,
            $student_id
        );

        $stmt2->execute();
        $stmt2->close();

    } elseif ($is_superadmin) {

        // Superadmin can uncheck/reset

        $stmt2 = $conn->prepare("
            UPDATE student_credentials
            SET `$column` = 0,
                `$date_col` = NULL,
                `$by_col` = NULL
            WHERE student_id = ?
        ");

        $stmt2->bind_param("s", $student_id);
        $stmt2->execute();
        $stmt2->close();
    }
}
 /*
|--------------------------------------------------------------------------
| Update students.status_credentials
|--------------------------------------------------------------------------
*/

$check_complete = $conn->prepare("
    SELECT
        tor_informative_copy,
        gmc,
        ctc_request,
        account_card,
        ctc_tor_granted,
        psa_birth_marriage_certificate,
        photo_2x2_hardcopy,
        photo_2x2_softcopy
    FROM student_credentials
    WHERE student_id = ?
");

$check_complete->bind_param("s", $student_id);
$check_complete->execute();

$cred = $check_complete->get_result()->fetch_assoc();

$check_complete->close();

$status_credentials = (
    ($cred['tor_informative_copy'] ?? 0) == 1 &&
    ($cred['gmc'] ?? 0) == 1 &&
    ($cred['ctc_request'] ?? 0) == 1 &&
    ($cred['account_card'] ?? 0) == 1 &&
    ($cred['ctc_tor_granted'] ?? 0) == 1 &&
    ($cred['psa_birth_marriage_certificate'] ?? 0) == 1 &&
    ($cred['photo_2x2_hardcopy'] ?? 0) == 1 &&
    ($cred['photo_2x2_softcopy'] ?? 0) == 1
) ? 1 : 0;

$update_student = $conn->prepare("
    UPDATE students
    SET status_credentials = ?
    WHERE student_id = ?
");

$update_student->bind_param(
    "is",
    $status_credentials,
    $student_id
);

$update_student->execute();
$update_student->close();
/*
|--------------------------------------------------------------------------
| Redirect properly (IMPORTANT FIX)
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("SELECT id FROM students WHERE student_id = ? LIMIT 1");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Student not found.");
}
header('Content-Type: application/json');

echo json_encode([
    'success' => true
]);

exit();