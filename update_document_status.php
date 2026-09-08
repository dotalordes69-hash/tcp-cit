<?php
session_start();
include 'db.php';

date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    exit("Invalid request.");
}

$id = intval($_POST['id']);
$status = mysqli_real_escape_string($conn, $_POST['status']);
$or_number = mysqli_real_escape_string($conn, trim($_POST['or_number'] ?? ""));

// Save OR Number first if provided
if($or_number != ""){
    mysqli_query($conn,"
        UPDATE document_request_monitoring
        SET or_number='$or_number'
        WHERE id='$id'
    ");
}

// Get latest record
$result = mysqli_query($conn,"
SELECT
    status,
    or_number,
    gcash_verified,
    delivery_mode
FROM document_request_monitoring
WHERE id='$id'
");

if(mysqli_num_rows($result)==0){
    exit("Record not found.");
}

$row=mysqli_fetch_assoc($result);

$currentStatus=$row['status'];
$gcashVerified=$row['gcash_verified'];
$deliveryMode=$row['delivery_mode'];

if($status=="Processing"){

    if(trim($row['or_number'])==""){
        exit("Please enter OR Number first.");
    }

    if($gcashVerified!=1){
        exit("Please verify GCash payment first.");
    }

}

if($status=="Released"){

    $released=date("Y-m-d H:i:s");

    $sql="
    UPDATE document_request_monitoring
    SET
        status='$status',
        released_date='$released'
    WHERE id='$id'
    ";

}else{

    $sql="
    UPDATE document_request_monitoring
    SET
        status='$status'
    WHERE id='$id'
    ";

}

if(mysqli_query($conn,$sql)){

    echo "success";

}else{

    echo mysqli_error($conn);

}
?>