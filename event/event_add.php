<?php
include("../db.php");
require("../auth.php");

$base_path = "../"; // Set base path for links in the navbar
$page_title = "Events Tracker";
$page_subtitle = "Manage your events and participation records";
$show_cookie_notice = false;

$message = "";
$message_type = "";

$event_title = "";
$event_type = "";
$organizer = "";
$event_date = "";
$location = "";
$description = "";


if(isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];

    $event_title = mysqli_real_escape_string($con, trim($_POST['event_title']));
    $event_type = mysqli_real_escape_string($con, trim($_POST['event_type']));
    $organizer = mysqli_real_escape_string($con, trim($_POST['organizer']));
    $event_date = mysqli_real_escape_string($con, trim($_POST['event_date']));
    $location = mysqli_real_escape_string($con, trim($_POST['location']));
    $description = mysqli_real_escape_string($con, trim($_POST['description']));

    if(!empty($event_title) && !empty($organizer) && !empty($event_date) && !empty($location)) {
        
        $query = "INSERT INTO events (user_id, event_title, event_type, organizer, event_date, location, description) 
                  VALUES ('$user_id', '$event_title', '$event_type', '$organizer', '$event_date', '$location', '$description')";

        if(mysqli_query($con, $query)) {
            header("Location: event_list.php");
            exit();
        } else {
            $message = "Error adding event. Please try again.";
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
            <h2>Add New Event</h2>
            <p>Fill in the details below to record a new event~</p>
        </div>

        <div class = "top-actions">
            <a href="event_list.php" class="btn btn-back">Back to Events List</a>

        </div>

        <div class = "page-box">
            <?php if(!empty($message)) { ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php } ?>

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
                    <input type="text" id="organizer" name="organizer" required value="<?php echo htmlspecialchars($organizer); ?>">

                    <label for="event_date">Event Date:</label>
                    <input type="date" id="event_date" name="event_date" required value="<?php echo htmlspecialchars($event_date); ?>">

                    <label for="location">Location:</label>
                    <input type="text" id="location" name="location" required value="<?php echo htmlspecialchars($location); ?>">

                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows = "5"><?php echo htmlspecialchars($description); ?></textarea>

                    <input type="submit" name="submit" value="Add Event">
            </form>

            </div>
        </div>

        </body>
</html>