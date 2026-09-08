<?php

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: registrar_directory.php");
    exit();
}

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    header("Location: registrar_directory.php");
    exit();
}

$stmt = $conn->prepare("
    DELETE FROM registrar_directory
    WHERE id = ?
");

$stmt->bind_param("i", $id);

$stmt->execute();

$stmt->close();

header("Location: registrar_directory.php");
exit();

?>