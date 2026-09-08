<style>

#sendEmailModal .modal-dialog{
    max-width:1400px;
}

#sendEmailModal .modal-content{
    height:92vh;
    border:none;
    border-radius:12px;
}

#sendEmailModal form{
    display:flex;
    flex-direction:column;
    height:100%;
}

#sendEmailModal .modal-header{
    flex-shrink:0;
}

#sendEmailModal .modal-body{
    flex:1;
    overflow-y:auto;
    background:#fff;
    padding:25px;
}

#sendEmailModal .modal-footer{
    flex-shrink:0;
    background:#fff;
    border-top:1px solid #dee2e6;
    padding:15px 25px;
}

#sendEmailModal .card{
    border-radius:10px;
}

#message{
    height:430px;
    min-height:430px;
    max-height:430px;
    resize:none;
    overflow-y:auto;
    line-height:1.8;
    font-size:15px;
}

#signaturePreview{
    height:220px;
    overflow-y:auto;
    white-space:pre-wrap;
}

</style>


<div class="modal fade" id="sendEmailModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <form action="send_registrar_email.php"
                  method="POST"
                  enctype="multipart/form-data">

                <!-- HEADER -->
                <div class="modal-header bg-danger text-white">

                    <div>
                        <h5 class="modal-title mb-1">
                            <i class="bi bi-envelope-paper-fill me-2"></i>
                            REGISTRAR EMAIL
                        </h5>

                        <small class="opacity-75">
                            Send an official communication to partner schools and registrars.
                        </small>
                    </div>

                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"></button>

                </div>

                <!-- BODY -->
               <div class="modal-body registrar-body">

                    <div class="row g-4">

                        <!-- LEFT SIDE -->
                        <div class="col-lg-8">

                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">

                                    <!-- School -->
                                    <div class="mb-3">

                                        <label class="form-label fw-bold">
                                            School
                                        </label>

                                        <select id="schoolSelect"
                                                name="school_id"
                                                class="form-select"
                                                required>

                                            <option value="">
                                                Select School
                                            </option>

                                            <?php
                                            $schools = mysqli_query($conn,"
                                                SELECT *
                                                FROM registrar_directory
                                                ORDER BY school_name ASC
                                            ");

                                            while($school=mysqli_fetch_assoc($schools)){
                                            ?>

                                            <option
                                                value="<?= $school['id'] ?>"
                                                data-email="<?= htmlspecialchars($school['school_email']) ?>">

                                                <?= htmlspecialchars($school['school_name']) ?>

                                            </option>

                                            <?php } ?>

                                        </select>

                                    </div>

                                    <!-- Recipient -->
                                    <div class="mb-3">

                                        <label class="form-label fw-bold">
                                            Recipient Email
                                        </label>

                                        <input type="email"
                                               id="schoolEmail"
                                               class="form-control bg-light"
                                               readonly>

                                    </div>

                                    <!-- Subject -->
                                    <div class="mb-3">

                                        <label class="form-label fw-bold">
                                            Subject
                                        </label>

                                        <input type="text"
                                               name="subject"
                                               class="form-control"
                                               placeholder="Enter email subject"
                                               required>

                                    </div>

                                    <!-- Message -->
                                   <div class="mb-3">

    <label class="form-label fw-bold">
        Message
    </label>

    <textarea
        id="message"
        name="message"
        class="form-control"
        placeholder="Type your official registrar email here..."
        required></textarea>

    <small class="text-muted">
        Scroll inside the message box to view long email content.
    </small>

</div>

                                </div>
                            </div>

                        </div>

                        <!-- RIGHT SIDE -->
                        <div class="col-lg-4">

                            <div class="card border-0 shadow-sm mb-3">

                                <div class="card-body">

                                    <div class="mb-3">

                                        <label class="form-label fw-bold">
                                            Select Signature
                                        </label>

                                        <select
                                            name="signature_id"
                                            id="signatureSelect"
                                            class="form-select">

                                            <option value="">
                                                -- Select Signature --
                                            </option>

                                            <?php
                                            $signatures=mysqli_query($conn,"
                                                SELECT id,signature_name,signature_content
                                                FROM email_signatures
                                                ORDER BY signature_name ASC
                                            ");

                                            while($sig=mysqli_fetch_assoc($signatures)){
                                            ?>

                                            <option
                                                value="<?= $sig['id'] ?>"
                                                data-content="<?= htmlspecialchars($sig['signature_content'],ENT_QUOTES) ?>">

                                                <?= htmlspecialchars($sig['signature_name']) ?>

                                            </option>

                                            <?php } ?>

                                        </select>

                                    </div>

                                    <div>

                                        <label class="form-label fw-bold">
                                            Signature Preview
                                        </label>

                                        <div
                                            id="signaturePreview"
                                            class="border rounded bg-light p-3"
                                            style="
                                                height:220px;
                                                overflow-y:auto;
                                                white-space:pre-wrap;
                                            ">

                                            <span class="text-muted">
                                                No signature selected.
                                            </span>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!-- CREATE SIGNATURE -->
                            <div class="card border-0 shadow-sm">

                                <div class="card-header">

                                    <button
                                        type="button"
                                        class="btn btn-outline-primary btn-sm w-100"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#newSignatureSection">

                                        <i class="bi bi-plus-circle"></i>

                                        Create New Signature

                                    </button>

                                </div>

                                <div class="collapse"
                                     id="newSignatureSection">

                                    <div class="card-body">

                                        <div class="mb-3">

                                            <label class="form-label fw-bold">
                                                Signature Name
                                            </label>

                                            <input
                                                type="text"
                                                id="signature_name"
                                                class="form-control"
                                                placeholder="Registrar Official">

                                        </div>

                                        <div class="mb-3">

                                            <label class="form-label fw-bold">
                                                Signature Content
                                            </label>

                                            <textarea
                                                id="signature_content"
                                                class="form-control"
                                                rows="6"
                                                placeholder="Type your signature..."></textarea>

                                        </div>

                                        <button
                                            type="button"
                                            id="saveSignatureBtn"
                                            class="btn btn-primary w-100">

                                            Save Signature

                                        </button>

                                        <div id="saveSignatureMsg"
                                             class="mt-2"></div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        <i class="bi bi-x-circle"></i>
                        Cancel

                    </button>

                    <button
                        type="submit"
                        class="btn btn-danger px-5">

                        <i class="bi bi-send-fill me-2"></i>
                        Send Email

                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<script>

document.addEventListener("DOMContentLoaded", function () {

    // ============================
    // SCHOOL -> RECIPIENT EMAIL
    // ============================

    const schoolSelect = document.getElementById("schoolSelect");
    const schoolEmail  = document.getElementById("schoolEmail");

    if (schoolSelect) {

        schoolSelect.addEventListener("change", function () {

            const option = this.options[this.selectedIndex];

            schoolEmail.value = option.getAttribute("data-email") || "";

        });

    }


    // ============================
    // SIGNATURE PREVIEW
    // ============================

    const signatureSelect  = document.getElementById("signatureSelect");
    const signaturePreview = document.getElementById("signaturePreview");

    if (signatureSelect) {

        signatureSelect.addEventListener("change", function () {

            const option = this.options[this.selectedIndex];

            const signature = option.getAttribute("data-content") || "";

            if(signature.trim() == ""){

                signaturePreview.innerHTML =
                '<span class="text-muted">No signature selected.</span>';

            }else{

                signaturePreview.innerHTML =
                signature.replace(/\n/g,"<br>");

            }

        });

    }

});

</script>