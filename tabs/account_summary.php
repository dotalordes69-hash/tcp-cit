<?php

/* PAYMENT HISTORY */

$history = mysqli_query($conn,"
    SELECT
        payment_date,
        or_number,
        gcash_ref,
        particular,
        amount_paid,
        received_by,
        gcash_status,
        verified_by
    FROM payment_history
    WHERE student_id='$student_id'
    ORDER BY id DESC
");

?>

<link rel="stylesheet" href="assets/css/account_summary.css">

<div class="card payment-card">


<div class="payment-header">

<h5>
Account Summary
</h5>

<small> 
Student Account Payment transaction records
</small>

</div>


<div class="card-body bg-light">


<div class="row mb-4">

<div class="col-md-3">

<div class="balance-box">

<div class="balance-title">
Remaining Balance
</div>


<div class="balance-value">
₱<?= number_format($balance,2); ?>
</div>


</div>

</div>

</div>



<div class="table-responsive">


<table class="table table-bordered table-hover table-payment bg-white">


<thead>

<tr>

<th>Date</th>
<th>OR Number</th>
<th>GCash Reference</th>
<th>Particular</th>
<th>Amount</th>
<th>Status</th>
<th>Verified By</th>
<th>Received By</th>

</tr>

</thead>


<tbody>


<?php if(mysqli_num_rows($history)>0): ?>


<?php while($row=mysqli_fetch_assoc($history)): ?>


<tr>


<td>
<?= date('M d, Y',strtotime($row['payment_date'])) ?>
</td>


<td>
<strong>
<?= htmlspecialchars($row['or_number']) ?>
</strong>
</td>


<td>
<span class="ref-box">
<?= htmlspecialchars($row['gcash_ref']) ?>
</span>
</td>


<td>
<?= htmlspecialchars($row['particular']) ?>
</td>


<td>
<strong>
₱<?= number_format($row['amount_paid'],2) ?>
</strong>
</td>


<td>

<?php if($row['gcash_status']=='verified'): ?>

<span class="status-verified">
✓ Verified
</span>

<?php else: ?>

<span class="status-pending">
Pending
</span>

<?php endif; ?>


</td>


<td>
<?= htmlspecialchars($row['verified_by'] ?? '-') ?>
</td>


<td>
<?= htmlspecialchars($row['received_by']) ?>
</td>


</tr>


<?php endwhile; ?>


<?php else: ?>


<tr>

<td colspan="8"
class="text-center text-muted py-4">

No payment history found.

</td>

</tr>


<?php endif; ?>


</tbody>


</table>


</div>


</div>

</div>