
<?php
$statusCounts = [];
$countQuery = mysqli_query($conn, "SELECT status, COUNT(*) AS total FROM document_request_monitoring GROUP BY status");
while($countRow = mysqli_fetch_assoc($countQuery)){
    $statusCounts[$countRow['status']] = $countRow['total'];
}
?>
<form method="GET" class="row g-3 align-items-end mb-4">
    <div class="col-md-4">
        <label class="form-label fw-semibold">Student ID</label>
        <input type="text" name="student_id" class="form-control" placeholder="Search Student ID" value="<?= htmlspecialchars($_GET['student_id'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select">
            <option value="">All Status</option>
            <option value="Pending" <?= (($_GET['status'] ?? '') == 'Pending') ? 'selected' : '' ?>>
                Pending (<?= $statusCounts['Pending'] ?? 0 ?>)
            </option>
            <option value="Verified Payment" <?= (($_GET['status'] ?? '') == 'Verified Payment') ? 'selected' : '' ?>>
                Verified Payment (<?= $statusCounts['Verified Payment'] ?? 0 ?>)
            </option>
            <option value="Processing" <?= (($_GET['status'] ?? '') == 'Processing') ? 'selected' : '' ?>>
                Processing (<?= $statusCounts['Processing'] ?? 0 ?>)
            </option>
            <option value="For Signature" <?= (($_GET['status'] ?? '') == 'For Signature') ? 'selected' : '' ?>>
                For Signature (<?= $statusCounts['For Signature'] ?? 0 ?>)
            </option>
            <option value="For Shipment" <?= (($_GET['status'] ?? '') == 'For Shipment') ? 'selected' : '' ?>>
                For Shipment (<?= $statusCounts['For Shipment'] ?? 0 ?>)
            </option>
            <option value="For Pick-up" <?= (($_GET['status'] ?? '') == 'For Pick-up') ? 'selected' : '' ?>>
                For Pick-up (<?= $statusCounts['For Pick-up'] ?? 0 ?>)
            </option>
            <option value="Released" <?= (($_GET['status'] ?? '') == 'Released') ? 'selected' : '' ?>>
                Released (<?= $statusCounts['Released'] ?? 0 ?>)
            </option>
        </select>
    </div>
    <div class="col-md-auto">
        <button type="submit" class="btn btn-danger">
            <i class="bi bi-funnel"></i> Filter
        </button>
        <a href="view_document_transactions.php" class="btn btn-secondary">
            <i class="bi bi-arrow-clockwise"></i> Reset
        </a>
    </div>
</form>

