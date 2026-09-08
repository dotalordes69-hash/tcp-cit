document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('filterForm');
    const tableContainer = document.getElementById('studentTableContainer');

    if (!form || !tableContainer) return;

    form.addEventListener('submit', function (e) {

        e.preventDefault();

        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        fetch('ajax_student_table.php?' + params.toString())
            .then(response => response.text())
            .then(data => {

                tableContainer.innerHTML = data;

            })
            .catch(error => {

                console.error('AJAX Error:', error);

            });

    });

});

document.querySelectorAll(
    'select[name="semester_filter"], select[name="school_year_filter"], select[name="section_filter"]'
).forEach(el => {

    el.addEventListener('change', () => {

        form.dispatchEvent(
            new Event('submit', {
                cancelable: true
            })
        );

    });

});
