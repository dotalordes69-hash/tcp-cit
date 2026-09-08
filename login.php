<?php
session_start();

$conn = mysqli_connect(
    getenv("MYSQLHOST") ?: "localhost",
    getenv("MYSQLUSER") ?: "root",
    getenv("MYSQLPASSWORD") ?: "",
    getenv("MYSQLDATABASE") ?: "tcp_db",
    getenv("MYSQLPORT") ?: 3306
);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


/* =========================================================
   LOGIN
========================================================= */

if (isset($_POST['login'])) {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {

        $_SESSION['login_error'] = true;

        header("Location: login.php");
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | FIND USER
    |--------------------------------------------------------------------------
    */

    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    $query = "
        SELECT *
        FROM users
        WHERE username = '$username'
        AND password = '$password'
        LIMIT 1
    ";

    $result = mysqli_query($conn, $query);


    /*
    |--------------------------------------------------------------------------
    | LOGIN SUCCESS
    |--------------------------------------------------------------------------
    */

    if ($result && mysqli_num_rows($result) > 0) {

        $row = mysqli_fetch_assoc($result);


        /*
        |--------------------------------------------------------------------------
        | SET SESSION
        |--------------------------------------------------------------------------
        */

        $_SESSION['login_id'] = $row['id'];
        $_SESSION['fullname'] = $row['fullname'];
        $_SESSION['role'] = $row['role'];


        /*
        |--------------------------------------------------------------------------
        | FIRST LOGIN / MUST CHANGE PASSWORD
        |--------------------------------------------------------------------------
        */

        if (isset($row['must_change_password']) &&
            (int)$row['must_change_password'] === 1) {

            $_SESSION['must_change_password'] = true;

            header("Location: change_password.php");
            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | NORMAL LOGIN
        |--------------------------------------------------------------------------
        */

        header("Location: dashboard.php");
        exit;

    } else {

        /*
        |--------------------------------------------------------------------------
        | LOGIN FAILED
        |--------------------------------------------------------------------------
        */

        $_SESSION['login_error'] = true;

        header("Location: login.php");
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

    <title>TCP Login</title>

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


        .login-box {
            width: 100%;
            max-width: 400px;

            background: #fff;

            padding: 35px;

            border-radius: 18px;

            box-shadow:
                0 15px 35px rgba(0, 0, 0, .12);

            text-align: center;

            border-top: 6px solid var(--primary);
        }


        .logo {
            width: 90px;
            height: auto;

            margin-bottom: 15px;
        }


        .login-box h2 {
            color: var(--primary);

            font-size: 24px;
            font-weight: 700;

            margin-bottom: 5px;
        }


        .subtitle {
            color: #777;

            margin-bottom: 25px;

            font-size: 14px;
        }


        .input-box {
            text-align: left;

            margin-bottom: 18px;
        }


        .input-box label {
            display: block;

            font-size: 14px;
            font-weight: 600;

            margin-bottom: 6px;

            color: #444;
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


        .btn-login {
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


        .btn-login:hover {
            background: var(--primary-hover);
        }


        .btn-create {
            width: 100%;
            height: 48px;

            display: flex;
            justify-content: center;
            align-items: center;

            text-decoration: none;

            border: 2px solid var(--primary);

            border-radius: 10px;

            color: var(--primary);

            font-weight: 600;

            margin-top: 12px;

            transition: .3s;
        }


        .btn-create:hover {
            background: var(--primary);

            color: #fff;
        }


        .footer {
            margin-top: 22px;

            font-size: 13px;

            color: #777;

            line-height: 1.6;
        }

    </style>

</head>


<body>


<div class="login-box">


<img
    src="img/logo.png"
    class="logo"
    alt="TCP Logo"
    onclick="adminAccess()"
    title="School Logo"
>


    <h2>
        Teacher Certificate Program
    </h2>


    <div class="subtitle">
        Sign in to continue
    </div>


    <form method="POST" autocomplete="off">


        <!-- USERNAME -->

        <div class="input-box">

            <label>
                Username
            </label>

            <input
                type="text"
                name="username"
                placeholder="Enter your username"

                autocomplete="off"
                autocorrect="off"
                autocapitalize="off"
                spellcheck="false"

                required
            >

        </div>


        <!-- PASSWORD -->

        <div class="input-box">

            <label>
                Password
            </label>

            <input
                type="password"
                name="password"
                placeholder="Enter your password"

                autocomplete="new-password"

                required
            >

        </div>


        <!-- LOGIN BUTTON -->

        <button
            type="submit"
            name="login"
            class="btn-login"
        >
            Login
        </button>


    </form>






    <div class="footer">

        © 2026 CIT TCP. All Rights Reserved.

    </div>


</div>


<?php if (isset($_SESSION['login_error'])): ?>

<script>

Swal.fire({

    toast: true,

    position: 'top-end',

    icon: 'error',

    title: 'Invalid username or password.',

    text: 'Please check your credentials and try again.',

    showConfirmButton: false,

    timer: 2000,

    timerProgressBar: true

});

</script>

<?php

unset($_SESSION['login_error']);

endif;

?>
<script>

function adminAccess() {

    window.location.href = "register.php";

}

</script>

</body>

</html>