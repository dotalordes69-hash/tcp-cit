<div id="emailHistoryContainer"></div>

<script>
function loadEmailHistory(page = 1) {

    fetch('ajax_email_history.php?student_id=<?php echo urlencode($student_id); ?>&student_pk=<?php echo urlencode($student_pk); ?>&email_page=' + page)
        .then(response => response.text())
        .then(html => {
            document.getElementById('emailHistoryContainer').innerHTML = html;
        })
        .catch(error => console.error(error));
}

document.addEventListener('DOMContentLoaded', function () {
    loadEmailHistory(1);
});

document.addEventListener('click', function(e){

    if(e.target.classList.contains('email-page-link')){
        e.preventDefault();

        let page = e.target.dataset.page;

        loadEmailHistory(page);
    }

});
</script>