document.addEventListener('DOMContentLoaded', () => {

    const addBtn = document.getElementById('addSectionRow');
    const container = document.getElementById('sectionContainer');

    if (!addBtn || !container) return;

    addBtn.addEventListener('click', () => {

        const html = `
        <div class="section-row border rounded p-3 mb-3 bg-light">

            <div class="row align-items-end">

                <div class="col-md-11">

                    <label class="form-label">
                        Section Name
                    </label>

                    <input type="text"
                           name="section_name[]"
                           class="form-control"
                           placeholder="Example: TCP-B"
                           required>

                </div>

                <div class="col-md-1 text-end">

                    <button type="button"
                            class="btn btn-danger removeSection">
                        ×
                    </button>

                </div>

            </div>

        </div>
        `;

        container.insertAdjacentHTML('beforeend', html);

    });

    document.addEventListener('click', (e) => {

        if (e.target.classList.contains('removeSection')) {

            e.target.closest('.section-row').remove();

        }

    });

});