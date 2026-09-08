
<?php
include 'db.php';

$student_id = $_GET['student_id'] ?? '';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if($page < 1){
    $page = 1;
}

$limit  = 100;
$offset = ($page - 1) * $limit;

$count = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS total
    FROM payment_history
    WHERE student_id='$student_id'
"));

$totalRecords = $count['total'];
$totalPages   = ceil($totalRecords / $limit);



$checkStudent = mysqli_query($conn,"
    SELECT
        fullname,
        semester,
        school_year,
        section
    FROM students
    WHERE student_id='$student_id'
");

if(mysqli_num_rows($checkStudent) == 0){
    echo 'NOT_FOUND';
    exit;
}

$student = mysqli_fetch_assoc($checkStudent);

$checkTransaction = mysqli_query($conn,"
    SELECT id
    FROM payment_history
    WHERE student_id='$student_id'
    LIMIT 1
");

if(mysqli_num_rows($checkTransaction) == 0){
    echo 'NO_TRANSACTION';
    exit;
}

$result = mysqli_query($conn,"
    SELECT *
    FROM payment_history
    WHERE student_id='$student_id'
    ORDER BY id DESC
    LIMIT $offset, $limit
");
?>

<div class="card border-0 shadow-sm">



    <div class="card-body">

       

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>OR Number</th>
                        <th>Reference No.</th>
                        <th>Particular</th>
                        <th class="text-end">Amount</th>
                        <th>Received By</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                <?php if(mysqli_num_rows($result) > 0): ?>

                    <?php
                    $total = 0;

                    while($row = mysqli_fetch_assoc($result)):
                        $total += $row['amount_paid'];
                    ?>

                    <tr>

                        <td>
                            <?= date('M d, Y', strtotime($row['payment_date'])) ?>
                        </td>

                        <td>
                            <span class="fw-semibold">
                                <?= htmlspecialchars($row['or_number']) ?>
                            </span>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['gcash_ref']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['particular']) ?>
                        </td>

                        <td class="text-end fw-bold text-success">
                            ₱<?= number_format($row['amount_paid'],2) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['received_by']) ?>
                        </td>

                        <td>

<?php if($row['gcash_status'] == 'verified'): ?>

<span class="badge bg-success px-3 py-2">
    ✓ Verified
</span>

    <?php if(!empty($row['verified_by'])): ?>
        <br>
        <small class="text-muted">
            By: <?= htmlspecialchars($row['verified_by']) ?>
        </small>
    <?php endif; ?>

<?php else: ?>

<span
    class="badge bg-warning text-dark px-3 py-2 verify-btn"
    style="cursor:pointer"
    data-id="<?= $row['id'] ?>"
    data-ref="<?= htmlspecialchars($row['gcash_ref']) ?>">
    Pending
</span>

<?php endif; ?>

</td>

                    </tr>

                    <?php endwhile; ?>

                    <tr class="table-light fw-bold">

                        <td colspan="4" class="text-end">
                            Total Payments
                        </td>

                        <td class="text-end text-success">
                            ₱<?= number_format($total,2) ?>
                        </td>

                        <td colspan="2"></td>

                    </tr>

                <?php else: ?>

                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No transaction records found.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>
<div class="d-flex justify-content-between align-items-center mt-3">

    <small class="text-muted">
        Showing <?= ($offset + 1) ?> -
        <?= min($offset + $limit, $totalRecords) ?>
        of <?= $totalRecords ?> records
    </small>

    <div>

        <?php if($page > 1): ?>
            <a href="?student_id=<?= urlencode($student_id) ?>&page=<?= $page-1 ?>"
               class="btn btn-sm btn-outline-secondary">
                Previous
            </a>
        <?php endif; ?>

        <?php if($page < $totalPages): ?>
            <a href="?student_id=<?= urlencode($student_id) ?>&page=<?= $page+1 ?>"
               class="btn btn-sm btn-outline-secondary">
                Next
            </a>
        <?php endif; ?>

    </div>

</div>
    </div>

</div>
