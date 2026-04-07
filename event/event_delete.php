<?php
include("../db.php");
require("../auth.php");

$user_id = $_SESSION['user_id'];

// Redirect back to list if no event_id provided
if(!isset($_GET['event_id'])) {
    header("Location: event_list.php");
    exit();
}

$event_id = mysqli_real_escape_string($con, $_GET['event_id']);

$delete_query = "DELETE FROM events WHERE event_id='$event_id' AND user_id='$user_id'";

if(mysqli_query($con, $delete_query)) {
    header("Location: event_list.php");
    exit();
} else {
    // Handle error if needed
    $message = "Error deleting event. Please try again.";
    $message_type = "error";
    
    header("Location: event_list.php");
    exit();
}