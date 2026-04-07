<?php
include("../db.php");
require("../auth.php");

$base_path = "../"; // Set base path for links in the navbar
$page_title = "Events Tracker";
$page_subtitle = "Manage your events and participation records";
$show_cookie_notice = false;

$message = "";
$message_type = "";

$user_id = $_SESSION['user_id'];

// Redirect back to list if no event_id provided
if(!isset($_GET['event_id'])) {
    header("Location: event_list.php");
    exit();
}

$event_id = mysqli_real_escape_string($con, $_GET['event_id']);

$select_query = "SELECT * FROM events WHERE event_id='$event_id' AND user_id='$user_id'";
$select_result = mysqli_query($con, $select_query);

// If event not found or does not belong to user, redirect back to list
if(mysqli_num_rows($select_result) != 1) {
    header("Location: event_list.php");
    exit();
}

$row = mysqli_fetch_assoc($select_result);


$event_title = $row['event_title'];
$event_type = $row['event_type'];
$organizer = $row['organizer'];
$event_date = $row['event_date'];
$location = $row['location'];
$description = $row['description'];

if(isset($_POST['submit'])) {
    $event_title = mysqli_real_escape_string($con, trim($_POST['event_title']));
    $event_type = mysqli_real_escape_string($con, trim($_POST['event_type']));
    $organizer = mysqli_real_escape_string($con, trim($_POST['organizer']));
    $event_date = mysqli_real_escape_string($con, trim($_POST['event_date']));
    $location = mysqli_real_escape_string($con, trim($_POST['location']));
    $description = mysqli_real_escape_string($con, trim($_POST['description']));

    if(!empty($event_title) && !empty($organizer) && !empty($event_date) && !empty($location)) {
        
        $update_query = "UPDATE events SET 
                         event_title='$event_title', 
                         event_type='$event_type', 
                         organizer='$organizer', 
                         event_date='$event_date', 
                         location='$location', 
                         description='$description' 
                         WHERE event_id='$event_id' AND user_id='$user_id'";

        if(mysqli_query($con, $update_query)) {
            header("Location: event_list.php");
            exit();
        } else {
            $message = "Error updating event. Please try again.";
            $message_type = "error";
        }
    } else {
        $message = "Event Title, Organizer, Date, Location are required fields.";
        $message_type = "error";
    }
}

include("../partials/header.php");
include("../partials/navbar.php");

?>
    <div class="container">
        <div class="header-box">
            <h2>Edit Event</h2>
            <p>Update the details of your event participation record here~</p>
            </div>

        <div class = "top-actions">
            <a href="event_list.php" class="btn btn-back">Back to Events List
            </a>
        </div>

        <form method="POST" action="" >
                    <label for="event_title">Event Title:</label>
                    <input type="text" id="event_title" name="event_title" value="<?php echo htmlspecialchars($event_title); ?>" required>

                    <label for="event_type">Event Type:</label>
                    <select name="event_type" id="event_type">
                        <!-- Add more event types as needed -->
                        <!-- for selecting event type using a dropdown -->
                        <option value="">-- Select Event Type --</option>
                        <option value="Workshop" <?php if ($event_type == "Workshop") echo "selected"; ?>>Workshop</option>
                        <option value="Competition" <?php if ($event_type == "Competition") echo "selected"; ?>>Competition</option>
                        <option value="Talk" <?php if ($event_type == "Talk") echo "selected"; ?>>Talk</option>
                        <option value="Seminar" <?php if ($event_type == "Seminar") echo "selected"; ?>>Seminar</option>
                        <option value="University Event" <?php if ($event_type == "University Event") echo "selected"; ?>>University Event</option>
                        <option value="Other" <?php if ($event_type == "Other") echo "selected"; ?>>Other</option>
                    </select>

                    <label for="organizer">Organizer:</label>
                    <input type="text" id="organizer" name="organizer" value="<?php echo htmlspecialchars($organizer); ?>">

                    <label for="event_date">Event Date:</label>
                    <input type="date" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event_date); ?>">

                    <label for="location">Location:</label>
                    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>">

                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows = "5"><?php echo htmlspecialchars($description); ?></textarea>

                    <input type="submit" name="submit" value="Add Event">
            </form>

        </div>
        </div>

</body>
</html>