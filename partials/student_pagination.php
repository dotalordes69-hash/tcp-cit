
<style>

.pagination{
    gap:2px;
}

.pagination .page-link{
    color:#b30000;
    background:#fff;
    border:1px solid #d9d9d9;
    border-radius:6px;
    min-width:32px;
    height:30px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:12px;
    font-weight:600;
    padding:0 8px;
    transition:.2s ease;
    box-shadow:0 1px 2px rgba(0,0,0,.04);
}

.pagination .page-link:hover{
    background:#fff5f5;
    border-color:#b30000;
    color:#8b0000;
}

.pagination .page-item.active .page-link{
    background:#b30000;
    border-color:#b30000;
    color:#fff;
    box-shadow:0 2px 5px rgba(179,0,0,.18);
}

.pagination .page-item.disabled .page-link{
    background:#f8f9fa;
    color:#adb5bd;
    border-color:#e9ecef;
    pointer-events:none;
}

.text-muted.small{
    font-size:12px;
    color:#6c757d !important;
}

</style>

<?php
$queryParams = $_GET;

$start_page = max(1, $page - 2);
$end_page   = min($total_pages, $page + 2);
?>

<div class="d-flex justify-content-between align-items-center mt-3 p-2 border-top">

    <div class="text-muted small">
        Showing
        <strong><?= $showing_start ?></strong>
        -
        <strong><?= $showing_end ?></strong>
        of
        <strong><?= number_format($total_rows) ?></strong>
        students
    </div>

    <nav>
        <ul class="pagination pagination-sm mb-0">

            <!-- Previous -->
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link"
                   href="?<?= http_build_query(array_merge(
                       $queryParams,
                       ['page' => max(1, $page - 1)]
                   )) ?>">
                    Previous
                </a>
            </li>

            <!-- Page Numbers -->
            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link"
                       href="?<?= http_build_query(array_merge(
                           $queryParams,
                           ['page' => $i]
                       )) ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <!-- Next -->
            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link"
                   href="?<?= http_build_query(array_merge(
                       $queryParams,
                       ['page' => min($total_pages, $page + 1)]
                   )) ?>">
                    Next
                </a>
            </li>

        </ul>
    </nav>

</div>