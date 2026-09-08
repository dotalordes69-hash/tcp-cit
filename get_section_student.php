<?php
include 'db.php';

$section = $_GET['section'] ?? '';

$stmt = $conn->prepare("
    SELECT email_address
    FROM students
    WHERE section = ?
    AND email_address <> ''
    ORDER BY email_address
");

$stmt->bind_param("s", $section);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){
    echo '
    <div class="text-center text-muted py-4">
        No email recipients found.
    </div>';
    exit;
}

echo '<div class="list-group list-group-flush">';

while($row = $result->fetch_assoc()){

    echo '
    <div class="list-group-item">
        📧 '.htmlspecialchars($row['email_address']).'
    </div>';
}

echo '</div>';
?>