
<?php
session_start();

if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

$userRole = $_SESSION['role'] ?? '';
$isSuperAdmin = strtolower($userRole) === 'superadmin';


include 'db.php';


if(isset($_POST['add'])){

    $request_date = $_POST['request_date'];
    $student_id = $_POST['student_id'];

    $request_type = isset($_POST['request_type'])
    ? implode(", ", $_POST['request_type'])
    : "";

    $request_sets = json_encode($_POST['sets']);

    $delivery_mode = $_POST['delivery_mode'];

    $total_amount = $_POST['total_amount'];

    $status = $_POST['status'];

    $gcash_ref   = $_POST['gcash_ref'];
    $courier_fee = $_POST['courier_fee'] ?? 0;

    $status = "Pending";

mysqli_query($conn,"
INSERT INTO document_request_monitoring
(
    request_date,
    student_id,
    request_type,
    request_sets,
    delivery_mode,
    gcash_ref,
    courier_fee,
    total_amount,
    status
)
VALUES
(
    '$request_date',
    '$student_id',
    '$request_type',
    '$request_sets',
    '$delivery_mode',
    '$gcash_ref',
    '$courier_fee',
    '$total_amount',
    '$status'
)
");


    header("Location: document_request_monitoring.php");
    exit();
}





?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Collection Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/collection.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content">
<div class="container-fluid">


    <!-- Header -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">

            <div>
                <h3 class="mb-1 fw-bold text-danger">
                    Document Request
                </h3>

                <small class="text-muted">
                    School Expense Records
                </small>
            </div>

<a href="view_document_transactions.php" class="btn btn-outline-danger">
    <i class="bi bi-clock-history"></i> View Transactions
</a>

        </div>
    </div>
<!-- Expense Form -->
<div class="card shadow-sm border-0 mb-4">
<div class="card-body">

<h5 class="text-danger mb-4">
    Document Request Details
</h5>

<form method="POST">

<div class="row" id="documentRequestList">

<div class="col-md-3 mb-4">

<label class="form-label fw-semibold">
Date
</label>

<input type="date"
       name="request_date"
       class="form-control"
       value="<?= date('Y-m-d')?>"
       required>

</div>

</div>
<h6 class="fw-bold border-bottom pb-2 mb-3">
Student Information
</h6>

<div class="row">

<div class="col-md-3 mb-3">

<label class="form-label">
Student ID
</label>

<input type="text"
       id="student_id"
       name="student_id"
       class="form-control"
       placeholder="Enter Student ID"
       autocomplete="off"
       required>

</div>

<div class="col-md-5 mb-3">

<label class="form-label">
Full Name
</label>

<input type="text"
       id="fullname"
       class="form-control"
       readonly>

</div>

<div class="col-md-2 mb-3">

<label class="form-label">
School Year
</label>

<input type="text"
       id="school_year"
       class="form-control"
       readonly>

</div>

<div class="col-md-2 mb-3">

<label class="form-label">
Semester
</label>

<input type="text"
       id="semester"
       class="form-control"
       readonly>

</div>

</div>
<h6 class="fw-bold border-bottom pb-2 mt-3 mb-3">
    Type of Request
</h6>

<?php

include "db.php";


// Load existing document fees
$fees = [];

$result = $conn->query("
    SELECT document_name, amount 
    FROM document_fees
");

while($row = $result->fetch_assoc()){

    $fees[$row['document_name']] = $row['amount'];

}



$requests = [

    ["TOR for PRC", "tor_prc"],
    ["TOR for Employment", "tor_employment"],
    ["Honorable Dismissal", "honorable"],
    ["Certificate of Completed Units", "completed_units"],

    ["Certificate of GWA", "gwa"],
    ["Certificate of GMC", "gmc"],
    ["Authenticated Copy of TOR", "auth_tor"],
    ["Authenticated Copy of Certificate", "auth_certificate"]

];


$columns = array_chunk($requests, 4);

?>

<div class="row">

<?php foreach($columns as $column){ ?>

<div class="col-md-6">

<?php foreach($column as $r){ ?>

<div class="card border mb-2 shadow-sm request-card" style="cursor:pointer;">

<div class="card-body py-2">

<div class="row align-items-center">


<!-- Document -->
<div class="col-6">

<div class="form-check">

<input
    type="checkbox"
    class="form-check-input request-check"
    id="request_<?= $r[1] ?>"
    name="request_type[]"
    value="<?= $r[0] ?>"
>
<label 
    class="form-check-label small fw-semibold"
    for="request_<?= $r[1] ?>">
    <?= $r[0] ?>
</label>

</div>

</div>



<!-- Amount -->
<div class="col-4">

<div class="input-group input-group-sm">

<span class="input-group-text">₱</span>

<input
    type="number"
    min="0"
    step="0.01"
    name="amount[<?= $r[1] ?>]"
    class="form-control amount-field"
    value="<?= $fees[$r[0]] ?? 0 ?>"
    placeholder="0.00"
>

</div>

</div>



<!-- Sets -->
<div class="col-2">

<input
    type="number"
    min="1"
    value="1"
    name="sets[<?= $r[1] ?>]"
    class="form-control form-control-sm text-center sets-field"
    placeholder="1"
>

</div>


</div>

</div>

</div>

<?php } ?>

</div>

<?php } ?>


</div>


<div class="text-end mt-3">

<button type="button" class="btn btn-primary" id="saveFees">

<i class="bi bi-save"></i> Save Document Fees

</button>

</div>

<h6 class="fw-bold border-bottom pb-2 mt-4 mb-3">
    Delivery & Payment Information
</h6>
<div class="row">

    <!-- Preferred Mode -->
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Preferred Mode of Delivery
        </label>

        <select
            id="delivery_mode"
            name="delivery_mode"
            class="form-select"
            required>

            <option value="">Select</option>
            <option value="Pick-up">Pick-up</option>
            <option value="Shipment">Shipment</option>

        </select>

    </div>

    <!-- GCash Reference -->
    <div class="col-md-6 mb-3">

        <label class="form-label">
            GCash Reference Number
        </label>

        <input
            type="text"
            name="gcash_ref"
            class="form-control"
            placeholder="Enter GCash Reference Number"
            required>

    </div>

    <!-- Courier Fee -->
    <div
        class="col-md-6 mb-3"
        id="courierFeeField"
        style="display:none;">

        <label class="form-label">
            Courier Fee
        </label>

        <div class="input-group">

            <span class="input-group-text">
                ₱
            </span>

            <input
                type="number"
                min="0"
                step="0.01"
                id="courier_fee"
                name="courier_fee"
                class="form-control"
                placeholder="0.00">

        </div>

    </div>

    <!-- Total Amount -->
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Total Amount
        </label>

        <div class="input-group">

            <span class="input-group-text">
                ₱
            </span>

<input
    type="number"
    step="0.01"
    min="0"
    name="total_amount"
    class="form-control"
    readonly
>

        </div>

    </div>

</div>

<div class="text-end mt-3">

<button
type="reset"
class="btn btn-secondary">
Reset
</button>

<button
type="submit"
name="add"
class="btn btn-danger">
Save Request
</button>
</div>
</div>
</form>
</div>
</div>
<script>
const deliveryMode = document.getElementById("delivery_mode");
const courierField = document.getElementById("courierFeeField");
const courierFee = document.getElementById("courier_fee");

function toggleCourierFee() {

    if (deliveryMode.value === "Shipment") {

        courierField.style.display = "block";
        courierFee.required = true;

    } else {

        courierField.style.display = "none";
        courierFee.required = false;
        courierFee.value = "";

    }

}

deliveryMode.addEventListener("change", toggleCourierFee);

// Run on page load
toggleCourierFee();
</script>

<script>
const studentID = document.getElementById("student_id");

studentID.addEventListener("keyup", function () {

    const id = this.value.trim();

    if (id === "") {
        document.getElementById("fullname").value = "";
        document.getElementById("semester").value = "";
        document.getElementById("school_year").value = "";
        return;
    }

    fetch("get_student.php?student_id=" + encodeURIComponent(id))
        .then(response => response.json())
        .then(data => {

            document.getElementById("fullname").value =
                data.fullname || "";

            document.getElementById("semester").value =
                data.semester || "";

            document.getElementById("school_year").value =
                data.school_year || "";

        });

});
</script>
<script>

document.getElementById("saveFees").addEventListener("click", function(){

    let data = [];

    document.querySelectorAll(".amount-field").forEach(function(input){

        let amount = input.value;

        if(amount > 0){

            let card = input.closest(".card");

            let document_name = card.querySelector(".form-check-label").innerText.trim();


            data.push({
                document_name: document_name,
                amount: amount
            });

        }

    });


    if(data.length === 0){

        Swal.fire(
            "No Data",
            "Please enter document fees first.",
            "warning"
        );

        return;

    }


    fetch("save_document_fees.php", {

        method: "POST",

        headers:{
            "Content-Type":"application/json"
        },

        body: JSON.stringify(data)

    })

    .then(res => res.json())

    .then(result => {

        if(result.status === "success"){

            Swal.fire(
                "Saved!",
                "Document fees successfully saved.",
                "success"
            );

        }else{

            Swal.fire(
                "Error",
                result.message,
                "error"
            );

        }

    })

    .catch(error => {

        console.log(error);

        Swal.fire(
            "Error",
            "Something went wrong.",
            "error"
        );

    });


});

</script><script>

function calculateTotalAmount(){

    let total = 0;


    // Calculate checked documents
    document.querySelectorAll(".request-check:checked").forEach(function(check){


        let card = check.closest(".card");


        let amountField = card.querySelector(".amount-field");
        let setsField = card.querySelector(".sets-field");


        if(amountField && setsField){

            let amount = parseFloat(amountField.value) || 0;
            let sets = parseInt(setsField.value) || 1;


            total += amount * sets;

        }


    });



    // Add courier fee if Shipment
    let deliveryMode = document.querySelector("#delivery_mode").value;

    if(deliveryMode === "Shipment"){

        let courierFee = parseFloat(
            document.querySelector("#courier_fee").value
        ) || 0;


        total += courierFee;

    }



    document.querySelector('input[name="total_amount"]').value =
        total.toFixed(2);

}



// Document check
document.querySelectorAll(".request-check").forEach(function(check){

    check.addEventListener("change", calculateTotalAmount);

});


// Amount change
document.querySelectorAll(".amount-field").forEach(function(input){

    input.addEventListener("input", calculateTotalAmount);

});


// Sets change
document.querySelectorAll(".sets-field").forEach(function(input){

    input.addEventListener("input", calculateTotalAmount);

});


// Delivery change
document.querySelector("#delivery_mode")
.addEventListener("change", calculateTotalAmount);


// Courier fee change
document.querySelector("#courier_fee")
.addEventListener("input", calculateTotalAmount);



calculateTotalAmount();


</script>

<script>

document.querySelectorAll(".request-card").forEach(function(card){

    card.addEventListener("click", function(e){


        // para dili mo-double click kung checkbox mismo
        if(e.target.type === "checkbox" || e.target.tagName === "INPUT"){
            return;
        }


        let checkbox = card.querySelector(".request-check");


        checkbox.checked = !checkbox.checked;


        // trigger calculation
        calculateTotalAmount();


    });

});

</script>
    

</body>
</html>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <script>

        