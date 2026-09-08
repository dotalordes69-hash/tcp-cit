<?php
include __DIR__ . '/db.php';
session_start();

if (!isset($_SESSION['fullname'])) {
    die("Unauthorized");
}

$student_id = trim($_POST['student_id'] ?? '');

if ($student_id == '') {
    die("Invalid student");
}

$user = $_SESSION['fullname'];

$items = $_POST['monitor_items'] ?? [];
$dates = $_POST['submitted_date'] ?? [];
$track = $_POST['tracking_number'] ?? [];
$month = $_POST['exam_month'] ?? [];
$year  = $_POST['exam_year'] ?? [];
$let_exam = isset($_POST['let_exam']) ? 1 : 0;
$let_status = in_array('Let Passers', $items) ? 1 : 0;
if (!$let_exam) {
    $let_status = 0;
}
function val($arr, $key)
{
    return $arr[$key] ?? null;
}

/* Ensure monitor row exists */
mysqli_query(
    $conn,
    "INSERT IGNORE INTO student_monitor (student_id)
     VALUES ('" . mysqli_real_escape_string($conn, $student_id) . "')"
);
/* SUPERADMIN CAN UNCHECK ONLY THE ITEMS PRESENT IN FORM */
if (isset($_SESSION['role']) && $_SESSION['role'] === 'superadmin') {

    $allItems = [
        'Admission',
        'Enrolled',
        'Assessment',
        'Release Documents',
        'TOR & Certificate Release',
        'Let Passers'
    ];

    foreach ($allItems as $monitorItem) {

        if (!in_array($monitorItem, $items)) {

            switch ($monitorItem) {

                case 'Admission':
                    mysqli_query($conn, "
                        UPDATE student_monitor SET
                            admission_status=NULL,
                            admission_date=NULL,
                            admission_by=NULL
                        WHERE student_id='$student_id'
                    ");
                break;

                case 'Enrolled':
                    mysqli_query($conn, "
                        UPDATE student_monitor SET
                            enrolled_status=NULL,
                            enrolled_date=NULL,
                            enrolled_by=NULL
                        WHERE student_id='$student_id'
                    ");
                break;

                case 'Assessment':
                    mysqli_query($conn, "
                        UPDATE student_monitor SET
                            assessment_status=NULL,
                            assessment_date=NULL,
                            assessment_by=NULL
                        WHERE student_id='$student_id'
                    ");
                break;

                case 'Release Documents':
                    mysqli_query($conn, "
                        UPDATE student_monitor SET
                            release_docs_status=NULL,
                            release_docs_date=NULL,
                            release_docs_by=NULL,
                            release_docs_tracking=NULL
                        WHERE student_id='$student_id'
                    ");
                break;

                case 'TOR & Certificate Release':
                    mysqli_query($conn, "
                        UPDATE student_monitor SET
                            tor_status=NULL,
                            tor_date=NULL,
                            tor_by=NULL,
                            tor_tracking=NULL
                        WHERE student_id='$student_id'
                    ");
                break;

                case 'Let Passers':
                    mysqli_query($conn, "
                        UPDATE student_monitor SET
                            let_status=NULL,
                            let_month=NULL,
                            let_year=NULL,
                            let_by=NULL
                        WHERE student_id='$student_id'
                    ");
                break;
            }
        }
    }
}
/* Process selected monitor items */
foreach ($items as $item) {

    $date = val($dates, $item);
    $tr   = val($track, $item);
    $m    = val($month, $item);
    $y    = val($year, $item);

    switch ($item) {

        case 'Admission':
            mysqli_query($conn, "
                UPDATE student_monitor SET
                    admission_status='Released',
                    admission_date=" . ($date ? "'$date'" : "NULL") . ",
                    admission_by='$user'
                WHERE student_id='$student_id'
            ");
        break;

        case 'Enrolled':
            mysqli_query($conn, "
                UPDATE student_monitor SET
                    enrolled_status='Released',
                    enrolled_date=" . ($date ? "'$date'" : "NULL") . ",
                    enrolled_by='$user'
                WHERE student_id='$student_id'
            ");
        break;

        case 'Assessment':
            mysqli_query($conn, "
                UPDATE student_monitor SET
                    assessment_status='Released',
                    assessment_date=" . ($date ? "'$date'" : "NULL") . ",
                    assessment_by='$user'
                WHERE student_id='$student_id'
            ");
        break;

        case 'Release Documents':
            mysqli_query($conn, "
                UPDATE student_monitor SET
                    release_docs_status='Released',
                    release_docs_date=" . ($date ? "'$date'" : "NULL") . ",
                    release_docs_by='$user',
                    release_docs_tracking=" . ($tr ? "'$tr'" : "NULL") . "
                WHERE student_id='$student_id'
            ");
        break;

        case 'TOR & Certificate Release':
            mysqli_query($conn, "
                UPDATE student_monitor SET
                    tor_status='Released',
                    tor_date=" . ($date ? "'$date'" : "NULL") . ",
                    tor_by='$user',
                    tor_tracking=" . ($tr ? "'$tr'" : "NULL") . "
                WHERE student_id='$student_id'
            ");
        break;

case 'Let Passers':
    mysqli_query($conn, "
        UPDATE student_monitor SET
            let_status='Passer',
            let_month=" . ($m ? "'$m'" : "NULL") . ",
            let_year=" . ($y ? "'$y'" : "NULL") . ",
            let_by='$user'
        WHERE student_id='$student_id'
    ");
break;
    }
}

/* SAVE LET EXAM STATUS */
$stmt = $conn->prepare("
    UPDATE students
    SET
        let_exam = ?,
        let_status = ?
    WHERE student_id = ?
");

$stmt->bind_param(
    "iis",
    $let_exam,
    $let_status,
    $student_id
);

$stmt->execute();
$stmt->close();

/* SUCCESS */
$_SESSION['monitor_success'] = "Student monitor updated successfully.";

header('Content-Type: application/json');

echo json_encode([
    'success' => true
]);

exit;