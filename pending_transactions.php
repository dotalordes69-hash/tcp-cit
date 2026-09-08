<?php
include 'db.php';

$result = mysqli_query($conn,"
SELECT
    p.*,
    s.fullname
FROM payment_history p
LEFT JOIN students s
    ON s.student_id = p.student_id
WHERE p.gcash_status != 'verified'
ORDER BY p.id DESC
");

$total_pending = mysqli_num_rows($result);
?>

<div class="card border-0 shadow-sm">

    <div class="card-header bg-white border-bottom py-3">



    </div>

    <div class="card-body">

        <?php if($total_pending > 0): ?>

        <div class="bg-white border rounded-3 p-3 mb-3 shadow-sm d-flex justify-content-between align-items-center">

            <div>
                <strong class="text-dark">
                    Pending Transactions
                </strong>
                <br>
                <small class="text-muted">
                    These payments are waiting for verification.
                </small>
            </div>

            <span class="badge bg-primary rounded-pill px-3 py-2">
                <?= $total_pending ?> Pending
            </span>

        </div>

        <?php endif; ?>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead style="background:#fff;border-bottom:2px solid #e9ecef;">

                    <tr>
                        <th class="fw-semibold">Student ID</th>
                        <th class="fw-semibold">Student Name</th>
                        <th class="fw-semibold">Date</th>
                        <th class="fw-semibold">OR Number</th>
                        <th class="fw-semibold">Reference No.</th>
                        <th class="fw-semibold">Particular</th>
                        <th class="fw-semibold text-end">Amount</th>
                        <th class="fw-semibold">Received By</th>
                        <th class="fw-semibold text-center">Status</th>
                    </tr>

                </thead>

                <tbody>

                <?php if($total_pending > 0): ?>

                    <?php while($row = mysqli_fetch_assoc($result)): ?>

                    <tr>

                        <td>
                            <span class="fw-semibold">
                                <?= htmlspecialchars($row['student_id']) ?>
                            </span>
                        </td>

                        <td>
                            <div class="fw-semibold text-dark">
                                <?= htmlspecialchars($row['fullname']) ?>
                            </div>
                        </td>

                        <td>
                            <?= date('M d, Y', strtotime($row['payment_date'])) ?>
                        </td>

                        <td>
                            <span class="badge bg-light text-dark border">
                                <?= htmlspecialchars($row['or_number']) ?>
                            </span>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['gcash_ref']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['particular']) ?>
                        </td>

                        <td class="text-end">
                            <span class="fw-bold text-success">
                                ₱<?= number_format($row['amount_paid'],2) ?>
                            </span>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['received_by']) ?>
                        </td>

                        <td class="text-center">

                            <span
                                class="badge rounded-pill verify-btn"
                                style="
                                    background:#fff3cd;
                                    color:#856404;
                                    border:1px solid #ffe69c;
                                    cursor:pointer;
                                    padding:8px 14px;
                                "
                                data-id="<?= $row['id'] ?>"
                                data-ref="<?= htmlspecialchars($row['gcash_ref']) ?>">

                                <i class="bi bi-check-circle"></i>
                                Verify

                            </span>

                        </td>

                    </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="9" class="text-center py-5">

                            <div class="mb-3">
                                <i class="bi bi-check-circle-fill text-success"
                                   style="font-size:3rem;"></i>
                            </div>

                            <h5 class="fw-bold text-success">
                                No Pending Transactions
                            </h5>

                            <p class="text-muted mb-0">
                                All GCash payments have been successfully verified.
                            </p>

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>