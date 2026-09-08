<?php
session_start();
include 'db.php';

$id = (int)$_POST['id'];

$verified_by =
    $_SESSION['name']
    ?? $_SESSION['fullname']
    ?? $_SESSION['username'];

$stmt = $conn->prepare("
UPDATE payment_history
SET gcash_status='Verified',
    verified_by=?
WHERE id=?
");

$stmt->bind_param("si",$verified_by,$id);
$stmt->execute();

echo 'success';