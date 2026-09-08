<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

$signature_name = trim($_POST['signature_name'] ?? '');
$signature_content = trim($_POST['signature_content'] ?? '');

if ($signature_name === '' || $signature_content === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please fill in all fields.'
    ]);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO email_signatures
    (
        signature_name,
        signature_content
    )
    VALUES
    (?, ?)
");

$stmt->bind_param(
    "ss",
    $signature_name,
    $signature_content
);

if ($stmt->execute()) {

    echo json_encode([
        'status' => 'success',
        'id' => $conn->insert_id,
        'message' => 'Signature saved successfully.'
    ]);

} else {

    echo json_encode([
        'status' => 'error',
        'message' => 'Database error.'
    ]);
}

$stmt->close();
$conn->close();