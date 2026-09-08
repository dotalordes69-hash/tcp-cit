<?php
include 'db.php';

header('Content-Type: application/json');

$student_id = trim($_GET['student_id'] ?? '');

if ($student_id === '') {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT
        s.student_id,
        s.fullname,
        s.section,
        s.semester,
        s.school_year,
        s.contact_no,
        s.email_address,
        COALESCE(a.balance,0) AS balance,
        COALESCE(a.registration_fee,0) AS registration_fee
    FROM students s
    LEFT JOIN student_accounts a
        ON a.student_id = s.student_id
    WHERE s.student_id = ?
    LIMIT 1
");

$stmt->bind_param("s", $student_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();

    echo json_encode($row);

} else {

    echo json_encode([]);

}

$stmt->close();
$conn->close();

?>