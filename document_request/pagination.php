<?php

// total records
$countQuery = "
SELECT COUNT(*) AS total 
FROM document_request_monitoring
";

$countResult = mysqli_query($conn,$countQuery);

$totalRow = mysqli_fetch_assoc($countResult);

$totalRecords = $totalRow['total'];


// records per page
$limit = 50;


// current page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

if($page < 1){
    $page = 1;
}


// offset
$offset = ($page - 1) * $limit;


// total pages
$totalPages = ceil($totalRecords / $limit);



?>
