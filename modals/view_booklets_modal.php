<style>
    #viewBookletsModal .modal-content{
    border:none;
    border-radius:15px;
    overflow:hidden;
}

#viewBookletsModal .modal-header{
    background:#dc3545;
    color:#fff;
}

#viewBookletsModal .btn-close{
    filter:brightness(0) invert(1);
}

#viewBookletsModal table th{
    font-size:13px;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:.5px;
}

#searchBK{
    border-radius:10px;
}

#filterBK,
#resetBK{
    border-radius:10px;
    font-weight:600;
}

.deposit-badge{
    transition:.2s;
}

.deposit-badge:hover{
    transform:scale(1.05);
}
</style>


<div class="modal fade"
     id="viewBookletsModal"
     tabindex="-1"
     data-bs-backdrop="static"
     data-bs-keyboard="false">


    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">OR Booklets</h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>
<div class="modal-body bg-light">

    <!-- ================= BOOKLET PAGE ================= -->
    <div id="bookletPage">
<div class="row mb-3">

    <div class="col-md-4">
        <label class="form-label fw-semibold">
            Filter Status
        </label>

        <select id="statusFilter" class="form-select">
            <option value="">All Booklets</option>
            <option value="Pending">Pending</option>
            <option value="Deposited">Deposited</option>
        </select>
    </div>

</div>
 <!-- TABLE -->
                <div class="table-responsive">

                    <table class="table table-hover table-bordered align-middle bg-white"
                        id="bkTable">

                        <thead class="table-danger">
    <tr>
        <th>Booklet No.</th>
        <th>OR Start</th>
        <th>OR End</th>
        <th>Current OR</th>
        <th>Total Collection</th>
        <th>Status</th>
    </tr>
</thead>

                        <tbody>

                        <?php
                        $totalDeposited = 0;
                        mysqli_data_seek($booklets, 0);

                        while($row = mysqli_fetch_assoc($booklets)):

                            if ($row['bk_status'] == 'Deposited') {
    $totalDeposited += $row['total_collection'];
}


                        ?>

                        <tr>

                            <td><?= htmlspecialchars($row['booklet_no']) ?></td>

                            <td><?= $row['or_start'] ?></td>

                            <td><?= $row['or_end'] ?></td>
<td>

<a href="#"
   class="viewORList fw-bold text-decoration-none"
   data-id="<?= $row['id']; ?>"
   data-booklet="<?= htmlspecialchars($row['booklet_no']); ?>">

<?php
if($row['current_or'] > $row['or_end']){
?>
<span class="badge bg-danger">
Completed
</span>
<?php
}else{
echo $row['current_or'];
}
?>

</a>

</td>

                            <td>
                                ₱<?= number_format($row['total_collection'],2) ?>
                            </td>

                            <td>

                            <?php if($row['bk_status'] == 'Deposited'): ?>

                                <span class="badge bg-success">
                                    Deposited
                                </span>

                                <div class="small text-muted mt-1">
                                    <?= date('M d, Y h:i A', strtotime($row['deposited_date'])) ?>
                                    <br>
                                    By: <?= htmlspecialchars($row['deposited_by']) ?>
                                </div>

                            <?php else: ?>

                                <span class="badge bg-warning text-dark deposit-badge"
                                      style="cursor:pointer"
                                      data-id="<?= $row['id'] ?>"
                                      data-booklet="<?= htmlspecialchars($row['booklet_no']) ?>">
                                    Pending
                                </span>

                            <?php endif; ?>

                            </td>

                        </tr>

                        <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>

<div class="card border-0 shadow-sm mt-3">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center">

            <span class="fw-bold">
                Total Deposited Booklets
            </span>

            <h4 class="text-success mb-0">
                ₱<?= number_format($totalDeposited, 2) ?>
            </h4>

        </div>

    </div>
</div>
                <!-- PAGINATION -->
               <div class="card border-0 shadow-sm mt-3">
    <div class="card-body py-2">

        <div class="d-flex justify-content-between align-items-center">

            <small id="pageInfo"
                   class="text-muted fw-semibold">
            </small>

            <div>

                <button class="btn btn-outline-secondary btn-sm"
                        id="prevPage">
                    <i class="bi bi-chevron-left"></i>
                    Previous
                </button>

                <button class="btn btn-outline-secondary btn-sm"
                        id="nextPage">
                    Next
                    <i class="bi bi-chevron-right"></i>
                </button>

            </div>

        </div>

    </div>
</div>
    </div>
    <!-- ================= OR PAGE ================= -->
    <div id="orPage" style="display:none;">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <button class="btn btn-outline-secondary btn-sm"
                    id="backBooklets">

                <i class="bi bi-arrow-left"></i>
                Back

            </button>

            <h6 class="fw-bold text-danger mb-0">
                Official Receipt List
            </h6>

        </div>

        <div id="orListContent"></div>

    </div>

</div>

               

            </div>

        </div>
    </div>
</div>
<script>
document.addEventListener('click', function(e){

    let badge = e.target.closest('.deposit-badge');

    if(!badge) return;

    let id = badge.dataset.id;
    let booklet = badge.dataset.booklet;

    Swal.fire({
        title: 'Deposit Booklet?',
        html: 'Do you want to deposit <b>' + booklet + '</b>?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Deposit',
        confirmButtonColor: '#198754'
    }).then((result)=>{

        if(result.isConfirmed){

            fetch('deposit_booklet.php?id=' + id)
            .then(res => res.text())
            .then(() => {

                Swal.fire({
                    icon:'success',
                    title:'Deposited',
                    text:'Booklet successfully deposited.',
                    timer:1500,
                    showConfirmButton:false

                }).then(()=>{
                    location.reload();
                });

            });

        }

    });

});
</script>
<script>
document.addEventListener('DOMContentLoaded', function(){

    const searchInput = document.getElementById('searchBK');
    const filterBtn   = document.getElementById('filterBK');
    const resetBtn    = document.getElementById('resetBK');
    const statusFilter = document.getElementById('statusFilter');
    const table = document.getElementById('bkTable');

    if(!table) return;

    const rows = Array.from(
        table.querySelectorAll('tbody tr')
    );

    const rowsPerPage = 100;

    let currentPage = 1;
    let filteredRows = rows;
statusFilter.addEventListener('change', function(){

    const status = this.value.toLowerCase();

    filteredRows = rows.filter(row => {

        if(status === '') return true;

        return row.cells[5].innerText.toLowerCase().includes(status);

    });

    currentPage = 1;
    renderTable();

});
    function renderTable(){

        rows.forEach(row=>{
            row.style.display = 'none';
        });

        let start = (currentPage - 1) * rowsPerPage;
        let end   = start + rowsPerPage;

        filteredRows.slice(start,end).forEach(row=>{
            row.style.display = '';
        });

        let totalPages =
            Math.ceil(filteredRows.length / rowsPerPage) || 1;

        let showingFrom =
            filteredRows.length === 0 ? 0 : start + 1;

        let showingTo =
            Math.min(end, filteredRows.length);

        document.getElementById('pageInfo').innerHTML =
            `${showingFrom}-${showingTo} of ${filteredRows.length}`;

        document.getElementById('prevPage').disabled =
            currentPage === 1;

        document.getElementById('nextPage').disabled =
            currentPage === totalPages;
    }

    filterBtn.addEventListener('click', function(){

        let keyword =
            searchInput.value.trim().toLowerCase();

        filteredRows = rows.filter(row => {

            return row.cells[0]
                .innerText
                .toLowerCase()
                .includes(keyword);

        });

        currentPage = 1;
        renderTable();

    });

    resetBtn.addEventListener('click', function(){

        searchInput.value = '';

        filteredRows = rows;

        currentPage = 1;

        renderTable();

    });

    document.getElementById('prevPage')
    .addEventListener('click', function(){

        if(currentPage > 1){

            currentPage--;

            renderTable();

        }

    });

    document.getElementById('nextPage')
    .addEventListener('click', function(){

        let totalPages =
            Math.ceil(filteredRows.length / rowsPerPage);

        if(currentPage < totalPages){

            currentPage++;

            renderTable();

        }

    });

    renderTable();

});
document.addEventListener("click", function (e) {

    const btn = e.target.closest(".viewORList");

    if (!btn) return;

    e.preventDefault();

    let booklet = btn.dataset.booklet;

    fetch("view_booklet_or.php?booklet=" + encodeURIComponent(booklet))
        .then(res => res.text())
        .then(html => {

            document.getElementById("orListContent").innerHTML = html;

            document.getElementById("bookletPage").style.display = "none";
            document.getElementById("orPage").style.display = "block";

        });

});

document.getElementById("backBooklets").addEventListener("click", function () {

    document.getElementById("orPage").style.display = "none";
    document.getElementById("bookletPage").style.display = "block";

    document.getElementById("orListContent").innerHTML = "";

});
</script>