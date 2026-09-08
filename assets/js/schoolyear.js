document.addEventListener('DOMContentLoaded', () => {

    const addBtn = document.getElementById('addSchoolYearRow');
    const container = document.getElementById('schoolYearContainer');

    if (!addBtn || !container) return;

    addBtn.addEventListener('click', () => {

        const html = `
        <div class="schoolyear-row border rounded p-3 mb-3 bg-light">

            <div class="row align-items-end">

                <div class="col-md-11">

                    <label class="form-label">
                        School Year
                    </label>

                    <input type="text"
                           name="school_year[]"
                           class="form-control"
                           placeholder="Example: 2026-2027"
                           required>

                </div>

                <div class="col-md-1 text-end">

                    <button type="button"
                            class="btn btn-danger removeSchoolYear">
                        ×
                    </button>

                </div>

            </div>

        </div>
        `;

        container.insertAdjacentHTML('beforeend', html);

    });

    document.addEventListener('click', (e) => {

        if (e.target.classList.contains('removeSchoolYear')) {

            e.target.closest('.schoolyear-row').remove();

        }

    });

});