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
$status_message = "";
$status_type = "";

if (isset($_GET['status'])) {
    if ($_GET['status'] == "added") {
        $status_message = "Club added successfully.";
        $status_type = "success";
    } elseif ($_GET['status'] == "updated") {
        $status_message = "Club updated successfully.";
        $status_type = "success";
    } elseif ($_GET['status'] == "deleted") {
        $status_message = "Club deleted successfully.";
        $status_type = "success";
    } elseif ($_GET['status'] == "role_added") {
        $status_message = "New role recorded successfully.";
        $status_type = "success";
    } elseif ($_GET['status'] == "delete_error") {
        $status_message = "Unable to delete club. Please try again.";
        $status_type = "error";
    } elseif ($_GET['status'] == "update_error") {
        $status_message = "Unable to update club. Please try again.";
        $status_type = "error";
    }
}

$search = "";
$filter_type = "";
$sort_order = "DESC";

if(isset($_GET['search'])) {
    $search = mysqli_real_escape_string($con, trim($_GET['search']));
}
if(isset($_GET['filter_type'])) {
    $filter_type = mysqli_real_escape_string($con, trim($_GET['filter_type']));
}
if(isset($_GET['sort_order'])) {
    $sort_order = mysqli_real_escape_string($con, trim($_GET['sort_order']));
}

$query = "SELECT * FROM clubs WHERE user_id='$user_id'";

if(!empty($search)) {
    $query .= " AND club_name LIKE '%$search%'";
}

if(!empty($filter_type)) {
    if ($filter_type == 'leader') { 
        $query .= " AND (role LIKE '%president%' OR role LIKE '%vice%' OR role LIKE '%secretary%' OR role LIKE '%treasurer%' OR role LIKE '%director%' OR role LIKE '%chairman%' OR role LIKE '%head%' OR role LIKE '%manager%' OR role LIKE '%coordinator%' OR role LIKE '%captain%')"; 
    } else if ($filter_type == 'member') { 
        $query .= " AND (role NOT LIKE '%president%' AND role NOT LIKE '%vice%' AND role NOT LIKE '%secretary%' AND role NOT LIKE '%treasurer%' AND role NOT LIKE '%director%' AND role NOT LIKE '%chairman%' AND role NOT LIKE '%head%' AND role NOT LIKE '%manager%' AND role NOT LIKE '%coordinator%' AND role NOT LIKE '%captain%')"; 
    }
}

$query .= " ORDER BY join_date $sort_order";
$result = mysqli_query($con, $query);

include("../partials/header.php");
$current_page = "club";
include("../partials/navbar.php");
?>

<div class="container module-shell club-shell">

    <?php
        $total_clubs = 0;
        $leadership_count = 0;
        $member_count = 0;
        $latest_join_date = "-";

        $stats_query = "SELECT role, join_date FROM clubs WHERE user_id='$user_id' ORDER BY join_date DESC";
        $stats_result = mysqli_query($con, $stats_query);

        if ($stats_result && mysqli_num_rows($stats_result) > 0) {
            while ($stats_row = mysqli_fetch_assoc($stats_result)) {
                $total_clubs++;

                $role_lower = strtolower($stats_row['role']);
                if (
                    strpos($role_lower, 'president') !== false ||
                    strpos($role_lower, 'vice') !== false ||
                    strpos($role_lower, 'secretary') !== false ||
                    strpos($role_lower, 'treasurer') !== false ||
                    strpos($role_lower, 'director') !== false ||
                    strpos($role_lower, 'chairman') !== false ||
                    strpos($role_lower, 'head') !== false ||
                    strpos($role_lower, 'manager') !== false ||
                    strpos($role_lower, 'coordinator') !== false ||
                    strpos($role_lower, 'captain') !== false
                ) {
                    $leadership_count++;
                } else {
                    $member_count++;
                }

                if ($latest_join_date === "-") {
                    $latest_join_date = $stats_row['join_date'];
                }
            }
        }
    ?>

    <section class="module-hero module-hero-club">
        <div class="module-hero-main">
            <div class="module-hero-icon club-accent-soft">👥</div>
            <div class="module-hero-text-wrap">
                <h2>Club Tracker</h2>
                <p>Manage your club memberships, leadership roles, and participation history.</p>
            </div>
        </div>

        <div class="module-hero-actions">
            <a href="club_timeline.php" class="module-btn module-btn-ghost">View Timeline</a>
            <a href="club_add.php" class="module-btn module-btn-primary club-accent-btn">+ Add Club</a>
        </div>
    </section>

    <?php if(!empty($status_message)) { ?>
        <div class="message <?php echo $status_type; ?> module-status-message">
            <?php echo $status_message; ?>
        </div>
    <?php } ?>

    <section class="module-stats-grid">
        <div class="module-stat-card">
            <div class="module-stat-icon club-accent-soft">🏛️</div>
            <div>
                <h3><?php echo $total_clubs; ?></h3>
                <p>Total Clubs</p>
            </div>
        </div>

        <div class="module-stat-card">
            <div class="module-stat-icon club-accent-soft">⭐</div>
            <div>
                <h3><?php echo $leadership_count; ?></h3>
                <p>Leadership Roles</p>
            </div>
        </div>

        <div class="module-stat-card">
            <div class="module-stat-icon club-accent-soft">👤</div>
            <div>
                <h3><?php echo $member_count; ?></h3>
                <p>Member Roles</p>
            </div>
        </div>

        <div class="module-stat-card">
            <div class="module-stat-icon club-accent-soft">📅</div>
            <div>
                <h3><?php echo htmlspecialchars($latest_join_date); ?></h3>
                <p>Latest Join Date</p>
            </div>
        </div>
    </section>

    <section class="module-filter-card">
        <form method="GET" action="club_list.php" class="module-filter-form">
            <div class="module-filter-full">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search club name...">
            </div>

            <div>
                <select name="filter_type">
                    <option value="">All Roles</option>
                    <option value="leader" <?php if ($filter_type == "leader") echo "selected"; ?>>Leadership</option>
                    <option value="member" <?php if ($filter_type == "member") echo "selected"; ?>>Member</option>
                </select>
            </div>

            <div>
                <select name="sort_order">
                    <option value="DESC" <?php if ($sort_order == "DESC") echo "selected"; ?>>Newest to Oldest</option>
                    <option value="ASC" <?php if ($sort_order == "ASC") echo "selected"; ?>>Oldest to Newest</option>
                </select>
            </div>

            <div class="module-filter-actions">
                <button type="submit" class="module-btn module-btn-primary club-accent-btn">Filter</button>
                <a href="club_list.php" class="module-btn module-btn-secondary">Reset</a>
            </div>
        </form>
    </section>

    <section class="module-content-card">
        <?php if(mysqli_num_rows($result) > 0) { ?>
            <div class="module-table-wrap">
                <table class="module-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Club Name</th>
                            <th>Role</th>
                            <th>Date Joined</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 1;
                        while($row = mysqli_fetch_assoc($result)) {

                            $role_lower = strtolower($row['role']);
                            $role_class = (
                                strpos($role_lower, 'president') !== false ||
                                strpos($role_lower, 'vice') !== false ||
                                strpos($role_lower, 'secretary') !== false ||
                                strpos($role_lower, 'treasurer') !== false ||
                                strpos($role_lower, 'director') !== false ||
                                strpos($role_lower, 'chairman') !== false ||
                                strpos($role_lower, 'head') !== false ||
                                strpos($role_lower, 'manager') !== false ||
                                strpos($role_lower, 'coordinator') !== false ||
                                strpos($role_lower, 'captain') !== false
                            ) ? 'club-role-leadership' : 'club-role-member';
                        ?>
                            <tr>
                                <td><?php echo $count; ?></td>
                                <td class="module-title-cell"><?php echo htmlspecialchars($row['club_name']); ?></td>
                                <td>
                                    <span class="club-inline-pill <?php echo $role_class; ?>">
                                        <?php echo htmlspecialchars($row['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($row['join_date']); ?></td>
                                <td class="module-description-cell"><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>
                                    <div class="module-action-links">
                                        <a class="module-action-link module-action-edit" href="club_edit.php?club_id=<?php echo $row['club_id']; ?>">Edit</a>
                                        <a class="module-action-link module-action-generate-merit" href="../merit/merit_add.php?club_id=<?php echo $row['club_id']; ?>">Generate Merit</a>
                                        <a class="module-action-link club-action-promote" href="club_role_update.php?club_id=<?php echo $row['club_id']; ?>">Promote</a>
                                        <a class="module-action-link module-action-delete" href="club_delete.php?club_id=<?php echo $row['club_id']; ?>">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php $count++; } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="module-empty-state">
                <div class="module-empty-icon club-accent-text">👥</div>
                <h3>No clubs found</h3>
                <p>Start by adding your first club membership record.</p>
                <a href="club_add.php" class="module-btn module-btn-primary club-accent-btn">Add Club</a>
            </div>
        <?php } ?>
    </section>
</div>
</body>
</html>