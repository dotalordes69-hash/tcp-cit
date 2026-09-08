<?php
session_start();
include 'db.php';

$id = (int)$_GET['id'];

$verified_by = $_SESSION['fullname'];

mysqli_query($conn,"
UPDATE payment_history
SET
    gcash_status='verified',
    verified_by='$verified_by'
WHERE id='$id'
");