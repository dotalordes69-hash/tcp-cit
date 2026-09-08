<div class="modal fade"
     id="importStudentModal"
     tabindex="-1"
     data-bs-backdrop="static"
     data-bs-keyboard="false">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <div class="d-flex align-items-center gap-3">

                    <div class="registrar-icon">
                       
                    </div>

                    <div>
                        <h5 class="modal-title mb-0">
                            Import Student Records
                        </h5>
                        <small>
                            Bulk upload TCP student records via CSV
                        </small>
                    </div>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <form action="includes/import_student_handler.php"
                  method="POST"
                  enctype="multipart/form-data">

                <div class="modal-body">

                    <div class="alert alert-light border">

                        <div class="fw-bold text-danger mb-2">
                            CSV File Format
                        </div>

                        <small class="text-muted">
                            student_id, fullname, email_address,
                            contact_no, semester, school_year, section
                        </small>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Select CSV File
                        </label>

                        <input type="file"
                               name="csv_file"
                               class="form-control"
                               accept=".csv"
                               required>

                    </div>

                    <div class="border rounded p-3 bg-light">

                        <h6 class="mb-2 text-danger">
                            Import Guidelines
                        </h6>

                        <ul class="mb-0 small text-muted">
                            <li>Duplicate Student IDs will be skipped automatically.</li>
                            <li>First row must contain column headers.</li>
                            <li>Only CSV files are accepted.</li>
                            <li>Student names will be converted to UPPERCASE.</li>
                        </ul>

                    </div>
<div class="mb-3">
  <!-- TUITION FEE -->
    <label class="form-label">
        Tuition Fee
    </label>

    <div class="input-group">
        <span class="input-group-text">₱</span>

        <input type="number"
               name="total_amount"
               class="form-control"
               placeholder="Enter Tuition Fee"
               min="0"
               step="0.01"
               required>
    </div>

    <small class="text-muted">
        This amount will be applied to all imported students.
    </small>

</div>
                </div>

                <div class="modal-footer">

                    <button type="submit"
                            class="btn btn-danger">
                        Import Students
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>