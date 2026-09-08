<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if (!isset($_SESSION['fullname'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$page = $_GET['page'] ?? 'account';

function activeTab($current, $tab)
{
    return $current === $tab
        ? 'btn-maroon text-white'
        : 'btn-outline-secondary';
}

$student_pk = (int)$_GET['id'];
/*
|--------------------------------------------------------------------------
| GET STUDENT INFORMATION
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        id,
        fullname,
        student_id,
        email_address,
        contact_no,
        semester,
        school_year,
        section,
        let_exam,
        let_status,
        let_month,
        let_year
    FROM students
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $student_pk);
$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Student not found.");
}


/*
|--------------------------------------------------------------------------
| STUDENT VARIABLES
|--------------------------------------------------------------------------
*/

$fullname      = $student['fullname'] ?? '';
$student_id    = $student['student_id'] ?? '';
$email_address = $student['email_address'] ?? '';
$contact_no    = $student['contact_no'] ?? '';
$semester      = $student['semester'] ?? '';
$school_year   = $student['school_year'] ?? '';
$section       = $student['section'] ?? '';


/*
|--------------------------------------------------------------------------
| LET INFORMATION
|--------------------------------------------------------------------------
*/

$let_exam   = (int)($student['let_exam'] ?? 0);
$let_status = $student['let_status'] ?? '';
$let_month  = $student['let_month'] ?? '';
$let_year   = $student['let_year'] ?? '';


/*
|--------------------------------------------------------------------------
| LET PASSER
|--------------------------------------------------------------------------
*/

$is_let_passer = (
    $let_exam === 1 &&
    $let_status === 'Passer'
);


/*
|--------------------------------------------------------------------------
| FORMAT LET MONTH
|--------------------------------------------------------------------------
*/

$displayMonth = match (strtoupper(trim($let_month))) {

    'MA',
    'MARCH'     => 'MARCH',

    'SE',
    'SEPTEMBER' => 'SEPTEMBER',

    default     => strtoupper($let_month)

};


/*
|--------------------------------------------------------------------------
| GET STUDENT ACCOUNT
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        total_amount,
        total_paid,
        balance
    FROM student_accounts
    WHERE student_id = ?
    LIMIT 1
");

$stmt->bind_param("s", $student_id);
$stmt->execute();

$account = $stmt->get_result()->fetch_assoc();

$stmt->close();


$total_amount = $account['total_amount'] ?? 0;
$total_paid   = $account['total_paid'] ?? 0;
$balance      = $account['balance'] ?? 0;


/*
|--------------------------------------------------------------------------
| GET AVAILABLE LET YEARS
|--------------------------------------------------------------------------
*/

$letYears = [];

$result = $conn->query("
    SELECT year
    FROM let_year
    ORDER BY year DESC
");

while ($year = $result->fetch_assoc()) {

    $letYears[] = $year['year'];

}

/*
|--------------------------------------------------------------------------
| GET EMAIL SIGNATURES
|--------------------------------------------------------------------------
*/

$signatures = [];

$result = $conn->query("
    SELECT *
    FROM email_signatures
    ORDER BY signature_name ASC
");

while ($signature = $result->fetch_assoc()) {
    $signatures[] = $signature;
}

/*
|--------------------------------------------------------------------------
| BACK QUERY
|--------------------------------------------------------------------------
*/

$backQuery = http_build_query([
    'search'             => $_GET['search'] ?? '',
    'semester_filter'    => $_GET['semester_filter'] ?? '',
    'school_year_filter' => $_GET['school_year_filter'] ?? '',
    'section_filter'     => $_GET['section_filter'] ?? '',
    'status_filter'      => $_GET['status_filter'] ?? '',
    'card_filter'        => $_GET['card_filter'] ?? '',
    'page'               => $_GET['page'] ?? 1
]);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/view_student.css">
   <style>/* =========================================================
   LET PASSER BADGE
========================================================= */

.let-result-card {
    position: relative;
    width: 100%;
    max-width: 320px;
    margin: 0 auto;
    padding: 24px;
    background: #fff;
    border: 1px solid #e2e2e2;
    border-radius: 16px;
    box-shadow: 0 5px 18px rgba(0,0,0,.08);
    overflow: hidden;
}

.let-result-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: #800000;
}

.let-result-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
}

.let-result-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    background: #800000;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 21px;
}

.let-result-title {
    font-size: 15px;
    font-weight: 700;
    color: #800000;
    margin: 0;
}

.let-result-subtitle {
    font-size: 11px;
    color: #777;
    margin-top: 2px;
}

.let-result-line {
    height: 1px;
    background: #eee;
    margin: 14px 0;
}

.let-result-label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #777;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 3px;
}

.let-result-value {
    font-size: 14px;
    font-weight: 700;
    color: #333;
}

.let-passed-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 13px;
    margin-top: 5px;
    border-radius: 20px;
    background: #e9f7ef;
    color: #198754;
    font-size: 12px;
    font-weight: 700;
}
#letModal .modal-content{
    border-radius:18px;
}

#letModal .modal-header{
    padding:22px 24px;
}

#letModal .modal-body{
    background:#fff;
}

#letModal .form-label{
    font-size:13px;
    margin-bottom:6px;
}

#letModal .form-control,
#letModal .form-select{
    height:46px;
    border-radius:10px;
    border:1px solid #dcdcdc;
    box-shadow:none;
}

#letModal .form-control:focus,
#letModal .form-select:focus{
    border-color:#800000;
    box-shadow:0 0 0 .15rem rgba(128,0,0,.15);
}

#letModal .btn-dark{
    background:#800000;
    border-color:#800000;
}

#letModal .btn-dark:hover{
    background:#660000;
    border-color:#660000;
}
</style>
    
    
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/sweetalert2.all.min.js"></script>

</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">

<div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-light">



    <div class="d-flex align-items-center">

        <div>

            <h4 class="mb-1 fw-bold text-dark">
                <?= htmlspecialchars($student['fullname'] ?? 'No Name'); ?>
            </h4>

            <small class="text-muted">
                Student ID: <?= htmlspecialchars($student['student_id']); ?>
            </small>

        </div>

        <div class="ms-auto d-flex gap-2">

            <!-- LET BUTTON -->
    <button class="btn <?= $is_let_passer ? 'btn-success' : 'btn-outline-secondary' ?>"
            data-bs-toggle="modal"
            data-bs-target="#letModal">
        <?= $is_let_passer ? '🏅 LET PASSER' : '🎓 Update LET' ?>
    </button>


            <button class="btn btn-primary btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#emailModal">
                📧 Send Email
            </button>


<a href="student_list.php"
   class="btn btn-secondary btn-sm">
    ← Back to Student List
</a>

</div>
</div>
<div class="row g-3">

    <!-- PERSONAL INFO -->
    <div class="col-md-4">

        <div class="student-card h-100">

            <h6 class="fw-bold text-danger mb-3">
                Personal Information
            </h6>

            <div class="mb-2">
                <span class="text-muted">Email:</span>
                <span class="fw-semibold">
                    <?= htmlspecialchars($student['email_address'] ?? 'N/A'); ?>
                </span>
            </div>

            <div class="mb-2">
                <span class="text-muted">Contact:</span>
                <span class="fw-semibold">
                    <?= htmlspecialchars($student['contact_no'] ?? 'N/A'); ?>
                </span>
            </div>

        </div>

    </div>


    <!-- ACADEMIC INFO -->
    <div class="col-md-4">

        <div class="student-card h-100">

            <h6 class="fw-bold text-danger mb-3">
                Academic Information
            </h6>

            <div class="mb-2">
                <span class="text-muted">Semester:</span>
                <span class="fw-semibold">
                    <?= htmlspecialchars($student['semester'] ?? 'N/A'); ?>
                </span>
            </div>

            <div class="mb-2">
                <span class="text-muted">School Year:</span>
                <span class="fw-semibold">
                    <?= htmlspecialchars($student['school_year'] ?? 'N/A'); ?>
                </span>
            </div>

            <div class="mb-2">
                <span class="text-muted">Section:</span>
                <span class="fw-semibold">
                    <?= htmlspecialchars($student['section'] ?? 'N/A'); ?>
                </span>
            </div>

        </div>

    </div>


    <!-- LET STATUS -->
    <div class="col-md-4">

        <div class="student-card h-100">

            <h6 class="fw-bold text-danger mb-3">
                LET Examination
            </h6>


            <?php if ($let_exam == 1 && $let_status == 1): ?>

                <!-- PASSER -->

                <div class="mb-3">

                    <span class="badge bg-success px-3 py-2"
                          style="font-size: 0.85rem;">

                        <i class="bi bi-patch-check-fill me-1"></i>
                        CERTIFIED LET PASSER

                    </span>

                </div>


                <div class="small">

                    <span class="text-muted">
                        Examination Period:
                    </span>

                    <div class="fw-semibold mt-1">

                        <i class="bi bi-calendar-event me-1"></i>

                        <?= htmlspecialchars($let_month) ?>
                        <?= htmlspecialchars($let_year) ?>

                    </div>

                </div>


            <?php elseif ($let_exam == 1 && $let_status == 0): ?>

                <!-- NOT PASSER -->

                <div class="mb-2">

                    <span class="badge bg-secondary px-3 py-2"
                          style="font-size: 0.85rem;">

                        <i class="bi bi-info-circle-fill me-1"></i>
                        LET EXAM TAKEN — NOT PASSER

                    </span>

                </div>


                <small class="text-muted">
                    Examination result: Not Passer
                </small>


            <?php else: ?>

                <!-- NOT TAKEN -->

                <div class="mb-2">

                    <span class="badge bg-light text-secondary border px-3 py-2"
                          style="font-size: 0.85rem;">

                        <i class="bi bi-dash-circle me-1"></i>
                        LET NOT TAKEN

                    </span>

                </div>


                <small class="text-muted">
                    No LET examination record.
                </small>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php

// TAB DEFAULT
$tab = $_GET['tab'] ?? 'account';




?>



<!-- STUDENT TABS -->

<div class="mb-3">

    <div class="student-tabs">


       <a href="view_student.php?id=<?= $student_pk ?>&tab=account"
   class="<?= ($tab == 'account') ? 'active' : '' ?>">
    Account Summary
</a>

<a href="view_student.php?id=<?= $student_pk ?>&tab=credentials"
   class="<?= ($tab == 'credentials') ? 'active' : '' ?>">
    Credentials
</a>


<a href="view_student.php?id=<?= $student_pk ?>&tab=email"
   class="<?= ($tab == 'email') ? 'active' : '' ?>">
    Email History
</a>


    </div>

</div>
</div>





<!-- TAB CONTENT -->

<div class="tab-content mt-3">


<?php

switch ($tab) {


    case 'credentials':

        include 'tabs/credentials.php';

    break;




    case 'email':

        include 'tabs/email_history.php';

    break;



    case 'account':

    default:

        include 'tabs/account_summary.php';

    break;


}


?>
<div class="modal fade"
     id="letModal"
     tabindex="-1"
     data-bs-backdrop="static">

    <div class="modal-dialog modal-dialog-centered">

        <form action="update_let.php" method="POST">

            <input type="hidden"
                   name="student_pk"
                   value="<?= $student_pk ?>">

            <input type="hidden"
                   name="student_id"
                   value="<?= htmlspecialchars($student_id) ?>">

            <div class="modal-content border-0 shadow rounded-4">

                <!-- HEADER -->
                <div class="modal-header bg-white border-bottom">

                    <div>
                        <h5 class="modal-title fw-bold mb-1">
                            LET Examination Record
                        </h5>

                        <small class="text-muted">
                            Update licensure examination information
                        </small>
                    </div>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>

                </div>


                <!-- BODY -->
                <div class="modal-body p-4">

                    <!-- TOOK LET EXAM -->
                    <div class="mb-4">

                        <div class="form-check">

                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="letExam"
                                name="let_exam"
                                value="1"
                                <?= $let_exam === 1 ? 'checked' : '' ?>
                            >

                            <label
                                class="form-check-label fw-semibold"
                                for="letExam">

                                Student took the LET Examination

                            </label>

                        </div>

                        <small class="text-muted">
                            Check this if the student took the LET examination.
                        </small>

                    </div>


                    <!-- EXAMINATION RESULT -->
                    <div class="mb-4">

                        <label class="form-label fw-semibold">
                            Examination Result
                        </label>

                        <select
                            class="form-select"
                            name="let_status"
                            id="letStatus"
                            required>

                            <option value="1"
                                <?= $let_status == 1 ? 'selected' : '' ?>>
                                Passer
                            </option>

                            <option value="0"
                                <?= $let_status == 0 ? 'selected' : '' ?>>
                                Not Passer
                            </option>

                        </select>

                    </div>


                    <!-- MONTH AND YEAR -->
                    <div class="row">

                        <!-- MONTH -->
                        <div class="col-6">

                            <label class="form-label fw-semibold">
                                Month
                            </label>

                            <select
                                class="form-select"
                                name="let_month"
                                id="letMonth">

                                <option value="">
                                    Select
                                </option>

                                <option value="March"
                                    <?= $let_month == 'March' ? 'selected' : '' ?>>
                                    March
                                </option>

                                <option value="September"
                                    <?= $let_month == 'September' ? 'selected' : '' ?>>
                                    September
                                </option>

                            </select>

                        </div>


                        <!-- YEAR -->
                        <div class="col-6">

                            <label class="form-label fw-semibold">
                                Year
                            </label>

                            <select
                                class="form-select"
                                name="let_year"
                                id="letYear">

                                <option value="">
                                    Select
                                </option>

                                <?php foreach ($letYears as $year): ?>

                                    <option
                                        value="<?= htmlspecialchars($year) ?>"
                                        <?= $let_year == $year ? 'selected' : '' ?>>

                                        <?= htmlspecialchars($year) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                    </div>

                </div>


                <!-- FOOTER -->
                <div class="modal-footer bg-white border-top">

                    <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit"
                            class="btn btn-maroon">

                        Save Changes

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


<script>

document.addEventListener("DOMContentLoaded", function () {

    const letExam   = document.getElementById("letExam");
    const letStatus = document.getElementById("letStatus");
    const letMonth  = document.getElementById("letMonth");
    const letYear   = document.getElementById("letYear");


    function updateLETFields() {

        /*
         * Month and Year are required ONLY when:
         * LET was taken AND student is a Passer.
         */

        const isTaken  = letExam.checked;
        const isPasser = letStatus.value === "1";

        const enablePeriod = isTaken && isPasser;


        letMonth.disabled = !enablePeriod;
        letYear.disabled  = !enablePeriod;


        letMonth.required = enablePeriod;
        letYear.required  = enablePeriod;


        /*
         * If not a passer, clear Month and Year.
         */

        if (!enablePeriod) {

            letMonth.value = "";
            letYear.value = "";

        }

    }


    // When checkbox changes
    letExam.addEventListener("change", updateLETFields);


    // When result changes
    letStatus.addEventListener("change", updateLETFields);


    // Initial state
    updateLETFields();

});

</script>

<!-- EMAIL MODAL -->
<div class="modal fade"
     id="emailModal"
     tabindex="-1"
     data-bs-backdrop="static">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <!-- Header -->
            <div class="modal-header bg-maroon text-white">

                <div>
       <h5 class="modal-title mb-1">
    📧 STUDENT EMAIL
</h5>

<small class="opacity-75">
    Send an official notification or update
</small>
                </div>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>

            </div>

            <form action="send_email.php" method="POST">

                <div class="modal-body">

                    <input type="hidden" name="student_pk"
                           value="<?= $student['id']; ?>">

                    <input type="hidden" name="student_id"
                           value="<?= htmlspecialchars($student_id); ?>">

                    <input type="hidden" name="page"
                           value="<?= htmlspecialchars($page); ?>">

                    <input type="hidden" name="email"
                           value="<?= htmlspecialchars($student['email_address'] ?? ''); ?>">

                    <div class="row g-4">

                        <!-- LEFT SIDE -->
                        <div class="col-lg-7">

                            <div class="student-card">

                                <div class="card-body">


                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            Recipient
                                        </label>

                                        <input type="text"
                                               class="form-control"
                                               value="<?= htmlspecialchars($student['email_address'] ?? ''); ?>"
                                               readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            Subject
                                        </label>

                                        <input type="text"
                                               name="subject"
                                               class="form-control"
                                               placeholder="Enter email subject"
                                               required>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label fw-bold">
                                            Message
                                        </label>

                                        <textarea
                                            name="message"
                                            id="messageBox"
                                            class="form-control"
                                            rows="10"
                                            placeholder="Type your message here..."
                                            required></textarea>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- RIGHT SIDE -->
                        <div class="col-lg-5">

                            <div class="card border-0 shadow-sm mb-3">

                           
                                <div class="card-body">

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            Select Signature
                                        </label>

                                        <select name="signature_id"
                                                id="signatureSelect"
                                                class="form-select">

                                            <option value="">
                                                -- Select Signature --
                                            </option>

                                            <?php foreach ($signatures as $sig): ?>
                                                <option
                                                    value="<?= htmlspecialchars($sig['id']); ?>"
                                                    data-content="<?= htmlspecialchars($sig['signature_content']); ?>">
                                                    <?= htmlspecialchars($sig['signature_name']); ?>
                                                </option>
                                            <?php endforeach; ?>

                                        </select>
                                    </div>

                                    <div>
                                        <label class="form-label fw-bold">
                                            Preview
                                        </label>

                                        <textarea
                                            name="signature_content"
                                            id="signaturePreview"
                                            class="form-control"
                                            rows="6"
                                            readonly></textarea>
                                    </div>

                                </div>

                            </div>

                            <div class="card border-0 shadow-sm">

                                <div class="card-header d-flex justify-content-between align-items-center">

                            

                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#newSignatureSection">
                                        Create New Signature
                                    </button>

                                </div>

                                <div class="collapse" id="newSignatureSection">

                                    <div class="card-body">

                                        <div class="mb-3">

                                            <label class="form-label fw-bold">
                                                Signature Name
                                            </label>

                                            <input type="text"
                                                   id="signature_name"
                                                   class="form-control"
                                                   placeholder="Registrar Official">

                                        </div>

                                        <div class="mb-3">

                                            <label class="form-label fw-bold">
                                                Signature Content
                                            </label>

                                            <textarea
                                                id="signature_content"
                                                class="form-control"
                                                rows="5"
                                                placeholder="Type your signature here..."></textarea>

                                        </div>

                                        <button type="button"
                                                id="saveSignatureBtn"
                                                class="btn btn-primary w-100">
                                            Save Signature
                                        </button>

                                        <div id="saveSignatureMsg"
                                             class="mt-2"></div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer">

                    <button type="submit"
                            class="btn btn-maroon px-4">
                        📩 Send Email
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
<!-- Email History Details Modal -->

<div class="modal fade"
     id="viewMessageModal"
     tabindex="-1"
     data-bs-backdrop="static"
     data-bs-keyboard="false"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow border-0">

            <div class="modal-header email-history-header text-white">
                <div>
                    <h5 class="modal-title mb-0">
                        📧 Email History Details
                    </h5>
                    <small class="opacity-75">
                        View sent email information
                    </small>
                </div>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body p-4">

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">
                        Subject
                    </label>

                    <div class="border rounded-3 p-3 bg-light">
                        <span id="modalSubject"></span>
                    </div>
                </div>

                <div>
                    <label class="form-label fw-bold text-secondary">
                        Message
                    </label>

                    <div id="modalMessage"
                         class="border rounded-3 p-3 bg-white"
                         style="
                            min-height:220px;
                            max-height:450px;
                            overflow-y:auto;
                            white-space:pre-wrap;
                            line-height:1.7;
                         ">
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<script>
document.addEventListener('click', function(e){

    const btn = e.target.closest('.view-message-btn');

    if (!btn) return;

    document.getElementById('modalSubject').textContent =
        btn.getAttribute('data-subject') || '';

    document.getElementById('modalMessage').textContent =
        btn.getAttribute('data-message') || 'No message content available.';

    const modal = new bootstrap.Modal(
        document.getElementById('viewMessageModal')
    );

    modal.show();
});
</script>

<script>
document.getElementById('saveSignatureBtn')
.addEventListener('click', function(){

    let signatureName =
        document.getElementById('signature_name').value.trim();

    let signatureContent =
        document.getElementById('signature_content').value.trim();

    if(signatureName === '' || signatureContent === ''){

        Swal.fire({
            icon:'warning',
            title:'Required',
            text:'Please complete all fields.'
        });

        return;
    }

    fetch('save_signature.php', {
        method:'POST',
        headers:{
            'Content-Type':'application/x-www-form-urlencoded'
        },
        body:
            'signature_name=' +
            encodeURIComponent(signatureName) +
            '&signature_content=' +
            encodeURIComponent(signatureContent)
    })
    .then(response => response.json())
    .then(data => {

        if(data.status === 'success'){

            Swal.fire({
                icon:'success',
                title:'Saved',
                text:data.message
            });

            let select =
                document.getElementById('signatureSelect');

            let option =
                document.createElement('option');

            option.value = data.id;
            option.text = signatureName;
            option.dataset.content = signatureContent;

            select.appendChild(option);

           

            // Clear form
            document.getElementById('signature_name').value = '';
            document.getElementById('signature_content').value = '';

            // Close Create Signature section
            const collapseElement =
                document.getElementById('newSignatureSection');

            const collapse =
                bootstrap.Collapse.getOrCreateInstance(collapseElement);

            collapse.hide();

        } else {

            Swal.fire({
                icon:'error',
                title:'Error',
                text:data.message
            });

        }

    })
    .catch(() => {

        Swal.fire({
            icon:'error',
            title:'Error',
            text:'Failed to save signature.'
        });

    });

});
</script>
<script>
document.getElementById('signatureSelect')
.addEventListener('change', function(){

    let selected =
        this.options[this.selectedIndex];

    document.getElementById('signaturePreview')
        .value =
        selected.dataset.content || '';

});
</script>
</body>
</html>