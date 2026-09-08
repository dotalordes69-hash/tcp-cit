<?php
session_start();
include 'db.php';

if (!isset($_GET['booklet']) || empty($_GET['booklet'])) {
    exit('<div class="alert alert-danger">Invalid booklet.</div>');
}

$booklet = trim($_GET['booklet']);

$stmt = $conn->prepare("
    SELECT
        ph.or_number,
        ph.payment_date,
        ph.student_id,
        s.fullname,
        ph.particular,
        ph.amount_paid
    FROM payment_history ph
    LEFT JOIN students s
        ON ph.student_id = s.student_id
    WHERE ph.booklet_no = ?
    ORDER BY ph.or_number ASC
");

$stmt->bind_param("s", $booklet);
$stmt->execute();

$result = $stmt->get_result();
?>

<h5 class="mb-3">
    Booklet:
    <span class="text-danger">
        <?= htmlspecialchars($booklet) ?>
    </span>
</h5>

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

    <thead class="table-danger">
        <tr>
            <th width="120">OR Number</th>
            <th width="140">Date</th>
            <th width="120">Student ID</th>
            <th>Student Name</th>
            <th>Particular</th>
            <th width="130" class="text-end">Amount</th>
        </tr>
    </thead>

    <tbody>

    <?php if($result->num_rows > 0): ?>

        <?php while($row = $result->fetch_assoc()): ?>

        <tr>

            <td class="fw-bold">
                <?= htmlspecialchars($row['or_number']) ?>
            </td>

            <td>
                <?= date('M d, Y', strtotime($row['payment_date'])) ?>
            </td>

            <td>
                <?= htmlspecialchars($row['student_id']) ?>
            </td>

            <td>
                <?= htmlspecialchars($row['fullname']) ?>
            </td>

            <td>
                <?= htmlspecialchars($row['particular']) ?>
            </td>

            <td class="text-end">
                ₱<?= number_format($row['amount_paid'],2) ?>
            </td>

        </tr>

        <?php endwhile; ?>

    <?php else: ?>

        <tr>
            <td colspan="6" class="text-center text-muted py-4">
                No OR records found for this booklet.
            </td>
        </tr>

    <?php endif; ?>

    </tbody>

</table>

</div>

<?php
$stmt->close();
$conn->close();
?>