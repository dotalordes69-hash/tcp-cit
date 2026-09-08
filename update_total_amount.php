<?php
include 'db.php';

header('Content-Type: application/json');

$student_id = $_POST['student_id'];
$extra_fee = floatval($_POST['extra_fee']);

if ($extra_fee <= 0) {
    echo json_encode(["status"=>"error","message"=>"Invalid fee"]);
    exit;
}

/* 
UPDATE BOTH:
- total_amount (increase tuition)
- balance
- extra_fee (SAVE HISTORY)
*/

$stmt = $conn->prepare("
UPDATE student_accounts 
SET 
    total_amount = total_amount + ?,
    balance = balance + ?,
    extra_fee = IFNULL(extra_fee,0) + ?
WHERE student_id = ?
");

$stmt->bind_param("ddds", $extra_fee, $extra_fee, $extra_fee, $student_id);
$stmt->execute();

/* GET UPDATED DATA */
$get = $conn->prepare("
    SELECT total_amount, balance, extra_fee 
    FROM student_accounts 
    WHERE student_id = ?
");
$get->bind_param("s", $student_id);
$get->execute();
$res = $get->get_result()->fetch_assoc();

echo json_encode([
    "status" => "success",
    "new_total_amount" => $res['total_amount'],
    "new_balance" => $res['balance'],
    "extra_fee" => $res['extra_fee']
]);
?>