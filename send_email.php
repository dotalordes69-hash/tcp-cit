<?php
// send_email.php
session_start();
include __DIR__ . '/db.php'; // make sure db.php exists
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';
require __DIR__ . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$status = '';
$errorInfo = '';
$student_id = '';
$student_pk = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['fullname']) || empty($_SESSION['fullname'])) {
        $status = 'error';
        $errorInfo = 'Session fullname not found. Please login again.';
    } else {
        $fullname = $_SESSION['fullname'];
        $student_pk = intval($_POST['student_pk'] ?? 0);
        $student_id = trim($_POST['student_id'] ?? '');
        $current_page = $_POST['page'] ?? 'account';
        $to = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'cit.dwightsevilla1997@gmail.com';
            $mail->Password   = 'gdkg lqfy oepx amlm'; // your App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('cit.dwightsevilla1997@gmail.com', 'CIT TCP');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
            $mail->Body    = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
            $mail->AltBody = $message;

            $mail->send();
            $status = 'success';

            // -----------------------------
            // Save email record to database
            // -----------------------------
            if ($student_id > 0) {
$stmt_insert = $conn->prepare("
    INSERT INTO email_history
    (student_id, sent_at, sent_by, subject, message)
    VALUES (?, NOW(), ?, ?, ?)
");

$stmt_insert->bind_param(
    "ssss",
    $student_id,
    $fullname,
    $subject,
    $message
);

                $stmt_insert->execute();
                $stmt_insert->close();
            }

        } catch (Exception $e) {
            $status = 'error';
            $errorInfo = $mail->ErrorInfo;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Status</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/sweetalert2.all.min.js"></script>
</head>
<style>
   .cit-swal{
    border-radius:14px !important;
    box-shadow:0 10px 30px rgba(0,0,0,.12) !important;
}

.cit-swal-title{
    color:#800000 !important;
    font-weight:600 !important;
}

.cit-swal-btn{
    background:#800000 !important;
    border:none !important;
    border-radius:8px !important;
    padding:10px 24px !important;
    font-weight:500 !important;
}

.cit-swal-btn:hover{
    background:#6d0000 !important;
}

.swal2-icon.swal2-success{
    border-color:#800000 !important;
}

.swal2-success-ring{
    border-color:rgba(128,0,0,.15) !important;
}

.swal2-popup{
    font-family:inherit !important;
}

.swal2-html-container{
    color:#6c757d !important;
    font-size:14px !important;
}

</style>
<body>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const status = "<?php echo $status; ?>";
    const errorInfo = "<?php echo htmlspecialchars($errorInfo, ENT_QUOTES); ?>";

    if (status === 'success') {

        Swal.fire({
            icon: 'success',
            title: 'Email Sent Successfully',
            html: `
                <div class="mt-2">
                    The message has been successfully delivered to the student.
                </div>
            `,
            confirmButtonText: 'Continue',
            customClass: {
                popup: 'cit-swal',
                title: 'cit-swal-title',
                confirmButton: 'cit-swal-btn'
            }
        }).then(() => {

            const studentPk = "<?php echo $student_pk; ?>";
            const currentPage = "<?php echo htmlspecialchars($current_page, ENT_QUOTES); ?>";

            window.location.href =
                'view_student.php?id=' + studentPk +
                '&page=' + currentPage;
        });

    } else if (status === 'error') {

        Swal.fire({
            icon: 'error',
            title: 'Email Sending Failed',
            html: `
                <div class="text-start">
                    <strong>Error Details:</strong><br>
                    ${errorInfo || 'Unknown error occurred.'}
                </div>
            `,
            confirmButtonText: 'Close',
            customClass: {
                popup: 'cit-swal',
                title: 'cit-swal-title',
                confirmButton: 'cit-swal-btn'
            }
        }).then(() => {
            window.history.back();
        });

    }

});
</script>
</body>
</html>