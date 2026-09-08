<?php
include 'request_modals/request_queries.php';
?>

<?php include 'request_modals/request_styles.php'; ?>

<div id="transactionContent">

<div class="modal fade"
     id="transactionModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-scrollable transaction-modal">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Document Request Transactions
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                <?php include 'request_modals/request_filters.php'; ?>

                <?php include 'request_modals/request_summary.php'; ?>

                <?php include 'request_modals/request_table.php'; ?>

                <?php include 'request_modals/request_pagination.php'; ?>

            </div>

        </div>

    </div>

</div>

</div>

<?php include 'request_modals/request_scripts.php'; ?>