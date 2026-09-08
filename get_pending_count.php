<?php
include 'db.php';

$row = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) total
    FROM payment_history
    WHERE gcash_status != 'verified'
"));

echo $row['total'];