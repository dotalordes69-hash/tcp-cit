<?php
session_start();
include 'db.php';

header('Content-Type: application/json');


if($_SERVER['REQUEST_METHOD'] !== 'POST'){

    echo json_encode([
        "success"=>false,
        "message"=>"Invalid request"
    ]);

    exit;
}


$student_id = $_POST['student_id'] ?? '';
$released_date = $_POST['released_date'] ?? '';
$particular = $_POST['particular'] ?? '';

$released_by = $_SESSION['fullname'] ?? 'Unknown';


$stmt = $conn->prepare("
INSERT INTO docs_history
(
    student_id,
    particular,
    date_request,
    date_release,
    released_by
)
VALUES (?,?,?,?,?)
");


$date_release = null;



$stmt->bind_param(
"sssss",
$student_id,
$particular,
$date_request,
$date_release,
$released_by
);

if($stmt->execute()){

    echo json_encode([
        "success"=>true
    ]);

}else{

    echo json_encode([
        "success"=>false,
        "message"=>$conn->error
    ]);

}

$stmt->close();
$conn->close();