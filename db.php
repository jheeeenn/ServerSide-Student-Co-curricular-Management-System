<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "student_cocurricular_db";

$con = mysqli_connect($host, $user, $password, $database);

if(!$con) {
    die("DB Connection failed: " . mysqli_connect_error());
}
?>