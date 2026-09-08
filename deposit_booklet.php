<?php
session_start();
include 'db.php';

$id = (int)$_GET['id'];

$deposited_by =
    $_SESSION['name']
    ?? $_SESSION['fullname']
    ?? $_SESSION['username']
    ?? 'System';

$stmt = $conn->prepare("
    UPDATE or_booklets
    SET
        bk_status='Deposited',
        deposited_date=NOW(),
        deposited_by=?
    WHERE id=?
");

$stmt->bind_param("si", $deposited_by, $id);
$stmt->execute();

echo "success";