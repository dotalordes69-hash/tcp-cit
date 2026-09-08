<?php
session_start();

if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

/*
|--------------------------------------------------------------------------
| SAVE
|--------------------------------------------------------------------------
*/

if(isset($_POST['save'])){

    foreach($_POST['amount'] as $id => $amount){

        $id = (int)$id;
        $amount = (float)$amount;

        mysqli_query($conn,"
            UPDATE document_fees
            SET amount='$amount'
            WHERE id='$id'
        ");

    }

    echo "<script>
        alert('Document fees updated successfully.');
        window.location='document_fees.php';
    </script>";

    exit();

}

$result = mysqli_query($conn,"
    SELECT *
    FROM document_fees
    ORDER BY document_name ASC
");
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Document Fees</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="assets/css/collection.css">

</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">

<div class="container-fluid">

<div class="card shadow-sm border-0">

<div class="card-header bg-danger text-white">

<h4 class="mb-0">

Document Fees

</h4>

</div>

<div class="card-body">

<form method="POST">

<div class="table-responsive">

<table class="table table-bordered align-middle">

<thead class="table-light">

<tr>

<th width="70%">
Document Type
</th>

<th width="30%">
Amount
</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td>

<strong>

<?= htmlspecialchars($row['document_name']) ?>

</strong>

</td>

<td>

<div class="input-group">

<span class="input-group-text">

₱

</span>

<input
type="number"
step="0.01"
min="0"
class="form-control"
name="amount[<?= $row['id'] ?>]"
value="<?= $row['amount'] ?>">

</div>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<div class="text-end mt-3">

<button
type="submit"
name="save"
class="btn btn-danger">

Save Changes

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</body>

</html>