<?php
session_start();

if (!isset($_SESSION['login_id'])) {
    echo json_encode([
        "status"=>"error",
        "message"=>"Unauthorized"
    ]);
    exit;
}

include 'db.php';


$id = $_POST['id'] ?? '';
$status = $_POST['status'] ?? '';
$tracking = $_POST['tracking'] ?? '';
$or_number = $_POST['or_number'] ?? '';



if($status == "Released"){

    $sql = "
    UPDATE document_request_monitoring
    SET 
        status=?,
        tracking_number=?,
        or_number=?,
        released_date=NOW()
    WHERE id=?
    ";

}else{

    $sql = "
    UPDATE document_request_monitoring
    SET 
        status=?,
        tracking_number=?,
        or_number=?
    WHERE id=?
    ";

}



$stmt = $conn->prepare($sql);



if($status == "Released"){

    $stmt->bind_param(
        "sssi",
        $status,
        $tracking,
        $or_number,
        $id
    );


}else{

    $stmt->bind_param(
        "sssi",
        $status,
        $tracking,
        $or_number,
        $id
    );

}



if($stmt->execute()){

    echo json_encode([
        "status"=>"success",
        "message"=>"Updated successfully"
    ]);

}else{

    echo json_encode([
        "status"=>"error",
        "message"=>$stmt->error
    ]);

}

?>