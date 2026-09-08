<!-- ADD LET YEAR MODAL -->

<style>
    .form-control-lg{
    border-radius:10px;
    font-size:16px;
    height:48px;
}

</style>


<div class="modal fade"
     id="addLetYearModal"
     tabindex="-1"
     data-bs-backdrop="static"
     data-bs-keyboard="false">

    <div class="modal-dialog modal-dialog-centered">

        <form action="save_let_year.php" method="POST">

            <div class="modal-content border-0 shadow-lg rounded-4">

                <!-- Header -->
                <div class="modal-header bg-maroon">

                    <div>
                        <h5 class="modal-title fw-bold mb-1">
                            📅 Add LET Examination Year
                        </h5>

                        <small class="opacity-75">
                            Register a new LET examination year for student records.
                        </small>
                    </div>

                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal">
                    </button>

                </div>

                <!-- Body -->
                <div class="modal-body p-4">

                    <div class="alert alert-light border mb-4">

                        <div class="fw-semibold text-dark mb-1">
                            Information
                        </div>

                        <small class="text-muted">
                            The examination year added here will automatically
                            become available when updating a student's
                            LET examination information.
                        </small>

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-bold">
                            Examination Year
                            <span class="text-danger">*</span>
                        </label>

                        <input type="number"
                               name="year"
                               class="form-control form-control-lg"
                               placeholder="Example: 2026"
                               min="2000"
                               max="2100"
                               required>

                        <div class="form-text">
                            Enter the official Professional Regulation Commission (PRC)
                            LET examination year.
                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer px-4 py-3">

                    <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-maroon px-4">
                        💾 Save Examination Year
                    </button>

                </div>

            </div>

        </form>

    </div>
</div>