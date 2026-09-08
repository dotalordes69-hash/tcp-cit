<?php
include 'db.php';

header('Content-Type: application/json');

$fees = [];

$query = mysqli_query($conn, "
    SELECT
        fee_key,
        amount
    FROM document_fees
");

while ($row = mysqli_fetch_assoc($query)) {

    $fees[$row['fee_key']] = (float)$row['amount'];

}

echo json_encode($fees);