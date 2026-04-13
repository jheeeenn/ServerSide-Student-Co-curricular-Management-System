<?php
include("../db.php");
require("../auth.php");

$base_path = "../"; // Path for CSS and Navbar
$page_title = "Edit Achievement";
$page_subtitle = "Update your recognition records";
$show_cookie_notice = false;

$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "";

if(!isset($_GET['achievement_id'])) {
    header("Location: achievement_list.php?status=error");
    exit();
}

$achievement_id = mysqli_real_escape_string($con, $_GET['achievement_id']);

$select_query = "SELECT * FROM achievements WHERE achievement_id='$achievement_id' AND user_id='$user_id'";
$select_result = mysqli_query($con, $select_query);

if(mysqli_num_rows($select_result) != 1) {
    header("Location: achievement_list.php?status=error");
    exit();
}

$row = mysqli_fetch_assoc($select_result);

// Fetch related event title if linked
$event_options = [];

$event_query = "SELECT event_id, event_title, event_date FROM events WHERE user_id='$user_id' ORDER BY event_date DESC";
$event_result = mysqli_query($con, $event_query);

if ($event_result && mysqli_num_rows($event_result) > 0) {
    while ($event_row = mysqli_fetch_assoc($event_result)) {
        $event_options[] = $event_row;
    }
}

if(isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($con, trim($_POST['title']));
    $achievement_type = mysqli_real_escape_string($con, trim($_POST['achievement_type']));
    $organizer = mysqli_real_escape_string($con, trim($_POST['organizer']));
    $date_achieved = mysqli_real_escape_string($con, trim($_POST['date_achieved']));
    $description = mysqli_real_escape_string($con, trim($_POST['description']));
    $event_id = isset($_POST['event_id']) ? mysqli_real_escape_string($con, trim($_POST['event_id'])) : "";
    $event_id_sql = !empty($event_id) ? "'$event_id'" : "NULL";
    
    // Validate the ownership of the selected event if an event is chosen
    if (!empty($event_id)) {
        $check_event_query = "SELECT * FROM events WHERE event_id='$event_id' AND user_id='$user_id'";
        $check_event_result = mysqli_query($con, $check_event_query);

        if (!$check_event_result || mysqli_num_rows($check_event_result) != 1) {
            $message = "Invalid related event selected.";
            $message_type = "error";
        }
    }
    
    $file_name = $row['certificate_file'];

    if(!empty($title) && !empty($organizer) && !empty($date_achieved)) {
        
        // Handle File Upload if a new file is chosen
        if (!empty($_FILES["certificate_file"]["name"])) {
            $target_dir = "uploads/";
            $new_file = time() . "_" . basename($_FILES["certificate_file"]["name"]);
            $target_file = $target_dir . $new_file;
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if (in_array($fileType, ['pdf', 'jpg', 'jpeg', 'png'])) {
                if (move_uploaded_file($_FILES["certificate_file"]["tmp_name"], $target_file)) {
                    $file_name = $new_file; 
                }
            }
        }

        $update_query = "UPDATE achievements SET 
                 event_id=$event_id_sql,
                 title='$title', 
                 achievement_type='$achievement_type', 
                 organizer='$organizer', 
                 date_achieved='$date_achieved', 
                 description='$description',
                 certificate_file='$file_name' 
                 WHERE achievement_id='$achievement_id' AND user_id='$user_id'";

        if(mysqli_query($con, $update_query)) {
            header("Location: achievement_list.php?status=updated");
            exit();
        } else {
            $message = "Error updating record: " . mysqli_error($con);
            $message_type = "error";
        }
    } else {
        $message = "Title, Organizer, and Date are required.";
        $message_type = "error";
    }
}

include("../partials/header.php");
$current_page = "achievement";
include("../partials/navbar.php");
?>

<div class="container module-shell achievement-shell">
    <section class="module-hero module-hero-achievement">
        <div class="module-hero-main">
            <div class="module-hero-icon achievement-accent-soft">✏️</div>
            <div class="module-hero-text-wrap">
                <h2>Edit Achievement</h2>
                <p>Update your achievement details and replace the proof file if needed.</p>
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
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                </div>

                <div class="module-field">
                    <label for="achievement_type">Category</label>
                    <select name="achievement_type" id="achievement_type">
                        <option value="Award" <?php if ($row['achievement_type'] == "Award") echo "selected"; ?>>Award</option>
                        <option value="Certificate" <?php if ($row['achievement_type'] == "Certificate") echo "selected"; ?>>Certificate</option>
                        <option value="Medal" <?php if ($row['achievement_type'] == "Medal") echo "selected"; ?>>Medal</option>
                        <option value="Other" <?php if ($row['achievement_type'] == "Other") echo "selected"; ?>>Other</option>
                    </select>
                </div>
                
                <div class="module-field">
                    <label for="event_id">Related Event (Optional)</label>
                    <select name="event_id" id="event_id">
                        <option value="">-- No Related Event --</option>
                        <?php foreach ($event_options as $event) { ?>
                            <option value="<?php echo $event['event_id']; ?>" <?php if ($row['event_id'] == $event['event_id']) echo "selected"; ?>>
                                <?php echo htmlspecialchars($event['event_title'] . " (" . $event['event_date'] . ")"); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="module-field">
                    <label for="organizer">Organizer / Issuing Body</label>
                    <input type="text" id="organizer" name="organizer" value="<?php echo htmlspecialchars($row['organizer']); ?>" required>
                </div>

                <div class="module-field">
                    <label for="date_achieved">Date Achieved</label>
                    <input type="date" id="date_achieved" name="date_achieved" value="<?php echo htmlspecialchars($row['date_achieved']); ?>" required>
                </div>

                <div class="module-field module-field-full">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($row['description']); ?></textarea>
                </div>

                <div class="module-field module-field-full">
                    <label>Current Proof File</label>
                    <div class="achievement-current-proof">
                        <?php if(!empty($row['certificate_file'])) { ?>
                            <a href="uploads/<?php echo $row['certificate_file']; ?>" target="_blank" class="achievement-proof-link">
                                <?php echo htmlspecialchars($row['certificate_file']); ?>
                            </a>
                        <?php } else { ?>
                            <span class="achievement-proof-empty">No file uploaded</span>
                        <?php } ?>
                    </div>
                </div>

                <div class="module-field module-field-full">
                    <label for="certificate_file">Upload New Certificate (Optional)</label>
                    <div class="module-upload-box achievement-upload-box">
                        <input type="file" id="certificate_file" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png">
                        <p class="module-upload-help">Accepted formats: PDF, JPG, JPEG, PNG</p>
                    </div>
                </div>
            </div>

            <div class="module-form-actions">
                <a href="achievement_list.php" class="module-btn module-btn-secondary">Cancel</a>
                <button type="submit" name="submit" class="module-btn module-btn-primary achievement-accent-btn">Update Achievement</button>
            </div>
        </form>
    </section>
</div>
</body>
</html>