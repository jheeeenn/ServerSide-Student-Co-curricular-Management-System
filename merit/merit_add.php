<?php
include("../db.php");
require("../auth.php");

$base_path = "../"; // Set base path for links in the navbar
$page_title = "Merit Tracker";
$page_subtitle = "Manage your merit contribution records";
$show_cookie_notice = false;

$message = "";
$message_type = "";

$activity_title = "";
$activity_type = "";
$organizer = "";
$activity_date = "";
$start_time = "";
$end_time = "";
$total_hours = "";
$description = "";

// 
$event_id = "";
$club_id = "";
$event_id_sql = "NULL";
$club_id_sql = "NULL";

$source_mode = "";
$source_title = "";
$source_badge = "";

$user_id = $_SESSION['user_id'];

// Fetch events and clubs for potential linking for merit records
if (isset($_GET['event_id']) && !empty($_GET['event_id'])) {
    $event_id = mysqli_real_escape_string($con, $_GET['event_id']);

    $event_query = "
        SELECT events.*, clubs.club_name
        FROM events
        LEFT JOIN clubs ON events.club_id = clubs.club_id
        WHERE events.event_id='$event_id' AND events.user_id='$user_id'
    ";
    $event_result = mysqli_query($con, $event_query);

    if ($event_result && mysqli_num_rows($event_result) == 1) {
        $event_row = mysqli_fetch_assoc($event_result);

        $activity_title = $event_row['event_title'];
        $organizer = $event_row['organizer'];
        $activity_date = $event_row['event_date'];

        $event_id_sql = "'$event_id'";

        if (!empty($event_row['club_id'])) {
            $club_id = $event_row['club_id'];
            $club_id_sql = "'$club_id'";
        }

        $source_mode = "event";
        $source_title = $event_row['event_title'];
        $source_badge = "Generated from Event";
    } else {
        $message = "Invalid event source selected.";
        $message_type = "error";
        $event_id = "";
    }
}
elseif (isset($_GET['club_id']) && !empty($_GET['club_id'])) {
    $club_id = mysqli_real_escape_string($con, $_GET['club_id']);

    $club_query = "SELECT * FROM clubs WHERE club_id='$club_id' AND user_id='$user_id'";
    $club_result = mysqli_query($con, $club_query);

    if ($club_result && mysqli_num_rows($club_result) == 1) {
        $club_row = mysqli_fetch_assoc($club_result);

        $activity_title = $club_row['club_name'];
        $organizer = $club_row['club_name'];

        $club_id_sql = "'$club_id'";

        $source_mode = "club";
        $source_title = $club_row['club_name'];
        $source_badge = "Generated from Club";
    } else {
        $message = "Invalid club source selected.";
        $message_type = "error";
        $club_id = "";
    }
}

if (isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];

    $activity_title = mysqli_real_escape_string($con, trim($_POST['activity_title']));
    $activity_type = mysqli_real_escape_string($con, trim($_POST['activity_type']));
    $organizer = mysqli_real_escape_string($con, trim($_POST['organizer']));
    $activity_date = mysqli_real_escape_string($con, trim($_POST['activity_date']));
    $start_time = mysqli_real_escape_string($con, trim($_POST['start_time']));
    $end_time = mysqli_real_escape_string($con, trim($_POST['end_time']));
    $description = mysqli_real_escape_string($con, trim($_POST['description']));

    $event_id = isset($_POST['event_id']) ? mysqli_real_escape_string($con, trim($_POST['event_id'])) : "";
    $club_id = isset($_POST['club_id']) ? mysqli_real_escape_string($con, trim($_POST['club_id'])) : "";

    $event_id_sql = !empty($event_id) ? "'$event_id'" : "NULL";
    $club_id_sql = !empty($club_id) ? "'$club_id'" : "NULL";

    
    if (
        (empty($message_type) || $message_type != "error") &&
        !empty($activity_title) &&
        !empty($activity_type) &&
        !empty($organizer) &&
        !empty($activity_date) &&
        !empty($start_time) &&
        !empty($end_time)
    ) {

        $start_timestamp = strtotime($start_time);
        $end_timestamp = strtotime($end_time);

        if (!empty($event_id)) {
            $check_event_query = "SELECT * FROM events WHERE event_id='$event_id' AND user_id='$user_id'";
            $check_event_result = mysqli_query($con, $check_event_query);

            if (!$check_event_result || mysqli_num_rows($check_event_result) != 1) {
                $message = "Invalid linked event selected.";
                $message_type = "error";
            }
        }

        if (!empty($club_id)) {
            $check_club_query = "SELECT * FROM clubs WHERE club_id='$club_id' AND user_id='$user_id'";
            $check_club_result = mysqli_query($con, $check_club_query);

            if (!$check_club_result || mysqli_num_rows($check_club_result) != 1) {
                $message = "Invalid linked club selected.";
                $message_type = "error";
            }
        }

        if ($end_timestamp > $start_timestamp) {
            $hours = ($end_timestamp - $start_timestamp) / 3600;
            $total_hours = number_format($hours, 2, '.', '');

           $query = "INSERT INTO merits (user_id, event_id, club_id, activity_title, activity_type, organizer, activity_date, start_time, end_time, total_hours, description) 
                VALUES ('$user_id', $event_id_sql, $club_id_sql, '$activity_title', '$activity_type', '$organizer', '$activity_date', '$start_time', '$end_time', '$total_hours', '$description')";

            if (mysqli_query($con, $query)) {
                header("Location: merit_list.php?status=added");
                exit();
            } else {
                $message = "Error adding merit record. Please try again.";
                $message_type = "error";
            }
        } else {
            $message = "End time must be later than start time.";
            $message_type = "error";
        }
    } else {
        $message = "Activity Title, Organizer, Activity Date, Start Time, and End Time are required fields.";
        $message_type = "error";
    }
}

include("../partials/header.php");
$current_page = "merit";
?>
<?php include("../partials/navbar.php"); ?>

<div class="container module-shell merit-shell">
    <section class="module-hero module-hero-merit">
        <div class="module-hero-main">
            <div class="module-hero-icon merit-accent-soft">⏳</div>
            <div class="module-hero-text-wrap">
                <h2>Add Merit Record</h2>
                <p>Record a new merit contribution activity and calculate the total hours automatically.</p>
            </div>
        </div>

        <div class="module-hero-actions">
            <a href="merit_list.php" class="module-btn module-btn-secondary">Back to Merit List</a>
        </div>
    </section>

    <section class="module-form-card">
        <?php if (!empty($message)) { ?>
            <div class="message <?php echo $message_type; ?> module-status-message">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <?php if (!empty($source_mode) && !empty($source_title)) { ?>
            <div class="module-source-box merit-source-box">
                <span class="module-source-badge merit-source-badge"><?php echo htmlspecialchars($source_badge); ?></span>
                <strong><?php echo htmlspecialchars($source_title); ?></strong>
            </div>
        <?php } ?>

        <form method="POST" action="" class="module-form-layout">
            <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event_id); ?>">
            <input type="hidden" name="club_id" value="<?php echo htmlspecialchars($club_id); ?>">
            <div class="module-form-grid">
                <div class="module-field">
                    <label for="activity_title">Activity Title</label>
                    <input type="text" id="activity_title" name="activity_title" value="<?php echo htmlspecialchars($activity_title); ?>" required>
                </div>

                <div class="module-field">
                    <label for="activity_type">Activity Type</label>
                    <select name="activity_type" id="activity_type" required>
                        <option value="">-- Select Activity Type --</option>
                        <option value="Volunteering" <?php if ($activity_type == "Volunteering") echo "selected"; ?>>Volunteering</option>
                        <option value="Community Service" <?php if ($activity_type == "Community Service") echo "selected"; ?>>Community Service</option>
                        <option value="Committee Work" <?php if ($activity_type == "Committee Work") echo "selected"; ?>>Committee Work</option>
                        <option value="Club Service" <?php if ($activity_type == "Club Service") echo "selected"; ?>>Club Service</option>
                        <option value="University Service" <?php if ($activity_type == "University Service") echo "selected"; ?>>University Service</option>
                        <option value="Other" <?php if ($activity_type == "Other") echo "selected"; ?>>Other</option>
                    </select>
                </div>

                <div class="module-field">
                    <label for="organizer">Organizer</label>
                    <input type="text" id="organizer" name="organizer" value="<?php echo htmlspecialchars($organizer); ?>" required>
                </div>

                <div class="module-field">
                    <label for="activity_date">Activity Date</label>
                    <input type="date" id="activity_date" name="activity_date" value="<?php echo htmlspecialchars($activity_date); ?>" required>
                </div>

                <div class="module-field module-field-full">
                    <label>Contribution Time</label>
                    <div class="merit-time-card">
                        <div class="merit-time-grid">
                            <div class="merit-time-input-box">
                                <label for="start_time" class="merit-time-label">Start Time</label>
                                <input type="time" id="start_time" name="start_time" value="<?php echo htmlspecialchars($start_time); ?>" required>
                            </div>

                            <div class="merit-time-input-box">
                                <label for="end_time" class="merit-time-label">End Time</label>
                                <input type="time" id="end_time" name="end_time" value="<?php echo htmlspecialchars($end_time); ?>" required>
                            </div>
                        </div>

                        <div class="merit-total-hours-card">
                            <div class="merit-total-hours-text">Total Hours</div>
                            <input type="text" id="total_hours_display"
                                value="<?php echo !empty($total_hours) ? htmlspecialchars(number_format((float)$total_hours, 2)) : ''; ?>"
                                readonly
                                placeholder="Auto-calculated">
                        </div>
                    </div>
                </div>

                <div class="module-field module-field-full">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="6"><?php echo htmlspecialchars($description); ?></textarea>
                </div>
            </div>

            <div class="module-form-actions">
                <a href="merit_list.php" class="module-btn module-btn-secondary">Cancel</a>
                <button type="submit" name="submit" class="module-btn module-btn-primary merit-accent-btn">Add Merit Record</button>
            </div>
        </form>
    </section>
</div>

<script>
    // Calculate total hours based on start and end time inputs
    function calculateHours() {
        const start = document.getElementById("start_time").value;
        const end = document.getElementById("end_time").value;
        const totalHoursField = document.getElementById("total_hours_display");

        if (start && end) {
            const startTime = new Date("1970-01-01T" + start + ":00");
            const endTime = new Date("1970-01-01T" + end + ":00");

            if (endTime > startTime) {
                const diffMs = endTime - startTime;
                const diffHours = diffMs / (1000 * 60 * 60);
                totalHoursField.value = diffHours.toFixed(2);
            } else {
                totalHoursField.value = "";
            }
        } else {
            totalHoursField.value = "";
        }
    }

    document.getElementById("start_time").addEventListener("input", calculateHours);
    document.getElementById("end_time").addEventListener("input", calculateHours);

    // Calculate once when page loads
    calculateHours();
</script>

</body>
</html>