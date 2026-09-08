<?php
include __DIR__ . '/db.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$batch_filter = isset($_GET['batch_filter']) ? mysqli_real_escape_string($conn, $_GET['batch_filter']) : '';

$where = "WHERE 1=1";
if($search) $where .= " AND fullname LIKE '%$search%'";
if($batch_filter) $where .= " AND batch = '$batch_filter'";

$query = "SELECT * FROM students $where ORDER BY id DESC";
$result = mysqli_query($conn, $query);

$html = '';
$total = mysqli_num_rows($result);

if($total > 0)
    while($row = mysqli_fetch_assoc($result)){
        $html .= "<tr>
                    <td>".htmlspecialchars($row['student_id'])."</td>
                    <td>".htmlspecialchars($row['fullname'])."</td>
                    <td>".htmlspecialchars($row['contact_no'])."</td>
                    <td>".htmlspecialchars($row['email_address'])."</td>
                    <td>".htmlspecialchars($row['batch'])."</td>
                    <td>
                        <a href='view_student.php?id=".$row['id']."' class='btn btn-info btn-sm'>View</a>
                        <a href='edit_student.php?id=".$row['id']."' class='btn btn-warning btn-sm'>Edit</a>
                        <a href='delete_student.php?id=".$row['id']."' class='btn btn-danger btn-sm' onclick=\"return confirm('Delete this student?')\">Delete</a>
                    </td>
                  </tr>";
} else {
    $html = "<tr><td colspan='6'>No students found.</td></tr>";
}

echo json_encode(['html'=>$html, 'total'=>$total]);


