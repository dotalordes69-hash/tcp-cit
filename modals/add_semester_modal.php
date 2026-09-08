<div class="modal fade"
     id="addSemesterModal"
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
                            Semester Management
                        </h5>
                        <small>
                            Add semester records
                        </small>
                    </div>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

            </div>

            <form action="includes/add_semester_handler.php"
                  method="POST">

                <div class="modal-body">


                    <div id="semesterContainer">

                        <div class="semester-row border rounded p-3 mb-3 bg-light">

                            <div class="row">

                                <div class="col-md-12">

                                    <label class="form-label">
                                        Create Semester Name
                                    </label>

                                    <input type="text"
                                           name="semester_name[]"
                                           class="form-control"
                                           placeholder="Example: 1ST SEMESTER"
                                           required>

                                </div>

                            </div>

                        </div>

                    </div>


                </div>

                <div class="modal-footer">

                    <button type="submit"
                            class="btn btn-danger">
                        Save Semester
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

