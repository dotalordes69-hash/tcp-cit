<?php
include __DIR__ . '/db.php';
session_start();

$semester = $_GET['semester'] ?? '';
$section = $_GET['section'] ?? '';

$where = "WHERE 1=1";
$params = [];
$types = '';

if ($semester !== '') {
    $where .= " AND semester = ?";
    $params[] = $semester;
    $types .= 's';
}

if ($section !== '') {
    $where .= " AND section = ?";
    $params[] = $section;
    $types .= 's';
}

$stmt = mysqli_prepare($conn, "SELECT student_id, fullname, contact_no, email_address, semester, section FROM students $where ORDER BY id ASC");
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=students_export_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Student ID', 'Fullname', 'Contact #', 'Email Address', 'Semester', 'Section']);

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [
        $row['student_id'],
        $row['fullname'],
        $row['contact_no'],
        $row['email_address'],
        $row['semester'],
        $row['section']
    ]);
}

fclose($output);
mysqli_stmt_close($stmt);
exit();
