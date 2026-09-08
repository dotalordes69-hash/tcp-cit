

<!-- ADD STUDENT MODAL -->
<div class="modal fade"
     id="addStudentModal"
     tabindex="-1"
     data-bs-backdrop="static">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <div class="d-flex align-items-center gap-3">
                    <div class="registrar-icon">
                    </div>

                    <div>
                        <h5 class="modal-title mb-0">
                            Student Registration
                        </h5>
                        <small>Add new TCP student record</small>
                    </div>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

            </div>

            <form method="POST">

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Student ID</label>
                            <input type="text"
                                   name="student_id"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text"
                                   name="contact_no"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text"
                                   name="fullname"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email"
                                   name="email_address"
                                   class="form-control"
                                   required>
                        </div>
<div class="col-md-4 mb-3">
    <label class="form-label">Semester</label>
    <select name="semester"
        class="form-select"
        required>
        <option value="">Select Semester</option>
        <?php foreach($semester_options as $sem): ?>
            <option value="<?= htmlspecialchars($sem) ?>">
                <?= htmlspecialchars($sem) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="col-md-4 mb-3">
    <label class="form-label">School Year</label>
    <select name="school_year" class="form-select" required>
        <option value="">Select School Year</option>
        <?php foreach($school_year_options as $sy): ?>
            <option value="<?= htmlspecialchars($sy) ?>">
                <?= htmlspecialchars($sy) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="col-md-4 mb-3">
    <label class="form-label">Section</label>
<select name="section"
        class="form-select"
        required>
        <option value="">Select Section</option>
        <?php foreach($section_options as $sec): ?>
            <option value="<?= htmlspecialchars($sec) ?>">
                <?= htmlspecialchars($sec) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="col-12 mb-3">
    <label class="form-label">Tuition Fee</label>

    <div class="input-group">
        <span class="input-group-text">₱</span>
        <input type="number"
               name="total_amount"
               class="form-control"
               placeholder="Enter Tuition Fee"
               required>
    </div>
</div>

                    </div>

                </div>

                <div class="modal-footer">
<button type="submit"
        name="add_student"
        class="btn btn-danger">
    Save Student
</button>
                </div>

            </form>

        </div>

    </div>

</div>
