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

$message = "";
$message_type = "";
$club_name = "";
$role = "";
$join_date = "";
$description = "";

if(isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];
    $club_name = mysqli_real_escape_string($con, trim($_POST['club_name']));
    $role = mysqli_real_escape_string($con, trim($_POST['role']));
    $join_date = mysqli_real_escape_string($con, trim($_POST['join_date']));
    $description = mysqli_real_escape_string($con, trim($_POST['description']));

    if(!empty($club_name) && !empty($role) && !empty($join_date)) {
        $check_query = "SELECT * FROM clubs WHERE user_id='$user_id' AND club_name='$club_name' AND role='$role'";
        $check_result = mysqli_query($con, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $message = "You already have an existing record as '$role' in '$club_name'.";
            $message_type = "error";
        } else {
            $current_date = date("Y-m-d");
            if ($join_date > $current_date) {
                $message = "Date Joined cannot be in the future.";
                $message_type = "error";
            } else {
                $query = "INSERT INTO clubs (user_id, club_name, role, join_date, description) VALUES ('$user_id', '$club_name', '$role', '$join_date', '$description')";
                if(mysqli_query($con, $query)) {
                    header("Location: club_list.php?status=added");
                    exit();
                } else {
                    $message = "Error adding club. Please try again.";
                    $message_type = "error";
                }
            }
        }
    } else {
        $message = "Club Name, Role, and Date Joined are required fields.";
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
    .header-box, .filter-box, .table-box, .page-box { background-color: #ffffff !important; }
    <?php if ($theme == 'dark') { ?>
    body { background-image: none !important; background-color: #121212 !important; }
    .header-box, .filter-box, .table-box, .page-box { background-color: #1e1e1e !important; color: #ffffff !important; border: 1px solid #333 !important; }
    h1, h2, h3, h4, label, p { color: #ffffff !important; }
    input, select, textarea { background-color: #333333 !important; color: #ffffff !important; border: 1px solid #555 !important; }
    <?php } ?>
</style>

<div class="container">
    <div class="header-box">
        <h2 style="margin-top: 0;">Add New Club</h2>
        <p>Fill in the details below to record a new club affiliation.</p>
        <div style="clear: both;"></div>
    </div>

    <div class="top-actions" style="overflow: auto; margin-bottom: 20px;">
        <div style="float: left;">
            <a href="club_list.php" class="btn btn-back">Back to Clubs List</a>
        </div>
    </div>

    <div class="table-box" style="max-width: 600px; margin: 0 auto; min-width: auto;">
        <?php if(!empty($message)) { ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <form method="POST" action="">
            <label for="club_name">Club Name:</label>
            <input type="text" id="club_name" name="club_name" value="<?php echo htmlspecialchars($club_name); ?>" placeholder = "E.g. Chess Club" required>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
             <option value="">-- Select a Role --</option>
             <option value="President" <?php if($role == 'President') echo 'selected'; ?>>President</option>
             <option value="Vice President" <?php if($role == 'Vice President') echo 'selected'; ?>>Vice President</option>
             <option value="Secretary" <?php if($role == 'Secretary') echo 'selected'; ?>>Secretary</option>
             <option value="Treasurer" <?php if($role == 'Treasurer') echo 'selected'; ?>>Treasurer</option>
             <option value="Committee Member" <?php if($role == 'Committee Member') echo 'selected'; ?>>Committee Member</option>
              <option value="Member" <?php if($role == 'Member') echo 'selected'; ?>>Member</option>
            </select>

            <label for="join_date">Date Joined:</label>
            <input type="date" id="join_date" name="join_date" value="<?php echo htmlspecialchars($join_date); ?>" max="<?php echo date('Y-m-d'); ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($description); ?></textarea>

            <input type="submit" name="submit" value="Add Club">
        </form>
    </div>
</div>
</body>
</html>