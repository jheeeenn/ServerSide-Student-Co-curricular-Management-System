<?php
include("../db.php");
require("../auth.php");

$base_path = "../";
$page_title = "Add Achievement";
$page_subtitle = "Create a new achievement record";
$show_cookie_notice = false;

$message = "";
$message_type = "";
$current_page = "achievement";

$title = "";
$type = "";
$organizer = "";
$date = "";
$description = "";

// achivement linked to an event to show in achievement list, 
// but optional when adding achievement
$event_id = "";
$event_options = [];

$user_id = $_SESSION['user_id'];

$event_query = "SELECT event_id, event_title, event_date FROM events WHERE user_id='$user_id' ORDER BY event_date DESC";
$event_result = mysqli_query($con, $event_query);

if ($event_result && mysqli_num_rows($event_result) > 0) {
    while ($event_row = mysqli_fetch_assoc($event_result)) {
        $event_options[] = $event_row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $type = mysqli_real_escape_string($con, $_POST['achievement_type']);
    $organizer = mysqli_real_escape_string($con, $_POST['organizer']);
    $date = mysqli_real_escape_string($con, $_POST['date_achieved']);
    $description = mysqli_real_escape_string($con, $_POST['description']);

    // the related event id
    $event_id = isset($_POST['event_id']) ? mysqli_real_escape_string($con, $_POST['event_id']) : "";
    $event_id_sql = !empty($event_id) ? "'$event_id'" : "NULL";

    // to prevent user from manually tampering with  the form and linking with another user's event,
    // we check if the event_id belongs to the user
    if (!empty($event_id)) {
    $check_event_query = "SELECT * FROM events WHERE event_id='$event_id' AND user_id='$user_id'";
    $check_event_result = mysqli_query($con, $check_event_query);

    if (!$check_event_result || mysqli_num_rows($check_event_result) != 1) {
        $error = "Invalid related event selected.";
    }
}

    $target_dir = "uploads/";
    $file_name = time() . "_" . basename($_FILES["certificate_file"]["name"]);
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

if (!empty($_FILES["certificate_file"]["name"])) {

        $file_name = time() . "_" . basename($_FILES["certificate_file"]["name"]);
        $target_file = $target_dir . $file_name;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type
        if ($fileType != "pdf" && $fileType != "jpg" && $fileType != "png" && $fileType != "jpeg") {
            $message = "Only PDF, JPG, JPEG, & PNG files are allowed.";
            $message_type = "error";
        } else {
            if (move_uploaded_file($_FILES["certificate_file"]["tmp_name"], $target_file)) {
                $certificate_name = $file_name;
            } else {
                $message = "Error uploading file.";
                $message_type = "error";
            }
        }
    }

    if (empty($message_type) || $message_type != "error") {
        $query = "INSERT INTO achievements 
            (user_id, event_id, title, achievement_type, organizer, date_achieved, description, certificate_file) 
            VALUES 
            ('$user_id', $event_id_sql, '$title', '$type', '$organizer', '$date', '$description', '$certificate_name')";

        if (mysqli_query($con, $query)) {
            header("Location: achievement_list.php?status=added");
            exit();
        } else {
            $message = "Database Error: " . mysqli_error($con);
            $message_type = "error";
        }
    }
}

$page_title = "Add Achievement";
include("../partials/header.php");
include("../partials/navbar.php");
?>


<div class="container module-shell achievement-shell">
    <section class="module-hero module-hero-achievement">
        <div class="module-hero-main">
            <div class="module-hero-icon achievement-accent-soft">🏅</div>
            <div class="module-hero-text-wrap">
                <h2>Add Achievement</h2>
                <p>Enter your achievement details and upload certificate proof if available.</p>
            </div>
        </div>

        <div class="module-hero-actions">
            <a href="achievement_list.php" class="module-btn module-btn-secondary">Back to Achievements</a>
        </div>
    </section>

    <section class="module-form-card">
        <?php if(!empty($message)) { ?>
            <div class="message <?php echo $message_type; ?> module-status-message">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <form method="POST" action="" enctype="multipart/form-data" class="module-form-layout">
            <div class="module-form-grid">
                <div class="module-field">
                    <label for="title">Achievement Title</label>
                    <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($title); ?>">
                </div>

                <div class="module-field">
                    <label for="achievement_type">Category</label>
                    <select name="achievement_type" id="achievement_type">
                        <option value="Award" <?php if ($type == "Award") echo "selected"; ?>>Award</option>
                        <option value="Certificate" <?php if ($type == "Certificate") echo "selected"; ?>>Certificate</option>
                        <option value="Medal" <?php if ($type == "Medal") echo "selected"; ?>>Medal</option>
                        <option value="Other" <?php if ($type == "Other") echo "selected"; ?>>Other</option>
                    </select>
                </div>

                <!-- Optional related event dropdown -->
                <div class="module-field">
                    <label for="event_id">Related Event (Optional)</label>
                    <select name="event_id" id="event_id">
                        <option value="">-- No Related Event --</option>
                        <?php foreach ($event_options as $event) { ?>
                            <option value="<?php echo $event['event_id']; ?>" <?php if ($event_id == $event['event_id']) echo "selected"; ?>>
                                <?php echo htmlspecialchars($event['event_title'] . " (" . $event['event_date'] . ")"); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>


                <div class="module-field">
                    <label for="organizer">Organizer</label>
                    <input type="text" id="organizer" name="organizer" required value="<?php echo htmlspecialchars($organizer); ?>">
                </div>

                <div class="module-field">
                    <label for="date_achieved">Date Achieved</label>
                    <input type="date" id="date_achieved" name="date_achieved" required value="<?php echo htmlspecialchars($date); ?>">
                </div>

                <div class="module-field module-field-full">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($description); ?></textarea>
                </div>

                <div class="module-field module-field-full">
                    <label for="certificate_file">Certificate / Proof File</label>
                    <div class="module-upload-box achievement-upload-box">
                        <input type="file" id="certificate_file" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png">
                        <p class="module-upload-help">Accepted formats: PDF, JPG, JPEG, PNG</p>
                    </div>
                </div>
            </div>

            <div class="module-form-actions">
                <a href="achievement_list.php" class="module-btn module-btn-secondary">Cancel</a>
                <button type="submit" name="submit" class="module-btn module-btn-primary achievement-accent-btn">Add Achievement</button>
            </div>
        </form>
    </section>
</div>
</body>
</html>