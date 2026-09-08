<?php

session_start();
include 'db.php';

header('Content-Type: application/json');

$id = intval($_POST['id'] ?? 0);
$date_release = trim($_POST['date_release'] ?? '');

if ($id <= 0 || empty($date_release)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Update docs_history
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    UPDATE docs_history
    SET date_release = ?
    WHERE id = ?
");

$stmt->bind_param(
    "si",
    $date_release,
    $id
);

$success = $stmt->execute();

if (!$success) {

    echo json_encode([
        'success' => false,
        'message' => 'Failed to update release date.'
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Get student_id from docs_history
|--------------------------------------------------------------------------
*/
$get = $conn->prepare("
    SELECT student_id
    FROM docs_history
    WHERE id = ?
    LIMIT 1
");

$get->bind_param("i", $id);
$get->execute();

$result = $get->get_result();
$row = $result->fetch_assoc();

if ($row) {

    $student_id = $row['student_id'];
    $released_by = $_SESSION['fullname'] ?? 'System';

    /*
    |--------------------------------------------------------------------------
    | Update Student Monitor
    |--------------------------------------------------------------------------
    */
    $update = $conn->prepare("
        UPDATE student_monitor
        SET
            release_docs_status = 'Released',
            release_docs_date = ?,
            release_docs_by = ?
        WHERE student_id = ?
    ");

    $update->bind_param(
        "sss",
        $date_release,
        $released_by,
        $student_id
    );

    $update->execute();
}

/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/
echo json_encode([
    'success' => true,
    'message' => 'Release date updated successfully.'
]);

$stmt->close();
$get->close();

if (isset($update)) {
    $update->close();
}

$conn->close();