<nav class="mt-3">

    <ul class="pagination justify-content-end">

        <?php if($page > 1){ ?>

            <li class="page-item">
                <a class="page-link"
                   href="?status=<?= urlencode($statusFilter) ?>&school_year=<?= urlencode($schoolYearFilter) ?>&page=<?= $page-1 ?>&modal=1">
                    Previous
                </a>
            </li>

        <?php } ?>

        <?php for($i = 1; $i <= $totalPages; $i++){ ?>

            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">

                <a class="page-link"
                   href="?status=<?= urlencode($statusFilter) ?>&school_year=<?= urlencode($schoolYearFilter) ?>&page=<?= $i ?>&modal=1">

                    <?= $i ?>

                </a>

            </li>

        <?php } ?>

        <?php if($page < $totalPages){ ?>

            <li class="page-item">
                <a class="page-link"
                   href="?status=<?= urlencode($statusFilter) ?>&school_year=<?= urlencode($schoolYearFilter) ?>&page=<?= $page+1 ?>&modal=1">
                    Next
                </a>
            </li>

        <?php } ?>

    </ul>

</nav>