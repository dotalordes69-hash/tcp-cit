<link rel="stylesheet" href="assets/css/student_table.css">

<div class="student-table-card">

    <div class="student-table-header">
        Student List
    </div>

    <div class="table-responsive">

        <table class="table student-table align-middle">

            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Semester</th>
                    <th>School Year</th>
                    <th>Section</th>
                    <th>LET</th>
                    <th>Student Status</th>
                    <th>Credentials</th>
                    <th>Balance</th>
                    <th width="100">Action</th>
                </tr>
            </thead>

            <tbody id="studentTableBody">

            <?php

            if(mysqli_num_rows($result) > 0){

                while($row = mysqli_fetch_assoc($result)){

                    $student_id = mysqli_real_escape_string(
                        $conn,
                        $row['student_id']
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | CREDENTIALS
                    |--------------------------------------------------------------------------
                    */

                    $cred_res = mysqli_query($conn, "
                        SELECT *
                        FROM student_credentials
                        WHERE student_id='$student_id'
                    ");

                    $cred = mysqli_fetch_assoc($cred_res) ?: [];

                    $ctc_tor_granted =
                        !empty($cred['ctc_tor_granted']) &&
                        $cred['ctc_tor_granted'] == 1;

                    $ctc_request =
                        !empty($cred['ctc_request']) &&
                        $cred['ctc_request'] == 1;

                    $gmc =
                        !empty($cred['gmc']) &&
                        $cred['gmc'] == 1;

                    $psa =
                        !empty($cred['psa_birth_marriage_certificate']) &&
                        $cred['psa_birth_marriage_certificate'] == 1;


                    /*
                    |--------------------------------------------------------------------------
                    | CREDENTIAL STATUS
                    |--------------------------------------------------------------------------
                    */

                    if(
                        $ctc_tor_granted &&
                        $ctc_request &&
                        $gmc &&
                        $psa
                    ){

                        $credential_badge = "
                            <span class='badge-status badge-complete'>
                                Complete
                            </span>
                        ";

                    }elseif($ctc_tor_granted){

                        $credential_badge = "
                            <span class='badge-status'
                                  style='background:#cfe2ff;color:#084298;'>
                                Partial
                            </span>
                        ";

                    }else{

                        $credential_badge = "
                            <span class='badge-status badge-incomplete'>
                                Incomplete
                            </span>
                        ";
                    }




              /*
|--------------------------------------------------------------------------
| LET STATUS
|--------------------------------------------------------------------------
*/

$let_badge = '';

if(
    isset($row['let_status']) &&
    $row['let_status'] == 1
){

    $let_badge = "
        <span class='badge-status badge-complete'>
            PASSED
        </span>
    ";
}


                    /*
                    |--------------------------------------------------------------------------
                    | STUDENT ACCOUNT
                    |--------------------------------------------------------------------------
                    */

                    $account_res = mysqli_query($conn, "
                        SELECT
                            registration_fee,
                            total_amount,
                            total_paid,
                            balance
                        FROM student_accounts
                        WHERE student_id='$student_id'
                        LIMIT 1
                    ");

                    $account = mysqli_fetch_assoc($account_res) ?: [];


                    $registration_fee =
                        (int)($account['registration_fee'] ?? 0);

                    $total_amount =
                        (float)($account['total_amount'] ?? 0);

                    $total_paid =
                        (float)($account['total_paid'] ?? 0);

                    /*
                    |--------------------------------------------------------------------------
                    | BALANCE
                    |--------------------------------------------------------------------------
                    | Use database balance.
                    | If balance is NULL, calculate using total_amount - total_paid.
                    |--------------------------------------------------------------------------
                    */

                    if(
                        isset($account['balance']) &&
                        $account['balance'] !== null
                    ){

                        $balance = (float)$account['balance'];

                    }else{

                        $balance = $total_amount - $total_paid;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | PREVENT NEGATIVE BALANCE
                    |--------------------------------------------------------------------------
                    */

                    if($balance < 0){
                        $balance = 0;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | STUDENT STATUS
                    |--------------------------------------------------------------------------
                    */

                    if($registration_fee == 1){

                        $student_status_badge = "
                            <span class='badge-status badge-official'>
                                Enrolled
                            </span>
                        ";

                    }else{

                        $student_status_badge = "
                            <span class='badge-status badge-unofficial'>
                                Unenrolled
                            </span>
                        ";
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PAYMENT STATUS
                    |--------------------------------------------------------------------------
                    */

                    if($balance <= 0){

                        $payment_badge = "
                            <span class='text-success'>
                                Fully Paid
                            </span>
                        ";

                    }else{

                        $payment_badge = "
                            <span>
                                ₱".number_format($balance, 2)."
                            </span>
                        ";
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | OUTPUT ROW
                    |--------------------------------------------------------------------------
                    */

                    echo "
                    <tr>

                        <td>
                            {$row['student_id']}
                        </td>

                        <td class='student-name'>
                            {$row['fullname']}
                        </td>

                        <td>
                            {$row['semester']}
                        </td>

                        <td>
                            {$row['school_year']}
                        </td>

                        <td>
                            {$row['section']}
                        </td>

                        <td>
                            {$let_badge}
                        </td>

                        <td>
                            {$student_status_badge}
                        </td>

                        <td>
                            {$credential_badge}
                        </td>

                        <td>
                            {$payment_badge}
                        </td>

                        <td>
                            <a
                                href='view_student.php?id={$row['id']}'
                                class='btn btn-view btn-sm'
                            >
                                View
                            </a>
                        </td>

                    </tr>
                    ";
                }

            }else{

                echo "
                <tr>
                    <td colspan='10' class='text-center py-4'>
                        No students found
                    </td>
                </tr>
                ";
            }

            ?>

            </tbody>

        </table>

    </div>

</div>