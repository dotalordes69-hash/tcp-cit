

   <?php

$is_superadmin = false;

if (isset($_SESSION['role']) && $_SESSION['role'] === 'superadmin') {
    $is_superadmin = true;
}

$credentials = [
    'tor_informative_copy' => 'TOR-Informative Copy',
    'gmc' => 'GMC',
    'ctc_request' => 'CTC Request',
    'account_card' => 'Account Card',
    'ctc_tor_granted' => 'CTC-TOR Granted',
    'psa_birth_marriage_certificate' => 'PSA Birth/Marriage Certificate',
    'photo_2x2_hardcopy' => '2x2 Photo (Hardcopy)',
    'photo_2x2_softcopy' => '2x2 Photo (Softcopy)'
];

$check = mysqli_query($conn, "
    SELECT * FROM student_credentials
    WHERE student_id = '$student_id'
");

$cred_status = mysqli_fetch_assoc($check) ?: [];

?>
 <link rel="stylesheet" href="assets/css/credentials.css">

<div class="card credentials-card mb-4">


    <div class="credentials-header">

        <h5>
            <i class="bi bi-folder-check"></i>
            Student Credentials
        </h5>

        <small>
            Monitor submitted requirements and documents
        </small>

    </div>



    <div class="card-body bg-light">


        <div class="card border-0 shadow-sm mb-4">


        </div>




        <form id="credentialsForm">

        <div class="d-flex justify-content-between align-items-center mb-3">




    <div class="d-flex gap-2">

        <button type="button"
                id="checkAllBtn"
                class="btn btn-checkall btn-sm">

            <i class="bi bi-check2-square"></i>
            Check All

        </button>


        <button type="button"
                id="uncheckAllBtn"
                class="btn btn-uncheckall btn-sm">

            <i class="bi bi-square"></i>
            Uncheck All

        </button>

    </div>

</div>


            <input type="hidden"
                   name="student_id"
                   value="<?= htmlspecialchars($student_id); ?>">


            <input type="hidden"
                   name="checked_by"
                   value="<?= htmlspecialchars($_SESSION['fullname']); ?>">



            <div class="row g-3">


                <?php foreach($credentials as $column=>$label): ?>


                    <?php

                    $is_submitted =
                        !empty($cred_status[$column])
                        && $cred_status[$column] == 1;


                    $checkbox_disabled =
                        (!$is_superadmin && $is_submitted)
                        ? 'disabled'
                        : '';


                    $date_value =
                        $cred_status[$column.'_date'] ?? '';


                    $checked_by_name =
                        $cred_status[$column.'_by'] ?? '';

                    ?>



                    <div class="col-md-6">


                        <div class="credential-item p-3 shadow-sm">


                            <div class="d-flex justify-content-between align-items-start">



                                <div class="d-flex gap-3">


                                    <input type="checkbox"

                                           class="cred-checkbox"

                                           name="credentials[]"

                                           value="<?= $column ?>"

                                           <?= $is_submitted ? 'checked' : '' ?>

                                           <?= $checkbox_disabled ?>>





                                    <div>


                                        <div class="credential-title">

                                            <?= htmlspecialchars($label) ?>

                                        </div>




                                        <?php if($is_submitted): ?>



                                            <div class="info-box">


                                                <div>

                                                    📅 Submitted:

                                                    <strong>

                                                        <?= !empty($date_value)
                                                            ? date("M d, Y", strtotime($date_value))
                                                            : ''; ?>

                                                    </strong>

                                                </div>




                                                <div>

                                                    👤 Checked by:

                                                    <strong>

                                                        <?= htmlspecialchars($checked_by_name); ?>

                                                    </strong>

                                                </div>


                                            </div>




                                        <?php else: ?>



                                            <div class="date-picker-wrap mt-3">


                                                <label>
                                                    Submission Date
                                                </label>



                                                <input type="date"

                                                       name="submitted_date[<?= $column ?>]"

                                                       class="form-control form-control-sm"

                                                       value="<?= date('Y-m-d'); ?>">



                                            </div>



                                        <?php endif; ?>



                                    </div>



                                </div>




                                <div>


                                    <?php if($is_submitted): ?>


                                        <span class="status-submitted">
                                            Submitted
                                        </span>


                                    <?php else: ?>


                                        <span class="status-missing">
                                            Not Yet Submitted
                                        </span>


                                    <?php endif; ?>


                                </div>



                            </div>


                        </div>


                    </div>



                <?php endforeach; ?>


            </div>





            <div class="mt-4">


                <button type="submit"
                        class="btn btn-update">


                    <i class="bi bi-save"></i>

                    Update Credentials


                </button>


            </div>



        </form>



    </div>


</div>
<script>
document.getElementById('credentialsForm').addEventListener('submit', function(e){

    e.preventDefault();

    let formData = new FormData(this);

    fetch('update_credentials.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {

        if(data.success){

    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Credentials updated successfully',
        timer: 1500,
        showConfirmButton: false
    }).then(() => {

        window.location.reload();

    });

}

    })
    .catch(error => {

        console.error(error);

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Server error.'
        });

    });

});

document.getElementById('checkAllBtn')
.addEventListener('click', function(){

    document.querySelectorAll('.cred-checkbox')
    .forEach(function(check){

        if(!check.disabled){

            check.checked = true;

        }

    });

});


document.getElementById('uncheckAllBtn')
.addEventListener('click', function(){

    document.querySelectorAll('.cred-checkbox')
    .forEach(function(check){

        if(!check.disabled){

            check.checked = false;

        }

    });

});

</script>