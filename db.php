<?php
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
?>