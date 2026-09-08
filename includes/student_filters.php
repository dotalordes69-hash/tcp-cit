<?php

/* =========================================================
   PAGINATION
========================================================= */

$limit = 100;

$page = isset($_GET['page'])
    ? max(1, (int)$_GET['page'])
    : 1;


/* =========================================================
   GET FILTER VALUES
========================================================= */

$search            = trim($_GET['search'] ?? '');
$semester_filter   = trim($_GET['semester_filter'] ?? '');
$section_filter    = trim($_GET['section_filter'] ?? '');
$school_year_filter = trim($_GET['school_year_filter'] ?? '');
$status_filter     = $_GET['status_filter'] ?? '';
$payment_filter    = $_GET['payment_filter'] ?? '';
$card_filter       = $_GET['card_filter'] ?? '';
$amount_filter     = $_GET['amount_filter'] ?? '';


/* =========================================================
   DROPDOWN OPTIONS
========================================================= */

/* SEMESTER */
$semester_options = [];

$sem_q = mysqli_query($conn, "
    SELECT DISTINCT semester_name
    FROM semester_sections
    WHERE semester_name <> ''
    ORDER BY semester_name ASC
");

while($row = mysqli_fetch_assoc($sem_q)){
    $semester_options[] = $row['semester_name'];
}


/* SCHOOL YEAR */
$school_year_options = [];

$sy_q = mysqli_query($conn, "
    SELECT DISTINCT school_year
    FROM semester_sections
    WHERE school_year <> ''
    ORDER BY school_year DESC
");

while($row = mysqli_fetch_assoc($sy_q)){
    $school_year_options[] = $row['school_year'];
}


/* SECTION */
$section_options = [];

$sec_q = mysqli_query($conn, "
    SELECT DISTINCT section_name
    FROM semester_sections
    WHERE section_name <> ''
    ORDER BY section_name ASC
");

while($row = mysqli_fetch_assoc($sec_q)){
    $section_options[] = $row['section_name'];
}


/* =========================================================
   TOTAL STUDENTS
========================================================= */

$total_students = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM students
    ")
)['total'];


/* =========================================================
   BUILD FILTER QUERY
========================================================= */

$where = "WHERE 1=1";


/* =========================================================
   STATUS FILTER
========================================================= */

if($status_filter === 'official'){

    $where .= "
        AND EXISTS(
            SELECT 1
            FROM student_accounts sa
            INNER JOIN payment_history ph
                ON ph.student_id = sa.student_id
            WHERE sa.student_id = students.student_id
            AND sa.registration_fee = 1
            AND ph.particular = 'Registration Fee'
            AND ph.gcash_status = 'verified'
        )
    ";

}elseif($status_filter === 'unofficial'){

    $where .= "
        AND NOT EXISTS(
            SELECT 1
            FROM student_accounts sa
            INNER JOIN payment_history ph
                ON ph.student_id = sa.student_id
            WHERE sa.student_id = students.student_id
            AND sa.registration_fee = 1
            AND ph.particular = 'Registration Fee'
            AND ph.gcash_status = 'verified'
        )
    ";
}


/* =========================================================
   LET PASSED FILTER
========================================================= */

/*
   IMPORTANT:

   let_exam   = 1
   → Student took the LET exam

   let_status = 1
   → Student PASSED the LET

   Therefore, PASSED filter must use let_status only.
*/

if($card_filter === 'let:PASSED'){

    $where .= "
        AND students.let_status = 1
    ";
}


/* =========================================================
   SEARCH
========================================================= */

if($search !== ''){

    $search = mysqli_real_escape_string(
        $conn,
        $search
    );

    $where .= "
        AND students.student_id = '$search'
    ";
}


/* =========================================================
   SEMESTER FILTER
========================================================= */

if($semester_filter !== ''){

    $semester_filter = mysqli_real_escape_string(
        $conn,
        $semester_filter
    );

    $where .= "
        AND students.semester = '$semester_filter'
    ";
}


/* =========================================================
   SECTION FILTER
========================================================= */

if($section_filter !== ''){

    $section_filter = mysqli_real_escape_string(
        $conn,
        $section_filter
    );

    $where .= "
        AND students.section = '$section_filter'
    ";
}


/* =========================================================
   SCHOOL YEAR FILTER
========================================================= */

if($school_year_filter !== ''){

    $school_year_filter = mysqli_real_escape_string(
        $conn,
        $school_year_filter
    );

    $where .= "
        AND students.school_year = '$school_year_filter'
    ";
}


/* =========================================================
   PAYMENT FILTER
========================================================= */

if($payment_filter === 'paid'){

    $where .= "
        AND EXISTS(
            SELECT 1
            FROM student_accounts sa
            WHERE sa.student_id = students.student_id
            AND IFNULL(sa.balance, 0) = 0
        )
    ";

}elseif($payment_filter === 'partial'){

    $where .= "
        AND EXISTS(
            SELECT 1
            FROM student_accounts sa
            WHERE sa.student_id = students.student_id
            AND IFNULL(sa.balance, 0) > 0
            AND IFNULL(sa.balance, 0) < IFNULL(sa.total_amount, 0)
        )
    ";

}elseif($payment_filter === 'unpaid'){

    $where .= "
        AND EXISTS(
            SELECT 1
            FROM student_accounts sa
            WHERE sa.student_id = students.student_id
            AND IFNULL(sa.balance, 0) > 0
        )
    ";
}


/* =========================================================
   AMOUNT SORT FILTER
========================================================= */

$order_by = "";

if($amount_filter === 'high'){

    $order_by = "
        ORDER BY (
            SELECT IFNULL(sa.balance, 0)
            FROM student_accounts sa
            WHERE sa.student_id = students.student_id
            LIMIT 1
        ) DESC
    ";

}elseif($amount_filter === 'low'){

    $order_by = "
        ORDER BY (
            SELECT IFNULL(sa.balance, 0)
            FROM student_accounts sa
            WHERE sa.student_id = students.student_id
            LIMIT 1
        ) ASC
    ";
}


/* =========================================================
   GET STUDENT LIST
========================================================= */

$result = mysqli_query($conn, "
    SELECT *
    FROM students
    $where
    $order_by
");

?>