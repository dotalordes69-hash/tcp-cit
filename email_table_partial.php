<?php
// email_table_partial.php
$email_limit = 5;
$email_page = isset($_GET['email_page']) ? intval($_GET['email_page']) : 1;
if ($email_page < 1) $email_page = 1;
$email_offset = ($email_page - 1) * $email_limit;

$email_query = "SELECT * FROM email_history WHERE student_id=$student_id ORDER BY sent_at DESC LIMIT $email_limit OFFSET $email_offset";
$res_email = mysqli_query($conn, $email_query);

$total_email_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM email_history WHERE student_id=$student_id");
$total_email_row = mysqli_fetch_assoc($total_email_res);
$total_email_records = $total_email_row['total'];
$total_email_pages = ceil($total_email_records / $email_limit);
?>
 <link rel="stylesheet" href="assets/css/email_history.css">
<table class="table table-striped table-bordered">
<thead>
<tr>
    <th>Date & Time</th>
    <th>Sent By</th>
    <th>Subject</th>
</tr>
</thead>
<tbody>
<?php if($res_email && mysqli_num_rows($res_email) > 0): ?>
    <?php while($row = mysqli_fetch_assoc($res_email)): ?>
    <tr>
        <td><?php echo date("M d, Y g:i A", strtotime($row['sent_at'])); ?></td>
        <td><?php echo htmlspecialchars($row['sent_by']); ?></td>
        <td><?php echo htmlspecialchars($row['subject']); ?></td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="3">No emails sent yet.</td></tr>
<?php endif; ?>
</tbody>
</table>

<!-- Pagination -->
<?php if($total_email_pages > 1): ?>
<ul class="pagination">
    <?php for($i=1; $i <= $total_email_pages; $i++): ?>
        <li class="page-item <?php echo ($i == $email_page) ? 'active' : ''; ?>">
            <a href="#" class="page-link email-page-link" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
        </li>
    <?php endfor; ?>
</ul>
<?php endif; ?>