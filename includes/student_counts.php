<?php

/*
|--------------------------------------------------------------------------
| FILTER VALUES
|--------------------------------------------------------------------------
*/

$semester_filter    = trim($_GET['semester_filter'] ?? '');
$school_year_filter = trim($_GET['school_year_filter'] ?? '');
$section_filter     = trim($_GET['section_filter'] ?? '');
$card_filter        = $_GET['card_filter'] ?? '';


/*
|--------------------------------------------------------------------------
| BASE STUDENT FILTER
|--------------------------------------------------------------------------
*/

$where = "WHERE 1=1";


if($semester_filter !== ''){

    $semester_filter = mysqli_real_escape_string(
        $conn,
        $semester_filter
    );

    $where .= " AND semester = '$semester_filter'";
}


if($school_year_filter !== ''){

    $school_year_filter = mysqli_real_escape_string(
        $conn,
        $school_year_filter
    );

    $where .= " AND school_year = '$school_year_filter'";
}


if($section_filter !== ''){

    $section_filter = mysqli_real_escape_string(
        $conn,
        $section_filter
    );

    $where .= " AND section = '$section_filter'";
}


/*
|--------------------------------------------------------------------------
| TOTAL STUDENTS
|--------------------------------------------------------------------------
*/

$result = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM students
    $where
");

$total_students =
    mysqli_fetch_assoc($result)['total'] ?? 0;

/*
|--------------------------------------------------------------------------
| LET CARD FILTER
|--------------------------------------------------------------------------
*/

$student_where = $where;

if($card_filter === 'let:PASSED'){

    $student_where .= "
        AND let_status = 1
    ";
}


/*
|--------------------------------------------------------------------------
| ENROLLED / OFFICIAL STUDENTS
|--------------------------------------------------------------------------
*/

$result = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM students
    $student_where
    AND status_type = 'Official'
");

$total_official_students =
    mysqli_fetch_assoc($result)['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| UNENROLLED / UNOFFICIAL STUDENTS
|--------------------------------------------------------------------------
*/

$result = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM students
    $student_where
    AND status_type = 'Unofficial'
");

$total_unofficial_students =
    mysqli_fetch_assoc($result)['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| LET PASSERS
|--------------------------------------------------------------------------
|
| IMPORTANT:
| let_exam = 1  -> Nag-take sa LET
| let_status = 1 -> PASSED
|
| Therefore, let_status = 1 ra ang gamiton
| para ma-count as LET Passer.
|--------------------------------------------------------------------------
*/

$result = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM students
    $where
    AND let_status = 1
");

$total_let_passers =
    mysqli_fetch_assoc($result)['total'] ?? 0;
/*
|--------------------------------------------------------------------------
| PAYMENT FILTER
|--------------------------------------------------------------------------
|
| PAID
| ------------------------------------------------
| total_amount = 0
| OR
| balance <= 0
|
| This means imported students with:
|
| total_amount = 0
| total_paid   = 0
| balance      = 0
|
| are considered PAID.
|
|--------------------------------------------------------------------------
*/

$account_filter = "
    WHERE
    (
        COALESCE(total_amount, 0) <= 0
        OR
        COALESCE(balance, 0) <= 0
    )
";


/*
|--------------------------------------------------------------------------
| APPLY STUDENT FILTERS TO PAYMENT COUNTS
|--------------------------------------------------------------------------
*/

$payment_join = "
    FROM student_accounts sa
    INNER JOIN students s
        ON s.student_id = sa.student_id
";


$payment_student_where = "WHERE 1=1";


if($semester_filter !== ''){

    $payment_student_where .= "
        AND s.semester = '$semester_filter'
    ";
}


if($school_year_filter !== ''){

    $payment_student_where .= "
        AND s.school_year = '$school_year_filter'
    ";
}


if($section_filter !== ''){

    $payment_student_where .= "
        AND s.section = '$section_filter'
    ";
}


/*
|--------------------------------------------------------------------------
| FULLY PAID STUDENTS
|--------------------------------------------------------------------------
*/

$paid_result = mysqli_query($conn, "
    SELECT COUNT(*) AS total

    $payment_join

    $payment_student_where

    AND (
        COALESCE(sa.total_amount, 0) <= 0
        OR
        COALESCE(sa.balance, 0) <= 0
    )
");

$total_paid_students =
    mysqli_fetch_assoc($paid_result)['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| PARTIAL PAYMENT STUDENTS
|--------------------------------------------------------------------------
|
| Has already paid something,
| but still has remaining balance.
|--------------------------------------------------------------------------
*/

$partial_result = mysqli_query($conn, "
    SELECT COUNT(*) AS total

    $payment_join

    $payment_student_where

    AND COALESCE(sa.total_amount, 0) > 0
    AND COALESCE(sa.total_paid, 0) > 0
    AND COALESCE(sa.balance, 0) > 0
");

$total_partial_students =
    mysqli_fetch_assoc($partial_result)['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| UNPAID STUDENTS
|--------------------------------------------------------------------------
|
| Important:
|
| total_amount MUST be greater than 0.
|
| Therefore imported students with:
|
| total_amount = 0
| total_paid   = 0
| balance      = 0
|
| WILL NOT be counted as unpaid.
|--------------------------------------------------------------------------
*/

$unpaid_result = mysqli_query($conn, "
    SELECT COUNT(*) AS total

    $payment_join

    $payment_student_where

    AND COALESCE(sa.total_amount, 0) > 0
    AND COALESCE(sa.total_paid, 0) <= 0
    AND COALESCE(sa.balance, 0) > 0
");

$total_unpaid_students =
    mysqli_fetch_assoc($unpaid_result)['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| COMPLETE CREDENTIALS
|--------------------------------------------------------------------------
*/

$complete_query = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM students
    $where
    AND status_credentials = 1
");

$complete_row =
    mysqli_fetch_assoc($complete_query);

$total_complete_students =
    $complete_row['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| INCOMPLETE CREDENTIALS
|--------------------------------------------------------------------------
*/

$incomplete_query = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM students
    $where
    AND (
        status_credentials IS NULL
        OR status_credentials = 0
    )
");

$incomplete_row =
    mysqli_fetch_assoc($incomplete_query);

$total_incomplete_students =
    $incomplete_row['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| ALL CREDENTIALS
|--------------------------------------------------------------------------
*/

$all_credentials = [

    'TOR-Informative Copy',

    'CTC-TOR Granted',

    'GMC',

    'CTC Request',

    'PSA Birth/Marriage Certificate',

    '2x2 Recent Photo (Hardcopy)',

    '2x2 Recent Photo (Softcopy)',

    'Account Card',

];

?>