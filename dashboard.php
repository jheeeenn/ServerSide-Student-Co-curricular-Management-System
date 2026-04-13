<?php
include("auth.php");
require("db.php");

$base_path = ""; // Set base path for navbar links
$page_title = "Student Co-curricular Management System";

$show_cookie_notice = true; // Set to true to show cookie notice if cookie is present

$hide_topbar = true;

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


// Calculate linked records and percentages for dashboard insights
    $event_linked_club_count = 0;
    $achievement_linked_event_count = 0;
    $merit_linked_event_count = 0;
    $merit_linked_club_count = 0;

    $event_linked_club_percent = 0;
    $achievement_linked_event_percent = 0;
    $merit_linked_event_percent = 0;
    $merit_linked_club_percent = 0;

    /* Event -> Club */
    $event_link_query = "SELECT COUNT(*) AS total FROM events WHERE user_id='$user_id' AND club_id IS NOT NULL";
    $event_link_result = mysqli_query($con, $event_link_query);
    if ($event_link_result) {
        $event_link_row = mysqli_fetch_assoc($event_link_result);
        $event_linked_club_count = (int)$event_link_row['total'];
    }
    if ($event_count > 0) {
        $event_linked_club_percent = round(($event_linked_club_count / $event_count) * 100);
    }

    /* Achievement -> Event */
    $achievement_link_query = "SELECT COUNT(*) AS total FROM achievements WHERE user_id='$user_id' AND event_id IS NOT NULL";
    $achievement_link_result = mysqli_query($con, $achievement_link_query);
    if ($achievement_link_result) {
        $achievement_link_row = mysqli_fetch_assoc($achievement_link_result);
        $achievement_linked_event_count = (int)$achievement_link_row['total'];
    }
    if ($achievement_count > 0) {
        $achievement_linked_event_percent = round(($achievement_linked_event_count / $achievement_count) * 100);
    }

    /* Merit -> Event */
    $merit_event_link_query = "SELECT COUNT(*) AS total FROM merits WHERE user_id='$user_id' AND event_id IS NOT NULL";
    $merit_event_link_result = mysqli_query($con, $merit_event_link_query);
    if ($merit_event_link_result) {
        $merit_event_link_row = mysqli_fetch_assoc($merit_event_link_result);
        $merit_linked_event_count = (int)$merit_event_link_row['total'];
    }
    if ($merit_count > 0) {
        $merit_linked_event_percent = round(($merit_linked_event_count / $merit_count) * 100);
    }

    /* Merit -> Club */
    $merit_club_link_query = "SELECT COUNT(*) AS total FROM merits WHERE user_id='$user_id' AND club_id IS NOT NULL";
    $merit_club_link_result = mysqli_query($con, $merit_club_link_query);
    if ($merit_club_link_result) {
        $merit_club_link_row = mysqli_fetch_assoc($merit_club_link_result);
        $merit_linked_club_count = (int)$merit_club_link_row['total'];
    }
    if ($merit_count > 0) {
        $merit_linked_club_percent = round(($merit_linked_club_count / $merit_count) * 100);
    }
    


include("partials/header.php");
$current_page = "dashboard";
include("partials/navbar.php");
?>

    <div class="container dashboard-shell">

    <?php
        $total_records = $event_count + $club_count + $achievement_count + $merit_count;
    ?>
    <div class="auth-bg-glass auth-bg-glass-one"></div>
    <div class="auth-bg-glass auth-bg-glass-two"></div>
    <section class="dashboard-hero">
        <div class="dashboard-hero-left">
            <span class="dashboard-badge">Co-Curricular Dashboard</span>

            <?php
                date_default_timezone_set('Asia/Singapore');
                $current_hour = date('H');

                if ($current_hour >= 5 && $current_hour < 12) {
                    $greeting = "Good morning";
                } elseif ($current_hour >= 12 && $current_hour < 18) {
                    $greeting = "Good afternoon";
                } else {
                    $greeting = "Good evening";
                }
            ?>
            <h2><?php echo $greeting; ?>, <?php echo htmlspecialchars($_SESSION['user_name']); ?> 👋</h2>
            <p class="dashboard-hero-text">
                Track your events, clubs, merit contribution hours, and achievements in one organised space.
            </p>

            <div class="dashboard-hero-actions">
                <a href="event/event_list.php" class="hero-btn hero-btn-event">Events</a>
                <a href="club/club_list.php" class="hero-btn hero-btn-club">Clubs</a>
                <a href="merit/merit_list.php" class="hero-btn hero-btn-merit">Merits</a>
                <a href="achievement/achievement_list.php" class="hero-btn hero-btn-achievement">Achievements</a>
            </div>
        </div>

        <div class="dashboard-hero-right">
            <div class="profile-chip">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                </div>
                <div class="profile-meta">
                    <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) { ?>
                        <span>Admin</span>
                    <?php } ?>
                </div>
            </div>

            <a href="logout.php" class="hero-logout">Logout</a>
        </div>
    </section>

    <section class="dashboard-stats-grid">
        <div class="dashboard-stat-card stat-event">
            <span class="stat-label">Events</span>
            <h3><?php echo $event_count; ?></h3>
            <p>Total event records</p>
        </div>

        <div class="dashboard-stat-card stat-club">
            <span class="stat-label">Clubs</span>
            <h3><?php echo $club_count; ?></h3>
            <p>Total club records</p>
        </div>

        <div class="dashboard-stat-card stat-merit">
            <span class="stat-label">Merit Hours</span>
            <h3><?php echo number_format((float)$total_merit_hours, 2); ?></h3>
            <p>Accumulated contribution hours</p>
        </div>

        <div class="dashboard-stat-card stat-achievement">
            <span class="stat-label">Achievements</span>
            <h3><?php echo $achievement_count; ?></h3>
            <p>Total achievement records</p>
        </div>
    </section>

    <section class="dashboard-main-grid">
        <div class="dashboard-modules-card">
            <div class="section-heading">
                <h3>Modules</h3>
                <p>Go directly to the module you want to manage.</p>
            </div>

            <div class="dashboard-module-list">
                <a class="dashboard-module-item module-event" href="event/event_list.php">
                    <div class="module-icon">📅</div>
                    <div class="module-info">
                        <h4>Event Tracker</h4>
                        <p>Programmes, workshops, competitions, and talks.</p>
                    </div>
                    <span class="module-arrow">›</span>
                </a>

                <a class="dashboard-module-item module-club" href="club/club_list.php">
                    <div class="module-icon">👥</div>
                    <div class="module-info">
                        <h4>Club Tracker</h4>
                        <p>Memberships, societies, and leadership roles.</p>
                    </div>
                    <span class="module-arrow">›</span>
                </a>

                <a class="dashboard-module-item module-merit" href="merit/merit_list.php">
                    <div class="module-icon">⏳</div>
                    <div class="module-info">
                        <h4>Merit Tracker</h4>
                        <p>Contribution hours and service participation.</p>
                    </div>
                    <span class="module-arrow">›</span>
                </a>

                <a class="dashboard-module-item module-achievement" href="achievement/achievement_list.php">
                    <div class="module-icon">🏆</div>
                    <div class="module-info">
                        <h4>Achievement Tracker</h4>
                        <p>Awards, certificates, and recognitions.</p>
                    </div>
                    <span class="module-arrow">›</span>
                </a>
            </div>
        </div>

       <?php
            $ring_cap = max(4, $total_records, $event_count, $club_count, $achievement_count, $merit_count);

            $event_percent = $ring_cap > 0 ? min(100, round(($event_count / $ring_cap) * 100)) : 0;
            $club_percent = $ring_cap > 0 ? min(100, round(($club_count / $ring_cap) * 100)) : 0;
            $achievement_percent = $ring_cap > 0 ? min(100, round(($achievement_count / $ring_cap) * 100)) : 0;
            $merit_percent = $ring_cap > 0 ? min(100, round(($merit_count / $ring_cap) * 100)) : 0;
        ?>

        <div class="dashboard-side-card dashboard-ring-card">
            <div class="section-heading">
                <h3>Overview</h3>
                <p>Your current co-curricular snapshot.</p>
            </div>

            <div class="dashboard-ring-layout">
                <div class="dashboard-ring-wrap">
                    <div class="ring ring-outer dashboard-ring-outer" style="--p: <?php echo $event_percent; ?>;">
                        <div class="ring ring-middle dashboard-ring-middle" style="--p: <?php echo $club_percent; ?>;">
                            <div class="ring ring-inner dashboard-ring-inner" style="--p: <?php echo $achievement_percent; ?>;">
                                <div class="ring ring-core dashboard-ring-core" style="--p: <?php echo $merit_percent; ?>;">
                                    <div class="ring-center dashboard-ring-center">
                                        <span class="ring-total"><?php echo $total_records; ?></span>
                                        <small>Total</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-ring-legend">
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

                    <div class="dashboard-merit-mini">
                        <span class="dashboard-merit-mini-label">Merit Hours</span>
                        <strong><?php echo number_format((float)$total_merit_hours, 2); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-linkage-card">
    <div class="section-heading">
        <h3>Insights</h3>
        <p>Some interesting patterns in your data.</p>
    </div>

    <div class="dashboard-linkage-grid">
        <div class="dashboard-linkage-item">
            <div class="dashboard-linkage-arc dashboard-linkage-arc-event" style="--p: <?php echo $event_linked_club_percent; ?>;"></div>
            <div class="dashboard-linkage-text">
                <h4><?php echo $event_linked_club_percent; ?>%</h4>
                <p>Event ↔ Club</p>
                <span><?php echo $event_linked_club_count; ?> linked record(s)</span>
            </div>
        </div>

        <div class="dashboard-linkage-item">
            <div class="dashboard-linkage-arc dashboard-linkage-arc-achievement" style="--p: <?php echo $achievement_linked_event_percent; ?>;"></div>
            <div class="dashboard-linkage-text">
                <h4><?php echo $achievement_linked_event_percent; ?>%</h4>
                <p>Achievement ↔ Event</p>
                <span><?php echo $achievement_linked_event_count; ?> linked record(s)</span>
            </div>
        </div>

        <div class="dashboard-linkage-item">
            <div class="dashboard-linkage-arc dashboard-linkage-arc-merit-event" style="--p: <?php echo $merit_linked_event_percent; ?>;"></div>
            <div class="dashboard-linkage-text">
                <h4><?php echo $merit_linked_event_percent; ?>%</h4>
                <p>Merit from Event</p>
                <span><?php echo $merit_linked_event_count; ?> linked record(s)</span>
            </div>
        </div>

        <div class="dashboard-linkage-item">
            <div class="dashboard-linkage-arc dashboard-linkage-arc-merit-club" style="--p: <?php echo $merit_linked_club_percent; ?>;"></div>
            <div class="dashboard-linkage-text">
                <h4><?php echo $merit_linked_club_percent; ?>%</h4>
                <p>Merit from Club</p>
                <span><?php echo $merit_linked_club_count; ?> linked record(s)</span>
            </div>
        </div>
    </div>
</section>

    


</div>

</body>
</html>