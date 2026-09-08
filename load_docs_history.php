<?php
include 'db.php';

$student_id = $_GET['student_id'] ?? '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

if($page < 1){
    $page = 1;
}

$limit = 10;
$offset = ($page - 1) * $limit;


// COUNT
$count = $conn->prepare("
    SELECT COUNT(*) as total
    FROM docs_history
    WHERE student_id = ?
");

$count->bind_param("s",$student_id);
$count->execute();

$total = $count->get_result()->fetch_assoc()['total'];

$total_pages = ceil($total / $limit);


// DATA
$stmt = $conn->prepare("
SELECT
    id,
    particular,
    date_request,
    date_release,
    released_by
FROM docs_history
WHERE student_id = ?
ORDER BY id DESC
LIMIT ?,?
");


$stmt->bind_param(
    "sii",
    $student_id,
    $offset,
    $limit
);


$stmt->execute();

$result = $stmt->get_result();

?>


<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">
<thead class="table-light">

<tr>
    <th width="180">Particular</th>
    <th width="140">Date Request</th>
    <th width="140">Date Release</th>
    <th width="220">Released By</th>
</tr>

</thead>


<tbody>

<?php if($result->num_rows > 0): ?>


<?php while($row = $result->fetch_assoc()): ?>

<tr>

    <td>
        <?= htmlspecialchars($row['particular']) ?>
    </td>

    <td>
        <?= date(
            'M d, Y',
            strtotime($row['date_request'])
        ) ?>
    </td>

    <td>

<?php if(empty($row['date_release'])): ?>

<button
    type="button"
    class="btn btn-sm btn-warning release-btn"
    data-id="<?= $row['id'] ?>">
    Pending
</button>

<input
    type="date"
    class="form-control form-control-sm release-date d-none mt-1"
    data-id="<?= $row['id'] ?>">

<?php else: ?>

<span class="badge bg-success">
    <?= date(
        'M d, Y',
        strtotime($row['date_release'])
    ) ?>
</span>

<?php endif; ?>

    </td>

    <td>
        <?= htmlspecialchars($row['released_by']) ?>
    </td>

</tr>

<?php endwhile; ?>


<?php else: ?>

<tr>
<td colspan="4"
    class="text-center text-muted py-3">
    No document release history found.
</td>
</tr>

<?php endif; ?>


</tbody>

</table>

</div>



<div class="d-flex justify-content-between align-items-center mt-3">


<small class="text-muted">

Showing 
<?= $offset + 1 ?>
-
<?= min($offset+$limit,$total) ?>

of <?= $total ?>

records

</small>



<nav>

<ul class="pagination pagination-sm mb-0">


<?php for($i=1;$i<=$total_pages;$i++): ?>

<li class="page-item <?= ($page==$i)?'active':'' ?>">

<button type="button"
        class="page-link docs-page"
        data-page="<?= $i ?>">
    <?= $i ?>
</button>

</li>


<?php endfor; ?>


</ul>

</nav>


</div>
<script>

document.addEventListener('click', function(e){

    if(e.target.classList.contains('release-btn')){

        let id = e.target.dataset.id;

        e.target.style.display = 'none';

        document
        .querySelector('.release-date[data-id="'+id+'"]')
        .classList.remove('d-none');

    }

});


document.addEventListener('change', function(e){

    if(e.target.classList.contains('release-date')){

        let id = e.target.dataset.id;
        let date_release = e.target.value;

        fetch('update_release_date.php', {
            method:'POST',
            headers:{
                'Content-Type':
                'application/x-www-form-urlencoded'
            },
            body:
                'id=' + id +
                '&date_release=' + date_release
        })

        .then(response => response.json())

        .then(data => {

            if(data.success){

                loadDocsHistory();

                Swal.fire({
                    icon:'success',
                    title:'Updated',
                    text:'Release date saved.',
                    timer:1200,
                    showConfirmButton:false
                });

            }

        });

    }

});

</script>