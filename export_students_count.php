<?php
include 'db.php';

$semester = $_GET['semester'] ?? '';
$section = $_GET['section'] ?? '';

$where = "WHERE 1=1";

if($semester !== '') {
    $semester_safe = mysqli_real_escape_string($conn, $semester);
    $where .= " AND semester = '$semester_safe'";
}

if($section !== '') {
    $section_safe = mysqli_real_escape_string($conn, $section);
    $where .= " AND section = '$section_safe'";
}

$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM students $where");
$row = mysqli_fetch_assoc($result);

echo json_encode(['count' => (int)$row['total']]);
