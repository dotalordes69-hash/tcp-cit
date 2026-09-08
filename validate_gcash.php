<?php

include __DIR__ . '/db.php';
session_start();


if (!isset($_SESSION['fullname'])) {
    header("Location: login.php");
    exit();
}



if (isset($_GET['type']) && $_GET['type']=="document") {


    $id = intval($_GET['id']);


    $stmt = mysqli_prepare(
        $conn,
        "
        UPDATE document_request_monitoring
        SET 
        gcash_verified = 1,
        status = 'Verified Payment'
        WHERE id = ?
        "
    );


    mysqli_stmt_bind_param($stmt,"i",$id);

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);



    header("Location: view_document_transactions.php");

    exit();


}




if (isset($_GET['id'])) {


    $payment_id = intval($_GET['id']);


    // Existing student payment validation
    $stmt = mysqli_prepare(
        $conn,
        "
        UPDATE student_payments 
        SET gcash_status = 'Validated' 
        WHERE id = ?
        "
    );


    mysqli_stmt_bind_param($stmt, "i", $payment_id);

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);



    $res = mysqli_query(
        $conn,
        "SELECT student_id 
         FROM student_payments 
         WHERE id = $payment_id"
    );


    $student_id = mysqli_fetch_assoc($res)['student_id'] ?? 0;



    header("Location: view_student.php?id=".$student_id);

    exit();

}


?>