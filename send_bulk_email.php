<div class="modal fade"
     id="bulkEmailModal"
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
                            Bulk Email Distribution
                        </h5>

                        <small>
                            Send announcements to students by section
                        </small>
                    </div>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

            </div>

            <form action="send_bulk_email.php"
                  method="POST">

                <div class="modal-body">

                    <div class="row">

                        <!-- Section -->
                        <div class="col-md-12 mb-3">

                            <label class="form-label">
                                Section
                            </label>

                            <select name="section"
                                    id="sectionSelect"
                                    class="form-select"
                                    required>

                                <option value="">
                                    Select Section
                                </option>

                                <?php
                                $sections = mysqli_query(
                                    $conn,
                                    "SELECT DISTINCT section
                                     FROM students
                                     WHERE section <> ''
                                     ORDER BY section"
                                );

                                while($sec = mysqli_fetch_assoc($sections)):
                                ?>

                                <option value="<?= htmlspecialchars($sec['section']) ?>">
                                    <?= htmlspecialchars($sec['section']) ?>
                                </option>

                                <?php endwhile; ?>

                            </select>

                        </div>

                        <!-- Recipients -->
                        <div class="col-md-12 mb-3">

                            <label class="form-label">
                                Recipient Email Addresses
                            </label>

                            <div id="recipientList"
                                 class="border rounded p-2 bg-light"
                                 style="height:220px;overflow-y:auto;">

                                <div class="text-muted text-center mt-5">
                                    Select a section first
                                </div>

                            </div>

                        </div>

                        <!-- Subject -->
                        <div class="col-md-12 mb-3">

                            <label class="form-label">
                                Subject
                            </label>

                            <input type="text"
                                   name="subject"
                                   class="form-control"
                                   required>

                        </div>

                        <!-- Message -->
                        <div class="col-md-12 mb-3">

                            <label class="form-label">
                                Message
                            </label>

                            <textarea name="message"
                                      rows="8"
                                      class="form-control"
                                      required></textarea>

                        </div>

                        <!-- Signature -->
                        <div class="col-md-12 mb-3">

                            <label class="form-label">
                                Email Signature
                            </label>

                            <select name="signature_id"
                                    class="form-select">

                                <option value="">
                                    No Signature
                                </option>

                                <?php
                                $sigQuery = mysqli_query(
                                    $conn,
                                    "SELECT *
                                     FROM email_signatures
                                     ORDER BY signature_name ASC"
                                );

                                while($sig = mysqli_fetch_assoc($sigQuery)):
                                ?>

                                <option value="<?= $sig['id'] ?>">
                                    <?= htmlspecialchars($sig['signature_name']) ?>
                                </option>

                                <?php endwhile; ?>

                            </select>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        📧 Send Bulk Email
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>