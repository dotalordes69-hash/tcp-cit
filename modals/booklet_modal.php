<style>

    #bookletModal .modal-content{
    border-radius:15px;
    overflow:hidden;
}

#bookletModal .form-control{
    border-radius:10px;
    height:45px;
}

#bookletModal .form-label{
    font-size:13px;
    color:#495057;
}

#bookletModal .btn{
    border-radius:10px;
    font-weight:600;
}

#bookletModal .card{
    border-radius:12px;
}

#bookletModal .modal-header{
    border-bottom:none;
}

#bookletModal .modal-footer{
    border-top:1px solid #e9ecef;
}

</style>

<div class="modal fade"
     id="bookletModal"
     tabindex="-1"
     data-bs-backdrop="static"
     data-bs-keyboard="false">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <form method="POST">

                <div class="modal-header bg-danger text-white">
                    <div>
                        <h5 class="modal-title mb-0">
                            Create OR Booklet
                        </h5>
                        <small class="text-light">
                            Official Receipt Booklet Management
                        </small>
                    </div>

                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body bg-light">

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Booklet Number
                                </label>

                                <input type="text"
                                       name="booklet_no"
                                       class="form-control"
                                       placeholder="BK-001"
                                       required>
                            </div>

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        OR Start
                                    </label>

                                    <input type="number"
                                           name="or_start"
                                           class="form-control"
                                           placeholder="1001"
                                           required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        OR End
                                    </label>

                                    <input type="number"
                                           name="or_end"
                                           class="form-control"
                                           placeholder="1500"
                                           required>
                                </div>

                            </div>

                            <div class="alert alert-light border mb-0">
                                <small>
                                    <strong>Note:</strong>
                                    OR Start should be lower than OR End.
                                    The system will automatically track the
                                    current OR number used.
                                </small>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-white">



                    <button type="submit"
                            class="btn btn-danger px-4">
                        <i class="bi bi-save"></i>
                        Save Booklet
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>