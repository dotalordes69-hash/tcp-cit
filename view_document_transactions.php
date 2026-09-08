<?php
session_start();



if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';
include 'document_request/pagination.php';


// FILTER CONDITION

$where = [];


if(!empty($_GET['student_id'])){

    $student_id = mysqli_real_escape_string(
        $conn,
        $_GET['student_id']
    );

    $where[] = "dr.student_id='$student_id'";

}



if(!empty($_GET['status'])){

    $status = mysqli_real_escape_string(
        $conn,
        $_GET['status']
    );

    $where[] = "dr.status='$status'";

}



$whereSQL = "";


if(count($where) > 0){

    $whereSQL = "WHERE " . implode(" AND ", $where);

}




$sql = "
SELECT

dr.id,
dr.request_date,
dr.student_id,
s.fullname,

dr.request_type,
dr.request_sets,
dr.delivery_mode,
dr.total_amount,
dr.or_number,
dr.gcash_ref,
dr.courier_fee,
dr.status,
dr.tracking_number,
dr.created_at,
dr.released_date,
dr.gcash_verified

FROM document_request_monitoring dr

LEFT JOIN students s
ON dr.student_id = s.student_id


$whereSQL


ORDER BY dr.id DESC


LIMIT $limit OFFSET $offset

";


$result = mysqli_query($conn,$sql);


?>


<style>

.transaction-table{
    width:100%;
    table-layout:fixed;
}

.transaction-table th{
    text-align:center;
    vertical-align:middle;
    white-space:nowrap;
    font-size:13px;
}

.transaction-table td{
    vertical-align:middle;
    font-size:13px;
}


.transaction-table th:nth-child(1){
    width:40px;
}

.transaction-table th:nth-child(2){
    width:100px;
}

.transaction-table th:nth-child(3){
    width:120px;
}

.transaction-table th:nth-child(4){
    width:180px;
}

.transaction-table th:nth-child(5){
    width:120px;
}

.transaction-table th:nth-child(6){
    width:180px;
}


.transaction-table th:nth-child(7){
    width:150px;
}


.transaction-table th:nth-child(8){
    width:130px;
}


.transaction-table th:nth-child(9){
    width:100px;
}


.transaction-table th:nth-child(10){
    width:150px;
}


.transaction-table th:nth-child(11){
    width:80px;
}






.transaction-table input{
    min-width:110px;
}



.request-item{

    display:flex;
    justify-content:space-between;
    align-items:center;

    padding:5px 0;

}


.request-name{

    max-width:190px;
    white-space:normal;

}


</style>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Document Request Records</title>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link rel="stylesheet" href="assets/css/collection.css">


</head>


<body>


<?php include 'sidebar.php'; ?>


<div class="content">

<div class="container-fluid">



<div class="card shadow-sm border-0 mb-4">


<div class="card-body d-flex justify-content-between align-items-center">


<div>

<h3 class="fw-bold text-danger mb-1">

Document Request Records

</h3>


<small class="text-muted">

Transaction History

</small>


</div>


<a href="document_request_monitoring.php"
class="btn btn-outline-danger">

<i class="bi bi-arrow-left"></i>
Back

</a>


</div>

</div>


<?php include 'document_request/filter_document_transactions.php'; ?>


<div class="card shadow-sm border-0">


<div class="card-body">


<div class="table-responsive">

<div class="d-flex justify-content-between align-items-center mb-3">

    <div class="text-muted fw-semibold">

        Showing 
        <?= ($offset + 1) ?> 
        to 
        <?= min($offset + $limit, $totalRecords) ?>

        of 
        <?= $totalRecords ?>

        records

    </div>


</div>


<table class="table table-bordered table-hover align-middle transaction-table">


<thead class="table-danger">


<tr>

<th>#</th>
<th>Date</th>
<th>Student ID</th>
<th>Request Details</th>
<th>Mode of Delivery</th>
<th>Tracking Number</th>
<th>OR Number</th>
<th>GCash Ref</th>
<th>Total</th>
<th>Status</th>
<th>Action</th>

</tr>

</thead>
<tbody>


<?php
$count = 1;
while($row=mysqli_fetch_assoc($result)){
?>

<tr>

<td>
<?= $count++ ?>
</td>


<td>
<?= date("M d, Y",strtotime($row['request_date'])) ?>
</td>


<td>

<button 
class="btn btn-link p-0 student-info"
data-id="<?= $row['student_id'] ?>">

<?= htmlspecialchars($row['student_id']) ?>

</button>

</td>




<td style="min-width:300px;">

<?php

$requests = explode(", ", $row['request_type']);

$sets = json_decode($row['request_sets'], true);


// mapping sa pangalan ngadto sa key sa sets
$requestMap = [

    "TOR for PRC" => "tor_prc",
    "TOR for Employment" => "tor_employment",
    "Honorable Dismissal" => "honorable",
    "Certificate of Completed Units" => "completed_units",
    "Certificate of GWA" => "gwa",
    "Certificate of GMC" => "gmc",
    "Authenticated Copy of TOR" => "auth_tor",
    "Authenticated Copy of Certificate" => "auth_certificate"

];


foreach($requests as $request){

    $key = $requestMap[$request] ?? '';

    $quantity = $sets[$key] ?? 1;

?>

<div class="request-item border-bottom">

<span class="request-name">
<?= htmlspecialchars($request) ?>
</span>


<span class="badge bg-danger rounded-pill">

<?= $quantity ?> set

</span>


</div>


<?php

}

?>

</td>



<td>
<?= $row['delivery_mode'] ?>
</td>


<td>

<input type="text"
class="form-control form-control-sm tracking-input"
value="<?= htmlspecialchars($row['tracking_number']) ?>"
id="tracking<?= $row['id'] ?>"
placeholder="Tracking Number"
<?= ($row['status']!="Verified Payment") ? "disabled" : "" ?>
>

</td>

<td>
<input type="text"
class="form-control form-control-sm or-input"
value="<?= htmlspecialchars($row['or_number']) ?>"
id="or<?= $row['id'] ?>"
placeholder="OR Number"
<?= ($row['status']!="Verified Payment") ? "disabled" : "" ?>
>
</td>


<td>

<?php if(!empty($row['gcash_ref'])){ ?>


<?php if($row['gcash_verified']==1){ ?>


<span class="text-success fw-bold">
<?= htmlspecialchars($row['gcash_ref']) ?>
</span>

<br>

<span class="badge bg-success">
Verified
</span>


<?php }else{ ?>


<button 
class="btn btn-link p-0 gcash-view"
data-id="<?= $row['id'] ?>"
data-ref="<?= $row['gcash_ref'] ?>"
<?= ($row['status']!="Verified Payment") ? "disabled" : "" ?>
>

<?= htmlspecialchars($row['gcash_ref']) ?>

</button>


<br>


<span class="badge bg-warning text-dark">
Pending
</span>


<?php } ?>


<?php }else{ ?>

<span class="text-muted">
No GCash Ref
</span>

<?php } ?>


</td>


<td>

₱<?= number_format($row['total_amount'],2) ?>

</td>




<td>
<select 
class="form-select form-select-sm status-select"
id="status<?= $row['id'] ?>"
onchange="changeStatus(this,<?= $row['id'] ?>)"
<?= 
(
    $row['status']=="Released" 
    && $_SESSION['role']!="superadmin"
) 
? "disabled" 
: "" 
?>
>

<option value="Pending"
<?= $row['status']=="Pending" ? "selected" : "" ?>>
Pending
</option>


<option value="Verified Payment"
<?= $row['status']=="Verified Payment" ? "selected" : "" ?>>
Verified Payment
</option>


<option value="Processing"
<?= $row['status']=="Processing" ? "selected" : "" ?>>
Processing
</option>


<option value="For Signature"
<?= $row['status']=="For Signature" ? "selected" : "" ?>>
For Signature
</option>


<option value="For Shipment"
<?= $row['status']=="For Shipment" ? "selected" : "" ?>>
For Shipment
</option>


<option value="For Pick-up"
<?= $row['status']=="For Pick-up" ? "selected" : "" ?>>
For Pick-up
</option>


<option value="Released"
<?= $row['status']=="Released" ? "selected" : "" ?>>
Released
</option>

</select>

<?php if($row['status']=="Released" && !empty($row['released_date'])){ ?>

<br>

<small class="text-success fw-bold">

Released:
<br>

<?= date("M d, Y h:i A", strtotime($row['released_date'])) ?>

</small>

<?php } ?>

</td>




<td>

<button 
class="btn btn-sm btn-success"
onclick="saveTransaction(<?= $row['id'] ?>)">

<i class="bi bi-save"></i>
Save

</button>


</td>



</tr>



<?php

}

?>



</tbody>


</table>
<style>

.custom-pagination .page-link{

    background:#fff;
    color:#dc3545;
    border:none;
    margin:0 4px;
    border-radius:10px;
    box-shadow:0 2px 8px rgba(0,0,0,0.12);
    font-weight:600;
    padding:8px 14px;
    transition:.2s;

}


.custom-pagination .page-link:hover{

    background:#dc3545;
    color:#fff;
    transform:translateY(-2px);

}



.custom-pagination .active .page-link{

    background:#dc3545;
    color:#fff;
    box-shadow:0 4px 12px rgba(220,53,69,.35);

}



.custom-pagination .disabled .page-link{

    background:#f8f9fa;
    color:#aaa;

}



</style>


<nav>
<ul class="pagination justify-content-end mt-4 custom-pagination">


<?php if($page > 1){ ?>

<li class="page-item">

<a class="page-link"
href="?page=<?= $page-1 ?>">
<i class="bi bi-chevron-left"></i>
</a>

</li>

<?php } ?>



<?php for($i=1;$i<=$totalPages;$i++){ ?>

<li class="page-item <?= ($page==$i)?'active':'' ?>">

<a class="page-link"
href="?page=<?= $i ?>">

<?= $i ?>

</a>

</li>

<?php } ?>



<?php if($page < $totalPages){ ?>

<li class="page-item">

<a class="page-link"
href="?page=<?= $page+1 ?>">

<i class="bi bi-chevron-right"></i>

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



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



<div class="modal fade" id="studentModal" tabindex="-1">

<div class="modal-dialog modal-dialog-centered">

<div class="modal-content">


<div class="modal-header bg-danger text-white">

<h5 class="modal-title">
Student Information
</h5>

<button 
class="btn-close btn-close-white"
data-bs-dismiss="modal">
</button>

</div>


<div class="modal-body">


<div class="mb-2">

<label class="fw-bold">
Student ID
</label>

<input 
type="text"
id="modal_student_id"
class="form-control"
readonly>

</div>


<div class="mb-2">

<label class="fw-bold">
Full Name
</label>

<input 
type="text"
id="modal_fullname"
class="form-control"
readonly>

</div>



<div class="mb-2">

<label class="fw-bold">
Semester
</label>

<input 
type="text"
id="modal_semester"
class="form-control"
readonly>

</div>



<div class="mb-2">

<label class="fw-bold">
School Year
</label>

<input 
type="text"
id="modal_school_year"
class="form-control"
readonly>

</div>


</div>


</div>

</div>

</div>

<div class="modal fade" id="gcashModal">

<div class="modal-dialog modal-dialog-centered">

<div class="modal-content">


<div class="modal-header bg-danger text-white">

<h5 class="modal-title">
Verify GCash Payment
</h5>

<button class="btn-close btn-close-white"
data-bs-dismiss="modal"></button>

</div>


<div class="modal-body">


<input type="hidden" id="gcash_transaction_id">


<label class="fw-bold">
GCash Reference Number
</label>


<input type="text"
id="gcash_reference_display"
class="form-control mb-3"
readonly>



<button 
class="btn btn-success w-100"
onclick="verifyGcash()">

<i class="bi bi-check-circle"></i>
Verify Payment

</button>


</div>


</div>

</div>

</div>


<script>


document.querySelectorAll(".student-info")
.forEach(function(btn){


btn.addEventListener("click",function(){


let id=this.dataset.id;


fetch("get_student.php?student_id="+id)

.then(res=>res.json())

.then(data=>{


document.getElementById("modal_student_id").value=data.student_id;

document.getElementById("modal_fullname").value=data.fullname;

document.getElementById("modal_semester").value=data.semester;

document.getElementById("modal_school_year").value=data.school_year;



let modal = new bootstrap.Modal(
document.getElementById("studentModal")
);


modal.show();


});


});


});


document.querySelectorAll(".gcash-view")
.forEach(function(btn){


btn.addEventListener("click",function(){


document.getElementById("gcash_transaction_id").value =
this.dataset.id;


document.getElementById("gcash_reference_display").value =
this.dataset.ref;



let modal = new bootstrap.Modal(
document.getElementById("gcashModal")
);


modal.show();


});


});


function verifyGcash(){


let id = document.getElementById("gcash_transaction_id").value;


window.location.href =
"validate_gcash.php?id=" + id + "&type=document";


}
function changeStatus(select,id){

    let status = select.value;

    let tracking = document.getElementById("tracking"+id);
    let or = document.getElementById("or"+id);


    // Enable ra kung Verified Payment
    if(status === "Verified Payment"){

        if(tracking){
            tracking.disabled = false;
        }

        if(or){
            or.disabled = false;
        }


    }else{


        if(tracking){
            tracking.disabled = true;
        }

        if(or){
            or.disabled = true;
        }


    }

}

document.querySelectorAll(".status-select").forEach(function(select){

    let id = select.id.replace("status","");

    changeStatus(select,id);

});function saveTransaction(id){

    let status = document.getElementById("status"+id).value;

    let tracking = document.getElementById("tracking"+id)?.value || "";

    let or_number = document.getElementById("or"+id)?.value || "";


    fetch("save_document_transaction.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },

        body:
        "id="+id+
        "&status="+encodeURIComponent(status)+
        "&tracking="+encodeURIComponent(tracking)+
        "&or_number="+encodeURIComponent(or_number)

    })

    .then(response => response.json())

    .then(data=>{


        if(data.status=="success"){

            Swal.fire({
                icon:"success",
                title:"Saved",
                text:data.message,
                timer:1500,
                showConfirmButton:false
            })
            .then(()=>{
                location.reload();
            });


        }else{

            Swal.fire({
                icon:"error",
                title:"Error",
                text:data.message
            });

        }


    })

    .catch(error=>{

        console.log(error);

        Swal.fire({
            icon:"error",
            title:"Failed",
            text:"Cannot connect to server"
        });

    });


}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>