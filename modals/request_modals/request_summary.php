<div class="d-flex justify-content-between align-items-center mb-3">

    <h6 class="mb-0">
        Total Transactions:
        <span class="badge bg-danger">
            <?= $totalTransactions ?>
        </span>
    </h6>

    <h6 class="mb-0">
        Total Collection:
        <span class="badge bg-success">
            ₱<?= number_format($totalAmount, 2) ?>
        </span>
    </h6>

</div>