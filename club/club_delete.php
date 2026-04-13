<?php
include("../db.php");
require("../auth.php");

$user_id = $_SESSION['user_id'];

if(!isset($_GET['club_id'])) {
    header("Location: club_list.php?status=error");
    exit();
}

$club_id = mysqli_real_escape_string($con, $_GET['club_id']);

$delete_query = "DELETE FROM clubs WHERE club_id='$club_id' AND user_id='$user_id'";

if(mysqli_query($con, $delete_query)) {
    if(mysqli_affected_rows($con) > 0) {
        header("Location: club_list.php?status=deleted");
        exit();
    } else {
        header("Location: club_list.php?status=error");
        exit();
    }
} else {
    header("Location: club_list.php?status=error");
    exit();
}
?>