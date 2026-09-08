<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $year = intval($_POST['year']);

    // Check duplicate
    $check = $conn->prepare("
        SELECT id
        FROM let_year
        WHERE year = ?
    ");
    $check->bind_param("i", $year);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {

        $_SESSION['error'] = "Year already exists.";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO let_year (year)
            VALUES (?)
        ");

        $stmt->bind_param("i", $year);

        if ($stmt->execute()) {
            $_SESSION['success'] = "LET Year added successfully.";
        } else {
            $_SESSION['error'] = "Unable to save LET Year.";
        }

        $stmt->close();
    }

    $check->close();

    header("Location: student_list.php");
    exit();
}