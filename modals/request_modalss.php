<style>

    .transaction-modal{
    max-width:98vw;
    width:98vw;
}

.transaction-modal .modal-content{
    min-height:90vh;
}

</style>
<?php

$statusFilter     = $_GET['status'] ?? 'All';
$schoolYearFilter = $_GET['school_year'] ?? 'All';
$openModal        = isset($_GET['status']) || isset($_GET['school_year']) || isset($_GET['page']);

$limit = 10;
$page  = max(1, (int)($_GET['page'] ?? 1));
$start = ($page - 1) * $limit;


/* ============================
   FILTER
============================ */

$where = "WHERE 1=1";

if ($statusFilter !== "All") {
    $status = mysqli_real_escape_string($conn, $statusFilter);
    $where .= " AND d.status = '$status'";
}

if ($schoolYearFilter !== "All") {
    $schoolYear = mysqli_real_escape_string($conn, $schoolYearFilter);
    $where .= " AND s.school_year = '$schoolYear'";
}


/* ============================
   TOTAL TRANSACTIONS
============================ */

$totalResult = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM document_request_monitoring d
    LEFT JOIN students s
        ON d.student_id = s.student_id
    $where
");

$totalTransactions = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = max(1, ceil($totalTransactions / $limit));


/* ============================
   STATUS COUNTS
============================ */

function getStatusCount($conn, $status, $schoolYearFilter)
{
    $status = mysqli_real_escape_string($conn, $status);

    $where = "WHERE d.status = '$status'";

    if ($schoolYearFilter !== "All") {
        $schoolYear = mysqli_real_escape_string($conn, $schoolYearFilter);
        $where .= " AND s.school_year = '$schoolYear'";
    }

    $result = mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM document_request_monitoring d
        LEFT JOIN students s
            ON d.student_id = s.student_id
        $where
    ");

    return mysqli_fetch_assoc($result)['total'] ?? 0;
}

$totalPending          = getStatusCount($conn, "Pending", $schoolYearFilter);
$totalVerifiedPayment  = getStatusCount($conn, "Verified Payment", $schoolYearFilter);
$totalProcessing       = getStatusCount($conn, "Processing", $schoolYearFilter);
$totalSignature        = getStatusCount($conn, "For Signature", $schoolYearFilter);
$totalShipment         = getStatusCount($conn, "For Shipment", $schoolYearFilter);
$totalPickup           = getStatusCount($conn, "For Pick Up", $schoolYearFilter);
$totalReleased         = getStatusCount($conn, "Released", $schoolYearFilter);


/* ============================
   TOTAL COLLECTION
============================ */

$collectionWhere = "WHERE 1=1";

if ($schoolYearFilter !== "All") {
    $schoolYear = mysqli_real_escape_string($conn, $schoolYearFilter);
    $collectionWhere .= " AND s.school_year = '$schoolYear'";
}

$totalAmountResult = mysqli_query($conn, "
    SELECT SUM(d.total_amount) AS total_amount
    FROM document_request_monitoring d
    LEFT JOIN students s
        ON d.student_id = s.student_id
    $collectionWhere
");

$totalAmount = mysqli_fetch_assoc($totalAmountResult)['total_amount'] ?? 0;


/* ============================
   TRANSACTION LIST
============================ */

$query = mysqli_query($conn, "
    SELECT
        d.*,
        s.fullname,
        s.semester,
        s.school_year
    FROM document_request_monitoring d
    LEFT JOIN students s
        ON d.student_id = s.student_id
    $where
    ORDER BY d.id DESC
    LIMIT $start, $limit
");

?>
<div id="transactionContent">

<div class="modal fade"
     id="transactionModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-scrollable transaction-modal">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Document Request Transactions
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">

    <!-- Status Filter -->
    <div class="d-flex flex-wrap gap-2">

        <a href="?status=All&school_year=<?= urlencode($schoolYearFilter) ?>"
        class="btn btn-sm rounded-pill border <?= $statusFilter=='All'?'btn-danger text-white':'btn-light' ?>">
            All
            <span class="badge bg-light text-dark ms-1"><?= $totalTransactions ?></span>
        </a>

        <a href="?status=Pending&school_year=<?= urlencode($schoolYearFilter) ?>"
        class="btn btn-sm rounded-pill border <?= $statusFilter=='Pending'?'btn-danger text-white':'btn-light' ?>">
            Pending
            <span class="badge bg-light text-dark ms-1"><?= $totalPending ?></span>
        </a>

        <a href="?status=Pending%20Approval%20Payment&school_year=<?= urlencode($schoolYearFilter) ?>"
        class="btn btn-sm rounded-pill border <?= $statusFilter=='Verified Payment'?'btn-danger text-white':'btn-light' ?>">
            Verified Payment
            <span class="badge bg-light text-dark ms-1"><?= $totalVerifiedPayment ?></span>
        </a>

        <a href="?status=Processing&school_year=<?= urlencode($schoolYearFilter) ?>"
        class="btn btn-sm rounded-pill border <?= $statusFilter=='Processing'?'btn-danger text-white':'btn-light' ?>">
            Processing
            <span class="badge bg-light text-dark ms-1"><?= $totalProcessing ?></span>
        </a>

        <a href="?status=For%20Signature&school_year=<?= urlencode($schoolYearFilter) ?>"
        class="btn btn-sm rounded-pill border <?= $statusFilter=='For Signature'?'btn-danger text-white':'btn-light' ?>">
           For Signature
            <span class="badge bg-light text-dark ms-1"><?= $totalSignature ?></span>
        </a>

        <a href="?status=For%20Shipment&school_year=<?= urlencode($schoolYearFilter) ?>"
        class="btn btn-sm rounded-pill border <?= $statusFilter=='For Shipment'?'btn-danger text-white':'btn-light' ?>">
            For Shipment
            <span class="badge bg-light text-dark ms-1"><?= $totalShipment ?></span>
        </a>

        <a href="?status=For%20Pick%20Up&school_year=<?= urlencode($schoolYearFilter) ?>"
        class="btn btn-sm rounded-pill border <?= $statusFilter=='For Pick Up'?'btn-danger text-white':'btn-light' ?>">
            For Pick-Up
            <span class="badge bg-light text-dark ms-1"><?= $totalPickup ?></span>
        </a>

        <a href="?status=Released&school_year=<?= urlencode($schoolYearFilter) ?>"
        class="btn btn-sm rounded-pill border <?= $statusFilter=='Released'?'btn-danger text-white':'btn-light' ?>">
            Released
            <span class="badge bg-light text-dark ms-1"><?= $totalReleased ?></span>
        </a>

    </div>

    <!-- School Year Filter -->
    <form method="GET" class="d-flex align-items-center gap-2">

        <input type="hidden" name="status" value="<?= $statusFilter ?>">
        <input type="hidden" name="modal" value="1">

        <select name="school_year"
                class="form-select form-select-sm"
                onchange="this.form.submit()"
                style="min-width:220px;">

            <option value="All">All School Year</option>

            <?php
            $syQuery = mysqli_query($conn,"
                SELECT DISTINCT school_year
                FROM students
                ORDER BY school_year DESC
            ");

            while($sy = mysqli_fetch_assoc($syQuery)){
            ?>
                <option value="<?= $sy['school_year'] ?>"
                    <?= $schoolYearFilter == $sy['school_year'] ? 'selected' : '' ?>>
                    <?= $sy['school_year'] ?>
                </option>
            <?php } ?>

        </select>

    </form>

</div>
                <div class="table-responsive">
<div class="d-flex justify-content-between align-items-center mb-3">

    <h6 class="mb-0">
        Total Transactions:
        <span class="badge bg-danger">
            <?= $totalTransactions ?>
        </span>
    </h6>

    <h6 class="mb-0">
        Total Collection:
        <span class="badge bg-success">
            ₱<?= number_format($totalAmount, 2) ?>
        </span>
    </h6>

</div>
</div>
                    <table class="table table-bordered table-hover">
                       <thead class="table-danger">
<tr>
    <th>Date</th>
    <th>Student ID</th>
    <th>Fullname</th>
    <th>Semester</th>
    <th>School Year</th>
    <th>Type of Request</th>
    <th>OR Number</th>
    <th>GCash Ref</th>
    <th>Mode of Delivery</th>
    <th>Tracking Number</th>
    <th>Total Amount</th>
    <th>Status</th>
</tr>
</thead>

                        <tbody>

                        <?php
$query=mysqli_query($conn,"
SELECT d.*,s.fullname,s.semester,s.school_year
FROM document_request_monitoring d
LEFT JOIN students s
ON d.student_id=s.student_id
$where
ORDER BY d.id DESC
LIMIT $start,$limit
");






                        while($row=mysqli_fetch_assoc($query)){

                        $order=[
"Pending",
"Verified Payment",
"Processing",
"For Signature",
"For Shipment",
"For Pick Up",
"Released"
];

$current=array_search($row['status'],$order);




                        ?>

                        <tr>
                           <td>
    <?= !empty($row['created_at'])
        ? date("M d, Y h:i A", strtotime($row['created_at']))
        : '-' ?>
</td>

<td><?= $row['student_id'] ?></td>
<td><?= $row['fullname'] ?></td>
<td><?= $row['semester'] ?></td>
<td><?= $row['school_year'] ?></td>
<td>
<?php
$types = explode(", ", $row['request_type']);
$sets = json_decode($row['request_sets'], true);

$map = [
    "TOR for PRC" => "tor_prc",
    "TOR for Employment" => "tor_employment",
    "Honorable Dismissal" => "honorable",
    "Certificate of Completed Units" => "completed_units",
    "Certificate of GWA" => "gwa",
    "Certificate of GMC" => "gmc",
    "Authenticated Copy of TOR" => "auth_tor",
    "Authenticated Copy of Certificate" => "auth_certificate"
];
foreach($types as $type){
    $key = $map[$type] ?? "";
    echo '<div class="d-flex justify-content-between border-bottom py-1">';
    echo '<span>'.$type.'</span>';
    if($key != "" && !empty($sets[$key])){
        echo '<span class="badge bg-primary">'.$sets[$key].' Set(s)</span>';
    }
echo '</div>';
}
?>
</td><td style="min-width:150px">

<?php
$enableOR = $isSuperAdmin || in_array(
    $row['status'],
    [
        "Verified Payment",
        "Processing",
        "For Signature",
        "For Shipment",
        "For Pick Up",
        "Released"
    ]
);
?>

<input
type="text"
class="form-control form-control-sm or-number"
data-id="<?= $row['id'] ?>"
value="<?= htmlspecialchars($row['or_number']) ?>"
placeholder="Enter OR Number"
<?= !$enableOR ? 'disabled' : '' ?>>

</td>
<td>

<?php if(empty($row['gcash_verified'])){ ?>

<button
class="btn btn-warning btn-sm verify-gcash"
data-id="<?= $row['id'] ?>"
<?= ($row['status']!="Verified Payment" && !$isSuperAdmin) ? 'disabled' : '' ?>>
Pending
</button>

<?php }else{ ?>

<span class="badge bg-success">
Verified
</span>

<?php } ?>

<?php if(!empty($row['gcash_ref'])){ ?>

<div class="small text-muted mt-1">
<?= htmlspecialchars($row['gcash_ref']) ?>
</div>

<?php } ?>

</td>  
<td><?= $row['delivery_mode'] ?></td>
<td><?= $row['tracking_number'] ?></td>
<td>₱<?= number_format($row['total_amount'],2) ?></td>
                    
<td>

<?php

if($row['status']=="Released"){

?>

<div class="text-center">

    <span class="badge bg-success px-3 py-2">
        Released
    </span>

    <?php if(!empty($row['released_date'])){ ?>

        <div class="small text-muted mt-2">
            <?= date("M d, Y h:i A",strtotime($row['released_date'])) ?>
        </div>

    <?php } ?>

</div>

<?php

}else{

if($isSuperAdmin){

    $allowedStatus=$order;

}else{

    $allowedStatus=[];

    $allowedStatus[]=$order[$current];

    if(isset($order[$current+1])){

        if(
            $order[$current+1]=="Processing"
            &&
            empty($row['gcash_verified'])
        ){

            // dili pa pwede

        }else{

            $allowedStatus[]=$order[$current+1];

        }

    }

}

?>

<select
class="form-select form-select-sm status-select"
data-id="<?= $row['id'] ?>">

<?php foreach($allowedStatus as $status){ ?>

<option
value="<?= $status ?>"
<?= $status==$row['status']?'selected':'' ?>>

<?= $status ?>

</option>

<?php } ?>

</select>

<?php } ?>

</td>



                        </tr>

                        <?php } ?>

                        </tbody>
                    </table>
<nav class="mt-3">

<ul class="pagination justify-content-end">

<?php if($page > 1){ ?>

<li class="page-item">
<a class="page-link"
href="?status=<?= urlencode($statusFilter) ?>&school_year=<?= urlencode($schoolYearFilter) ?>&page=<?= $page-1 ?>&modal=1">
Previous
</a>
</li>

<?php } ?>

<?php for($i=1;$i<=$totalPages;$i++){ ?>

<li class="page-item <?= ($page==$i)?'active':'' ?>">
<a class="page-link"
href="?status=<?= urlencode($statusFilter) ?>&school_year=<?= urlencode($schoolYearFilter) ?>&page=<?= $i ?>&modal=1">
<?= $i ?>
</a>
</li>

<?php } ?>

<?php if($page < $totalPages){ ?>

<li class="page-item">
<a class="page-link"
href="?status=<?= urlencode($statusFilter) ?>&school_year=<?= urlencode($schoolYearFilter) ?>&page=<?= $page+1 ?>&modal=1">
Next
</a>
</li>

<?php } ?>

</ul>

</nav>
                </div>

            </div>

        </div>

    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>



<script>// ================================
// Auto Fill Student Information
// ================================
document.getElementById("student_id").addEventListener("input", function () {

    let student_id = this.value;

    if(student_id != ""){

        fetch("get_document_student.php?student_id=" + encodeURIComponent(student_id))
        .then(response => response.json())
        .then(data => {

            if(data.status === "success"){

                document.getElementById("fullname").value = data.fullname;
                document.getElementById("semester").value = data.semester;
                document.getElementById("school_year").value = data.school_year;

            }else{

                document.getElementById("fullname").value = "";
                document.getElementById("semester").value = "";
                document.getElementById("school_year").value = "";

            }

        });

    }else{

        document.getElementById("fullname").value = "";
        document.getElementById("semester").value = "";
        document.getElementById("school_year").value = "";

    }

});


// ================================
// Delivery Mode
// ================================
const deliveryMode = document.getElementById("delivery_mode");
const trackingField = document.getElementById("trackingField");
const trackingNumber = document.getElementById("tracking_number");

if(deliveryMode){

    deliveryMode.addEventListener("change", function(){

        if(this.value === "Shipment"){

            trackingField.style.display = "block";
            trackingNumber.required = true;

        }else{

            trackingField.style.display = "none";
            trackingNumber.required = false;
            trackingNumber.value = "";

        }

    });

}


// ================================
// Bind Status Dropdown
// ================================
function bindStatusEvents(){

    document.querySelectorAll(".status-select").forEach(function(select){

        select.onchange = function(){

            let id = this.dataset.id;
            let status = this.value;

            fetch("update_document_status.php",{

                method:"POST",

                headers:{
                    "Content-Type":"application/x-www-form-urlencoded"
                },

                body:
                    "id=" + encodeURIComponent(id) +
                    "&status=" + encodeURIComponent(status)

            })

            .then(response => response.text())

            .then(result => {

                if(result.trim() === "success"){

<?php if(!$isSuperAdmin){ ?>
                    if(status === "Released"){
                        select.disabled = true;
                    }
<?php } ?>

                    // Reload current page while keeping modal open
                    let url = new URL(window.location.href);

                    url.searchParams.set("status","<?= $statusFilter ?>");
                    url.searchParams.set("page","<?= $page ?>");
                    url.searchParams.set("modal","1");

                    window.location.href = url.toString();

                }else{

                    alert("Failed to update status.");

                }

            })

            .catch(function(){

                alert("Connection error.");

            });

        };

    });

}

bindStatusEvents();


// ================================
// Auto Open Modal
// ================================
<?php if($openModal){ ?>

document.addEventListener("DOMContentLoaded", function(){

    new bootstrap.Modal(
        document.getElementById("transactionModal")
    ).show();

});

<?php } ?>
</script>
