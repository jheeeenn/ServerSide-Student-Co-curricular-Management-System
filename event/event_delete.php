<?php
include("../db.php");
require("../auth.php");

$user_id = $_SESSION['user_id'];

// Redirect back to list if no event_id provided
if(!isset($_GET['event_id'])) {
    header("Location: event_list.php?status=delete_error");
    exit();
}

$event_id = mysqli_real_escape_string($con, $_GET['event_id']);

$delete_query = "DELETE FROM events WHERE event_id='$event_id' AND user_id='$user_id'";

if(mysqli_query($con, $delete_query)) {
    // Check if any row was affected -> -> event was deleted
    if(mysqli_affected_rows($con) > 0) {
        header("Location: event_list.php?status=deleted");
        exit();
    } 
    // If no rows affected, it means event was not found or did not belong to user
    else {
        header("Location: event_list.php?status=delete_error");
        exit();
    }
}
// If query execution failed
else {
    header("Location: event_list.php?status=delete_error");
    exit();
}