document.addEventListener('DOMContentLoaded', () => {

    const addBtn = document.getElementById('addSemesterRow');
    const container = document.getElementById('semesterContainer');

    if (!addBtn || !container) return;

    addBtn.addEventListener('click', () => {

        const html = `
        <div class="semester-row border rounded p-3 mb-3 bg-light">

            <div class="row align-items-end">

                <div class="col-md-11">

                    <label class="form-label">
                        Semester Name
                    </label>

                    <input type="text"
                           name="semester_name[]"
                           class="form-control"
                           placeholder="Example: 2ND SEMESTER"
                           required>

                </div>

                <div class="col-md-1 text-end">

                    <button type="button"
                            class="btn btn-danger removeSemester">
                        ×
                    </button>

                </div>

            </div>

        </div>
        `;

        container.insertAdjacentHTML('beforeend', html);

    });

    document.addEventListener('click', (e) => {

        if (e.target.classList.contains('removeSemester')) {

            e.target.closest('.semester-row').remove();

        }

    });

});