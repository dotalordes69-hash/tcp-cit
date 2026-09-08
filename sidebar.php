<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
$fullname = $_SESSION['fullname'] ?? 'Guest';

$studentPages = [
    'student_list.php',
    'view_student.php',
    'edit_student.php',
    'add_student.php'
];
$financePages = [
    'collection.php',
    'expense.php'
];

$isFinanceActive = in_array($currentPage, $financePages);
$studentPages = [
    'student_list.php',
    'view_student.php',
    'edit_student.php',
    'add_student.php'
];

$facultyPages = [
    'teachers.php'
];

$isAcademicActive =
    in_array($currentPage, $studentPages) ||
    in_array($currentPage, $facultyPages);

$registrarPages = [
    'registrar_directory.php',
    'edit_registrar.php'
];


?>

<link rel="stylesheet" href="assets/css/sidebar.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        <img src="img/logo.png" alt="Logo">
    </div>
<div style="text-align:center;font-size:12px;opacity:.8;">
    Teacher Certificate Program
</div>
    <!-- User info + logout -->
    <div class="user-info">
    <h3>Welcome, <?php echo htmlspecialchars($fullname); ?></h3>
        <form method="POST" action="logout.php">
            <button class="logout" type="submit">Logout</button>
        </form>
    </div>
<a href="dashboard.php"
   class="<?= ($currentPage=='dashboard.php') ? 'active' : '' ?>">
    <i class="bi bi-grid-fill"></i>
    Dashboard
</a>

<?php
$isAcademicActive =
    in_array($currentPage, $studentPages) ||
    in_array($currentPage, $facultyPages);

$isFinanceActive =
    in_array($currentPage, $financePages);
?>

<!-- ACADEMIC MANAGEMENT -->
<div class="menu-group">

 <a href="javascript:void(0)"
   onclick="toggleAcademicMenu()">

    <i class="bi bi-mortarboard-fill"></i>
    Academic Management

    <i class="bi bi-chevron-down ms-auto"></i>
</a>


    <div id="academicSubmenu"
         class="submenu <?= $isAcademicActive ? 'show' : '' ?>">

        <a href="student_list.php"
           class="<?= in_array($currentPage,$studentPages) ? 'active' : '' ?>">

            <i class="bi bi-people-fill"></i>
            Student Management

        </a>


        <a href="teachers.php"
           class="<?= in_array($currentPage,$facultyPages) ? 'active' : '' ?>">

            <i class="bi bi-person-badge-fill"></i>
            Faculty Management

        </a>

    </div>

</div>



<!-- FINANCIAL MANAGEMENT -->
<div class="menu-group">

   <a href="javascript:void(0)"
   onclick="toggleFinanceMenu()">

    <i class="bi bi-wallet-fill"></i>
    Financial Management

    <i class="bi bi-chevron-down ms-auto"></i>

</a>


    <div id="financeSubmenu"
         class="submenu <?= $isFinanceActive ? 'show' : '' ?>">


        <a href="collection.php"
           class="<?= ($currentPage=='collection.php') ? 'active' : '' ?>">

            <i class="bi bi-wallet2"></i>
            Collection

        </a>


        <a href="expense.php"
           class="<?= ($currentPage=='expense.php') ? 'active' : '' ?>">

            <i class="bi bi-receipt"></i>
            Expense

        </a>


    </div>

</div>


<a href="document_request_monitoring.php"
   class="<?= 
   ($currentPage=='document_request_monitoring.php' || $currentPage=='view_document_transactions.php') 
   ? 'active' 
   : '' 
   ?>">
    <i class="bi bi-folder-check"></i>
    Document Requests
</a>

<a href="registrar_directory.php"
   class="<?= in_array($currentPage, $registrarPages) ? 'active' : '' ?>">

    <i class="bi bi-envelope-fill"></i>
    Registrar Directory

</a>

</div>

</div>

<script>

function toggleAcademicMenu(){

    let academic = document.getElementById('academicSubmenu');
    let finance = document.getElementById('financeSubmenu');

    // close finance
    finance.classList.remove('show');

    // toggle academic
    academic.classList.toggle('show');

}



function toggleFinanceMenu(){

    let finance = document.getElementById('financeSubmenu');
    let academic = document.getElementById('academicSubmenu');

    // close academic
    academic.classList.remove('show');

    // toggle finance
    finance.classList.toggle('show');

}

</script>