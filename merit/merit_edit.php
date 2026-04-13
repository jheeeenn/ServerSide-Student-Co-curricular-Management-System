<?php
include("../db.php");
require("../auth.php");

$base_path = "../";
$page_title = "Merit Tracker";
$page_subtitle = "Edit your merit contribution record";
$show_cookie_notice = false;

$message = "";
$message_type = "";

$user_id = $_SESSION['user_id'];

$merit_id = "";
$activity_title = "";
$activity_type = "";
$organizer = "";
$activity_date = "";
$start_time = "";
$end_time = "";
$total_hours = "";
$description = "";

// Check merit_id from URL
if (isset($_GET['merit_id'])) {
    $merit_id = mysqli_real_escape_string($con, $_GET['merit_id']);

    $query = "SELECT * FROM merits WHERE merit_id='$merit_id' AND user_id='$user_id'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        $activity_title = $row['activity_title'];
        $activity_type = $row['activity_type'];
        $organizer = $row['organizer'];
        $activity_date = $row['activity_date'];
        $start_time = $row['start_time'];
        $end_time = $row['end_time'];
        $total_hours = $row['total_hours'];
        $description = $row['description'];
    } else {
        header("Location: merit_list.php?status=update_error");
        exit();
    }
} else {
    header("Location: merit_list.php?status=update_error");
    exit();
}

// Handle update form submission
if (isset($_POST['submit'])) {
    $merit_id = mysqli_real_escape_string($con, $_POST['merit_id']);
    $activity_title = mysqli_real_escape_string($con, trim($_POST['activity_title']));
    $activity_type = mysqli_real_escape_string($con, trim($_POST['activity_type']));
    $organizer = mysqli_real_escape_string($con, trim($_POST['organizer']));
    $activity_date = mysqli_real_escape_string($con, trim($_POST['activity_date']));
    $start_time = mysqli_real_escape_string($con, trim($_POST['start_time']));
    $end_time = mysqli_real_escape_string($con, trim($_POST['end_time']));
    $description = mysqli_real_escape_string($con, trim($_POST['description']));

    if (!empty($activity_title) && !empty($organizer) && !empty($activity_date) && !empty($start_time) && !empty($end_time)) {

        $start_timestamp = strtotime($start_time);
        $end_timestamp = strtotime($end_time);

        if ($end_timestamp > $start_timestamp) {
            $hours = ($end_timestamp - $start_timestamp) / 3600;
            $total_hours = number_format($hours, 2, '.', '');

            $update_query = "UPDATE merits 
                             SET activity_title='$activity_title',
                                 activity_type='$activity_type',
                                 organizer='$organizer',
                                 activity_date='$activity_date',
                                 start_time='$start_time',
                                 end_time='$end_time',
                                 total_hours='$total_hours',
                                 description='$description'
                             WHERE merit_id='$merit_id' AND user_id='$user_id'";

            if (mysqli_query($con, $update_query)) {
                header("Location: merit_list.php?status=updated");
                exit();
            } else {
                $message = "Error updating merit record. Please try again.";
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
            <div class="module-hero-icon merit-accent-soft">✏️</div>
            <div class="module-hero-text-wrap">
                <h2>Edit Merit Record</h2>
                <p>Update your merit contribution activity and recalculate the total hours automatically.</p>
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

        <form method="POST" action="" class="module-form-layout">
            <input type="hidden" name="merit_id" value="<?php echo htmlspecialchars($merit_id); ?>">

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
                <button type="submit" name="submit" class="module-btn module-btn-primary merit-accent-btn">Update Merit Record</button>
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