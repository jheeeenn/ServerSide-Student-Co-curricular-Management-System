<?php
include("../db.php");
require("../auth.php");

$base_path = "../"; 
$page_title = "Club Tracker";
$page_subtitle = "Record a new role for an existing club";
$show_cookie_notice = false;

if (isset($_POST['toggle_theme'])) {
    $_SESSION['theme_mode'] = (isset($_SESSION['theme_mode']) && $_SESSION['theme_mode'] == 'dark') ? 'light' : 'dark';
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
$theme = isset($_SESSION['theme_mode']) ? $_SESSION['theme_mode'] : 'light';

$message = "";
$message_type = "";
$user_id = $_SESSION['user_id'];

if(!isset($_GET['club_id'])) {
    header("Location: club_list.php");
    exit();
}

$club_id = mysqli_real_escape_string($con, $_GET['club_id']);
$select_query = "SELECT * FROM clubs WHERE club_id='$club_id' AND user_id='$user_id'";
$select_result = mysqli_query($con, $select_query);

if(mysqli_num_rows($select_result) != 1) {
    header("Location: club_list.php");
    exit();
}

$row = mysqli_fetch_assoc($select_result);
$club_name = $row['club_name'];
$new_role = "";
$join_date = "";
$description = "";

if(isset($_POST['submit'])) {
    $new_role = mysqli_real_escape_string($con, trim($_POST['new_role']));
    $join_date = mysqli_real_escape_string($con, trim($_POST['join_date']));
    $description = mysqli_real_escape_string($con, trim($_POST['description']));

    if(!empty($new_role) && !empty($join_date)) {
        
        $check_query = "SELECT * FROM clubs WHERE user_id='$user_id' AND club_name='$club_name' AND role='$new_role'";
        $check_result = mysqli_query($con, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $message = "You already have a record as '$new_role' in this club.";
            $message_type = "error";
        } else {
            $current_date = date("Y-m-d");
            if ($join_date > $current_date) {
                $message = "Date cannot be in the future.";
                $message_type = "error";
            } else {
                $insert_query = "INSERT INTO clubs (user_id, club_name, role, join_date, description) VALUES ('$user_id', '$club_name', '$new_role', '$join_date', '$description')";
                if(mysqli_query($con, $insert_query)) {
                    header("Location: club_list.php?status=role_added");
                    exit();
                } else {
                    $message = "Error recording new role. Please try again.";
                    $message_type = "error";
                }
            }
        }
    } else {
        $message = "New Role and Date are required fields.";
        $message_type = "error";
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
    .header-box, .table-box { background-color: #ffffff !important; }
    <?php if ($theme == 'dark') { ?>
    body { background-image: none !important; background-color: #121212 !important; }
    .header-box, .table-box { background-color: #1e1e1e !important; color: #ffffff !important; border: 1px solid #333 !important; }
    h1, h2, h3, h4, label, p { color: #ffffff !important; }
    input, select, textarea { background-color: #333333 !important; color: #ffffff !important; border: 1px solid #555 !important; }
    input[readonly] { background-color: #e9ecef !important; color: #495057 !important; cursor: not-allowed; }
    <?php if ($theme == 'dark') { ?> input[readonly] { background-color: #2a2a2a !important; color: #888 !important; } <?php } ?>
    <?php } else { ?>
    input[readonly] { background-color: #e9ecef; color: #495057; cursor: not-allowed; }
    <?php } ?>
</style>

<div class="container">
    <div class="header-box">
        <h2 style="margin-top: 0;">Update Club Role</h2>
        <p>Record a promotion or new position for <strong><?php echo htmlspecialchars($club_name); ?></strong>.</p>
        <div style="clear: both;"></div>
    </div>

    <div class="top-actions" style="margin-bottom: 20px;">
        <a href="club_list.php" class="btn btn-back">Back to Clubs List</a>
    </div>

    <div class="table-box" style="max-width: 600px; margin: 0 auto;">
        <?php if(!empty($message)) { ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <form method="POST" action="">
            <label for="club_name">Club Name:</label>
            <input type="text" id="club_name" name="club_name" value="<?php echo htmlspecialchars($club_name); ?>" readonly>

            <label for="new_role">New Role:</label>
            <select id="new_role" name="new_role" required>
            <option value="">-- Select a Role --</option>
             <option value="President" <?php if($new_role == 'President') echo 'selected'; ?>>President</option>
             <option value="Vice President" <?php if($new_role == 'Vice President') echo 'selected'; ?>>Vice President</option>
             <option value="Secretary" <?php if($new_role == 'Secretary') echo 'selected'; ?>>Secretary</option>
             <option value="Treasurer" <?php if($new_role == 'Treasurer') echo 'selected'; ?>>Treasurer</option>
             <option value="Committee Member" <?php if($new_role == 'Committee Member') echo 'selected'; ?>>Committee Member</option>
             <option value="Member" <?php if($new_role == 'Member') echo 'selected'; ?>>Member</option>
            </select>

            <label for="join_date">Date of Role Change:</label>
            <input type="date" id="join_date" name="join_date" value="<?php echo htmlspecialchars($join_date); ?>" max="<?php echo date('Y-m-d'); ?>" required>

            <label for="description">Role Description:</label>
            <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($description); ?></textarea>

            <input type="submit" name="submit" value="Save New Role" style="background-color: #17a2b8;">
        </form>
    </div>
</div>
</body>
</html>