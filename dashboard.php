<?php
include("auth.php");

$base_path = ""; // Set base path for navbar links
$page_title = "Student Co-curricular Management System";
//$page_subtitle = "Welcome to your dashboard";
$show_cookie_notice = true; // Set to true to show cookie notice if cookie is present

include("partials/header.php");
include("partials/navbar.php");
?>

    <div class="container">
        <div class="header-box">
                <h2>Dashboard</h2>

                <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
                
                        
        </div>

        <div class="module-grid">
            <div class="module-card">
                <h3>Event Tracker Module</h3>
                <p>Manage your programmes, talks, workshops, and other events.</p>
                <a href="event/event_list.php">Open Module</a>
            </div>
            <div class="module-card">
                <h3>Club Tracker Module</h3>
                <p>Manage your club memberships, activities, and communications.</p>
                <a href="club/club_list.php">Open Module</a>
            </div>
            <div class="module-card">
                <h3>Merit Tracker Module</h3>
                <p>Manage your merit points, co-co contribution hours, and service activities.</p>
                <a href="merit/merit_list.php">Open Module</a>
            </div>
            <div class="module-card">
                <h3>Achievements Tracker Module</h3>
                <p>Manage your achievements, awards, and recognition.</p>
                <a href="achievement/achievement_list.php">Open Module</a>
            </div>
        </div>

<!--
        <div class="logout-box">
        <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
-->
    </div>
</body>
</html>