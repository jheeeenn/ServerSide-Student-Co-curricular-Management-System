<?php
include("auth.php");
require("db.php");

$base_path = ""; // Set base path for navbar links
$page_title = "Student Co-curricular Management System";

$show_cookie_notice = true; // Set to true to show cookie notice if cookie is present

$user_id = $_SESSION['user_id'];
$event_count = 0;
$club_count = 0;
$achievement_count = 0;
$merit_count = 0;
$total_merit_hours = 0;

$event_result = mysqli_query($con, "SELECT COUNT(*) AS total FROM events WHERE user_id = '$user_id'");
if ($event_result) {
    $event_row = mysqli_fetch_assoc($event_result);
    $event_count = $event_row['total'];
}

$club_result = mysqli_query($con, "SELECT COUNT(*) AS total FROM clubs WHERE user_id = '$user_id'");
if ($club_result) {
    $club_row = mysqli_fetch_assoc($club_result);
    $club_count = $club_row['total'];
}

$achievement_result = mysqli_query($con, "SELECT COUNT(*) AS total FROM achievements WHERE user_id = '$user_id'");
if ($achievement_result) {
    $achievement_row = mysqli_fetch_assoc($achievement_result);
    $achievement_count = $achievement_row['total'];
}

$merit_result = mysqli_query($con, "SELECT COUNT(*) AS total FROM merits WHERE user_id = '$user_id'");
if ($merit_result) {
    $merit_row = mysqli_fetch_assoc($merit_result);
    $merit_count = $merit_row['total'];
}
$merit_hours_result = mysqli_query($con, "SELECT IFNULL(SUM(total_hours), 0) AS total_hours FROM merits WHERE user_id = '$user_id'");
if ($merit_hours_result) {
    $merit_hours_row = mysqli_fetch_assoc($merit_hours_result);
    $total_merit_hours = $merit_hours_row['total_hours'];
}

include("partials/header.php");
include("partials/navbar.php");
?>

    <div class="container">
        <div class="header-box">
                <h2>Dashboard</h2>

                <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>   ^-^</p>
                
                        
        </div>

        <?php
            $total_records = $event_count + $club_count + $achievement_count + $merit_count;

            $ring_cap = 10; // Set a cap for the rings to prevent overflow

            $event_percent = min(100, round(($event_count / $ring_cap) * 100));
            $club_percent = min(100, round(($club_count / $ring_cap) * 100));
            $achievement_percent = min(100, round(($achievement_count / $ring_cap) * 100));
            $merit_percent = min(100, round(($merit_count / $ring_cap) * 100));
         ?>

        <div class = "summary-hero-card">
            <div class="summary-hero-left">
                <h3>My Activity Summary</h3>
                <p class="summary-subtext">Overview of your current co-curricular records</p>

                    <div class="rings-wrap">
                    <div class="ring ring-outer" style="--p: <?php echo $event_percent; ?>;">
                    <div class="ring ring-middle" style="--p: <?php echo $club_percent; ?>;">
                    
                    <div class="ring ring-inner" style="--p: <?php echo $achievement_percent; ?>;">
                    <div class="ring ring-core" style="--p: <?php echo $merit_percent; ?>;">  

                    <div class="ring-center">
                    <span class="ring-total"><?php echo $total_records; ?></span>
                        <small>Total</small>
                    </div>
                    </div>
                    </div>
    
            
                    </div>
                    </div>
                    </div>
             </div>

    <div class="summary-hero-right">
        <div class="summary-legend">
            <span class="legend-dot dot-events"></span>
            <div>
                <strong>Events</strong>
                <p><?php echo $event_count; ?> record(s)</p>
            </div>
        </div>

        <div class="summary-legend">
            <span class="legend-dot dot-clubs"></span>
            <div>
                <strong>Clubs</strong>
                <p><?php echo $club_count; ?> record(s)</p>
            </div>
        </div>

        <div class="summary-legend">
            <span class="legend-dot dot-achievements"></span>
            <div>
                <strong>Achievements</strong>
                <p><?php echo $achievement_count; ?> record(s)</p>
            </div>
        </div>

        <div class="summary-legend">
            <span class="legend-dot dot-merits"></span>
            <div>
                <strong>Merits</strong>
                <p><?php echo $merit_count; ?> record(s)</p>
            </div>
        </div>
            
    </div>
        <div class="summary-hero-merit">
        <h3>Merit Hours</h3>
        <p class="summary-subtext">Accumulated contribution hours</p>
        <div class="merit-hours-big"><?php echo number_format((float)$total_merit_hours, 2); ?></div>
        <div class="merit-hours-unit">hours</div>
        </div>

    </div>

        <div class="module-grid">
            <div class="module-card">
                <h3>Event Tracker Module</h3>
                <p>Manage your programmes, talks, workshops, and other events.</p>
                <a href="event/event_list.php">Open Events ></a>
            </div>
            <div class="module-card">
                <h3>Club Tracker Module</h3>
                <p>Manage your club memberships, activities, and communications.</p>
                <a href="club/club_list.php">Open Clubs ></a>
            </div>
            <div class="module-card">
                <h3>Merit Tracker Module</h3>
                <p>Manage your merit points, co-co contribution hours, and service activities.</p>
                <a href="merit/merit_list.php">Open Merit tab ></a>
            </div>
            <div class="module-card">
                <h3>Achievements Tracker Module</h3>
                <p>Manage your achievements, awards, and recognition.</p>
                <a href="achievement/achievement_list.php">Open Achievements tab ></a>
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