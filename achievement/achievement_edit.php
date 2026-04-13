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

if(isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($con, trim($_POST['title']));
    $achievement_type = mysqli_real_escape_string($con, trim($_POST['achievement_type']));
    $organizer = mysqli_real_escape_string($con, trim($_POST['organizer']));
    $date_achieved = mysqli_real_escape_string($con, trim($_POST['date_achieved']));
    $description = mysqli_real_escape_string($con, trim($_POST['description']));
    
    
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
include("../partials/navbar.php");
?>

<div class="container">
    <div class="header-box">
        <h2>Edit Achievement</h2>
        <p>Modify your achievement details and certificate proof.</p>
    </div>

    <?php if(!empty($message)) { ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <div class="top-actions">
        <a href="achievement_list.php" class="btn btn-back">Back to List</a>
    </div>

    <div class="table-box" style="max-width: 600px; margin: 0 auto; min-width: auto;">
        <form method="POST" action="" enctype="multipart/form-data">
            
            <label for="title">Achievement Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>

            <label for="achievement_type">Category:</label>
            <select name="achievement_type" id="achievement_type">
                <option value="Award" <?php if ($row['achievement_type'] == "Award") echo "selected"; ?>>Award</option>
                <option value="Certificate" <?php if ($row['achievement_type'] == "Certificate") echo "selected"; ?>>Certificate</option>
                <option value="Medal" <?php if ($row['achievement_type'] == "Medal") echo "selected"; ?>>Medal</option>
                <option value="Other" <?php if ($row['achievement_type'] == "Other") echo "selected"; ?>>Other</option>
            </select>

            <label for="organizer">Organizer / Issuing Body:</label>
            <input type="text" id="organizer" name="organizer" value="<?php echo htmlspecialchars($row['organizer']); ?>" required>

            <label for="date_achieved">Date Achieved:</label>
            <input type="date" id="date_achieved" name="date_achieved" value="<?php echo htmlspecialchars($row['date_achieved']); ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($row['description']); ?></textarea>

            <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px dashed #ccc;">
                <label>Current Proof:</label>
                <p style="font-size: 13px; color: #666;">
                    File: <?php echo !empty($row['certificate_file']) ? htmlspecialchars($row['certificate_file']) : "No file uploaded"; ?>
                </p>
                
                <label for="certificate_file">Upload New Certificate (Optional):</label>
                <input type="file" id="certificate_file" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <input type="submit" name="submit" value="Update Achievement" style="background: linear-gradient(135deg, #2f6a28, #4f8f46); color: white; cursor: pointer; border: none;">
        </form>
    </div>
</div>
</body>
</html>