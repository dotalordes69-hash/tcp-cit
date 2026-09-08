<?php

include "db.php";


$data = json_decode(file_get_contents("php://input"), true);


if(empty($data)){

    echo json_encode([
        "status"=>"error",
        "message"=>"No data received"
    ]);

    exit;

}


$stmt = $conn->prepare("
    UPDATE document_fees
    SET amount = ?
    WHERE document_name = ?
");


foreach($data as $row){

    $document_name = trim($row['document_name']);
    $amount = floatval($row['amount']);


    $stmt->bind_param(
        "ds",
        $amount,
        $document_name
    );


    $stmt->execute();

}


$stmt->close();
$conn->close();


echo json_encode([
    "status"=>"success",
    "message"=>"Document fees updated successfully"
]);

?>