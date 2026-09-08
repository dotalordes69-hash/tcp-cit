<div class="table-responsive">

<table class="table table-bordered table-hover">

    <thead class="table-danger">

        <tr>
            <th>Date</th>
            <th>Student ID</th>
            <th>Fullname</th>
            <th>Semester</th>
            <th>School Year</th>
            <th>Type of Request</th>
            <th>OR Number</th>
            <th>GCash Ref</th>
            <th>Mode of Delivery</th>
            <th>Tracking Number</th>
            <th>Total Amount</th>
            <th>Status</th>
        </tr>

    </thead>

    <tbody>

<?php while($row = mysqli_fetch_assoc($query)):

$current = array_search($row['status'], $order);

$enableOR = $isSuperAdmin || in_array(
    $row['status'],
    [
        "Verified Payment",
        "Processing",
        "For Signature",
        "For Shipment",
        "For Pick Up",
        "Released"
    ]
);

?>

<tr>

<td>
<?= !empty($row['created_at'])
    ? date("M d, Y h:i A", strtotime($row['created_at']))
    : '-' ?>
</td>

<td><?= $row['student_id'] ?></td>

<td><?= $row['fullname'] ?></td>

<td><?= $row['semester'] ?></td>

<td><?= $row['school_year'] ?></td>

<td>

<?php

$types = explode(", ", $row['request_type']);
$sets  = json_decode($row['request_sets'], true);

$map = [
    "TOR for PRC"                       => "tor_prc",
    "TOR for Employment"                => "tor_employment",
    "Honorable Dismissal"               => "honorable",
    "Certificate of Completed Units"    => "completed_units",
    "Certificate of GWA"                => "gwa",
    "Certificate of GMC"                => "gmc",
    "Authenticated Copy of TOR"         => "auth_tor",
    "Authenticated Copy of Certificate" => "auth_certificate"
];

foreach($types as $type){

    $key = $map[$type] ?? "";

    echo '<div class="d-flex justify-content-between border-bottom py-1">';
    echo '<span>'.$type.'</span>';

    if($key && !empty($sets[$key])){
        echo '<span class="badge bg-primary">'.$sets[$key].' Set(s)</span>';
    }

    echo '</div>';

}

?>

</td>

<td style="min-width:150px">

<input
    type="text"
    class="form-control form-control-sm or-number"
    data-id="<?= $row['id'] ?>"
    value="<?= htmlspecialchars($row['or_number']) ?>"
    placeholder="Enter OR Number"
    <?= !$enableOR ? 'disabled' : '' ?>>

</td>

<td>

<?php if(empty($row['gcash_verified'])){ ?>

<button
    class="btn btn-warning btn-sm verify-gcash"
    data-id="<?= $row['id'] ?>"
    <?= ($row['status']!="Verified Payment" && !$isSuperAdmin) ? 'disabled' : '' ?>>
    Pending
</button>

<?php }else{ ?>

<span class="badge bg-success">Verified</span>

<?php } ?>

<?php if(!empty($row['gcash_ref'])){ ?>

<div class="small text-muted mt-1">
    <?= htmlspecialchars($row['gcash_ref']) ?>
</div>

<?php } ?>

</td>

<td><?= $row['delivery_mode'] ?></td>

<td><?= $row['tracking_number'] ?></td>

<td>₱<?= number_format($row['total_amount'],2) ?></td>

<td>

<?php

if($row['status']=="Released"){

?>

<div class="text-center">

    <span class="badge bg-success px-3 py-2">
        Released
    </span>

    <?php if(!empty($row['released_date'])){ ?>

    <div class="small text-muted mt-2">
        <?= date("M d, Y h:i A", strtotime($row['released_date'])) ?>
    </div>

    <?php } ?>

</div>

<?php

}else{

   if($isSuperAdmin){

    $allowedStatus = $order;

}else{

    switch($row['status']){

        case "Pending":
            $allowedStatus = ["Pending","Verified Payment"];
            break;

        case "Verified Payment":
            $allowedStatus = ["Verified Payment","Processing"];
            break;

        case "Processing":
            $allowedStatus = ["Processing","For Signature"];
            break;

        case "For Signature":

            if($row['delivery_mode']=="Shipment"){
                $allowedStatus = ["For Signature","For Shipment"];
            }else{
                $allowedStatus = ["For Signature","For Pick Up"];
            }

            break;

        case "For Shipment":
            $allowedStatus = ["For Shipment","Released"];
            break;

        case "For Pick Up":
            $allowedStatus = ["For Pick Up","Released"];
            break;

        case "Released":
            $allowedStatus = ["Released"];
            break;
    }

}

    }

?>

<select
    class="form-select form-select-sm status-select"
    data-id="<?= $row['id'] ?>">

<?php foreach($allowedStatus as $status){ ?>

<option
    value="<?= $status ?>"
    <?= $status==$row['status'] ? 'selected' : '' ?>>

    <?= $status ?>

</option>

<?php } ?>

</select>

<?php  ?>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>