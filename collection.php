
<?php
session_start();

if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}


$userRole = $_SESSION['role'] ?? '';
$isSuperAdmin = strtolower(trim($userRole)) === 'superadmin';


include 'db.php';

if(isset($_POST['student_id'])){

    $student_id   = trim($_POST['student_id']);
    $payment_date = $_POST['payment_date'];
    $or_number    = trim($_POST['or_number']);
    $reference_no = trim($_POST['reference_number']);
    $particular   = trim($_POST['particular']);
    $amount       = (float)$_POST['amount'];

    /* GET ACTIVE BOOKLET */
    $bk = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT *
    FROM or_booklets
    WHERE current_or <= or_end
    ORDER BY id ASC
    LIMIT 1
    "));

if(!$bk){
    $_SESSION['error'] =
        'No active OR booklet available. Please create a new BK to continue transactions.';
    header("Location: collection.php");
    exit;
}

    $booklet_no = $bk['booklet_no'];

    $received_by = $_SESSION['name']
        ?? $_SESSION['fullname']
        ?? $_SESSION['username']
        ?? 'System';


        /* CHECK DUPLICATE OR NUMBER */
$check_or = $conn->prepare("
    SELECT id
    FROM payment_history
    WHERE or_number = ?
    LIMIT 1
");

$check_or->bind_param("s", $or_number);
$check_or->execute();
$check_or->store_result();

if($check_or->num_rows > 0){

    $_SESSION['error'] =
        "Duplicate OR Number detected. OR No. {$or_number} already exists.";

    header("Location: collection.php");
    exit;
}

$getReg = $conn->prepare("
    SELECT registration_fee
    FROM student_accounts
    WHERE student_id = ?
");

$getReg->bind_param("s", $student_id);
$getReg->execute();

$regData = $getReg->get_result()->fetch_assoc();

$registration_paid = $regData['registration_fee'] ?? 0;


/* CHECK IF REGISTRATION FEE ALREADY PAID */
if (strtolower(trim($particular)) == 'registration fee') {

    $checkReg = $conn->prepare("
        SELECT id
        FROM payment_history
        WHERE student_id = ?
        AND LOWER(particular) = 'registration fee'
        LIMIT 1
    ");

    $checkReg->bind_param("s", $student_id);
    $checkReg->execute();
    $checkReg->store_result();

    if ($checkReg->num_rows > 0) {

        $_SESSION['error'] = "Registration Fee already paid.";

        header("Location: collection.php");
        exit;
    }
}
    /* SAVE PAYMENT HISTORY */
    $stmt = $conn->prepare("
INSERT INTO payment_history
(
    student_id,
    payment_date,
    or_number,
    gcash_ref,
    particular,
    amount_paid,
    received_by,
    booklet_no,
    gcash_status
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
$stmt->bind_param(
    "sssssdss",
    $student_id,
    $payment_date,
    $or_number,
    $reference_no,
    $particular,
    $amount,
    $received_by,
    $booklet_no
);

    if(!$stmt->execute()){
    die("Payment Error: ".$stmt->error);
}

/* IF REGISTRATION FEE IS PAID -> UPDATE REGISTRATION FEE STATUS */
if (strtolower(trim($particular)) == 'registration fee') {

    $updateReg = $conn->prepare("
        UPDATE student_accounts
        SET registration_fee = 1
        WHERE student_id = ?
    ");

    $updateReg->bind_param(
        "s",
        $student_id
    );

    $updateReg->execute();
}


    mysqli_query($conn,"
UPDATE or_booklets
SET current_or = current_or + 1
WHERE id = {$bk['id']}
");
/* UPDATE BOOKLET TOTAL COLLECTION */
$update_booklet = $conn->prepare("
    UPDATE or_booklets
    SET total_amount = total_amount + ?
    WHERE id = ?
");

$update_booklet->bind_param(
    "di",
    $amount,
    $bk['id']
);

$update_booklet->execute();
    /* GET CURRENT ACCOUNT */
    $acct = $conn->prepare("
        SELECT total_amount,total_paid
        FROM student_accounts
        WHERE student_id = ?
    ");

    $acct->bind_param("s", $student_id);
    $acct->execute();

    $account = $acct->get_result()->fetch_assoc();

    if($account){

        $total_amount = (float)$account['total_amount'];
        $total_paid   = (float)$account['total_paid'];

        $new_total_paid = $total_paid + $amount;
        $new_balance    = $total_amount - $new_total_paid;

        if($new_balance < 0){
            $new_balance = 0;
        }

        $update = $conn->prepare("
            UPDATE student_accounts
            SET
                total_paid = ?,
                balance = ?
            WHERE student_id = ?
        ");

        $update->bind_param(
            "dds",
            $new_total_paid,
            $new_balance,
            $student_id
        );

        $update->execute();
    }

    $_SESSION['success'] =
        'Payment transaction saved successfully.';

    header("Location: collection.php");
    exit;
}

$booklets = mysqli_query($conn,"
SELECT
    b.*,
    COALESCE(SUM(p.amount_paid),0) AS total_collection
FROM or_booklets b
LEFT JOIN payment_history p
    ON p.booklet_no = b.booklet_no
GROUP BY b.id
ORDER BY b.id DESC
");

$next_or = 'No Active Booklet';

$active_booklet = mysqli_query(
    $conn,
    "SELECT *
     FROM or_booklets
     WHERE current_or <= or_end
     ORDER BY id ASC
     LIMIT 1"
);

if ($row = mysqli_fetch_assoc($active_booklet)) {
    $next_or = $row['current_or'];
}
$pending_count = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) as total
    FROM payment_history
    WHERE gcash_status != 'verified'
"));

$pending_count = $pending_count['total'];
$result = mysqli_query($conn,"
SELECT
    p.*,
    s.fullname
FROM payment_history p
LEFT JOIN students s
    ON p.student_id = s.student_id
WHERE p.gcash_status != 'verified'
ORDER BY p.id DESC
");

$active_bk = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT *
    FROM or_booklets
    WHERE current_or <= or_end
    ORDER BY id ASC
    LIMIT 1
"));
/* PENDING BOOKLETS */
$pendingBooklets = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS total
    FROM or_booklets
    WHERE bk_status != 'Deposited'
"));

$pendingBooklets = $pendingBooklets['total'];


/* CREATE NEW OR BOOKLET */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booklet_no'])) {

    $booklet_no = trim($_POST['booklet_no']);
    $or_start   = (int)$_POST['or_start'];
    $or_end     = (int)$_POST['or_end'];

    if ($booklet_no == '' || $or_start <= 0 || $or_end <= 0) {

        $_SESSION['error'] = "Please complete all fields.";

    } elseif ($or_start > $or_end) {

        $_SESSION['error'] = "OR Start cannot be greater than OR End.";

    } else {

        $check = $conn->prepare("
            SELECT id
            FROM or_booklets
            WHERE booklet_no = ?
            LIMIT 1
        ");

        $check->bind_param("s", $booklet_no);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $_SESSION['error'] =
                "Booklet No. {$booklet_no} already exists.";

        } else {

            $current_or = $or_start;

            $stmt = $conn->prepare("
                INSERT INTO or_booklets
                (
                    booklet_no,
                    or_start,
                    or_end,
                    current_or
                )
                VALUES (?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "siii",
                $booklet_no,
                $or_start,
                $or_end,
                $current_or
            );

            if ($stmt->execute()) {

                $_SESSION['success'] =
                    "Booklet created successfully.";

            } else {

                $_SESSION['error'] =
                    "Database Error: " . $stmt->error;
            }
        }
    }

    header("Location: collection.php");
    exit;
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



    <div class="row">

       <div class="card shadow-sm border-0 mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">

        <div>
            <h3 class="mb-1 fw-bold text-danger">
                Collection Management
            </h3>
            <small class="text-muted">
                Official Receipt Booklet Management
            </small>
        </div>

        <div>
            <button class="btn btn-outline-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#bookletModal">
                <i class="bi bi-plus-circle"></i> Create BK
            </button>

<?php if ($isSuperAdmin): ?>

<div class="position-relative d-inline-block">

    <button class="btn btn-outline-danger"
            data-bs-toggle="modal"
            data-bs-target="#viewBookletsModal">
        <i class="bi bi-journal-text"></i> View BK
    </button>

    <?php if($pendingBooklets > 0): ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= $pendingBooklets ?>
        </span>
    <?php endif; ?>

</div>

<?php endif; ?>
<div class="position-relative d-inline-block">

    <button class="btn btn-outline-danger"
            data-bs-toggle="modal"
            data-bs-target="#transactionModal">
        <i class="bi bi-receipt"></i> Transactions
    </button>

    <?php if($pending_count > 0): ?>
    <span id="pendingBadge"
          class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
          style="cursor:pointer;">
        <?= $pending_count ?>
    </span>
    <?php endif; ?>

</div>

        </div>
        

    </div>
</div>
<div class="card shadow-sm border-0">

    <div class="card-body">

        <form method="POST">

            <div class="section-title mb-3">
                Payment Details
            </div>
<div class="row mb-3">

    <div class="col-md-3">
    <label class="form-label fw-semibold">
        Active BK
    </label>

    <input type="text"
           class="form-control bg-light fw-bold"
           value="<?= $active_bk ? $active_bk['booklet_no'] : 'No Active BK' ?>"
           readonly>
</div>

</div>
            <div class="row g-3 mb-4">

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Student ID
                    </label>

                    <input type="text"
                           id="student_id"
                           name="student_id"
                           class="form-control"
                           autocomplete="off"
                           required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        OR Number
                    </label>

                    <input type="number"
                           name="or_number"
                           class="form-control"
                           placeholder="Enter OR Number"
                           required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Date of Payment
                    </label>

                    <input type="date"
                           name="payment_date"
                           class="form-control"
                           value="<?= date('Y-m-d') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Amount
                    </label>

                    <input type="number"
                           step="0.01"
                           name="amount"
                           class="form-control"
                           placeholder="0.00"
                           required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Particular
                    </label>

                   <select name="particular" id="particular" class="form-select" required>

<option value="" selected>-- Select Particular --</option>

<option id="registrationOption" value="Registration Fee">
    Registration Fee
</option>

<option value="Tuition Fee">Tuition Fee</option>
<option value="Certificate">Certificate</option>
<option value="Miscellaneous">Miscellaneous</option>
<option value="TOR-Board Exam">TOR-Board Exam</option>
<option value="TOR-For Employment">TOR-For Employment</option>
<option value="Document Request">Document Request</option>
<option value="DST">DST</option>

</select>

<div id="registrationMessage" class="mt-2"></div>




                </div>
<div class="col-md-3">
    <label class="form-label fw-semibold">
        Reference Number
    </label>

    <input type="text"
           name="reference_number"
           class="form-control"
           placeholder="Reference No.">
</div>

<div class="col-md-4">
    <label class="form-label fw-semibold">
        Received By
    </label>

    <input type="text"
           class="form-control bg-light"
           value="<?= htmlspecialchars($_SESSION['name'] ?? $_SESSION['fullname'] ?? $_SESSION['username']) ?>"
           readonly>
</div>
            </div>
           <div class="row g-2 align-items-end mb-3">

    <div class="col-md-4">
        <label class="form-label fw-semibold">
            Balance
        </label>

        <input type="text"
               id="balance"
               class="form-control bg-light fw-bold"
               readonly>
    </div>

    <div class="col-md-8 text-end">
<button type="button"
        id="resetFormBtn"
        class="btn btn-secondary px-3">
    <i class="bi bi-arrow-clockwise"></i> Reset
</button>

<button type="submit"
        class="btn btn-danger px-3">
    <i class="bi bi-save"></i> Save Transaction
</button>

    </div>

</div>

<hr>
            <div class="section-title mb-3">
                Student Information
            </div>

          <div class="row g-2 mb-2">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Full Name
                    </label>

                    <input type="text"
                           id="fullname"
                           class="form-control bg-light"
                           readonly>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Contact No.
                    </label>

                    <input type="text"
                           id="contact_no"
                           class="form-control bg-light"
                           readonly>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-semibold">
                        Email Address
                    </label>

                    <input type="text"
                           id="email_address"
                           class="form-control bg-light"
                           readonly>
                </div>

            </div>

           <div class="row g-2">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Section
                    </label>

                    <input type="text"
                           id="section"
                           class="form-control bg-light"
                           readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Semester
                    </label>

                    <input type="text"
                           id="semester"
                           class="form-control bg-light"
                           readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        School Year
                    </label>

                    <input type="text"
                           id="school_year"
                           class="form-control bg-light"
                           readonly>
                </div>

    

</div>
            </div>
        </form>

    </div>

</div>
<?php include 'modals/booklet_modal.php'; ?>
<?php include 'modals/view_booklets_modal.php'; ?>
<?php include 'modals/transaction_modal.php'; ?>
<?php include 'modals/pending_modal.php'; ?>

<script>
document.getElementById('resetTransactionBtn').addEventListener('click', function() {

 document.getElementById('searchStudentTransaction').value = '';
    document.getElementById('studentName').value = '';

    document.getElementById('studentSemester').value = '';
    document.getElementById('studentSchoolYear').value = '';
    document.getElementById('studentSection').value = '';

    document.getElementById('transactionResult').innerHTML = '';

});
</script>
<script>
document.getElementById('filterTransactionBtn')
.addEventListener('click', function(){

    let student_id =
        document.getElementById('searchStudentTransaction').value.trim();

    if(student_id == ''){
        return;
    }

   fetch('get_student.php?student_id=' + encodeURIComponent(student_id))
.then(res => res.json())
.then(data => {

    document.getElementById('studentName').value =
        data.fullname ?? '';

    document.getElementById('studentSemester').value =
        data.semester ?? '';

    document.getElementById('studentSchoolYear').value =
        data.school_year ?? '';

    document.getElementById('studentSection').value =
        data.section ?? '';

    return fetch(
        'transaction_history.php?student_id=' +
        encodeURIComponent(student_id)
    );
})
    .then(res => res.text())
    .then(html => {
        document.getElementById('transactionResult').innerHTML = html;
    });

});
</script>
<script>
document.getElementById('student_id').addEventListener('input', function(){

    let student_id = this.value.trim();

    if(student_id === ''){
        document.getElementById('fullname').value = '';
        document.getElementById('section').value = '';
        document.getElementById('semester').value = '';
        document.getElementById('school_year').value = '';
        document.getElementById('contact_no').value = '';
        document.getElementById('email_address').value = '';
        document.getElementById('balance').value = '';
        return;
    }

    fetch('get_student.php?student_id=' + encodeURIComponent(student_id))
    .then(res => res.json())
    .then(data => {

        if(!data.student_id){
            document.getElementById('fullname').value = '';
            document.getElementById('section').value = '';
            document.getElementById('semester').value = '';
            document.getElementById('school_year').value = '';
            document.getElementById('contact_no').value = '';
            document.getElementById('email_address').value = '';
            document.getElementById('balance').value = '';
            return;
        }

        document.getElementById('fullname').value = data.fullname ?? '';
        document.getElementById('section').value = data.section ?? '';
        document.getElementById('semester').value = data.semester ?? '';
        document.getElementById('school_year').value = data.school_year ?? '';
        document.getElementById('contact_no').value = data.contact_no ?? '';
        document.getElementById('email_address').value = data.email_address ?? '';
        let registrationOption = document.getElementById('registrationOption');
let registrationMessage = document.getElementById('registrationMessage');

if(data.registration_fee == 1){

    registrationOption.disabled = true;

    registrationMessage.innerHTML = `
        <div class="alert alert-success py-2 mt-2">
            <i class="bi bi-check-circle"></i>
            Registration Fee is already paid.
        </div>
    `;

}else{

    registrationOption.disabled = false;

    registrationMessage.innerHTML = '';

}
        document.getElementById('balance').value =
    '₱ ' + parseFloat(data.balance || 0).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

        let bal = parseFloat(data.balance || 0);

        if(bal <= 0){
            document.getElementById('balance').style.color = 'green';
        }else{
            document.getElementById('balance').style.color = 'red';
        }

    })
    .catch(error => {
        console.error(error);
    });

});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if(isset($_SESSION['success'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Payment Saved',
        text: '<?= $_SESSION['success']; ?>',
        confirmButtonColor: '#198754'
    });
});
</script>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php if(isset($_SESSION['error'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function(){

    let msg = '<?= addslashes($_SESSION['error']) ?>';

    let title = 'Warning';

    if(msg.includes('Duplicate OR Number')){
        title = 'Duplicate OR Number';
    }

    if(msg.includes('No active OR booklet')){
        title = 'No Active Booklet';
    }

    Swal.fire({
        icon: 'warning',
        title: title,
        text: msg,
        confirmButtonColor: '#dc3545'
    });

});
</script>
<?php unset($_SESSION['error']); ?>
<?php endif; ?>
<script>
document.addEventListener('click', function(e){

    let btn = e.target.closest('.verify-btn');

    if(!btn) return;

    let payment_id = btn.dataset.id;
    let ref = btn.dataset.ref;

    Swal.fire({
        title: 'Verify Payment?',
        html: `
            <b>GCash Ref:</b><br>${ref}
            <br><br>
            Do you want to verify this payment?
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Verify',
        confirmButtonColor: '#198754'
    }).then((result)=>{

        if(result.isConfirmed){

            fetch('verify_payment.php?id=' + payment_id)
            .then(res => res.text())
            .then(() => {

                Swal.fire({
                    icon:'success',
                    title:'Verified',
                    text:'Payment successfully verified.',
                    timer:1500,
                    showConfirmButton:false
                });

                // Reload pending transactions modal
                fetch('pending_transactions.php')
                .then(res => res.text())
                .then(html => {
                    document.getElementById('pendingList').innerHTML = html;
                });

                // Update pending badge
                fetch('get_pending_count.php')
                .then(res => res.text())
                .then(count => {

                    let badge = document.getElementById('pendingBadge');

                    if(parseInt(count) > 0){

                        if(badge){
                            badge.innerHTML = count;
                        }

                    }else{

                        if(badge){
                            badge.remove();
                        }

                    }

                });

                // Reload student transaction history if open
                let student_id =
                    document.getElementById('searchStudentTransaction')?.value;

                if(student_id){

                    fetch(
                        'transaction_history.php?student_id=' +
                        encodeURIComponent(student_id)
                    )
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('transactionResult').innerHTML = html;
                    });

                }

            });

        }

    });

});
</script>
<script>
    document.addEventListener('click', function(e){

    if(e.target.id === 'pendingBadge'){

        fetch('pending_transactions.php')
        .then(res => res.text())
        .then(html => {

            document.getElementById('pendingList').innerHTML = html;

            new bootstrap.Modal(
                document.getElementById('pendingModal')
            ).show();

        });

    }

});
</script>

<script>
document.getElementById('resetFormBtn').addEventListener('click', function(e){

    e.preventDefault();

    // Payment fields
    document.getElementById('student_id').value = '';
    document.querySelector('[name="or_number"]').value = '';
    document.querySelector('[name="amount"]').value = '';
    document.querySelector('[name="reference_number"]').value = '';
    document.querySelector('[name="particular"]').selectedIndex = 0;
    document.getElementById('registrationMessage').innerHTML = '';

    // Student information
    document.getElementById('fullname').value = '';
    document.getElementById('contact_no').value = '';
    document.getElementById('email_address').value = '';
    document.getElementById('section').value = '';
    document.getElementById('semester').value = '';
    document.getElementById('school_year').value = '';
    document.getElementById('balance').value = '';

    // Focus balik sa Student ID
    document.getElementById('student_id').focus();

});
</script>
</body>
</html>