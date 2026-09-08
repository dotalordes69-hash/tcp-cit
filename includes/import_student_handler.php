
<?php

include '../db.php';

$imported = 0;
$skipped = 0;

$total_amount = floatval($_POST['total_amount'] ?? 0);

/*
|--------------------------------------------------------------------------
| TUITION FEE LOGIC
|--------------------------------------------------------------------------
| If Tuition Fee is 0:
| - Total Amount = 0
| - Total Paid = 0
| - Balance = 0
| - Student is automatically considered Fully Paid/Cleared
|--------------------------------------------------------------------------
*/

if ($total_amount < 0) {
    $total_amount = 0;
}


if(isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0){

    $file = fopen($_FILES['csv_file']['tmp_name'], 'r');

    // Skip header row
    fgetcsv($file);

    while(($row = fgetcsv($file, 1000, ",")) !== FALSE){

        $student_id  = mysqli_real_escape_string($conn, trim($row[0]));
        $fullname    = strtoupper(mysqli_real_escape_string($conn, trim($row[1])));
        $email       = mysqli_real_escape_string($conn, trim($row[2]));
        $contact     = mysqli_real_escape_string($conn, trim($row[3]));
        $semester    = mysqli_real_escape_string($conn, trim($row[4]));
        $school_year = mysqli_real_escape_string($conn, trim($row[5]));
        $section     = mysqli_real_escape_string($conn, trim($row[6]));


        if(empty($student_id)){
            continue;
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK DUPLICATE STUDENT ID
        |--------------------------------------------------------------------------
        */

        $check = mysqli_query(
            $conn,
            "SELECT id
             FROM students
             WHERE student_id='$student_id'"
        );


        if(mysqli_num_rows($check) > 0){
            $skipped++;
            continue;
        }


        /*
        |--------------------------------------------------------------------------
        | INSERT STUDENT
        |--------------------------------------------------------------------------
        */

        $save = mysqli_query($conn,"
            INSERT INTO students(
                student_id,
                fullname,
                email_address,
                contact_no,
                semester,
                school_year,
                section
            )
            VALUES(
                '$student_id',
                '$fullname',
                '$email',
                '$contact',
                '$semester',
                '$school_year',
                '$section'
            )
        ");


        if($save){

            /*
            |--------------------------------------------------------------------------
            | ACCOUNT BALANCE
            |--------------------------------------------------------------------------
            */

            if($total_amount == 0){

                // No tuition required
                $total_account_amount = 0;
                $total_paid = 0;
                $balance = 0;

            } else {

                // Normal tuition account
                $total_account_amount = $total_amount;
                $total_paid = 0;
                $balance = $total_amount;
            }


            /*
            |--------------------------------------------------------------------------
            | INSERT STUDENT ACCOUNT
            |--------------------------------------------------------------------------
            */

            mysqli_query($conn,"
                INSERT INTO student_accounts(
                    student_id,
                    total_amount,
                    total_paid,
                    balance
                )
                VALUES(
                    '$student_id',
                    '$total_account_amount',
                    '$total_paid',
                    '$balance'
                )
            ");


            $imported++;
        }
    }


    fclose($file);
}

?>
<!DOCTYPE html>
<html>
<head>

    <script src="../js/sweetalert2.all.min.js"></script>

</head>

<body>

<script>

Swal.fire({

    icon: 'success',

    title: 'Import Completed Successfully',

    html: `
        <div style="padding:10px">

            <div style="
                font-size:16px;
                color:#495057;
                margin-bottom:15px;">

                Student records have been processed successfully.

            </div>


            <div style="
                background:#f8f9fa;
                border-radius:12px;
                padding:15px;
                border:1px solid #dee2e6;">

                <div style="
                    font-size:28px;
                    font-weight:700;
                    color:#198754;">

                    <?= $imported ?>

                </div>


                <div style="
                    font-size:13px;
                    color:#6c757d;
                    margin-bottom:12px;">

                    Students Imported

                </div>


                <hr>


                <div style="
                    font-size:22px;
                    font-weight:700;
                    color:#fd7e14;">

                    <?= $skipped ?>

                </div>


                <div style="
                    font-size:13px;
                    color:#6c757d;">

                    Duplicate Records Skipped

                </div>

            </div>

        </div>
    `,

    confirmButtonText: 'Continue',

    confirmButtonColor: '#8B0000',

    allowOutsideClick: false,

    allowEscapeKey: false,

    width: 550

}).then(() => {

    window.location.href = '../student_list.php';

});

</script>

</body>
</html>
