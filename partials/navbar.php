<?php
// partials/navbar.php
// Ensure $base_path is defined for correct link generation
if (!isset($base_path)) {
    $base_path = "";
}
?>

<div class="navbar">
    <div class="nav-left">
        <a href="<?php echo $base_path; ?>dashboard.php">Dashboard</a>
        <a href="<?php echo $base_path; ?>event/event_list.php">Event</a>
        <a href="<?php echo $base_path; ?>club/club_list.php">Club</a>
        <a href="<?php echo $base_path; ?>merit/merit_list.php">Merit</a>
        <a href="<?php echo $base_path; ?>achievement/achievement_list.php">Achievement</a>

        <!-- Show admin link if user is admin -->
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) { ?>
            <a href="<?php echo $base_path; ?>admin/admin_users.php">Admin</a>
        <?php } ?>

    </div>

    <!-- Right side of the navbar for user profile links -->
    <div class="nav-right">

        <?php if (isset($_SESSION['user_name'])) { ?>
            <a href="#">Hi,  <?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
            <a href="<?php echo $base_path; ?>logout.php">Logout</a>
        <?php } 
        
        else { ?>
            <a href="<?php echo $base_path; ?>login.php">Login</a>
            <a href="<?php echo $base_path; ?>register.php">Register</a>
        <?php } ?>

    </div>
</div>