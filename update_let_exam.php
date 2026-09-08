<?php

include 'db.php';
session_start();

header('Content-Type: application/json');

$student_id = $_POST['student_id'] ?? '';
$let_exam   = (int)($_POST['let_exam'] ?? 0);

$stmt = $conn->prepare("
    UPDATE students
    SET let_exam = ?
    WHERE student_id = ?
");

$stmt->bind_param(
    "is",
    $let_exam,
    $student_id
);

if($stmt->execute()){

    echo json_encode([
        'success' => true
    ]);

}else{

    echo json_encode([
        'success' => false,
        'message' => 'Unable to save.'
    ]);

}