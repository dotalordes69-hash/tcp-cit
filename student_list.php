<?php
include 'db.php';
session_start();

if (!isset($_SESSION['fullname'])) {
    header("Location: login.php");
    exit();
}

include 'includes/student_import.php';
include 'includes/student_add.php';
include 'includes/student_filters.php';
include 'includes/student_counts.php';
include 'includes/student_query.php';


?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/student_list.css">
<link rel="stylesheet" href="assets/css/sidebar.css">

<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/sweetalert2.all.min.js"></script>


</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="main">
    
    <?php include 'partials/student_header.php'; ?>

    <?php include 'partials/student_cards.php'; ?>

    <?php include 'partials/student_filters_form.php'; ?>

    <div id="studentTableContainer">
    <?php include 'partials/student_table.php'; ?>
</div>

    <?php include 'partials/student_pagination.php'; ?>

</div>

<?php include 'modals/add_student_modal.php'; ?>
<?php include 'modals/import_student_modal.php'; ?>
<?php include 'modals/export_student_modal.php'; ?>
<?php include 'modals/add_semester_modal.php'; ?>
<?php include 'modals/add_schoolyear_modal.php'; ?>
<?php include 'modals/add_section_modal.php'; ?>
<?php include 'modals/send_bulk_modal.php'; ?>
<?php include 'modals/add_let_year_modal.php'; ?>

<script src="assets/js/student_modal.js"></script>

<script src="assets/js/student_export.js"></script>
<script src="assets/js/semester.js"></script>
<script src="assets/js/schoolyear.js"></script>
<script src="assets/js/section.js"></script>




</div>

</body>
</html>
<?php if (!empty($_SESSION['student_saved'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {

    Swal.fire({
        icon: 'success',
        title: 'Registration Complete',
        text: 'Student record has been successfully saved.',
        confirmButtonColor: '#0d6efd'
    });

});
</script>
<?php unset($_SESSION['student_saved']); ?>
<?php endif; ?>