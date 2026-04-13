<?php
// partials/navbar.php
// Ensure $base_path is defined for correct link generation
if (!isset($base_path)) {
    $base_path = "";
}


if (!isset($current_page)) {
    $current_page = "";
}

function nav_active($page_name, $current_page) {
    return $page_name === $current_page ? "nav-link active" : "nav-link";
}
?>

<nav class="main-navbar">
    <div class="nav-brand">
        <a href="<?php echo $base_path; ?>dashboard.php" class="nav-brand-link">
            <div class="nav-brand-logo">JX</div>
            <div class="nav-brand-text">
                <strong>JX Student CoCo Hub</strong>
                <span>Co-Curricular Management System</span>
            </div>
        </a>
    </div>

    <div class="nav-menu">
        <a class="<?php echo nav_active('dashboard', $current_page); ?>" href="<?php echo $base_path; ?>dashboard.php">Dashboard</a>
        <a class="<?php echo nav_active('event', $current_page); ?>" href="<?php echo $base_path; ?>event/event_list.php">Events</a>
        <a class="<?php echo nav_active('club', $current_page); ?>" href="<?php echo $base_path; ?>club/club_list.php">Clubs</a>
        <a class="<?php echo nav_active('merit', $current_page); ?>" href="<?php echo $base_path; ?>merit/merit_list.php">Merits</a>
        <a class="<?php echo nav_active('achievement', $current_page); ?>" href="<?php echo $base_path; ?>achievement/achievement_list.php">Achievements</a>

        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) { ?>
            <a class="<?php echo nav_active('admin', $current_page); ?> nav-admin-link" href="<?php echo $base_path; ?>admin/admin_users.php">Admin</a>
        <?php } ?>
    </div>

    <div class="nav-user-area">
        <?php if (isset($_SESSION['user_name'])) { ?>
            <div class="nav-user-chip">
                <span class="nav-user-dot"></span>
                <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            </div>
            <a href="<?php echo $base_path; ?>logout.php" class="nav-logout-btn">Logout</a>
        <?php } else { ?>
            <a href="<?php echo $base_path; ?>login.php" class="nav-link">Login</a>
            <a href="<?php echo $base_path; ?>register.php" class="nav-link">Register</a>
        <?php } ?>
    </div>
</nav>