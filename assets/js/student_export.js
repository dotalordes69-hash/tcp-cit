document.addEventListener('DOMContentLoaded', () => {

    const exportModal =
        document.getElementById('exportStudentModal');

    if (!exportModal) return;

    exportModal.addEventListener('show.bs.modal', () => {

        const studentId =
            document.getElementById('searchInput')?.value.trim() || '';

        const semester =
            document.querySelector('select[name="semester_filter"]')?.value || '';

        const schoolYear =
            document.querySelector('select[name="school_year_filter"]')?.value || '';

        const section =
            document.querySelector('select[name="section_filter"]')?.value || '';

        // Display sa modal
        document.getElementById('currentStudentId').textContent =
            studentId || 'All Students';

        document.getElementById('currentSemester').textContent =
            semester || 'All';

        document.getElementById('currentSchoolYear').textContent =
            schoolYear || 'All';

        document.getElementById('currentSection').textContent =
            section || 'All';

        // Ipadala sa export php
        document.getElementById('exportSearch').value =
            studentId;

        document.getElementById('exportSemester').value =
            semester;

        document.getElementById('exportSchoolYear').value =
            schoolYear;

        document.getElementById('exportSection').value =
            section;

    });

});