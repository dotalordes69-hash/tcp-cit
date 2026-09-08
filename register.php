<?php
session_start();

$conn = mysqli_connect(
    getenv("MYSQLHOST") ?: "localhost",
    getenv("MYSQLUSER") ?: "root",
    getenv("MYSQLPASSWORD") ?: "",
    getenv("MYSQLDATABASE") ?: "tcp_db",
    getenv("MYSQLPORT") ?: 3306
);

if(!$conn){
    die("Connection Failed: ".mysqli_connect_error());
}

if(isset($_POST['save'])){

    $fullname = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));
    $role     = mysqli_real_escape_string($conn, $_POST['role']);

    // Check if username already exists
    $check = mysqli_query($conn,"SELECT id FROM users WHERE username='$username'");

    if(mysqli_num_rows($check) > 0){

        echo "<script>
            alert('Username already exists!');
        </script>";

    }else{

        mysqli_query($conn,"
            INSERT INTO users(fullname,username,password,role)
            VALUES('$fullname','$username','$password','$role')
        ");

        echo "<script>
            alert('User successfully created.');
            window.location='login.php';
        </script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Create User</title>

<link rel="stylesheet" href="css/bootstrap.min.css">

<style>

body{
    margin:0;
    padding:30px 15px;
    background:#f4f6f9;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:'Segoe UI',sans-serif;
    overflow-y:auto;
}

.card-register{
    width:100%;
    max-width:430px;
    background:#fff;
    padding:35px;
    border-radius:18px;
    box-shadow:0 15px 35px rgba(0,0,0,.10);
    border-top:5px solid #dc3545;
}

.logo{
    width:85px;
    display:block;
    margin:auto;
}

.title{
    text-align:center;
    color:#dc3545;
    font-size:25px;
    font-weight:bold;
    margin-top:15px;
}

.subtitle{
    text-align:center;
    color:#777;
    margin-bottom:25px;
}

.form-label{
    font-weight:600;
}

.form-control,
.form-select{
    height:46px;
}

.btn-save{
    width:100%;
    height:48px;
    background:#dc3545;
    color:white;
    border:none;
    border-radius:10px;
    font-weight:600;
}

.btn-save:hover{
    background:#bb2d3b;
}

.btn-back{
    width:100%;
    margin-top:10px;
}
@media (max-width:576px){

    body{
        padding:20px 12px;
        align-items:flex-start;
    }

    .card-register{
        padding:25px;
        border-radius:15px;
    }

    .logo{
        width:70px;
    }

    .title{
        font-size:22px;
    }
}
</style>

</head>

<body>

<div class="card-register">

<img src="img/logo.png" class="logo">

<div class="title">
Create New Account
</div>

<div class="subtitle">
Teacher Certificate Program
</div>
<form method="POST" autocomplete="off">

<div class="mb-3">
    <label class="form-label">Full Name</label>
    <input
        type="text"
        name="fullname"
        class="form-control"
        autocomplete="off"
        autocorrect="off"
        autocapitalize="off"
        spellcheck="false"
        required>
</div>

<div class="mb-3">
    <label class="form-label">Username</label>
    <input
        type="text"
        name="username"
        class="form-control"
        autocomplete="off"
        autocorrect="off"
        autocapitalize="off"
        spellcheck="false"
        required>
</div>

<div class="mb-3">
    <label class="form-label">Password</label>
    <input
        type="password"
        name="password"
        class="form-control"
        autocomplete="new-password"
        required>
</div>

<div class="mb-4">
    <label class="form-label">Role</label>

    <select
        name="role"
        class="form-select"
        autocomplete="off"
        required>

        <option value="">Select Role</option>
        <option value="Superadmin">Superadmin</option>
        <option value="Admin">Admin</option>
        <option value="User">User</option>

    </select>
</div>

<button
type="submit"
name="save"
class="btn-save">
Create Account
</button>

<a href="login.php"
class="btn btn-outline-secondary btn-back">
Back to Login
</a>

</form>

</div>

</body>
</html>