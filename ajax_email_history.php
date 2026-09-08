<?php

session_start();
include 'db.php';

$student_id = $_GET['student_id'] ?? '';
$student_pk = intval($_GET['student_pk'] ?? 0);


$email_limit = 10;
$email_page = isset($_GET['email_page']) ? max(1, intval($_GET['email_page'])) : 1;
$email_offset = ($email_page - 1) * $email_limit;

$stmt = $conn->prepare("
    SELECT *
    FROM email_history
    WHERE student_id = ?
    ORDER BY sent_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("sii", $student_id, $email_limit, $email_offset);
$stmt->execute();

$res_email = $stmt->get_result();
$count_stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM email_history
    WHERE student_id = ?
");
$count_stmt->bind_param("s", $student_id);
$count_stmt->execute();

$count_result = $count_stmt->get_result()->fetch_assoc();

$total_email_records = $count_result['total'] ?? 0;
$count_stmt->close();

$email_limit = 10;
$email_page = isset($_GET['email_page']) ? max(1, intval($_GET['email_page'])) : 1;

$total_email_pages = ceil($total_email_records / $email_limit);

$start_record = ($total_email_records > 0)
    ? (($email_page - 1) * $email_limit) + 1
    : 0;

$end_record = min(
    $email_page * $email_limit,
    $total_email_records
);
?>
        
        
        <div class="card shadow-sm mb-4" id="email-history">
            <div class="card-header bg-maroon text-black">
                <h5 class="mb-0">Email History</h5>
            </div>
            <div class="card-body">
                <?php if ($res_email && mysqli_num_rows($res_email) > 0): ?>
                    <p class="text-muted small">
                        Showing <?php echo $start_record; ?> to <?php echo $end_record; ?> of <?php echo $total_email_records; ?> emails
                    </p>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Sent By</th>
                                    <th>Subject</th>
                                    <th width="120">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($res_email)): ?>
                                    <tr>
                                        <td><?php echo date("M d, Y g:i A", strtotime($row['sent_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['sent_by']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($row['subject']); ?>
                                        </td>

<td class="text-center">
    <button
        type="button"
        class="btn btn-outline-primary btn-sm view-message-btn"
        data-subject="<?= htmlspecialchars($row['subject'], ENT_QUOTES); ?>"
        data-message="<?= htmlspecialchars($row['message'] ?? '', ENT_QUOTES); ?>">
        <i class="bi bi-eye"></i> View
    </button>
</td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_email_pages > 1): ?>
                        <nav aria-label="Email history pagination">
                            <ul class="pagination">
                                <?php for ($i = 1; $i <= $total_email_pages; $i++): ?>
                                  <li class="page-item <?php echo ($i == $email_page) ? 'active' : ''; ?>">
                                       <a class="page-link email-page-link"
   href="#"
   data-page="<?php echo $i; ?>">
    <?php echo $i; ?>
</a>
                                    
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted mt-2">No emails sent yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
