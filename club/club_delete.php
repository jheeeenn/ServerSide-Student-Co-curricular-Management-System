<?php
include("../db.php");
require("../auth.php");

$base_path = "../"; 
$page_title = "Club Tracker";
$page_subtitle = "Manage your club memberships and roles";
$show_cookie_notice = false;

if (isset($_POST['toggle_theme'])) {
    $_SESSION['theme_mode'] = (isset($_SESSION['theme_mode']) && $_SESSION['theme_mode'] == 'dark') ? 'light' : 'dark';
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
$theme = isset($_SESSION['theme_mode']) ? $_SESSION['theme_mode'] : 'light';

$user_id = $_SESSION['user_id'];

if(!isset($_GET['club_id'])) {
    header("Location: club_list.php?status=delete_error");
    exit();
}

$club_id = mysqli_real_escape_string($con, $_GET['club_id']);

$query = "SELECT * FROM clubs WHERE club_id='$club_id' AND user_id='$user_id'";
$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) != 1) {
    header("Location: club_list.php?status=delete_error");
    exit();
}

$row = mysqli_fetch_assoc($result);

if (isset($_POST['confirm_delete'])) {
    $delete_query = "DELETE FROM clubs WHERE club_id='$club_id' AND user_id='$user_id'";
    if (mysqli_query($con, $delete_query)) {
        header("Location: club_list.php?status=deleted");
        exit();
    } else {
        header("Location: club_list.php?status=delete_error");
        exit();
    }
}

include("../partials/header.php");
include("../partials/navbar.php");
?>

<div style="position: absolute; top: 18px; right: 30px; z-index: 9999;">
    <form method="POST" style="margin: 0;">
        <button type="submit" name="toggle_theme" class="btn" style="background-color: #343a40; color: white; border: 1px solid #fff;">Toggle Theme</button>
    </form>
</div>

<style>
    .header-box, .page-box {
        background-color: #ffffff !important;
    }

    .alert {
        padding: 20px;
        background-color: #f44336;
        color: white;
        margin-bottom: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    }

    <?php if ($theme == 'dark') { ?>
    body { background-image: none !important; background-color: #121212 !important; }
    .header-box, .page-box { background-color: #1e1e1e !important; color: #ffffff !important; border: 1px solid #333 !important; }
    h2, h3, p { color: #ffffff !important; }
    <?php } ?>
</style>

<div class="container">
    <div class="header-box">
        <h2 style="margin-top: 0;">Delete Club Record</h2>
        <p>Please confirm the removal of this club affiliation.</p>
        <div style="clear: both;"></div>
    </div>

    <div class="alert">
        <strong>Warning!</strong> You are about to permanently delete your membership record for <strong><?php echo htmlspecialchars($row['club_name']); ?></strong>.
    </div>

    <div class="page-box">
        <h3 style="margin-top: 0; color: <?php echo ($theme == 'dark') ? '#17a2b8' : '#007bff'; ?>;">
            <?php echo htmlspecialchars($row['club_name']); ?>
        </h3>
        
        <p><strong>Role:</strong> <?php echo htmlspecialchars($row['role']); ?></p>
        <p><strong>Date Joined:</strong> <?php echo htmlspecialchars($row['join_date']); ?></p>
        <p><strong>Description:</strong><br> <?php echo nl2br(htmlspecialchars($row['description'])); ?></p>

        <form method="POST" style="margin-top: 25px;">
            <button type="submit" name="confirm_delete" class="btn btn-danger" style="margin-right: 10px;">Yes, Delete Record</button>
            <a href="club_list.php" class="btn btn-back">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>