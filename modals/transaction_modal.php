<style>

    .modal-content{
    border:none;
    border-radius:12px;
    overflow:hidden;
}

.card{
    border-radius:10px;
}

.form-control{
    border-radius:8px;
}

.table thead{
    background:#dc3545;
    color:#fff;
}

.btn-danger{
    font-weight:600;
}

.btn-dark{
    font-weight:600;
}
.modal-header.bg-danger .modal-title,
.modal-header.bg-danger small{
    color:#fff !important;
}

</style>

<div class="modal fade"
     id="transactionModal"
     tabindex="-1"
     data-bs-backdrop="static"
     data-bs-keyboard="false">

    <div class="modal-dialog modal-dialog-centered modal-xl">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-danger text-white">

                <div>
                    <h5 class="modal-title mb-0">
                        <i class="bi bi-receipt-cutoff me-2"></i>
                        Student Transaction Records
                    </h5>

                    <small class="text-white">
                        Search and review student payment transactions
                    </small>
                </div>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <!-- BODY -->
            <div class="modal-body">

                <div class="card shadow-sm border-0">

                    <div class="card-header bg-light">
                        <strong>
                            <i class="bi bi-person-vcard me-1"></i>
                            Student Information
                        </strong>
                    </div>

                    <div class="card-body">

                      <div class="row g-3">

    <!-- Student ID -->
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            Student ID
        </label>

        <input type="text"
               id="searchStudentTransaction"
               class="form-control"
               placeholder="Enter Student ID">
    </div>

    <!-- Reference Number -->
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            Reference Number
        </label>

        <input type="text"
               id="searchReference"
               class="form-control"
               placeholder="Enter Reference Number">
    </div>

    <!-- Full Name -->
    <div class="col-md-6">
        <label class="form-label fw-semibold">
            Full Name
        </label>

        <input type="text"
               id="studentName"
               class="form-control bg-light"
               readonly>
    </div>

    <!-- Semester -->
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            Semester
        </label>

        <input type="text"
               id="studentSemester"
               class="form-control bg-light"
               readonly>
    </div>

    <!-- School Year -->
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            School Year
        </label>

        <input type="text"
               id="studentSchoolYear"
               class="form-control bg-light"
               readonly>
    </div>

    <!-- Section -->
    <div class="col-md-3">
        <label class="form-label fw-semibold">
            Section
        </label>

        <input type="text"
               id="studentSection"
               class="form-control bg-light"
               readonly>
    </div>

    <!-- Buttons -->
    <div class="col-md-3 d-flex align-items-end gap-2">

        <button type="button"
                id="filterTransactionBtn"
                class="btn btn-danger flex-fill">
            <i class="bi bi-search"></i> Search
        </button>

        <button type="button"
                id="resetTransactionBtn"
                class="btn btn-dark">
            <i class="bi bi-arrow-clockwise"></i>
        </button>

    </div>

</div>

                    </div>

                </div>

                <div id="transactionResult" class="mt-3"></div>

            </div>

        </div>

    </div>

</div>
<script>

   document.addEventListener("DOMContentLoaded", () => {

    const referenceInput = document.getElementById("searchReference");

    referenceInput.addEventListener("change", () => {

        const reference = referenceInput.value.trim();

        if (!reference) return;

        fetch("search_reference.php?reference=" + encodeURIComponent(reference))
            .then(res => res.json())
            .then(data => {

                if (!data.id) {

                    Swal.fire({
                        icon: "warning",
                        title: "Reference Not Found",
                        text: "No transaction found for this reference number."
                    });

                    return;
                }

                document.getElementById("searchStudentTransaction").value = data.student_id ?? "";
                document.getElementById("studentName").value = data.fullname ?? "";
                document.getElementById("studentSemester").value = data.semester ?? "";
                document.getElementById("studentSchoolYear").value = data.school_year ?? "";
                document.getElementById("studentSection").value = data.section ?? "";

                return fetch(
                    "transaction_history.php?student_id=" +
                    encodeURIComponent(data.student_id)
                );

            })
            .then(res => res ? res.text() : "")
            .then(html => {

                if (html) {
                    document.getElementById("transactionResult").innerHTML = html;
                }

            })
            .catch(() => {

                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Unable to retrieve transaction."
                });

            });

    });

});
</script>