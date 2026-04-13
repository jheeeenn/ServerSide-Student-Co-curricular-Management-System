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
$user_id = $_SESSION['user_id'];

if(!isset($_GET['club_id'])) {
    header("Location: club_list.php?status=update_error");
    exit();
}

$club_id = mysqli_real_escape_string($con, $_GET['club_id']);
$select_query = "SELECT * FROM clubs WHERE club_id='$club_id' AND user_id='$user_id'";
$select_result = mysqli_query($con, $select_query);

if(mysqli_num_rows($select_result) != 1) {
    header("Location: club_list.php?status=update_error");
    exit();
}

$row = mysqli_fetch_assoc($select_result);

if(isset($_POST['submit'])) {
    $club_name = mysqli_real_escape_string($con, trim($_POST['club_name']));
    $role = mysqli_real_escape_string($con, trim($_POST['role']));
    $join_date = mysqli_real_escape_string($con, trim($_POST['join_date']));
    $description = mysqli_real_escape_string($con, trim($_POST['description']));

    if(!empty($club_name) && !empty($role) && !empty($join_date)) {
        $check_query = "SELECT * FROM clubs WHERE user_id='$user_id' AND club_name='$club_name' AND role='$role' AND club_id != '$club_id'";
        $check_result = mysqli_query($con, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $message = "You already have another record as '$role' in '$club_name'.";
            $message_type = "error";
        } else {
            $current_date = date("Y-m-d");
            if ($join_date > $current_date) {
                $message = "Date Joined cannot be in the future.";
                $message_type = "error";
            } else {
                $update_query = "UPDATE clubs SET club_name='$club_name', role='$role', join_date='$join_date', description='$description' WHERE club_id='$club_id' AND user_id='$user_id'";
                if(mysqli_query($con, $update_query)) {
                    header("Location: club_list.php?status=updated");
                    exit();
                } else {
                    $message = "Error updating club. Please try again.";
                    $message_type = "error";
                }
            }
        }
    } else {
        $message = "Club Name, Role, and Date Joined are required fields.";
        $message_type = "error";
    }
} else {
    $club_name = $row['club_name'];
    $role = $row['role'];
    $join_date = $row['join_date'];
    $description = $row['description'];
}

include("../partials/header.php");
$current_page = "club";
include("../partials/navbar.php");
?>

<div class="container module-shell club-shell">
    <section class="module-hero module-hero-club">
        <div class="module-hero-main">
            <div class="module-hero-icon club-accent-soft">✏️</div>
            <div class="module-hero-text-wrap">
                <h2>Edit Club</h2>
                <p>Update your club membership details and role information.</p>
            </div>
        </div>

        <div class="module-hero-actions">
            <a href="club_list.php" class="module-btn module-btn-secondary">Back to Clubs</a>
        </div>
    </section>

    <section class="module-form-card">
        <?php if(!empty($message)) { ?>
            <div class="message <?php echo $message_type; ?> module-status-message">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <form method="POST" action="" class="module-form-layout">
            <div class="module-form-grid">
                <div class="module-field">
                    <label for="club_name">Club Name</label>
                    <input type="text" id="club_name" name="club_name" value="<?php echo htmlspecialchars($club_name); ?>" required>
                </div>

                <div class="module-field">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="">-- Select a Role --</option>
                        <option value="President" <?php if($role == 'President') echo 'selected'; ?>>President</option>
                        <option value="Vice President" <?php if($role == 'Vice President') echo 'selected'; ?>>Vice President</option>
                        <option value="Secretary" <?php if($role == 'Secretary') echo 'selected'; ?>>Secretary</option>
                        <option value="Treasurer" <?php if($role == 'Treasurer') echo 'selected'; ?>>Treasurer</option>
                        <option value="Committee Member" <?php if($role == 'Committee Member') echo 'selected'; ?>>Committee Member</option>
                        <option value="Member" <?php if($role == 'Member') echo 'selected'; ?>>Member</option>
                    </select>
                </div>

                <div class="module-field">
                    <label for="join_date">Date Joined</label>
                    <input type="date" id="join_date" name="join_date" value="<?php echo htmlspecialchars($join_date); ?>" max="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="module-field module-field-full">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="6"><?php echo htmlspecialchars($description); ?></textarea>
                </div>
            </div>

            <div class="module-form-actions">
                <a href="club_list.php" class="module-btn module-btn-secondary">Cancel</a>
                <button type="submit" name="submit" class="module-btn module-btn-primary club-accent-btn">Update Club</button>
            </div>
        </form>
    </section>
</div>
</body>
</html>