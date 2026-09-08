<div class="modal fade"
     id="addSectionModal"
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
                            Section Management
                        </h5>
                        <small>
                            Add section records
                        </small>
                    </div>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

            </div>

            <form action="includes/add_section_handler.php"
                  method="POST">

                <div class="modal-body">


                    <div id="sectionContainer">

                        <div class="section-row border rounded p-3 mb-3 bg-light">

                            <div class="row align-items-end">

                                <div class="col-md-11">

                                    <label class="form-label">
                                       Create Section
                                    </label>

                                    <input type="text"
                                           name="section_name[]"
                                           class="form-control"
                                           placeholder="Example: A"
                                           required>

                                </div>

                            </div>

                        </div>

                    </div>

                    <button type="button"
                            id="addSectionRow"
                            class="btn btn-outline-danger btn-sm">

                        + Add Another Section

                    </button>

                </div>

                <div class="modal-footer">

                    <button type="submit"
                            class="btn btn-danger">
                        Save Section
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>