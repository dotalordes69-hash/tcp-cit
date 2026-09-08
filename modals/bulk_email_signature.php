<div class="card">

<div class="card-header">

Email Signature

</div>

<div class="card-body">

<select
    name="signature_id"
    class="form-select">

<option value="">

No Signature

</option>

<?php
$sigQuery=mysqli_query($conn,"
SELECT *
FROM email_signatures
ORDER BY signature_name
");

while($sig=mysqli_fetch_assoc($sigQuery)):
?>

<option value="<?=$sig['id']?>">

<?=htmlspecialchars($sig['signature_name'])?>

</option>

<?php endwhile; ?>

</select>

</div>

</div>