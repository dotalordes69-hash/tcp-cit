<?php

if (!isset($conn) || $conn === null) {
    require_once __DIR__ . '/../db.php';
}

if (!isset($conn) || !$conn) {
    die("Database connection failed.");
}

$student_id = $student_id_for_history ?? ($_GET['student_id'] ?? '');
$student_id = trim($student_id);

if (empty($student_id)) {
    echo "<div class='alert alert-warning'>Student ID not found.</div>";
    return;
}

$history_total_paid = 0;
$history_total_extra_fee = 0;

$history_limit = 5;
$history_page = isset($_GET['history_page']) ? intval($_GET['history_page']) : 1;

if ($history_page < 1) {
    $history_page = 1;
}

$history_offset = ($history_page - 1) * $history_limit;
     $history_total_paid = 0;
            $history_total_extra_fee = 0;

            $history_limit = 5;
            $history_page = isset($_GET['history_page']) ? intval($_GET['history_page']) : 1;
            if ($history_page < 1) {
                $history_page = 1;
            }

            $history_offset = ($history_page - 1) * $history_limit;

            // total records
            $count_stmt = $conn->prepare("
                SELECT COUNT(*) AS total_records
                FROM payment_history
                WHERE student_id = ?
            ");
            $count_stmt->bind_param("s", $student_id);
            $count_stmt->execute();
            $count_result = $count_stmt->get_result();
            $count_row = $count_result->fetch_assoc();
            $count_stmt->close();

            $total_history_records = intval($count_row['total_records'] ?? 0);
            $total_history_pages = ($total_history_records > 0) ? ceil($total_history_records / $history_limit) : 1;

            // current page records
           $stmt = $conn->prepare("
    SELECT
        id,
        payment_date,
        or_number,
        gcash_ref,
        gcash_status,
        verified_by,
        amount_paid,
        particular,
        received_by
    FROM payment_history
    WHERE student_id = ?
    ORDER BY id DESC
    LIMIT ? OFFSET ?
");
            $stmt->bind_param("sii", $student_id, $history_limit, $history_offset);
            $stmt->execute();
            $query = $stmt->get_result();

            // summary from student_accounts
            $summary_stmt = $conn->prepare("
                SELECT total_amount, total_paid, balance
                FROM student_accounts
                WHERE student_id = ?
                LIMIT 1
            ");
            $summary_stmt->bind_param("s", $student_id);
            $summary_stmt->execute();
            $summary_result = $summary_stmt->get_result();
            $summary = $summary_result->fetch_assoc();
            $summary_stmt->close();

            $history_tuition = floatval($summary['total_amount'] ?? 0);
            $history_total_paid_summary = floatval($summary['total_paid'] ?? 0);
            $history_balance = floatval($summary['balance'] ?? ($history_tuition - $history_total_paid_summary));        
            
?>
<style>
.modal.fade .modal-dialog{
    transform: translateY(-15px) scale(.98);
    transition: all .25s ease;
}

.modal.show .modal-dialog{
    transform: translateY(0) scale(1);
}

.modal-content{
    border:none;
    border-radius:14px;
    overflow:hidden;
    box-shadow:0 15px 40px rgba(0,0,0,.15);
}

.modal-backdrop.show{
    opacity:.15 !important;
}

.pagination .page-link{
    transition:all .2s ease;
}

.pagination .page-link:hover{
    transform:translateY(-2px);
}

.pagination .page-item.active .page-link{
    transform:scale(1.05);
}

#payment-history-container .card{
    border:none;
    animation:fadeInUp .25s ease;
}

@keyframes fadeInUp{
    from{
        opacity:0;
        transform:translateY(10px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}
</style>
<div class="modal fade"
     id="paymentHistoryModal"
     tabindex="-1"
     data-bs-backdrop="static"
     data-bs-keyboard="false"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Payment History</h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
  <!-- PAYMENT HISTORY -->
<div id="payment-history-container">

    <div class="row mb-4">
        <div class="col-12">

            <div class="card p-3 shadow-sm rounded">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>OR Number</th>
                                <th>GCash Ref</th>
                                <th>Amount Paid</th>
                                <th>Particular</th>
                                <th>Received By</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if ($query && $query->num_rows > 0): ?>

                                <?php
                                $count = $history_offset + 1;

                                while ($row = $query->fetch_assoc()):

                                    $payment_date = !empty($row['payment_date'])
                                        ? date('M d, Y', strtotime($row['payment_date']))
                                        : 'N/A';

                                    $or_number = !empty($row['or_number'])
                                        ? htmlspecialchars($row['or_number'])
                                        : 'N/A';

                                    $gcash_ref = !empty($row['gcash_ref'])
                                        ? htmlspecialchars($row['gcash_ref'])
                                        : 'N/A';

                                    $payment = isset($row['amount_paid'])
                                        ? floatval($row['amount_paid'])
                                        : 0;

                                    $particular = !empty($row['particular'])
                                        ? htmlspecialchars($row['particular'])
                                        : 'N/A';

                                    $received_by = !empty($row['received_by'])
                                        ? htmlspecialchars($row['received_by'])
                                        : 'N/A';

                                    $history_total_paid += $payment;

                                    $status = strtolower(trim($row['gcash_status'] ?? ''));

                                    if ($status == '') {
                                        $status = 'pending';
                                    }

                                    $verified_by = $row['verified_by'] ?? '';
                                    $isVerified = ($status === 'verified');
                                ?>

                                <tr>

                                    <td><?php echo $count; ?></td>

                                    <td><?php echo $payment_date; ?></td>

                                    <td><?php echo $or_number; ?></td>

                                    <td>

                                        <div class="d-flex align-items-center gap-2 flex-wrap">

                                            <span><?php echo $gcash_ref; ?></span>

                                            <?php if (($_SESSION['role'] ?? '') === 'superadmin'): ?>

<button
    type="button"
    id="status-<?php echo $row['id']; ?>"
    class="btn btn-sm <?php echo $isVerified ? 'verified-btn' : 'btn-warning'; ?>"
    onclick="updateStatus(<?php echo $row['id']; ?>)"
    <?php echo $isVerified ? 'disabled' : ''; ?>>
    <?php echo ucfirst($status); ?>
</button>

                                            <?php else: ?>

                                                <span class="badge <?php echo $isVerified ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                                    <?php echo ucfirst($status); ?>
                                                </span>

                                            <?php endif; ?>

                                        </div>

                                        <?php if ($isVerified && !empty($verified_by)): ?>

                                            <div
                                                id="verified-by-<?php echo $row['id']; ?>"
                                                style="font-size:0.80rem;margin-top:3px;color:#555;">

                                                <strong>Verified by:</strong>
                                                <?php echo htmlspecialchars($verified_by); ?>

                                            </div>

                                        <?php endif; ?>

                                    </td>

                                    <td>₱<?php echo number_format($payment, 2); ?></td>

                                    <td><?php echo $particular; ?></td>

                                    <td><?php echo $received_by; ?></td>

                                </tr>

                                <?php
                                    $count++;
                                endwhile;
                                ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        No payment history found.
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

                <?php $stmt->close(); ?>

                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">

                    <nav>
                        <ul class="pagination mb-0">

                           <?php for ($i = 1; $i <= $total_history_pages; $i++): ?>
<li class="page-item <?php echo ($i == $history_page) ? 'active' : ''; ?>">
   <a class="page-link"
   href="view_student.php?id=<?php echo urlencode($_GET['id'] ?? ''); ?>&tab=accountTab&show_history=1&history_page=<?php echo $i; ?>">
        <?php echo $i; ?>
    </a>
</li>
<?php endfor; ?>

                        </ul>
                    </nav>

                </div>

            </div>

        </div>
    </div>

</div>
<script>
function updateStatus(id) {

    let row = document.getElementById('status-' + id).closest('tr');

    let or_number = row.children[2].innerText.trim();
    let gcash_ref = row.children[3].querySelector('span').innerText.trim();
    let particular = row.children[5].innerText.trim();

    console.log(or_number, gcash_ref, particular);
if (
    or_number === 'N/A' ||
    gcash_ref === 'N/A' ||
    particular === 'N/A'
) {
    Swal.fire(
        'Missing Information',
        'Please fill in OR Number, GCash Reference, and Particular.',
        'error'
    );
    return;
}

    Swal.fire({
        title: 'Are you sure?',
        text: "Change status to verified?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, verify it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("update_status.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "id=" + id
            })
            .then(res => res.text())
            .then(data => {
                if (data === "success") {

    let btn = document.getElementById("status-" + id);

    btn.innerText = "Verified";

    btn.classList.remove("btn-warning");
    btn.classList.remove("btn-success");
    btn.classList.add("verified-btn");

    btn.disabled = true;
    btn.onclick = null;

let verifiedDiv = document.getElementById("verified-by-" + id);

if (!verifiedDiv) {
    verifiedDiv = document.createElement("div");
    verifiedDiv.id = "verified-by-" + id;
    verifiedDiv.style.fontSize = "0.80rem";
    verifiedDiv.style.marginTop = "3px";
    verifiedDiv.style.color = "#555";

    btn.parentElement.parentElement.appendChild(verifiedDiv);
}

verifiedDiv.innerHTML =
    "<strong>Verified by:</strong> <?php echo htmlspecialchars($_SESSION['fullname'] ?? 'Admin'); ?>";
    Swal.fire({
        icon: 'success',
        title: 'Verified!',
        text: 'Payment status updated successfully.',
        timer: 1200,
        showConfirmButton: false
    });
}
 else {
                    Swal.fire('Error', 'Failed to update status.', 'error');
                }
            });
        }
    });
}
</script>

</div>
<?php if (isset($_GET['show_history'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modalElement = document.getElementById('paymentHistoryModal');

    if (modalElement) {

        const modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });

        <?php if (isset($_GET['show_history'])): ?>
        modal.show();
        <?php endif; ?>

        modalElement.addEventListener('hidden.bs.modal', function () {

            // Ibalik ang scroll sa page
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';

            // Tangtanga tanan backdrop
            document.querySelectorAll('.modal-backdrop').forEach(function(el) {
                el.remove();
            });

            // Limpyo URL ug balik sa page 1
            const url = new URL(window.location);

            url.searchParams.delete('show_history');
            url.searchParams.delete('history_page');

            window.location.href = url.toString();

        });

    }

});
</script>
<?php endif; ?>

