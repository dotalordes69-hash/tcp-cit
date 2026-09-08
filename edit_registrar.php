<?php
session_start();

if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: registrar_directory.php");
    exit;
}

$id = (int)$_GET['id'];

$query = mysqli_query($conn,"
    SELECT *
    FROM registrar_directory
    WHERE id='$id'
");

if(mysqli_num_rows($query)==0){
    header("Location: registrar_directory.php");
    exit;
}

$row = mysqli_fetch_assoc($query);

if(isset($_POST['update'])){

    $school_name = mysqli_real_escape_string($conn,$_POST['school_name']);
    $school_email = mysqli_real_escape_string($conn,$_POST['school_email']);
    $contact_number = mysqli_real_escape_string($conn,$_POST['contact_number']);

    mysqli_query($conn,"
        UPDATE registrar_directory
        SET
            school_name='$school_name',
            school_email='$school_email',
            contact_number='$contact_number'
        WHERE id='$id'
    ");

    header("Location: registrar_directory.php?updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit School Email</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/collection.css">

</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
<div class="container-fluid">

<div class="card shadow-sm border-0 mb-4">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center">

<div>

<h3 class="fw-bold text-danger">
Edit School Email
</h3>

<small class="text-muted">
Update school information
</small>

</div>


</div>

</div>

</div>

<div class="card shadow-sm border-0">

<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">
School Name
</label>

<input
type="text"
name="school_name"
class="form-control"
value="<?= htmlspecialchars($row['school_name']) ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">
School Email Address
</label>

<input
type="email"
name="school_email"
class="form-control"
value="<?= htmlspecialchars($row['school_email']) ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">
Contact Number
</label>

<input
type="text"
name="contact_number"
class="form-control"
value="<?= htmlspecialchars($row['contact_number']) ?>">

</div>

</div>

<div class="text-end">

<a href="registrar_directory.php"
class="btn btn-secondary">

Cancel

</a>

<button
type="submit"
name="update"
class="btn btn-danger">

Update Information

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</body>

</html>