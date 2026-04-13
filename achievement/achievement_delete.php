<?php
include("../db.php");
require("../auth.php");

$base_path = "../";
$page_title = "Delete Achievement";
$page_subtitle = "Confirm deletion of record";
$show_cookie_notice = false;

$user_id = $_SESSION['user_id'];

// Check ID
if (!isset($_GET['achievement_id'])) {
    header("Location: achievement_list.php?status=error");
    exit();
}

$achievement_id = mysqli_real_escape_string($con, $_GET['achievement_id']);

// Get record
$query = "SELECT * FROM achievements WHERE achievement_id='$achievement_id' AND user_id='$user_id'";
$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) != 1) {
    header("Location: achievement_list.php?status=error");
    exit();
}

$row = mysqli_fetch_assoc($result);

// Handle delete
if (isset($_POST['confirm_delete'])) {

    // Delete file if exists
    if (!empty($row['certificate_file'])) {
        $file_path = "uploads/" . $row['certificate_file'];

        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    $delete_query = "DELETE FROM achievements WHERE achievement_id='$achievement_id' AND user_id='$user_id'";

    if (mysqli_query($con, $delete_query)) {
        header("Location: achievement_list.php?status=deleted");
        exit();
    } else {
        header("Location: achievement_list.php?status=error");
        exit();
    }
}

include("../partials/header.php");
include("../partials/navbar.php");
?>

<div class="container">

    <div class="header-box">
        <h2>⚠️ Delete Achievement</h2>
        <p>Please confirm before removing this record.</p>
    </div>

    <div class="page-box">

        <h3><?php echo htmlspecialchars($row['title']); ?></h3>

        <p><strong>Category:</strong> <?php echo htmlspecialchars($row['achievement_type']); ?></p>
        <p><strong>Organizer:</strong> <?php echo htmlspecialchars($row['organizer']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($row['date_achieved']); ?></p>

        <?php if(!empty($row['certificate_file'])) { ?>
            <p>
                <strong>Certificate:</strong>
                <a href="uploads/<?php echo $row['certificate_file']; ?>" target="_blank">
                    View File
                </a>
            </p>
        <?php } ?>

        <p style="color:#dc3545; font-weight:bold; margin-top:15px;">
            This action cannot be undone.
        </p>

        <form method="POST" style="margin-top:20px;">
            <button type="submit" name="confirm_delete" class="btn btn-danger"
                onclick="return confirm('Are you sure you want to delete this achievement?')">
                Yes, Delete
            </button>

            <a href="achievement_list.php" class="btn btn-back">
                Cancel
            </a>
        </form>

    </div>

</div>

</body>
</html>