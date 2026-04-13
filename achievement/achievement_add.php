<?php
include("../db.php");
require("../auth.php");

$base_path = "../";
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $type = mysqli_real_escape_string($con, $_POST['achievement_type']);
    $organizer = mysqli_real_escape_string($con, $_POST['organizer']);
    $date = mysqli_real_escape_string($con, $_POST['date_achieved']);
    $description = mysqli_real_escape_string($con, $_POST['description']);

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
            $error = "Only PDF, JPG, JPEG, & PNG files are allowed.";
        } else {
            if (move_uploaded_file($_FILES["certificate_file"]["tmp_name"], $target_file)) {
                $certificate_name = $file_name;
            } else {
                $error = "Error uploading file.";
            }
        }
    }

    if (empty($error)) {
        $query = "INSERT INTO achievements 
        (user_id, title, achievement_type, organizer, date_achieved, description, certificate_file) 
        VALUES 
        ('$user_id', '$title', '$type', '$organizer', '$date', '$description', '$certificate_name')";

        if (mysqli_query($con, $query)) {
            header("Location: achievement_list.php?status=added");
            exit();
        } else {
            $error = "Database Error: " . mysqli_error($con);
        }
    }
}

$page_title = "Add Achievement";
include("../partials/header.php");
include("../partials/navbar.php");
?>


<div class="container">
    <div class="header-box">
        <h2>Add Achievement</h2>
        <p>Enter your achievement details and upload certificate proof.</p>
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
            <input type="text" id="title" name="title" required>

            <label for="achievement_type">Category:</label>
            <select name="achievement_type" id="achievement_type">
                <option value="Award">Award</option>
                <option value="Certificate">Certificate</option>
                <option value="Medal">Medal</option>
                <option value="Other">Other</option>
            </select>

            <label for="organizer">Organizer</label>
            <input type="text" id="organizer" name="organizer" required>

            <label for="date_achieved">Date Achieved:</label>
            <input type="date" id="date_achieved" name="date_achieved" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4"></textarea>

            <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px dashed #ccc;">
                <label for="certificate_file">Upload Certificate:</label>
                <input type="file" id="certificate_file" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <input type="submit" name="submit" value="Add Achievement" 
                style="background: linear-gradient(135deg, #2f6a28, #4f8f46); color: white; cursor: pointer; border: none;">
        </form>
    </div>
</div>

</body>
</html>