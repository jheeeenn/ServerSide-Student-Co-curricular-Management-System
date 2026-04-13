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

if (isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];

    $activity_title = mysqli_real_escape_string($con, trim($_POST['activity_title']));
    $activity_type = mysqli_real_escape_string($con, trim($_POST['activity_type']));
    $organizer = mysqli_real_escape_string($con, trim($_POST['organizer']));
    $activity_date = mysqli_real_escape_string($con, trim($_POST['activity_date']));
    $start_time = mysqli_real_escape_string($con, trim($_POST['start_time']));
    $end_time = mysqli_real_escape_string($con, trim($_POST['end_time']));
    $description = mysqli_real_escape_string($con, trim($_POST['description']));

    if (!empty($activity_title) && !empty($activity_type) && !empty($organizer) && !empty($activity_date) && !empty($start_time) && !empty($end_time)) {

        $start_timestamp = strtotime($start_time);
        $end_timestamp = strtotime($end_time);

        if ($end_timestamp > $start_timestamp) {
            $hours = ($end_timestamp - $start_timestamp) / 3600;
            $total_hours = number_format($hours, 2, '.', '');

            $query = "INSERT INTO merits (user_id, activity_title, activity_type, organizer, activity_date, start_time, end_time, total_hours, description) 
                      VALUES ('$user_id', '$activity_title', '$activity_type', '$organizer', '$activity_date', '$start_time', '$end_time', '$total_hours', '$description')";

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
?>
<?php include("../partials/navbar.php"); ?>

<div class="container">
    <div class="header-box">
        <h2>Add New Merit Record</h2>
        <p>Fill in the details below to record a new merit contribution.</p>
    </div>

    <div class="top-actions">
        <a href="merit_list.php" class="btn btn-back">Back to Merit List</a>
    </div>

    <div class="page-box">
        <?php if (!empty($message)) { ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <form method="POST" action="">
            <label for="activity_title">Activity Title:</label>
            <input type="text" id="activity_title" name="activity_title" value="<?php echo htmlspecialchars($activity_title); ?>" required>

            <label for="activity_type">Activity Type:</label>
            <select name="activity_type" id="activity_type" required>
                <option value="">-- Select Activity Type --</option>
                <option value="Volunteering" <?php if ($activity_type == "Volunteering") echo "selected"; ?>>Volunteering</option>
                <option value="Community Service" <?php if ($activity_type == "Community Service") echo "selected"; ?>>Community Service</option>
                <option value="Committee Work" <?php if ($activity_type == "Committee Work") echo "selected"; ?>>Committee Work</option>
                <option value="Club Service" <?php if ($activity_type == "Club Service") echo "selected"; ?>>Club Service</option>
                <option value="University Service" <?php if ($activity_type == "University Service") echo "selected"; ?>>University Service</option>
                <option value="Other" <?php if ($activity_type == "Other") echo "selected"; ?>>Other</option>
            </select>

            <label for="organizer">Organizer:</label>
            <input type="text" id="organizer" name="organizer" value="<?php echo htmlspecialchars($organizer); ?>" required>

            <label for="activity_date">Activity Date:</label>
            <input type="date" id="activity_date" name="activity_date" value="<?php echo htmlspecialchars($activity_date); ?>" required>

            <div class="merit-time-wrapper">
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

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($description); ?></textarea>

            <input type="submit" name="submit" value="Add Merit Record">
        </form>
    </div>
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