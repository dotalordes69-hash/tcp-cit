<?php
include 'db.php';

$student_id = $_GET['student_id'] ?? '';

$q = mysqli_query($conn,"
SELECT
    CONCAT(firstname,' ',middle_initial,' ',surname) fullname
    ,balance
FROM student_accounts sa
LEFT JOIN students s
ON sa.student_id = s.student_id
WHERE sa.student_id='$student_id'
LIMIT 1
");

echo json_encode(mysqli_fetch_assoc($q));