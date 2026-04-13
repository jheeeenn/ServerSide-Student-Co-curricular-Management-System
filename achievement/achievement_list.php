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


$query = "SELECT * FROM achievements WHERE user_id='$user_id'";

if (!empty($search)) {
    $query .= " AND title LIKE '%" . mysqli_real_escape_string($con, $search) . "%'";
}

if (!empty($type)) {
    $query .= " AND achievement_type = '" . mysqli_real_escape_string($con, $type) . "'";
}

$query .= " ORDER BY date_achieved $sort";
$result = mysqli_query($con, $query);

include("../partials/header.php");
include("../partials/navbar.php");
?>

<div class="container">

    <div class="header-box">
        <h2>🏆 Achievements Tracker</h2>
        <p>Keep track of your achievements and milestones.</p>
    </div>

    <?php if (!empty($status_message)) { ?>
        <div class="message <?php echo $status_type; ?>">
            <?php echo $status_message; ?>
        </div>
    <?php } ?>

    <div class="top-actions">
        <a href="../dashboard.php" class="btn btn-back">Back to Dashboard</a>
        <a href="achievement_add.php" class="btn btn-add">Add Achievement</a>
    </div>

    <div class="filter-box">
        <form method="GET" class="filter-form">
            <div class="form-group group-search">
                <label>Search Title</label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search title...">
            </div>

            <div class="form-group group-type">
                <label>Category</label>
                <select name="type">
                    <option value="">All Categories</option>
                    <option value="Award" <?php if($type=="Award") echo "selected"; ?>>Award</option>
                    <option value="Certificate" <?php if($type=="Certificate") echo "selected"; ?>>Certificate</option>
                    <option value="Medal" <?php if($type=="Medal") echo "selected"; ?>>Medal</option>
                    <option value="Other" <?php if($type=="Other") echo "selected"; ?>>Other</option>
                </select>
            </div>

            <div class="form-group group-sort">
                <label>Sort Date</label>
                <select name="sort">
                    <option value="DESC" <?php if($sort=="DESC") echo "selected"; ?>>Newest First</option>
                    <option value="ASC" <?php if($sort=="ASC") echo "selected"; ?>>Oldest First</option>
                </select>
            </div>

            <div class="form-group group-btn">
                <button type="submit" class="btn btn-filter">Apply</button>
            </div>

            <div class="form-group group-btn">
                <a href="achievement_list.php" class="btn btn-reset">Reset</a>
            </div>
        </form>
    </div>

    <div class="table-box">
        <?php if(mysqli_num_rows($result) > 0) { ?>
        <table>
            <thead>
                <tr>
                    <th style="min-width: 50px;">#</th>
                    <th>Achievement Details</th>
                    <th>Category</th>
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
                        <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
                        <small style="color: #666;"><?php echo htmlspecialchars($row['description']); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($row['achievement_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['organizer']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_achieved']); ?></td>
                    <td>
                        <?php if(!empty($row['certificate_file'])) { ?>
                            <a href="uploads/<?php echo $row['certificate_file']; ?>" target="_blank" class="action-link edit-link">View File</a>
                        <?php } else { ?>
                            <span style="color:#aaa; font-style: italic;">No Proof</span>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="achievement_edit.php?achievement_id=<?php echo $row['achievement_id']; ?>" class="action-link edit-link">Edit</a>
                        <a href="achievement_delete.php?achievement_id=<?php echo $row['achievement_id']; ?>" class="action-link delete-link">
                           Delete
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } else { ?>
        <div class="empty-message">
            🎯 No achievements found.<br><br>
            Start adding your accomplishments to build your profile!
        </div>
        <?php } ?>
    </div>
</div>
</body>
</html>