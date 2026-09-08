 function openModal() {
    document.getElementById("addStudentModal").classList.add("show");
    document.body.classList.add("modal-open"); /* ✅ lock background */
}

function closeModal() {
    document.getElementById("addStudentModal").classList.remove("show");
    document.body.classList.remove("modal-open"); /* ✅ balik normal */
} 

<?php if(isset($_GET['success'])): ?>

<script>
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: 'Student added successfully.',
    showConfirmButton: false,
    timer: 2000
});
</script>

<?php endif; ?>

function openImportModal() {
    document.getElementById("importExcelModal").classList.add("show");
    document.body.classList.add("modal-open"); /* lock background */
}

function closeImportModal() {
    document.getElementById("importExcelModal").classList.remove("show");
    document.body.classList.remove("modal-open"); /* unlock */
}

function openExportModal() {
    document.getElementById("exportModal").classList.add("show");
    document.body.classList.add("modal-open");
    updateExportCount();
}

function closeExportModal() {
    document.getElementById("exportModal").classList.remove("show");
    document.body.classList.remove("modal-open");
}

// Update student count dynamically
function updateExportCount() {
    const semester = document.getElementById('exportSemester').value;
    const section = document.getElementById('exportSection').value;

    // AJAX request to count students
    fetch(`export_students_count.php?semester=${encodeURIComponent(semester)}&section=${encodeURIComponent(section)}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('exportCount').innerText = `Total students to export: ${data.count}`;
        });
}
