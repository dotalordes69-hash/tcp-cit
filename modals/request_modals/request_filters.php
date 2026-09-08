<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">

    <!-- Status Filter -->
    <div class="d-flex flex-wrap gap-2">

        <a href="?status=All&school_year=<?= urlencode($schoolYearFilter) ?>&modal=1"
           class="btn btn-sm rounded-pill border <?= $statusFilter=='All' ? 'btn-danger text-white' : 'btn-light' ?>">
            All
            <span class="badge bg-light text-dark ms-1"><?= $totalTransactions ?></span>
        </a>

        <a href="?status=Pending&school_year=<?= urlencode($schoolYearFilter) ?>&modal=1"
           class="btn btn-sm rounded-pill border <?= $statusFilter=='Pending' ? 'btn-danger text-white' : 'btn-light' ?>">
            Pending
            <span class="badge bg-light text-dark ms-1"><?= $totalPending ?></span>
        </a>

        <a href="?status=Verified%20Payment&school_year=<?= urlencode($schoolYearFilter) ?>&modal=1"
           class="btn btn-sm rounded-pill border <?= $statusFilter=='Verified Payment' ? 'btn-danger text-white' : 'btn-light' ?>">
            Verified Payment
            <span class="badge bg-light text-dark ms-1"><?= $totalVerifiedPayment ?></span>
        </a>

        <a href="?status=Processing&school_year=<?= urlencode($schoolYearFilter) ?>&modal=1"
           class="btn btn-sm rounded-pill border <?= $statusFilter=='Processing' ? 'btn-danger text-white' : 'btn-light' ?>">
            Processing
            <span class="badge bg-light text-dark ms-1"><?= $totalProcessing ?></span>
        </a>

        <a href="?status=For%20Signature&school_year=<?= urlencode($schoolYearFilter) ?>&modal=1"
           class="btn btn-sm rounded-pill border <?= $statusFilter=='For Signature' ? 'btn-danger text-white' : 'btn-light' ?>">
            For Signature
            <span class="badge bg-light text-dark ms-1"><?= $totalSignature ?></span>
        </a>

        <a href="?status=For%20Shipment&school_year=<?= urlencode($schoolYearFilter) ?>&modal=1"
           class="btn btn-sm rounded-pill border <?= $statusFilter=='For Shipment' ? 'btn-danger text-white' : 'btn-light' ?>">
            For Shipment
            <span class="badge bg-light text-dark ms-1"><?= $totalShipment ?></span>
        </a>

        <a href="?status=For%20Pick%20Up&school_year=<?= urlencode($schoolYearFilter) ?>&modal=1"
           class="btn btn-sm rounded-pill border <?= $statusFilter=='For Pick Up' ? 'btn-danger text-white' : 'btn-light' ?>">
            For Pick-Up
            <span class="badge bg-light text-dark ms-1"><?= $totalPickup ?></span>
        </a>

        <a href="?status=Released&school_year=<?= urlencode($schoolYearFilter) ?>&modal=1"
           class="btn btn-sm rounded-pill border <?= $statusFilter=='Released' ? 'btn-danger text-white' : 'btn-light' ?>">
            Released
            <span class="badge bg-light text-dark ms-1"><?= $totalReleased ?></span>
        </a>

    </div>

    <!-- School Year Filter -->
    <form method="GET" class="d-flex align-items-center gap-2">

        <input type="hidden" name="status" value="<?= $statusFilter ?>">
        <input type="hidden" name="modal" value="1">

        <select name="school_year"
                class="form-select form-select-sm"
                style="min-width:220px"
                onchange="this.form.submit()">

            <option value="All">All School Year</option>

            <?php while($sy = mysqli_fetch_assoc($schoolYears)){ ?>

                <option
                    value="<?= $sy['school_year'] ?>"
                    <?= $schoolYearFilter == $sy['school_year'] ? 'selected' : '' ?>>

                    <?= $sy['school_year'] ?>

                </option>

            <?php } ?>

        </select>

    </form>

</div>