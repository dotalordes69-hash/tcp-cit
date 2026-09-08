<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "tcp_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


/* =========================================================
   CHECK LOGIN
========================================================= */

if (!isset($_SESSION['login_id'])) {

    header("Location: login.php");
    exit;
}


$userId = (int)$_SESSION['login_id'];


/* =========================================================
   CHECK IF PASSWORD CHANGE IS REQUIRED
========================================================= */

$query = "
    SELECT id, fullname, must_change_password
    FROM users
    WHERE id = '$userId'
    LIMIT 1
";

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {

    session_destroy();

    header("Location: login.php");
    exit;
}

$user = mysqli_fetch_assoc($result);


/*
|--------------------------------------------------------------------------
| If password change is no longer required,
| send user directly to dashboard
|--------------------------------------------------------------------------
*/

if ((int)$user['must_change_password'] !== 1) {

    header("Location: dashboard.php");
    exit;
}


/* =========================================================
   CHANGE PASSWORD
========================================================= */

if (isset($_POST['change_password'])) {

    $currentPassword = trim($_POST['current_password'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');


    /* -----------------------------------------------------
       VALIDATION
    ----------------------------------------------------- */

    if (
        $currentPassword === '' ||
        $newPassword === '' ||
        $confirmPassword === ''
    ) {

        $_SESSION['password_error'] =
            "Please complete all password fields.";

        header("Location: change_password.php");
        exit;
    }


    if ($newPassword !== $confirmPassword) {

        $_SESSION['password_error'] =
            "New password and confirmation do not match.";

        header("Location: change_password.php");
        exit;
    }


    if (strlen($newPassword) < 8) {

        $_SESSION['password_error'] =
            "New password must be at least 8 characters.";

        header("Location: change_password.php");
        exit;
    }


    if ($newPassword === $currentPassword) {

        $_SESSION['password_error'] =
            "New password must be different from the current password.";

        header("Location: change_password.php");
        exit;
    }


    /* -----------------------------------------------------
       GET CURRENT PASSWORD
    ----------------------------------------------------- */

    $passwordQuery = "
        SELECT password
        FROM users
        WHERE id = '$userId'
        LIMIT 1
    ";

    $passwordResult = mysqli_query($conn, $passwordQuery);

    if (
        !$passwordResult ||
        mysqli_num_rows($passwordResult) === 0
    ) {

        $_SESSION['password_error'] =
            "Unable to verify your account.";

        header("Location: change_password.php");
        exit;
    }


    $passwordRow = mysqli_fetch_assoc($passwordResult);

    $storedPassword = $passwordRow['password'];


    /* -----------------------------------------------------
       VERIFY CURRENT PASSWORD
       Current database uses plain-text passwords.
    ----------------------------------------------------- */

    if ($currentPassword !== $storedPassword) {

        $_SESSION['password_error'] =
            "Current password is incorrect.";

        header("Location: change_password.php");
        exit;
    }


    /* -----------------------------------------------------
       UPDATE PASSWORD
    ----------------------------------------------------- */

    $newPasswordEscaped =
        mysqli_real_escape_string($conn, $newPassword);

    $updateQuery = "
        UPDATE users
        SET
            password = '$newPasswordEscaped',
            must_change_password = 0
        WHERE id = '$userId'
    ";

    if (mysqli_query($conn, $updateQuery)) {

        unset($_SESSION['must_change_password']);

        $_SESSION['password_changed'] = true;

        header("Location: dashboard.php");
        exit;

    } else {

        $_SESSION['password_error'] =
            "Failed to update password. Please try again.";

        header("Location: change_password.php");
        exit;
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Change Password - TCP</title>

    <link rel="stylesheet"
          href="css/bootstrap.min.css">

    <script src="js/bootstrap.bundle.min.js"></script>

    <script src="js/sweetalert2.all.min.js"></script>


    <style>

        :root {
            --primary: #dc3545;
            --primary-hover: #bb2d3b;
            --primary-light: #ffe5e8;
        }


        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }


        body {
            background: #f4f6f9;

            min-height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;

            padding: 20px;
        }


        .password-box {
            width: 100%;
            max-width: 430px;

            background: #fff;

            padding: 35px;

            border-radius: 18px;

            box-shadow:
                0 15px 35px rgba(0, 0, 0, .12);

            border-top: 6px solid var(--primary);
        }


        .password-box h2 {
            color: var(--primary);

            font-size: 24px;

            font-weight: 700;

            margin-bottom: 8px;
        }


        .subtitle {
            color: #777;

            font-size: 14px;

            line-height: 1.6;

            margin-bottom: 25px;
        }


        .user-name {
            background: var(--primary-light);

            color: var(--primary);

            padding: 12px 15px;

            border-radius: 10px;

            font-size: 14px;

            font-weight: 600;

            margin-bottom: 20px;
        }


        .input-box {
            margin-bottom: 18px;
        }


        .input-box label {
            display: block;

            font-size: 14px;

            font-weight: 600;

            color: #444;

            margin-bottom: 6px;
        }


        .input-box input {
            width: 100%;

            height: 48px;

            padding: 0 15px;

            border: 1px solid #d9d9d9;

            border-radius: 10px;

            font-size: 15px;

            transition: .3s;
        }


        .input-box input:focus {
            outline: none;

            border-color: var(--primary);

            box-shadow:
                0 0 0 4px rgba(220, 53, 69, .15);
        }


        .password-note {
            font-size: 12px;

            color: #777;

            margin-top: -8px;

            margin-bottom: 20px;
        }


        .btn-change {
            width: 100%;

            height: 50px;

            border: none;

            border-radius: 10px;

            background: var(--primary);

            color: #fff;

            font-size: 16px;

            font-weight: 600;

            cursor: pointer;

            transition: .3s;
        }


        .btn-change:hover {
            background: var(--primary-hover);
        }


        .footer {
            margin-top: 22px;

            text-align: center;

            font-size: 13px;

            color: #777;
        }

    </style>

</head>


<body>


<div class="password-box">


    <h2>
        Change Your Password
    </h2>


    <div class="subtitle">

        This is your first login. For security,
        please create your own password before
        continuing to the system.

    </div>


    <div class="user-name">

        Account:
        <?= htmlspecialchars($user['fullname']) ?>

    </div>


    <form method="POST" autocomplete="off">


        <!-- CURRENT PASSWORD -->

        <div class="input-box">

            <label>
                Current Password
            </label>

            <input
                type="password"
                name="current_password"
                placeholder="Enter your current password"
                autocomplete="new-password"
                required
            >

        </div>


        <!-- NEW PASSWORD -->

        <div class="input-box">

            <label>
                New Password
            </label>

            <input
                type="password"
                name="new_password"
                placeholder="Enter your new password"
                minlength="8"
                autocomplete="new-password"
                required
            >

        </div>


        <!-- CONFIRM PASSWORD -->

        <div class="input-box">

            <label>
                Confirm New Password
            </label>

            <input
                type="password"
                name="confirm_password"
                placeholder="Confirm your new password"
                minlength="8"
                autocomplete="new-password"
                required
            >

        </div>


        <div class="password-note">

            Password must be at least 8 characters.

        </div>


        <button
            type="submit"
            name="change_password"
            class="btn-change"
        >
            Update Password
        </button>


    </form>


    <div class="footer">

        © 2026 CIT TCP. All Rights Reserved.

    </div>


</div>


<?php if (isset($_SESSION['password_error'])): ?>

<script>

Swal.fire({

    toast: true,

    position: 'top-end',

    icon: 'error',

    title: 'Password Update Failed',

    text: <?= json_encode($_SESSION['password_error']) ?>,

    showConfirmButton: false,

    timer: 2500,

    timerProgressBar: true

});

</script>

<?php

unset($_SESSION['password_error']);

endif;

?>


</body>

</html>