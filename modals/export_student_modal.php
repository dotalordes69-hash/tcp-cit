<div class="modal fade"
     id="exportStudentModal"
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
                            Export Student Records
                        </h5>
                        <small>
                            Download filtered student records to Excel/CSV
                        </small>
                    </div>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <form action="includes/student_export.php" method="GET">

                <!-- Current Filters -->
<input type="hidden" id="exportSearch" name="search">
<input type="hidden" id="exportSemester" name="semester_filter">
<input type="hidden" id="exportSchoolYear" name="school_year_filter">
<input type="hidden" id="exportSection" name="section_filter">
                <div class="modal-body">

                    <div class="alert alert-light border">

                        <div class="fw-bold text-danger mb-2">
                            Export Information
                        </div>

                        <small class="text-muted">
                            The exported file will follow the currently
                            selected filters from Student List.
                        </small>

                    </div>

                    <div class="border rounded p-3 bg-light">

                        <h6 class="text-danger mb-2">
                            Current Export Filters
                        </h6>

                        <table class="table table-sm mb-0">

                            <tr>
    <td width="180"><b>Student ID</b></td>
    <td id="currentStudentId">All Students</td>
</tr>

<tr>
    <td><b>Semester</b></td>
    <td id="currentSemester">All</td>
</tr>

<tr>
    <td><b>School Year</b></td>
    <td id="currentSchoolYear">All</td>
</tr>

<tr>
    <td><b>Section</b></td>
    <td id="currentSection">All</td>
</tr>

                        </table>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="submit"
                            class="btn btn-danger">
                        Download Excel File
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>