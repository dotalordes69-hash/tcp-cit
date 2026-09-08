<link rel="stylesheet" href="assets/css/send_bulk.css">

<div class="modal fade"
     id="bulkEmailModal"
     tabindex="-1"
     data-bs-backdrop="static">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">

                <div>
                    <h5 class="modal-title">
                        📧 Bulk Email Distribution
                    </h5>

                    <small>
                        Send announcements to students
                    </small>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

            </div>

            <form action="send_bulk_email.php"
                  method="POST">

                <div class="modal-body">

                    <div class="row g-4">

                        <!-- LEFT -->
                        <div class="col-lg-4">

                            <?php include 'modals/bulk_email_filter.php'; ?>

                        </div>

                        <!-- RIGHT -->
                        <div class="col-lg-8">

                            <?php include 'modals/bulk_email_preview.php'; ?>

                        </div>

                    </div>

                    <hr class="my-4">

                    <?php include 'modals/bulk_email_form.php'; ?>

                    <hr class="my-4">

                    <?php include 'modals/bulk_email_signature.php'; ?>

                </div>

                <div class="modal-footer">

                    <button class="btn btn-light"
                            data-bs-dismiss="modal"
                            type="button">

                        Cancel

                    </button>

                    <button class="btn btn-danger"
                            type="submit">

                        📧 Send Bulk Email

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script src="assets/js/bulk_email.js"></script>