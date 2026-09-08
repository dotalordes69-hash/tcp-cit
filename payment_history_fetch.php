<?php
include 'db.php';

$student_id = $_GET['student_id'];
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare("
    SELECT id, payment_date, or_number, gcash_ref,
           amount_paid, particular, received_by
    FROM payment_history
    WHERE student_id = ?
    ORDER BY payment_date DESC, id DESC
    LIMIT ? OFFSET ?
");

$stmt->bind_param("sii", $student_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$count = $offset + 1;

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$count}</td>
        <td>{$row['payment_date']}</td>
        <td>{$row['or_number']}</td>
        <td>{$row['gcash_ref']}</td>
        <td>₱{$row['amount_paid']}</td>
        <td>{$row['particular']}</td>
        <td>{$row['received_by']}</td>
    </tr>";
    $count++;
}
?>