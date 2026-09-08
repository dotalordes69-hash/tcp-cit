
<link rel="stylesheet" href="assets/css/student_cards.css">

<div class="dashboard-banner">
    <div class="banner-left">
        <div class="banner-number">
            <?= number_format($total_students ?? 0) ?>
        </div>

        <div class="banner-label">
            Total Students
        </div>
    </div>
</div>
<div class="row g-3">
<div class="col-xl-3 col-md-6">
    <div class="stats-card filter-card"
         data-filter-type="status"
         data-filter-value="Official">

        <div class="stats-content">
            <div class="stats-title">Enrolled Students</div>
            <div class="stats-value">
                <?= $total_official_students ?? 0 ?>
            </div>
        </div>

    </div>
</div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card filter-card"
             data-filter-type="status"
             data-filter-value="Unofficial">
            <div class="stats-content">
                <div class="stats-title">Unerolled Students</div>
                <div class="stats-value"><?= $total_unofficial_students ?? 0 ?></div>
            </div>
        </div>
    </div>



<div class="col-xl-3 col-md-6">
    <div class="stats-card filter-card"
         data-filter-type="let"
         data-filter-value="PASSED">
        <div class="stats-content">
            <div class="stats-title">LET Passers</div>
            <div class="stats-value"><?= $total_let_passers ?? 0 ?></div>
        </div>
    </div>
</div>


    <div class="col-xl-3 col-md-6">
        <div class="stats-card filter-card"
             data-filter-type="payment"
             data-filter-value="Paid">
            <div class="stats-content">
                <div class="stats-title">Paid Students</div>
                <div class="stats-value"><?= $total_paid_students ?? 0 ?></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card filter-card"
             data-filter-type="payment"
             data-filter-value="Partial">
            <div class="stats-content">
                <div class="stats-title">Partial Payment</div>
                <div class="stats-value"><?= $total_partial_students ?? 0 ?></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card filter-card"
             data-filter-type="payment"
             data-filter-value="Unpaid">
            <div class="stats-content">
                <div class="stats-title">Unpaid Students</div>
                <div class="stats-value"><?= $total_unpaid_students ?? 0 ?></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card filter-card"
             data-filter-type="credentials"
             data-filter-value="Complete">
            <div class="stats-content">
                <div class="stats-title">Complete Credentials</div>
                <div class="stats-value"><?= $total_complete_students ?? 0 ?></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card filter-card"
             data-filter-type="credentials"
             data-filter-value="Incomplete">
            <div class="stats-content">
                <div class="stats-title">Incomplete Credentials</div>
                <div class="stats-value"><?= $total_incomplete_students ?? 0 ?></div>
            </div>
        </div>
    </div>

</div>
<div class="mb-5"></div>
<script>
document.addEventListener('DOMContentLoaded', function(){

    document.querySelectorAll('.stats-card[data-filter-type]').forEach(card => {

        card.addEventListener('click', function(){

            const type = this.dataset.filterType;
            const value = this.dataset.filterValue;

            const cardFilter =
                document.getElementById('cardFilter');

            const filterForm =
                document.getElementById('filterForm');

            if(!cardFilter || !filterForm){
                console.log('cardFilter or filterForm not found');
                return;
            }

            cardFilter.value = type + ':' + value;

            filterForm.submit();
        });

    });

});
</script>  