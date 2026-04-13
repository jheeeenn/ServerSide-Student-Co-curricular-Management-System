<?php 
include("../db.php");
require("../auth.php");

$base_path = "../";
$page_title = "Achievements Tracker";
$page_subtitle = "Track your awards, recognitions, and accomplishments";
$show_cookie_notice = false; 

$user_id = $_SESSION['user_id'];

$status_message = "";
$status_type = "";

if (isset($_GET['status'])) {
    if ($_GET['status'] == "added") {
        $status_message = "Achievement added successfully.";
        $status_type = "success";
    } elseif ($_GET['status'] == "updated") {
        $status_message = "Achievement updated successfully.";
        $status_type = "success";
    } elseif ($_GET['status'] == "deleted") {
        $status_message = "Achievement deleted successfully.";
        $status_type = "success";
    } elseif ($_GET['status'] == "error") {
        $status_message = "An error occurred. Please try again.";
        $status_type = "error";
    }
}

// Filters
$search = $_GET['search'] ?? "";
$type = $_GET['type'] ?? "";
$sort = $_GET['sort'] ?? "DESC";

/*
$query = "SELECT * FROM achievements WHERE user_id='$user_id'";

if (!empty($search)) {
    $query .= " AND title LIKE '%" . mysqli_real_escape_string($con, $search) . "%'";
}

if (!empty($type)) {
    $query .= " AND achievement_type = '" . mysqli_real_escape_string($con, $type) . "'";
}
    
$query .= " ORDER BY date_achieved $sort";
$result = mysqli_query($con, $query);*/

$query = "
    SELECT achievements.*, events.event_title AS related_event_title
    FROM achievements
    LEFT JOIN events ON achievements.event_id = events.event_id
    WHERE achievements.user_id='$user_id'
";

if (!empty($search)) {
    $query .= " AND achievements.title LIKE '%" . mysqli_real_escape_string($con, $search) . "%'";
}

if (!empty($type)) {
    $query .= " AND achievements.achievement_type = '" . mysqli_real_escape_string($con, $type) . "'";
}

$query .= " ORDER BY achievements.date_achieved $sort";
$result = mysqli_query($con, $query);



include("../partials/header.php");
$current_page = "achievement";
include("../partials/navbar.php");
?>

<div class="container module-shell achievement-shell">

    <?php
       $total_achievements = 0;
        $total_types = [];
        $linked_count = 0;
        $unlinked_count = 0;
        $link_percent = 0;

        $stats_query = "SELECT achievement_type, event_id FROM achievements WHERE user_id='$user_id'";
        $stats_result = mysqli_query($con, $stats_query);

        if ($stats_result && mysqli_num_rows($stats_result) > 0) {
            while ($stats_row = mysqli_fetch_assoc($stats_result)) {
                $total_achievements++;

                if (!empty($stats_row['achievement_type'])) {
                    $total_types[$stats_row['achievement_type']] = true;
                }

                if (!empty($stats_row['event_id'])) {
                    $linked_count++;
                } else {
                    $unlinked_count++;
                }
            }
        }

        $type_count = count($total_types);

        if ($total_achievements > 0) {
            $link_percent = round(($linked_count / $total_achievements) * 100);
        }
    ?>

    <section class="module-hero module-hero-achievement">
        <div class="module-hero-main">
            <div class="module-hero-icon achievement-accent-soft">🏆</div>
            <div class="module-hero-text-wrap">
                <h2>Achievement Tracker</h2>
                <p>Track your awards, recognitions, certificates, and accomplishments.</p>
            </div>
        </div>

        <div class="module-hero-actions">
            <a href="achievement_add.php" class="module-btn module-btn-primary achievement-accent-btn">+ Add Achievement</a>
        </div>
    </section>

    <?php if (!empty($status_message)) { ?>
        <div class="message <?php echo $status_type; ?> module-status-message">
            <?php echo $status_message; ?>
        </div>
    <?php } ?>

    <section class="module-stats-grid">
        <div class="module-stat-card">
            <div class="module-stat-icon achievement-accent-soft">🏆</div>
            <div>
                <h3><?php echo $total_achievements; ?></h3>
                <p>Total Achievements</p>
            </div>
        </div>

        <div class="module-stat-card">
            <div class="module-stat-icon achievement-accent-soft">✺</div>
            <div>
                <h3><?php echo $type_count; ?></h3>
                <p>Types of Awards</p>
            </div>
        </div>

        <div class="module-stat-card achievement-linkage-mini-card">
            <div class="achievement-linkage-mini-arc" style="--p: <?php echo $link_percent; ?>;"></div>
            <div class="achievement-linkage-mini-text">
                <h3><?php echo $link_percent; ?>%</h3>
                <p>Linked Event</p>
            </div>
        </div>
    </section>

    <section class="module-filter-card">
        <form method="GET" action="achievement_list.php" class="module-filter-form">
            <div class="module-filter-full">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search achievement title...">
            </div>

            <div>
                <select name="type">
                    <option value="">All Types</option>
                    <option value="Award" <?php if($type=="Award") echo "selected"; ?>>Award</option>
                    <option value="Certificate" <?php if($type=="Certificate") echo "selected"; ?>>Certificate</option>
                    <option value="Medal" <?php if($type=="Medal") echo "selected"; ?>>Medal</option>
                    <option value="Other" <?php if($type=="Other") echo "selected"; ?>>Other</option>
                </select>
            </div>

            <div>
                <select name="sort">
                    <option value="DESC" <?php if($sort=="DESC") echo "selected"; ?>>Newest First</option>
                    <option value="ASC" <?php if($sort=="ASC") echo "selected"; ?>>Oldest First</option>
                </select>
            </div>

            <div class="module-filter-actions">
                <button type="submit" class="module-btn module-btn-primary achievement-accent-btn">Filter</button>
                <a href="achievement_list.php" class="module-btn module-btn-secondary">Reset</a>
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
                            <th>Achievement Details</th>
                            <th>Category</th>
                            <th>Related Event</th>
                            <th>Organizer</th>
                            <th>Date</th>
                            <th>Proof</th>
                            <th>Actions</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; while($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td>
                                <div class="achievement-title-block">
                                    <strong class="module-title-cell"><?php echo htmlspecialchars($row['title']); ?></strong>
                                    <div class="module-description-cell"><?php echo htmlspecialchars($row['description']); ?></div>
                                </div>
                            </td>
                            <td>
                                <span class="module-tag achievement-tag"><?php echo htmlspecialchars($row['achievement_type']); ?></span>
                            </td>

                            <td>
                                <?php if (!empty($row['related_event_title'])) { ?>
                                    <span class="module-tag event-tag"><?php echo htmlspecialchars($row['related_event_title']); ?></span>
                                <?php } else { ?>
                                    <span class="achievement-proof-empty">Not linked</span>
                                <?php } ?>
                            </td>

                            <td><?php echo htmlspecialchars($row['organizer']); ?></td>
                            <td><?php echo htmlspecialchars($row['date_achieved']); ?></td>
                            <td>
                                <?php if(!empty($row['certificate_file'])) { ?>
                                    <a href="uploads/<?php echo $row['certificate_file']; ?>" target="_blank" class="achievement-proof-link">View File</a>
                                <?php } else { ?>
                                    <span class="achievement-proof-empty">No Proof</span>
                                <?php } ?>
                            </td>
                            <td>
                                <div class="module-action-links">
                                    <a href="achievement_edit.php?achievement_id=<?php echo $row['achievement_id']; ?>" class="module-action-link module-action-edit">Edit</a>
                                    <a href="achievement_delete.php?achievement_id=<?php echo $row['achievement_id']; ?>" class="module-action-link module-action-delete">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="module-empty-state">
                <div class="module-empty-icon achievement-accent-text">🏆</div>
                <h3>No achievements found</h3>
                <p>Start by adding your first achievement record.</p>
                <a href="achievement_add.php" class="module-btn module-btn-primary achievement-accent-btn">Add Achievement</a>
            </div>
        <?php } ?>
    </section>
</div>
</body>
</html>