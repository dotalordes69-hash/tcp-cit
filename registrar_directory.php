
<?php
session_start();

if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

$userRole = $_SESSION['role'] ?? '';
$isSuperAdmin = strtolower($userRole) === 'superadmin';


include 'db.php';
if(isset($_POST['save'])){

    $school_name = mysqli_real_escape_string($conn,$_POST['school_name']);
    $school_email = mysqli_real_escape_string($conn,$_POST['school_email']);
    $contact_number = mysqli_real_escape_string($conn,$_POST['contact_number']);

    mysqli_query($conn,"
        INSERT INTO registrar_directory
        (
            school_name,
            school_email,
            contact_number
        )
        VALUES
        (
            '$school_name',
            '$school_email',
            '$contact_number'
        )
    ");

    header("Location: registrar_directory.php");
    exit();
}
$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1){
    $page = 1;
}

$start = ($page - 1) * $limit;

$totalResult = mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM registrar_directory
");

$totalRows = mysqli_fetch_assoc($totalResult)['total'];

$totalPages = ceil($totalRows / $limit);

$query = mysqli_query($conn,"
SELECT *
FROM registrar_directory
ORDER BY school_name ASC
LIMIT $start,$limit
");

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
Registrar School Email Directory
</h3>

<small class="text-muted">
Maintain the official school email directory for communication and correspondence.
</small>

</div>

<button class="btn btn-outline-danger"
        data-bs-toggle="modal"
        data-bs-target="#sendEmailModal">

<i class="bi bi-envelope-paper-fill"></i>
Send Email

</button>

</div>


    </div><h5 class="text-danger mb-4">
    Registrar Directory
</h5>

<form method="POST">

<div class="row">

    <div class="col-md-5 mb-3">
        <label class="form-label">School Name</label>
        <input type="text"
               name="school_name"
               class="form-control"
               required>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">School Email Address</label>
        <input type="email"
               name="school_email"
               class="form-control"
               required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Contact Number</label>
        <input type="text"
               name="contact_number"
               class="form-control"
               required>
    </div>

</div>

<div class="text-end">

    <button type="reset"
            class="btn btn-secondary">
        Reset
    </button>

    <button type="submit"
            name="save"
            class="btn btn-danger">
        Save School
    </button>

</div>

</form>


</div>
</form>
<?php
$query = mysqli_query($conn,"
SELECT *
FROM registrar_directory
ORDER BY school_name ASC
");
?>

<div class="card shadow-sm border-0 mt-4">

<div class="card-body">

<h5 class="text-danger mb-3">
Registered Schools
</h5>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-danger">

<tr>
    <th width="60">#</th>
    <th>School Name</th>
    <th>School Email Address</th>
    <th>Contact Number</th>
    <th width="170">Date Added</th>
    <th width="120">Action</th>
</tr>

</thead>

<tbody>

<?php
$i=1;

while($row=mysqli_fetch_assoc($query)){
?>

<tr>

<td><?= $i++ ?></td>

<td><?= htmlspecialchars($row['school_name']) ?></td>

<td><?= htmlspecialchars($row['school_email']) ?></td>

<td><?= htmlspecialchars($row['contact_number']) ?></td>

<td><?= date("M d, Y",strtotime($row['created_at'])) ?></td>



<td class="text-center">

    <a href="edit_registrar.php?id=<?= $row['id'] ?>"
       class="btn btn-warning btn-sm">
        <i class="bi bi-pencil-square"></i>
    </a>

<button type="button"
        class="btn btn-danger btn-sm delete-btn"
        data-id="<?= $row['id'] ?>">

    <i class="bi bi-trash"></i>

</button>

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
href="?page=<?= $page-1 ?>">
Previous
</a>
</li>

<?php } ?>

<?php for($i=1;$i<=$totalPages;$i++){ ?>

<li class="page-item <?= $page==$i ? 'active' : '' ?>">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'modals/school_email_modal.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

document.querySelectorAll('.delete-btn').forEach(function(button) {

    button.addEventListener('click', function() {

        const id = this.dataset.id;

        Swal.fire({
            title: 'Delete School?',
            text: 'This school will be permanently removed from the registrar directory.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#8B0000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {

            if (result.isConfirmed) {

                const form = document.createElement('form');

                form.method = 'POST';
                form.action = 'delete_registrar.php';

                const input = document.createElement('input');

                input.type = 'hidden';
                input.name = 'id';
                input.value = id;

                form.appendChild(input);

                document.body.appendChild(form);

                form.submit();

            }

        });

    });

});

</script>