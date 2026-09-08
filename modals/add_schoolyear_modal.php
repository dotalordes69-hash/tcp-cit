<div class="modal fade"
     id="addSchoolYearModal"
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
                            School Year Management
                        </h5>
                        <small>
                            Add school year records
                        </small>
                    </div>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

            </div>

            <form action="includes/add_schoolyear_handler.php"
                  method="POST">

                <div class="modal-body">

                 

                    <div id="schoolYearContainer">

                        <div class="schoolyear-row border rounded p-3 mb-3 bg-light">

                            <div class="row align-items-end">

                                <div class="col-md-11">

                                    <label class="form-label">
                                       Create School Year
                                    </label>

                                    <input type="text"
                                           name="school_year[]"
                                           class="form-control"
                                           placeholder="Example: 2025-2026"
                                           required>

                                </div>

                            </div>

                        </div>

                    </div>



                </div>

                <div class="modal-footer">

                    <button type="submit"
                            class="btn btn-danger">
                        Save School Year
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>