<?php
include 'db.php';

$reference = trim($_GET['reference'] ?? '');

$stmt = $conn->prepare("
SELECT
    p.*,
    s.fullname,
    s.semester,
    s.school_year,
    s.section
FROM payment_history p
LEFT JOIN students s
    ON s.student_id = p.student_id
WHERE p.gcash_ref = ?
LIMIT 1
");

$stmt->bind_param("s", $reference);
$stmt->execute();

$result = $stmt->get_result();

if($row = $result->fetch_assoc()){
    echo json_encode($row);
}else{
    echo json_encode([]);
}

