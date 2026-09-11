<?php
/*
|--------------------------------------------------------------------------
| STUDENT EDIT / DELETE
|--------------------------------------------------------------------------
| Allowed roles: admin, superadmin
| This file expects $conn and $result to already be available.
|--------------------------------------------------------------------------
*/

$allowed_roles = ['admin', 'superadmin'];
$current_role = strtolower(trim($_SESSION['role'] ?? ''));

if (!in_array($current_role, $allowed_roles, true)) {
    // Keep the student list visible, but do not allow edit/delete actions.
    $can_manage_students = false;
} else {
    $can_manage_students = true;
}

/*
|--------------------------------------------------------------------------
| UPDATE STUDENT
|--------------------------------------------------------------------------
*/
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) &&
    $_POST['action'] === 'update_student'
) {
    if (!$can_manage_students) {
        $_SESSION['student_action_error'] = 'You are not authorized to edit student records.';
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    $id          = (int)($_POST['student_db_id'] ?? 0);
    $student_id  = trim($_POST['student_id'] ?? '');
    $fullname    = trim($_POST['fullname'] ?? '');
    $semester    = trim($_POST['semester'] ?? '');
    $school_year = trim($_POST['school_year'] ?? '');
    $section     = trim($_POST['section'] ?? '');
    $let_status  = trim($_POST['let_status'] ?? '');

    if (
        $id <= 0 ||
        $student_id === '' ||
        $fullname === '' ||
        $semester === '' ||
        $school_year === '' ||
        $section === ''
    ) {
        $_SESSION['student_action_error'] = 'Please complete all required student details.';
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE students
         SET student_id = ?,
             fullname = ?,
             semester = ?,
             school_year = ?,
             section = ?,
             let_status = ?
         WHERE id = ?
         LIMIT 1"
    );

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "ssssssi",
            $student_id,
            $fullname,
            $semester,
            $school_year,
            $section,
            $let_status,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['student_action_success'] = 'Student details updated successfully.';
        } else {
            $_SESSION['student_action_error'] = 'Unable to update student details: ' . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['student_action_error'] = 'Unable to prepare the update query.';
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

/*
|--------------------------------------------------------------------------
| DELETE STUDENT
|--------------------------------------------------------------------------
*/
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) &&
    $_POST['action'] === 'delete_student'
) {
    if (!$can_manage_students) {
        $_SESSION['student_action_error'] = 'You are not authorized to delete student records.';
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    $id = (int)($_POST['student_db_id'] ?? 0);

    if ($id <= 0) {
        $_SESSION['student_action_error'] = 'Invalid student record.';
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM students WHERE id = ? LIMIT 1"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['student_action_success'] = 'Student record deleted successfully.';
        } else {
            $_SESSION['student_action_error'] =
                'Unable to delete student record. If related records exist, remove or update those records first.';
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['student_action_error'] = 'Unable to prepare the delete query.';
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

$student_action_success = $_SESSION['student_action_success'] ?? '';
$student_action_error   = $_SESSION['student_action_error'] ?? '';

unset(
    $_SESSION['student_action_success'],
    $_SESSION['student_action_error']
);
?>

<link rel="stylesheet" href="assets/css/student_table.css">

<div class="student-table-card">

    <div class="student-table-header d-flex justify-content-between align-items-center">
        <span>Student List</span>
    </div>

    <?php if ($student_action_success !== ''): ?>
        <div class="alert alert-success mx-3 mt-3 mb-0">
            <?= htmlspecialchars($student_action_success, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($student_action_error !== ''): ?>
        <div class="alert alert-danger mx-3 mt-3 mb-0">
            <?= htmlspecialchars($student_action_error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">

        <table class="table student-table align-middle">

            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Semester</th>
                    <th>School Year</th>
                    <th>Section</th>
                    <th>LET</th>
                    <th>Student Status</th>
                    <th>Credentials</th>
                    <th>Balance</th>
                    <th width="170">Action</th>
                </tr>
            </thead>

            <tbody id="studentTableBody">

            <?php

            if(mysqli_num_rows($result) > 0){

                while($row = mysqli_fetch_assoc($result)){

                    $student_id = mysqli_real_escape_string(
                        $conn,
                        $row['student_id']
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | CREDENTIALS
                    |--------------------------------------------------------------------------
                    */

                    $cred_res = mysqli_query($conn, "
                        SELECT *
                        FROM student_credentials
                        WHERE student_id='$student_id'
                    ");

                    $cred = mysqli_fetch_assoc($cred_res) ?: [];

                    $ctc_tor_granted =
                        !empty($cred['ctc_tor_granted']) &&
                        $cred['ctc_tor_granted'] == 1;

                    $ctc_request =
                        !empty($cred['ctc_request']) &&
                        $cred['ctc_request'] == 1;

                    $gmc =
                        !empty($cred['gmc']) &&
                        $cred['gmc'] == 1;

                    $psa =
                        !empty($cred['psa_birth_marriage_certificate']) &&
                        $cred['psa_birth_marriage_certificate'] == 1;

                    /*
                    |--------------------------------------------------------------------------
                    | CREDENTIAL STATUS
                    |--------------------------------------------------------------------------
                    */

                    if(
                        $ctc_tor_granted &&
                        $ctc_request &&
                        $gmc &&
                        $psa
                    ){

                        $credential_badge = "
                            <span class='badge-status badge-complete'>
                                Complete
                            </span>
                        ";

                    }elseif($ctc_tor_granted){

                        $credential_badge = "
                            <span class='badge-status'
                                  style='background:#cfe2ff;color:#084298;'>
                                Partial
                            </span>
                        ";

                    }else{

                        $credential_badge = "
                            <span class='badge-status badge-incomplete'>
                                Incomplete
                            </span>
                        ";
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | LET STATUS
                    |--------------------------------------------------------------------------
                    */

                    $let_badge = '';

                    if(
                        isset($row['let_status']) &&
                        $row['let_status'] == 1
                    ){

                        $let_badge = "
                            <span class='badge-status badge-complete'>
                                PASSED
                            </span>
                        ";
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | STUDENT ACCOUNT
                    |--------------------------------------------------------------------------
                    */

                    $account_res = mysqli_query($conn, "
                        SELECT
                            registration_fee,
                            total_amount,
                            total_paid,
                            balance
                        FROM student_accounts
                        WHERE student_id='$student_id'
                        LIMIT 1
                    ");

                    $account = mysqli_fetch_assoc($account_res) ?: [];

                    $registration_fee =
                        (int)($account['registration_fee'] ?? 0);

                    $total_amount =
                        (float)($account['total_amount'] ?? 0);

                    $total_paid =
                        (float)($account['total_paid'] ?? 0);

                    /*
                    |--------------------------------------------------------------------------
                    | BALANCE
                    |--------------------------------------------------------------------------
                    */

                    if(
                        isset($account['balance']) &&
                        $account['balance'] !== null
                    ){

                        $balance = (float)$account['balance'];

                    }else{

                        $balance = $total_amount - $total_paid;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | PREVENT NEGATIVE BALANCE
                    |--------------------------------------------------------------------------
                    */

                    if($balance < 0){
                        $balance = 0;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | STUDENT STATUS
                    |--------------------------------------------------------------------------
                    */

                    if($registration_fee == 1){

                        $student_status_badge = "
                            <span class='badge-status badge-official'>
                                Enrolled
                            </span>
                        ";

                    }else{

                        $student_status_badge = "
                            <span class='badge-status badge-unofficial'>
                                Unenrolled
                            </span>
                        ";
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | PAYMENT STATUS
                    |--------------------------------------------------------------------------
                    */

                    if($balance <= 0){

                        $payment_badge = "
                            <span class='text-success'>
                                Fully Paid
                            </span>
                        ";

                    }else{

                        $payment_badge = "
                            <span>
                                ₱".number_format($balance, 2)."
                            </span>
                        ";
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | SAFE OUTPUT VALUES
                    |--------------------------------------------------------------------------
                    */

                    $db_id       = (int)$row['id'];
                    $safe_sid    = htmlspecialchars($row['student_id'] ?? '', ENT_QUOTES, 'UTF-8');
                    $safe_name   = htmlspecialchars($row['fullname'] ?? '', ENT_QUOTES, 'UTF-8');
                    $safe_sem    = htmlspecialchars($row['semester'] ?? '', ENT_QUOTES, 'UTF-8');
                    $safe_sy     = htmlspecialchars($row['school_year'] ?? '', ENT_QUOTES, 'UTF-8');
                    $safe_section = htmlspecialchars($row['section'] ?? '', ENT_QUOTES, 'UTF-8');
                    $safe_let_status = htmlspecialchars((string)($row['let_status'] ?? ''), ENT_QUOTES, 'UTF-8');

                    /*
                    |--------------------------------------------------------------------------
                    | OUTPUT ROW
                    |--------------------------------------------------------------------------
                    */

                    echo "
                    <tr>

                        <td>
                            {$safe_sid}
                        </td>

                        <td class='student-name'>
                            {$safe_name}
                        </td>

                        <td>
                            {$safe_sem}
                        </td>

                        <td>
                            {$safe_sy}
                        </td>

                        <td>
                            {$safe_section}
                        </td>

                        <td>
                            {$let_badge}
                        </td>

                        <td>
                            {$student_status_badge}
                        </td>

                        <td>
                            {$credential_badge}
                        </td>

                        <td>
                            {$payment_badge}
                        </td>

                        <td>
                            <div class='d-flex gap-1 flex-wrap'>
                                <a
                                    href='view_student.php?id={$db_id}'
                                    class='btn btn-view btn-sm'
                                >
                                    View
                                </a>
                    ";

                    if ($can_manage_students) {
                        echo "
                                <button
                                    type='button'
                                    class='btn btn-warning btn-sm'
                                    data-bs-toggle='modal'
                                    data-bs-target='#editStudentModal'
                                    data-id='{$db_id}'
                                    data-student-id=\"" . htmlspecialchars($row['student_id'] ?? '', ENT_QUOTES, 'UTF-8') . "\"
                                    data-fullname=\"" . htmlspecialchars($row['fullname'] ?? '', ENT_QUOTES, 'UTF-8') . "\"
                                    data-semester=\"" . htmlspecialchars($row['semester'] ?? '', ENT_QUOTES, 'UTF-8') . "\"
                                    data-school-year=\"" . htmlspecialchars($row['school_year'] ?? '', ENT_QUOTES, 'UTF-8') . "\"
                                    data-section=\"" . htmlspecialchars($row['section'] ?? '', ENT_QUOTES, 'UTF-8') . "\"
                                    data-let-status=\"" . htmlspecialchars((string)($row['let_status'] ?? ''), ENT_QUOTES, 'UTF-8') . "\"
                                >
                                    Edit
                                </button>

                                <button
                                    type='button'
                                    class='btn btn-danger btn-sm btn-delete-student'
                                    data-id='{$db_id}'
                                    data-name=\"" . htmlspecialchars($row['fullname'] ?? '', ENT_QUOTES, 'UTF-8') . "\"
                                >
                                    Delete
                                </button>
                        ";
                    }

                    echo "
                            </div>
                        </td>

                    </tr>
                    ";
                }

            }else{

                echo "
                <tr>
                    <td colspan='10' class='text-center py-4'>
                        No students found
                    </td>
                </tr>
                ";
            }

            ?>

            </tbody>

        </table>

    </div>

</div>

<?php if ($can_manage_students): ?>

<!-- =========================================================
     EDIT STUDENT MODAL
========================================================= -->
<div
    class="modal fade"
    id="editStudentModal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form method="POST">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Student Details</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="action" value="update_student">
                    <input type="hidden" name="student_db_id" id="edit_student_db_id">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Student ID</label>
                            <input
                                type="text"
                                name="student_id"
                                id="edit_student_id"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input
                                type="text"
                                name="fullname"
                                id="edit_fullname"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Semester</label>
                            <input
                                type="text"
                                name="semester"
                                id="edit_semester"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">School Year</label>
                            <input
                                type="text"
                                name="school_year"
                                id="edit_school_year"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Section</label>
                            <input
                                type="text"
                                name="section"
                                id="edit_section"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">LET Status</label>
                            <select
                                name="let_status"
                                id="edit_let_status"
                                class="form-select"
                            >
                                <option value="">Not Set</option>
                                <option value="1">PASSED</option>
                                <option value="0">NOT PASSED</option>
                            </select>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Save Changes
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- =========================================================
     DELETE FORM
========================================================= -->
<form
    method="POST"
    id="deleteStudentForm"
    style="display:none;"
>
    <input type="hidden" name="action" value="delete_student">
    <input type="hidden" name="student_db_id" id="delete_student_db_id">
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =====================================================
       EDIT STUDENT
    ===================================================== */
    const editModal = document.getElementById('editStudentModal');

    if (editModal) {

        editModal.addEventListener('show.bs.modal', function (event) {

            const button = event.relatedTarget;

            document.getElementById('edit_student_db_id').value =
                button.getAttribute('data-id') || '';

            document.getElementById('edit_student_id').value =
                button.getAttribute('data-student-id') || '';

            document.getElementById('edit_fullname').value =
                button.getAttribute('data-fullname') || '';

            document.getElementById('edit_semester').value =
                button.getAttribute('data-semester') || '';

            document.getElementById('edit_school_year').value =
                button.getAttribute('data-school-year') || '';

            document.getElementById('edit_section').value =
                button.getAttribute('data-section') || '';

            document.getElementById('edit_let_status').value =
                button.getAttribute('data-let-status') || '';

        });

    }

    /* =====================================================
       DELETE STUDENT
    ===================================================== */
    document.querySelectorAll('.btn-delete-student').forEach(function (button) {

        button.addEventListener('click', function () {

            const studentId = this.getAttribute('data-id');
            const studentName = this.getAttribute('data-name') || 'this student';

            const deleteForm = document.getElementById('deleteStudentForm');
            const deleteId = document.getElementById('delete_student_db_id');

            deleteId.value = studentId;

            if (typeof Swal !== 'undefined') {

                Swal.fire({
                    title: 'Delete Student?',
                    html: 'You are about to delete <strong>' +
                          escapeHtml(studentName) +
                          '</strong>.<br><br>This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then(function (result) {

                    if (result.isConfirmed) {
                        deleteForm.submit();
                    }

                });

            } else {

                if (
                    confirm(
                        'Delete ' +
                        studentName +
                        '? This action cannot be undone.'
                    )
                ) {
                    deleteForm.submit();
                }

            }

        });

    });

    function escapeHtml(value) {
        return value
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

});
</script>

<?php endif; ?>
