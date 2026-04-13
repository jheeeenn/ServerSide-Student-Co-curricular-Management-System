<?php

include("../db.php");
require("../auth.php");

// Only allow access to admin users
if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../dashboard.php");
    exit();
}


$base_path = "../";
$page_title = "Admin Panel";
$page_subtitle = "View registered users and basic usage summaries";
$show_cookie_notice = false;
$hide_topbar = true;

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$role_filter = isset($_GET['role']) ? trim($_GET['role']) : "";

$search_escaped = mysqli_real_escape_string($con, $search);

$where = [];

if ($search !== "") {
    $where[] = "(users.name LIKE '%$search_escaped%' OR users.email LIKE '%$search_escaped%' OR users.user_id LIKE '%$search_escaped%')";
}

if ($role_filter === "admin") {
    $where[] = "users.is_admin = 1";
} elseif ($role_filter === "user") {
    $where[] = "users.is_admin = 0";
}

$where_sql = "";
if (!empty($where)) {
    $where_sql = "WHERE " . implode(" AND ", $where);
}

/*
$query = "
    SELECT 
        users.user_id,
        users.name,
        users.email,
        users.created_at,
        users.is_admin,

        (SELECT COUNT(*) 
         FROM events e 
         WHERE e.user_id = users.user_id) AS total_events,

        (SELECT COUNT(*) 
         FROM clubs c 
         WHERE c.user_id = users.user_id) AS total_clubs,

        (SELECT COUNT(*) 
         FROM merits m 
         WHERE m.user_id = users.user_id) AS total_merits,

        (SELECT COUNT(*) 
         FROM achievements a 
         WHERE a.user_id = users.user_id) AS total_achievements

    FROM users
    ORDER BY users.user_id ASC
";
*/
$query = "
    SELECT 
        users.user_id,
        users.name,
        users.email,
        users.created_at,
        users.is_admin,

        (SELECT COUNT(*) FROM events e WHERE e.user_id = users.user_id) AS total_events,
        (SELECT COUNT(*) FROM clubs c WHERE c.user_id = users.user_id) AS total_clubs,
        (SELECT COUNT(*) FROM merits m WHERE m.user_id = users.user_id) AS total_merits,
        (SELECT COUNT(*) FROM achievements a WHERE a.user_id = users.user_id) AS total_achievements

    FROM users
    $where_sql
    ORDER BY users.user_id ASC
";

$result = mysqli_query($con, $query);

$users = [];
$total_users = 0;
$total_admins = 0;
$total_normal_users = 0;
$total_all_records = 0;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
        $total_users++;

        if ((int)$row['is_admin'] === 1) {
            $total_admins++;
        } else {
            $total_normal_users++;
        }

        $total_all_records +=
            (int)$row['total_events'] +
            (int)$row['total_clubs'] +
            (int)$row['total_merits'] +
            (int)$row['total_achievements'];
    }
}

include("../partials/header.php");
$current_page = "admin";
include("../partials/navbar.php");

?>
    <div class="container admin-shell">

    <section class="admin-hero">
        <div class="admin-hero-left">
            <span class="admin-badge">Admin Management</span>
            <h2>User Overview</h2>
            <p class="admin-hero-text">
                Review registered accounts, monitor usage summaries, and manage access across the system.
            </p>
        </div>

        <div class="admin-hero-right">
            <div class="admin-profile-card">
                <div class="admin-profile-icon">🛡️</div>
                <div>
                    <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                    <span>Administrator access</span>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-stats-grid">
        <div class="admin-stat-card">
            <span class="admin-stat-label">Total Users</span>
            <h3><?php echo $total_users; ?></h3>
            <p>Accounts in current view</p>
        </div>

        <div class="admin-stat-card">
            <span class="admin-stat-label">Admins</span>
            <h3><?php echo $total_admins; ?></h3>
            <p>Administrator accounts</p>
        </div>

        <div class="admin-stat-card">
            <span class="admin-stat-label">Normal Users</span>
            <h3><?php echo $total_normal_users; ?></h3>
            <p>Standard user accounts</p>
        </div>

        <div class="admin-stat-card">
            <span class="admin-stat-label">Activity Records</span>
            <h3><?php echo $total_all_records; ?></h3>
            <p>Total linked module records</p>
        </div>
    </section>

    <section class="admin-filter-card">
        <form method="get" class="admin-filter-form">
            <div class="admin-filter-group admin-filter-search">
                <label for="search">Search user</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    placeholder="Search by name, email, or user ID"
                    value="<?php echo htmlspecialchars($search); ?>"
                >
            </div>

            <div class="admin-filter-group admin-filter-role">
                <label for="role">Role</label>
                <select name="role" id="role">
                    <option value="">All Roles</option>
                    <option value="admin" <?php echo ($role_filter === "admin") ? "selected" : ""; ?>>Admin</option>
                    <option value="user" <?php echo ($role_filter === "user") ? "selected" : ""; ?>>User</option>
                </select>
            </div>

            <div class="admin-filter-actions">
                <button type="submit" class="admin-btn admin-btn-primary">Apply</button>
                <a href="admin_users.php" class="admin-btn admin-btn-secondary">Reset</a>
            </div>
        </form>
    </section>

    <section class="admin-table-card">
        <div class="section-heading admin-section-heading">
            <h3>Registered Users</h3>
            <p>
                Showing <?php echo count($users); ?> user(s)
                <?php if ($search !== "" || $role_filter !== "") { ?>
                    in the filtered result
                <?php } ?>
            </p>
        </div>

        <?php if (!empty($users)) { ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Activity Summary</th>
                            <th>Registered</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; ?>
                        <?php foreach ($users as $row) { ?>
                            <tr>
                                <td class="admin-index-cell"><?php echo $count; ?></td>

                                <td>
                                    <div class="admin-user-cell">
                                        <div class="admin-user-avatar">
                                            <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                        </div>
                                        <div class="admin-user-meta">
                                            <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                                            <span><?php echo htmlspecialchars($row['email']); ?></span>
                                            <small>ID: <?php echo htmlspecialchars($row['user_id']); ?></small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <?php if ((int)$row['is_admin'] === 1) { ?>
                                        <span class="admin-role-badge admin-role-admin">Admin</span>
                                    <?php } else { ?>
                                        <span class="admin-role-badge admin-role-user">User</span>
                                    <?php } ?>
                                </td>

                                <td>
                                    <div class="admin-activity-badges">
                                        <span class="activity-pill pill-event">Evt <?php echo (int)$row['total_events']; ?></span>
                                        <span class="activity-pill pill-club">Club <?php echo (int)$row['total_clubs']; ?></span>
                                        <span class="activity-pill pill-merit">Merit <?php echo (int)$row['total_merits']; ?></span>
                                        <span class="activity-pill pill-achievement">Ach <?php echo (int)$row['total_achievements']; ?></span>
                                    </div>
                                </td>

                                <td>
                                    <div class="admin-date-cell">
                                        <?php echo htmlspecialchars($row['created_at']); ?>
                                    </div>
                                </td>

                                
                            </tr>
                            <?php $count++; ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="admin-empty-state">
                <div class="admin-empty-icon">👤</div>
                <h3>No users found</h3>
                <p>Try changing the search term or reset the filters.</p>
                <a href="admin_users.php" class="admin-btn admin-btn-secondary">Clear Filters</a>
            </div>
        <?php } ?>
    </section>
</div>

</body>
</html>