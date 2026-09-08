<link rel="stylesheet" href="assets/css/student_filter.css">

<form id="filterForm" method="GET">

    <input type="hidden"
           name="page"
           value="1">

    <input type="hidden"
           name="card_filter"
           id="cardFilter"
           value="<?= htmlspecialchars($_GET['card_filter'] ?? '') ?>">

    <div class="filter-card">

        <div class="row g-2 align-items-end">

            <div class="col-lg-4">
                <label class="form-label">Student ID</label>
                <input type="text"
                       id="searchInput"
                       name="search"
                       class="form-control form-control-sm"
                       placeholder="Enter Student ID..."
                       value="<?= htmlspecialchars($search ?? '') ?>">
            </div>

            <div class="col-lg-2">
                <label class="form-label">Semester</label>
                <select name="semester_filter"
                        class="form-select form-select-sm">
                    <option value="">All</option>
                    <?php foreach ($semester_options as $sem): ?>
                        <option value="<?= htmlspecialchars($sem) ?>"
                            <?= ($semester_filter ?? '') === $sem ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sem) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-lg-2">
                <label class="form-label">School Year</label>
                <select name="school_year_filter"
                        class="form-select form-select-sm">
                    <option value="">All</option>
                    <?php foreach ($school_year_options as $sy): ?>
                        <option value="<?= htmlspecialchars($sy) ?>"
                            <?= ($school_year_filter ?? '') === $sy ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sy) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-lg-2">
                <label class="form-label">Section</label>
                <select name="section_filter"
                        class="form-select form-select-sm">
                    <option value="">All</option>
                    <?php foreach ($section_options as $sec): ?>
                        <option value="<?= htmlspecialchars($sec) ?>"
                            <?= ($section_filter ?? '') === $sec ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sec) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
<div class="col-lg-2">
    <label class="form-label">Balance</label>

    <select name="amount_filter"
            class="form-select form-select-sm">

        <option value="">
            Default
        </option>

        <option value="high"
        <?= ($amount_filter=='high')?'selected':'' ?>>
            Highest Balance
        </option>

        <option value="low"
        <?= ($amount_filter=='low')?'selected':'' ?>>
            Lowest Balance
        </option>

    </select>
</div>
            <div class="col-lg-2 mt-2">
                <div class="d-flex gap-2">

                    <button type="submit" name="apply_filter" value="1" class="btn btn-filter">
                        Filter
                    </button>

                    <a href="student_list.php" class="btn btn-reset">
                        Reset
                    </a>

                </div>
            </div>

        </div>

    </div>

</form>



<div class="mb-4"></div>

