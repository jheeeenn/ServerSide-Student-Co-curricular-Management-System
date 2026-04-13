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

// to link to a related club, but optional when adding event
$club_id = "";
$club_options = [];

$user_id = $_SESSION['user_id'];

$club_query = "SELECT club_id, club_name FROM clubs WHERE user_id='$user_id' ORDER BY club_name ASC";
$club_result = mysqli_query($con, $club_query);

if ($club_result && mysqli_num_rows($club_result) > 0) {
    while ($club_row = mysqli_fetch_assoc($club_result)) {
        $club_options[] = $club_row;
    }
}

if(isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];

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
        
        $query = "INSERT INTO events (user_id, club_id, event_title, event_type, organizer, event_date, location, description) 
           VALUES ('$user_id', $club_id_sql, '$event_title', '$event_type', '$organizer', '$event_date', '$location', '$description')";

        if(mysqli_query($con, $query)) {
            header("Location: event_list.php?status=added");
            exit();
        } else {
            $message = "Error adding event. Please try again.";
            $message_type = "error";
        }
    } else {
        $message = "Event Title, Event Type, Organizer, Date, and Location are required fields.";
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
            <div class="module-hero-icon event-accent-soft">📅</div>
            <div class="module-hero-text-wrap">
                <h2>Add Event</h2>
                <p>Create a new event participation record for your co-curricular activities.</p>
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

                <!-- Optional related club dropdown -->
                <div class="module-field">
                    <label for="club_id">Related Club (Optional)</label>
                    <select name="club_id" id="club_id">
                        <option value="">-- No Related Club --</option>
                        <?php foreach ($club_options as $club) { ?>
                            <option value="<?php echo $club['club_id']; ?>" <?php if ($club_id == $club['club_id']) echo "selected"; ?>>
                                <?php echo htmlspecialchars($club['club_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="module-field">
                    <label for="organizer">Organizer</label>
                    <input type="text" id="organizer" name="organizer" required value="<?php echo htmlspecialchars($organizer); ?>">
                </div>

                <div class="module-field">
                    <label for="event_date">Event Date</label>
                    <input type="date" id="event_date" name="event_date" required value="<?php echo htmlspecialchars($event_date); ?>">
                </div>

                <div class="module-field module-field-full">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" required value="<?php echo htmlspecialchars($location); ?>">
                </div>

                <div class="module-field module-field-full">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="6"><?php echo htmlspecialchars($description); ?></textarea>
                </div>
            </div>

            <div class="module-form-actions">
                <a href="event_list.php" class="module-btn module-btn-secondary">Cancel</a>
                <button type="submit" name="submit" class="module-btn module-btn-primary event-accent-btn">Add Event</button>
            </div>
        </form>
    </section>
</div>


</body>
</html>