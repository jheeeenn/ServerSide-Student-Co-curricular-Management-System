<?php
include("../db.php");
require("../auth.php");

$user_id = $_SESSION['user_id'];

if (isset($_GET['merit_id'])) {
    $merit_id = mysqli_real_escape_string($con, $_GET['merit_id']);

    $check_query = "SELECT * FROM merits WHERE merit_id='$merit_id' AND user_id='$user_id'";
    $check_result = mysqli_query($con, $check_query);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $delete_query = "DELETE FROM merits WHERE merit_id='$merit_id' AND user_id='$user_id'";

        if (mysqli_query($con, $delete_query)) {
            header("Location: merit_list.php?status=deleted");
            exit();
        } else {
            header("Location: merit_list.php?status=delete_error");
            exit();
        }
    } else {
        header("Location: merit_list.php?status=delete_error");
        exit();
    }
} else {
    header("Location: merit_list.php?status=delete_error");
    exit();
}
?>