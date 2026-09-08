<?php

$statusFilter     = $_GET['status'] ?? 'All';
$schoolYearFilter = $_GET['school_year'] ?? 'All';
$openModal        = isset($_GET['status']) || isset($_GET['school_year']) || isset($_GET['page']);

$limit = 10;
$page  = max(1, (int)($_GET['page'] ?? 1));
$start = ($page - 1) * $limit;


/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/

$where = "WHERE 1=1";

if ($statusFilter !== "All") {
    $status = mysqli_real_escape_string($conn, $statusFilter);
    $where .= " AND d.status='$status'";
}

if ($schoolYearFilter !== "All") {
    $schoolYear = mysqli_real_escape_string($conn, $schoolYearFilter);
    $where .= " AND s.school_year='$schoolYear'";
}


/*
|--------------------------------------------------------------------------
| TOTAL TRANSACTIONS
|--------------------------------------------------------------------------
*/

$totalResult = mysqli_query($conn,"
    SELECT COUNT(*) AS total
    FROM document_request_monitoring d
    LEFT JOIN students s
        ON d.student_id=s.student_id
    $where
");

$totalTransactions = mysqli_fetch_assoc($totalResult)['total'];
$totalPages        = max(1, ceil($totalTransactions / $limit));


/*
|--------------------------------------------------------------------------
| STATUS COUNTS
|--------------------------------------------------------------------------
*/

function getStatusCount($conn,$status,$schoolYearFilter)
{
    $status = mysqli_real_escape_string($conn,$status);

    $where = "WHERE d.status='$status'";

    if($schoolYearFilter!="All"){

        $schoolYear = mysqli_real_escape_string($conn,$schoolYearFilter);
        $where .= " AND s.school_year='$schoolYear'";

    }

    $result = mysqli_query($conn,"
        SELECT COUNT(*) total
        FROM document_request_monitoring d
        LEFT JOIN students s
            ON d.student_id=s.student_id
        $where
    ");

    return mysqli_fetch_assoc($result)['total'] ?? 0;
}

$totalPending         = getStatusCount($conn,"Pending",$schoolYearFilter);
$totalVerifiedPayment = getStatusCount($conn,"Verified Payment",$schoolYearFilter);
$totalProcessing      = getStatusCount($conn,"Processing",$schoolYearFilter);
$totalSignature       = getStatusCount($conn,"For Signature",$schoolYearFilter);
$totalShipment        = getStatusCount($conn,"For Shipment",$schoolYearFilter);
$totalPickup          = getStatusCount($conn,"For Pick Up",$schoolYearFilter);
$totalReleased        = getStatusCount($conn,"Released",$schoolYearFilter);


/*
|--------------------------------------------------------------------------
| TOTAL COLLECTION
|--------------------------------------------------------------------------
*/

$collectionWhere = "WHERE 1=1";

if($schoolYearFilter!="All"){

    $schoolYear = mysqli_real_escape_string($conn,$schoolYearFilter);
    $collectionWhere .= " AND s.school_year='$schoolYear'";

}

$totalAmountResult = mysqli_query($conn,"
    SELECT SUM(d.total_amount) total_amount
    FROM document_request_monitoring d
    LEFT JOIN students s
        ON d.student_id=s.student_id
    $collectionWhere
");

$totalAmount = mysqli_fetch_assoc($totalAmountResult)['total_amount'] ?? 0;


/*
|--------------------------------------------------------------------------
| SCHOOL YEAR LIST
|--------------------------------------------------------------------------
*/

$schoolYears = mysqli_query($conn,"
    SELECT DISTINCT school_year
    FROM students
    ORDER BY school_year DESC
");


/*
|--------------------------------------------------------------------------
| STATUS ORDER
|--------------------------------------------------------------------------
*/

$order = [
    "Pending",
    "Verified Payment",
    "Processing",
    "For Signature",
    "For Shipment",
    "For Pick Up",
    "Released"
];


/*
|--------------------------------------------------------------------------
| TRANSACTION LIST
|--------------------------------------------------------------------------
*/

$query = mysqli_query($conn,"
    SELECT
        d.*,
        s.fullname,
        s.semester,
        s.school_year
    FROM document_request_monitoring d
    LEFT JOIN students s
        ON d.student_id=s.student_id
    $where
    ORDER BY d.id DESC
    LIMIT $start,$limit
");

?>