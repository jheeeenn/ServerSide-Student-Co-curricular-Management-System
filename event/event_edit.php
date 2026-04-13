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
    header("Location: event_list.php?status=update_error");
    exit();
}

$event_id = mysqli_real_escape_string($con, $_GET['event_id']);

$select_query = "SELECT * FROM events WHERE event_id='$event_id' AND user_id='$user_id'";
$select_result = mysqli_query($con, $select_query);

// If event not found or does not belong to user, redirect back to list
if(mysqli_num_rows($select_result) != 1) {
    header("Location: event_list.php?status=update_error");
    exit();
}

$row = mysqli_fetch_assoc($select_result);

$club_options = [];

// Fetch user's clubs for dropdown
$club_query = "SELECT club_id, club_name FROM clubs WHERE user_id='$user_id' ORDER BY club_name ASC";
$club_result = mysqli_query($con, $club_query);

if ($club_result && mysqli_num_rows($club_result) > 0) {
    while ($club_row = mysqli_fetch_assoc($club_result)) {
        $club_options[] = $club_row;
    }
}

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

    // the related club id
    $club_id = isset($_POST['club_id']) ? mysqli_real_escape_string($con, trim($_POST['club_id'])) : "";
    $club_id_sql = !empty($club_id) ? "'$club_id'" : "NULL";

    if (!empty($club_id)) {
        $check_club_query = "SELECT * FROM clubs WHERE club_id='$club_id' AND user_id='$user_id'";
        $check_club_result = mysqli_query($con, $check_club_query);

        if (!$check_club_result || mysqli_num_rows($check_club_result) != 1) {
            $message = "Invalid related club selected.";
            $message_type = "error";
        }
    }

    if(
        (empty($message_type) || $message_type != "error") &&
        !empty($event_title) &&
        !empty($event_type) &&
        !empty($organizer) &&
        !empty($event_date) &&
        !empty($location)
    ) {
        
        $update_query = "UPDATE events SET 
                 club_id=$club_id_sql,
                 event_title='$event_title', 
                 event_type='$event_type', 
                 organizer='$organizer', 
                 event_date='$event_date', 
                 location='$location', 
                 description='$description' 
                 WHERE event_id='$event_id' AND user_id='$user_id'";

        if(mysqli_query($con, $update_query)) {
            header("Location: event_list.php?status=updated");
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
$current_page = "event";
include("../partials/navbar.php");

?>
    <div class="container module-shell event-shell">
    <section class="module-hero module-hero-event">
        <div class="module-hero-main">
            <div class="module-hero-icon event-accent-soft">✏️</div>
            <div class="module-hero-text-wrap">
                <h2>Edit Event</h2>
                <p>Update the details of your event participation record.</p>
            </div>
        </div>

        <div class="module-hero-actions">
            <a href="event_list.php" class="module-btn module-btn-secondary">Back to Events</a>
        </div>
    </section>

    <section class="module-form-card">
        <?php if(!empty($message)) { ?>
            <div class="message <?php echo $message_type; ?> module-status-message">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <form method="POST" action="" class="module-form-layout">
            <div class="module-form-grid">
                <div class="module-field">
                    <label for="event_title">Event Title</label>
                    <input type="text" id="event_title" name="event_title" value="<?php echo htmlspecialchars($event_title); ?>" required>
                </div>

                <div class="module-field">
                    <label for="event_type">Event Type</label>
                    <select name="event_type" id="event_type" required>
                        <option value="">-- Select Event Type --</option>
                        <option value="Workshop" <?php if ($event_type == "Workshop") echo "selected"; ?>>Workshop</option>
                        <option value="Competition" <?php if ($event_type == "Competition") echo "selected"; ?>>Competition</option>
                        <option value="Talk" <?php if ($event_type == "Talk") echo "selected"; ?>>Talk</option>
                        <option value="Seminar" <?php if ($event_type == "Seminar") echo "selected"; ?>>Seminar</option>
                        <option value="University Event" <?php if ($event_type == "University Event") echo "selected"; ?>>University Event</option>
                        <option value="Other" <?php if ($event_type == "Other") echo "selected"; ?>>Other</option>
                    </select>
                </div>

                <!-- Related Club Dropdown -->
                <div class="module-field">
                    <label for="club_id">Related Club (Optional)</label>
                    <select name="club_id" id="club_id">
                        <option value="">-- No Related Club --</option>
                        <?php foreach ($club_options as $club) { ?>
                            <option value="<?php echo $club['club_id']; ?>" <?php if ($row['club_id'] == $club['club_id']) echo "selected"; ?>>
                                <?php echo htmlspecialchars($club['club_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="module-field">
                    <label for="organizer">Organizer</label>
                    <input type="text" id="organizer" name="organizer" value="<?php echo htmlspecialchars($organizer); ?>" required>
                </div>

                <div class="module-field">
                    <label for="event_date">Event Date</label>
                    <input type="date" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event_date); ?>" required>
                </div>

                <div class="module-field module-field-full">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>" required>
                </div>

                <div class="module-field module-field-full">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="6"><?php echo htmlspecialchars($description); ?></textarea>
                </div>
            </div>

            <div class="module-form-actions">
                <a href="event_list.php" class="module-btn module-btn-secondary">Cancel</a>
                <button type="submit" name="submit" class="module-btn module-btn-primary event-accent-btn">Update Event</button>
            </div>
        </form>
    </section>
</div>
</body>
</html>