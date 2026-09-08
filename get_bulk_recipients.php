<?php

include 'db.php';


$school_year = $_GET['school_year'] ?? '';

$balance_only = $_GET['balance_only'] ?? 0;



$sql = "
SELECT 
    s.fullname,
    s.email_address,
    sa.balance

FROM students s

LEFT JOIN student_accounts sa
ON s.student_id = sa.student_id

WHERE s.email_address != ''
";



$params = [];



if($school_year != ''){

    $sql .= " AND s.school_year = ? ";

    $params[] = $school_year;

}



if($balance_only == 1){

    $sql .= " AND sa.balance > 0 ";

}



$sql .= "
ORDER BY s.fullname ASC
";



$stmt = $conn->prepare($sql);



if(count($params) > 0){

    $types = str_repeat("s", count($params));

    $stmt->bind_param(
        $types,
        ...$params
    );

}



$stmt->execute();


$result = $stmt->get_result();



if($result->num_rows == 0){

echo '

<div class="text-center text-muted py-5">

No students found.

</div>

';

exit;

}



while($row = mysqli_fetch_assoc($result)):

?>

<div class="student-item p-3 border-bottom">


<div class="student-name fw-bold">

<?=htmlspecialchars($row['fullname'])?>

</div>


<div class="student-email text-danger">

📧 <?=htmlspecialchars($row['email_address'])?>

</div>



<?php if($balance_only == 1): ?>

<div class="text-danger small mt-1">

Balance:
₱ <?=number_format($row['balance'] ?? 0,2)?>

</div>

<?php endif; ?>


</div>


<?php endwhile; ?>