
<?php
session_start();

if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';


// ADD EXPENSE
if(isset($_POST['add'])){
    $date = $_POST['date'];
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $semester = $_POST['semester'];
    $year = $_POST['year'];

    mysqli_query($conn,"
        INSERT INTO expenses(date,type_expenses,amount,semester,year)
        VALUES('$date','$type','$amount','$semester','$year')
    ");

    header("Location: expense.php");
    exit();
}

$result = mysqli_query($conn,"SELECT * FROM expenses ORDER BY id DESC");

$filter_year = $_GET['school_year'] ?? '';

// Grand Total
$grandQuery = mysqli_query($conn,"
    SELECT SUM(amount) AS total
    FROM expenses
");

$grandRow = mysqli_fetch_assoc($grandQuery);
$grand_total = $grandRow['total'] ?? 0;


// Filter Total
if($filter_year != ''){

    $filterQuery = mysqli_query($conn,"
        SELECT SUM(amount) AS total
        FROM expenses
        WHERE year = '$filter_year'
    ");

}else{

    $filterQuery = mysqli_query($conn,"
        SELECT SUM(amount) AS total
        FROM expenses
    ");

}

$filterRow = mysqli_fetch_assoc($filterQuery);
$filter_total = $filterRow['total'] ?? 0;


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
                    Expense Management
                </h3>

                <small class="text-muted">
                    School Expense Records
                </small>
            </div>



        </div>
    </div>
<!-- Expense Form -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">

        <h5 class="text-danger mb-4">Expense Details</h5>

        <?php
        $semesterQuery = mysqli_query($conn,"
            SELECT DISTINCT semester_name
            FROM semester_sections
            WHERE semester_name <> ''
            ORDER BY FIELD(semester_name,'1st Semester','2nd Semester','Summer')
        ");

        $yearQuery = mysqli_query($conn,"
            SELECT DISTINCT school_year
            FROM semester_sections
            WHERE school_year <> ''
            ORDER BY school_year DESC
        ");
        ?>

        <form method="POST">

            <div class="row">

                <div class="col-lg-2 mb-3">
                    <label class="form-label">Date</label>
                    <input type="date"
                           name="date"
                           class="form-control"
                           value="<?= date('Y-m-d') ?>"
                           required>
                </div>

                <div class="col-lg-3 mb-3">
                    <label class="form-label">Particular</label>
                    <input type="text"
                           name="type"
                           class="form-control"
                           placeholder="Expense Particular"
                           required>
                </div>

                <div class="col-lg-2 mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number"
                           step="0.01"
                           name="amount"
                           class="form-control"
                           placeholder="0.00"
                           required>
                </div>

                <div class="col-lg-2 mb-3">
                    <label class="form-label">Semester</label>

                    <select name="semester"
                            class="form-select"
                            required>

                        <option value="">Select</option>

                        <?php while($semester = mysqli_fetch_assoc($semesterQuery)){ ?>

                        <option value="<?= $semester['semester_name'] ?>">
                            <?= $semester['semester_name'] ?>
                        </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="col-lg-3 mb-3">
                    <label class="form-label">School Year</label>

                    <select name="year"
                            class="form-select"
                            required>

                        <option value="">Select</option>

                        <?php while($year = mysqli_fetch_assoc($yearQuery)){ ?>

                        <option value="<?= $year['school_year'] ?>">
                            <?= $year['school_year'] ?>
                        </option>

                        <?php } ?>

                    </select>

                </div>

            </div>

            <div class="text-end">

                <button type="reset"
                        class="btn btn-secondary">
                    Reset
                </button>

                <button type="submit"
                        name="add"
                        class="btn btn-danger">
                    Save Expense
                </button>

            </div>

        </form>

    </div>
</div>

<?php
$filter_year = $_GET['school_year'] ?? '';
?>

<form method="GET" class="row mb-3">

    <div class="col-md-3">
        <select name="school_year" class="form-select" onchange="this.form.submit()">
            <option value="">All School Years</option>

            <?php
            $years = mysqli_query($conn,"
                SELECT DISTINCT year
                FROM expenses
                ORDER BY year DESC
            ");

            while($y = mysqli_fetch_assoc($years)){
            ?>

            <option value="<?= $y['year'] ?>"
                <?= ($filter_year == $y['year']) ? 'selected' : '' ?>>
                <?= $y['year'] ?>
            </option>

            <?php } ?>

        </select>
    </div>

</form>


<div class="card shadow-sm border-0">
    <div class="card-body">

        <h5 class="text-danger mb-3">
            Expense Transactions
        </h5>

        <div class="table-responsive">

            <table class="table table-hover table-bordered align-middle">

                <thead class="table-danger">
                    <tr>
                        <th>Date</th>
                        <th>Particular</th>
                        <th>Amount</th>
                        <th>Semester</th>
                        <th>School Year</th>
                    </tr>
                </thead>

                <tbody>

                <?php
                $total = 0;

                while($row = mysqli_fetch_assoc($result)){
                    $total += $row['amount'];
                ?>

                <tr>
                    <td><?= $row['date'] ?></td>
                    <td><?= htmlspecialchars($row['type_expenses']) ?></td>
                    <td>₱ <?= number_format($row['amount'],2) ?></td>
                    <td><?= $row['semester'] ?></td>
                    <td><?= $row['year'] ?></td>
                </tr>

                <?php } ?>

                </tbody>


            </table>

        </div>

    </div>
</div>
<div class="row mt-3">

    <div class="col-md-6">

        <div class="alert alert-danger">

            <strong>Grand Total Expenses</strong><br>

            ₱ <?= number_format($grand_total,2) ?>

        </div>

    </div>

    <div class="col-md-6">

        <div class="alert alert-primary">

            <strong>
                <?= $filter_year == '' ? 'Current View Total' : 'Total for '.$filter_year ?>
            </strong><br>

            ₱ <?= number_format($filter_total,2) ?>

        </div>

    </div>

</div>