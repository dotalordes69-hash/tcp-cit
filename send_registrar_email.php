<?php
session_start();

include 'db.php';

require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';
require __DIR__ . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$status = '';
$errorInfo = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!isset($_SESSION['fullname'])) {

        $status = "error";
        $errorInfo = "Session expired. Please login again.";

    } else {

        $fullname = $_SESSION['fullname'];

        $school_id = intval($_POST['school_id']);
        $subject   = trim($_POST['subject']);
        $message   = trim($_POST['message']);
        $signature_id = intval($_POST['signature_id']);

        // =========================
        // GET SCHOOL INFORMATION
        // =========================

        $qry = mysqli_query($conn,"
            SELECT *
            FROM registrar_directory
            WHERE id='$school_id'
            LIMIT 1
        ");

        if(mysqli_num_rows($qry)==0){

            $status="error";
            $errorInfo="School not found.";

        }else{

            $school=mysqli_fetch_assoc($qry);

            $recipientEmail=$school['school_email'];
            $schoolName=$school['school_name'];

            // =========================
            // GET SIGNATURE
            // =========================

            $signature="";

            if($signature_id>0){

                $sig=mysqli_query($conn,"
                    SELECT signature_content
                    FROM email_signatures
                    WHERE id='$signature_id'
                    LIMIT 1
                ");

                if(mysqli_num_rows($sig)>0){

                    $signature=mysqli_fetch_assoc($sig)['signature_content'];

                }

            }

            $emailBody='
                <div style="font-family:Arial,sans-serif;font-size:15px;line-height:1.8;color:#333;">
                    '.nl2br(htmlspecialchars($message)).'
                    <br><br>
                    '.$signature.'
                </div>
            ';

            $mail=new PHPMailer(true);

            try{

                $mail->isSMTP();
                $mail->Host='smtp.gmail.com';
                $mail->SMTPAuth=true;
                $mail->Username='cit.dwightsevilla1997@gmail.com';
                $mail->Password='gdkg lqfy oepx amlm';
                $mail->SMTPSecure=PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port=587;

                $mail->setFrom(
                    'cit.dwightsevilla1997@gmail.com',
                    'CIT Teacher Certificate Program'
                );

                $mail->addAddress(
                    $recipientEmail,
                    $schoolName
                );

                $mail->isHTML(true);
                $mail->Subject=$subject;
                $mail->Body=$emailBody;
                $mail->AltBody=$message;

                $mail->send();

                $status="success";

                // =========================
                // SAVE EMAIL HISTORY
                // =========================

                mysqli_query($conn,"
                    INSERT INTO registrar_email_history
                    (
                        school_id,
                        subject,
                        message,
                        signature_id,
                        sent_by,
                        sent_at
                    )
                    VALUES
                    (
                        '$school_id',
                        '".mysqli_real_escape_string($conn,$subject)."',
                        '".mysqli_real_escape_string($conn,$message)."',
                        '$signature_id',
                        '".mysqli_real_escape_string($conn,$fullname)."',
                        NOW()
                    )
                ");

            }catch(Exception $e){

                $status="error";
                $errorInfo=$mail->ErrorInfo;

            }

        }

    }

}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<title>Send Registrar Email</title>

<link rel="stylesheet" href="css/bootstrap.min.css">

<script src="js/bootstrap.bundle.min.js"></script>

<script src="js/sweetalert2.all.min.js"></script>

<style>

.cit-swal{
    border-radius:15px;
}

.cit-swal-btn{
    background:#800000!important;
}

</style>

</head>

<body>

<script>

document.addEventListener("DOMContentLoaded",function(){

let status="<?= $status ?>";
let error="<?= htmlspecialchars($errorInfo,ENT_QUOTES) ?>";

if(status=="success"){

Swal.fire({

icon:"success",

title:"Email Sent Successfully",

text:"The registrar email has been successfully delivered.",

confirmButtonText:"Continue",

customClass:{
popup:"cit-swal",
confirmButton:"cit-swal-btn"
}

}).then(()=>{

window.location="registrar_directory.php";

});

}else if(status=="error"){

Swal.fire({

icon:"error",

title:"Sending Failed",

html:error,

confirmButtonText:"Close",

customClass:{
popup:"cit-swal",
confirmButton:"cit-swal-btn"
}

}).then(()=>{

history.back();

});

}

});

</script>

</body>

</html>