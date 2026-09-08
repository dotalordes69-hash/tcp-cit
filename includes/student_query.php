<?php

/*
|--------------------------------------------------------------------------
| DASHBOARD COUNTS
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| TOTAL STUDENTS
|--------------------------------------------------------------------------
*/

$total_students = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM students
"))['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| OFFICIAL / ENROLLED STUDENTS
|--------------------------------------------------------------------------
*/

$total_official_students = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM students
    WHERE EXISTS(
        SELECT 1
        FROM student_accounts sa
        INNER JOIN payment_history ph
            ON ph.student_id = sa.student_id
        WHERE sa.student_id = students.student_id
        AND sa.registration_fee = 1
        AND ph.particular = 'Registration Fee'
        AND ph.gcash_status = 'verified'
    )
"))['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| UNOFFICIAL / UNENROLLED STUDENTS
|--------------------------------------------------------------------------
*/

$total_unofficial_students = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM students
    WHERE NOT EXISTS(
        SELECT 1
        FROM student_accounts sa
        INNER JOIN payment_history ph
            ON ph.student_id = sa.student_id
        WHERE sa.student_id = students.student_id
        AND sa.registration_fee = 1
        AND ph.particular = 'Registration Fee'
        AND ph.gcash_status = 'verified'
    )
"))['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/

$limit = 100;

$page = isset($_GET['page'])
    ? max(1, (int)$_GET['page'])
    : 1;


/*
|--------------------------------------------------------------------------
| FILTER VALUES
|--------------------------------------------------------------------------
*/

$search             = trim($_GET['search'] ?? '');
$semester_filter    = trim($_GET['semester_filter'] ?? '');
$school_year_filter = trim($_GET['school_year_filter'] ?? '');
$section_filter     = trim($_GET['section_filter'] ?? '');
$status_filter      = trim($_GET['status_filter'] ?? '');
$card_filter        = trim($_GET['card_filter'] ?? '');


/*
|--------------------------------------------------------------------------
| BUILD BASE FILTER
|--------------------------------------------------------------------------
*/

$where = "WHERE 1=1";


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

if($search !== ''){

    $search = mysqli_real_escape_string(
        $conn,
        $search
    );

    $where .= "
        AND (
            student_id LIKE '%$search%'
            OR fullname LIKE '%$search%'
        )
    ";
}


/*
|--------------------------------------------------------------------------
| SEMESTER FILTER
|--------------------------------------------------------------------------
*/

if($semester_filter !== ''){

    $semester_filter = mysqli_real_escape_string(
        $conn,
        $semester_filter
    );

    $where .= "
        AND semester = '$semester_filter'
    ";
}


/*
|--------------------------------------------------------------------------
| SCHOOL YEAR FILTER
|--------------------------------------------------------------------------
*/

if($school_year_filter !== ''){

    $school_year_filter = mysqli_real_escape_string(
        $conn,
        $school_year_filter
    );

    $where .= "
        AND school_year = '$school_year_filter'
    ";
}


/*
|--------------------------------------------------------------------------
| SECTION FILTER
|--------------------------------------------------------------------------
*/

if($section_filter !== ''){

    $section_filter = mysqli_real_escape_string(
        $conn,
        $section_filter
    );

    $where .= "
        AND section = '$section_filter'
    ";
}


/*
|--------------------------------------------------------------------------
| CARD FILTER
|--------------------------------------------------------------------------
*/

if($card_filter !== ''){

    $parts = explode(':', $card_filter, 2);

    $type  = $parts[0] ?? '';
    $value = $parts[1] ?? '';


    /*
    |--------------------------------------------------------------------------
    | OFFICIAL
    |--------------------------------------------------------------------------
    */

    if(
        $type === 'status' &&
        $value === 'Official'
    ){

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
    }


    /*
    |--------------------------------------------------------------------------
    | UNOFFICIAL
    |--------------------------------------------------------------------------
    */

    elseif(
        $type === 'status' &&
        $value === 'Unofficial'
    ){

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


    /*
    |--------------------------------------------------------------------------
    | FULLY PAID
    |--------------------------------------------------------------------------
    */

    elseif(
        $type === 'payment' &&
        $value === 'Paid'
    ){

        $where .= "
            AND EXISTS(
                SELECT 1
                FROM student_accounts sa
                WHERE sa.student_id = students.student_id
                AND (
                    COALESCE(sa.total_amount, 0) <= 0
                    OR COALESCE(sa.balance, 0) <= 0
                )
            )
        ";
    }


    /*
    |--------------------------------------------------------------------------
    | PARTIAL PAYMENT
    |--------------------------------------------------------------------------
    */

    elseif(
        $type === 'payment' &&
        $value === 'Partial'
    ){

        $where .= "
            AND EXISTS(
                SELECT 1
                FROM student_accounts sa
                WHERE sa.student_id = students.student_id
                AND COALESCE(sa.total_amount, 0) > 0
                AND COALESCE(sa.total_paid, 0) > 0
                AND COALESCE(sa.balance, 0) > 0
            )
        ";
    }


    /*
    |--------------------------------------------------------------------------
    | UNPAID
    |--------------------------------------------------------------------------
    */

    elseif(
        $type === 'payment' &&
        $value === 'Unpaid'
    ){

        $where .= "
            AND EXISTS(
                SELECT 1
                FROM student_accounts sa
                WHERE sa.student_id = students.student_id
                AND COALESCE(sa.total_amount, 0) > 0
                AND COALESCE(sa.total_paid, 0) <= 0
                AND COALESCE(sa.balance, 0) > 0
            )
        ";
    }


    /*
    |--------------------------------------------------------------------------
    | COMPLETE CREDENTIALS
    |--------------------------------------------------------------------------
    */

    elseif(
        $type === 'credentials' &&
        $value === 'Complete'
    ){

        $where .= "
            AND status_credentials = 1
        ";
    }


    /*
    |--------------------------------------------------------------------------
    | INCOMPLETE CREDENTIALS
    |--------------------------------------------------------------------------
    */

    elseif(
        $type === 'credentials' &&
        $value === 'Incomplete'
    ){

        $where .= "
            AND (
                status_credentials IS NULL
                OR status_credentials = 0
            )
        ";
    }


    /*
    |--------------------------------------------------------------------------
    | LET PASSED
    |--------------------------------------------------------------------------
    |
    | let_exam = 1
    | → Student took the LET exam
    |
    | let_status = 1
    | → Student PASSED the LET
    |
    | IMPORTANT:
    | PASSED uses let_status ONLY.
    |--------------------------------------------------------------------------
    */

    elseif(
        $type === 'let' &&
        $value === 'PASSED'
    ){

        $where .= "
            AND COALESCE(let_status, 0) = 1
        ";
    }
}


/*
|--------------------------------------------------------------------------
| STATUS FILTER
|--------------------------------------------------------------------------
|
| Supports existing status_filter if used separately.
|--------------------------------------------------------------------------
*/

if($status_filter !== ''){

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
}


/*
|--------------------------------------------------------------------------
| TOTAL FILTERED RECORDS
|--------------------------------------------------------------------------
*/

$count_sql = "
    SELECT COUNT(*) AS total
    FROM students
    $where
";

$count_result = mysqli_query(
    $conn,
    $count_sql
);

$count_row = mysqli_fetch_assoc($count_result);

$total_rows = (int)($count_row['total'] ?? 0);


/*
|--------------------------------------------------------------------------
| TOTAL PAGES
|--------------------------------------------------------------------------
*/

$total_pages = max(
    1,
    (int)ceil($total_rows / $limit)
);


/*
|--------------------------------------------------------------------------
| PREVENT INVALID PAGE
|--------------------------------------------------------------------------
*/

if($page > $total_pages){

    $page = 1;
}


/*
|--------------------------------------------------------------------------
| OFFSET
|--------------------------------------------------------------------------
*/

$offset = ($page - 1) * $limit;


/*
|--------------------------------------------------------------------------
| SHOWING RANGE
|--------------------------------------------------------------------------
*/

if($total_rows > 0){

    $showing_start = $offset + 1;

    $showing_end = min(
        $offset + $limit,
        $total_rows
    );

}else{

    $showing_start = 0;
    $showing_end = 0;
}


/*
|--------------------------------------------------------------------------
| STUDENT LIST QUERY
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT *
    FROM students
    $where
    ORDER BY fullname ASC
    LIMIT $offset, $limit
";

$result = mysqli_query(
    $conn,
    $sql
);

?>