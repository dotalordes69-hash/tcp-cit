<?php

include '../db.php';
include 'student_filters.php';

$sql = "
SELECT *
FROM students
$where
ORDER BY fullname ASC
";

$result = mysqli_query($conn, $sql);

$filename = "Student_Records_" . date("Y-m-d_H-i-s") . ".csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

/* CSV HEADER */
fputcsv($output, [
    'Student ID',
    'Full Name',
    'Email Address',
    'Contact Number',
    'Semester',
    'School Year',
    'Section'
]);

/* DATA */
while($row = mysqli_fetch_assoc($result)){

    fputcsv($output, [
        $row['student_id'],
        $row['fullname'],
        $row['email_address'],
        $row['contact_no'],
        $row['semester'],
        $row['school_year'],
        $row['section']
    ]);

}

fclose($output);
exit;