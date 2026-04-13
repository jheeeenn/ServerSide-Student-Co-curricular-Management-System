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
include("../partials/navbar.php");
?>

<div style="position: absolute; top: 18px; right: 30px; z-index: 9999;">
    <form method="POST" style="margin: 0;">
        <button type="submit" name="toggle_theme" class="btn" style="background-color: #343a40; color: white; border: 1px solid #fff;">Toggle Theme</button>
    </form>
</div>

<style>
    .header-box, .filter-box, .table-box, .page-box {
        background-color: #ffffff !important;
    }

    <?php if ($theme == 'dark') { ?>
    body { background-image: none !important; background-color: #121212 !important; }
    .header-box, .filter-box, .table-box, .page-box { background-color: #1e1e1e !important; color: #ffffff !important; border: 1px solid #333 !important; }
    h1, h2, h3, h4, label, th, td, p { color: #ffffff !important; }
    th { background-color: #333333 !important; }
    input, select, textarea { background-color: #333333 !important; color: #ffffff !important; border: 1px solid #555 !important; }
    .glow-text { color: #e0e0e0 !important; text-shadow: 0px 0px 8px rgba(255, 255, 255, 0.5); }
    <?php } ?>
</style>

<div class="container">
    <div class="header-box">
        <h2 style="margin-top: 0;">Club Tracker Module</h2>
        <p>Manage and track your club memberships and roles.</p>
        <div style="clear: both;"></div>
    </div>
        
    <?php if(!empty($status_message)) { ?>
        <div class="message <?php echo $status_type; ?>">
            <?php echo $status_message; ?>
        </div>
    <?php } ?>

    <div class="top-actions" style="overflow: auto; margin-bottom: 20px;">
        <div style="float: left;">
            <a href="../dashboard.php" class="btn btn-back">Back to Dashboard</a>
        </div>
        <div style="float: right;">
            <a href="club_timeline.php" class="btn btn-filter" style="background-color: #17a2b8; color: white;">View Timeline</a>
            <a href="club_add.php" class="btn btn-add">Add New Club</a>
        </div>
    </div>

    <div class="filter-box">
        <form method="GET" action="club_list.php" class="filter-form">
            <div class="form-group group-search">
                <label for="search">Search Club Name</label>
                <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Enter club name">
            </div>
                
            <div class="form-group group-type">
                <label for="filter_type">Filter by Role Type</label>
                <select name="filter_type" id="filter_type">
                    <option value="">-- All Roles --</option>
                    <option value="leader" <?php if ($filter_type == "leader") echo "selected"; ?>>Leadership</option>
                    <option value="member" <?php if ($filter_type == "member") echo "selected"; ?>>Member</option>
                </select>
            </div>

            <div class="form-group group-sort">
                <label for="sort_order">Sort by Date</label>
                <select name="sort_order" id="sort_order">
                    <option value="DESC" <?php if ($sort_order == "DESC") echo "selected"; ?>>Newest to Oldest</option>
                    <option value="ASC" <?php if ($sort_order == "ASC") echo "selected"; ?>>Oldest to Newest</option>
                </select>
            </div>

            <div class="form-group group-btn">
                <button type="submit" class="btn btn-filter">Apply</button>
            </div>
            <div class="form-group group-btn">
                <a href="club_list.php" class="btn btn-reset">Clear Filters</a>
            </div>
        </form>
    </div>

    <div class="table-box">
        <?php if(mysqli_num_rows($result) > 0) { ?>
            <table>
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
                    while($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo htmlspecialchars($row['club_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['role']); ?></td>
                            <td><?php echo htmlspecialchars($row['join_date']); ?></td>
                            <td class="glow-text"><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <a class="action-link edit-link" href="club_edit.php?club_id=<?php echo $row['club_id']; ?>">Edit</a>
                                <a class="action-link" href="club_role_update.php?club_id=<?php echo $row['club_id']; ?>" style="color: #17a2b8; text-decoration: none; margin-right: 10px; font-weight: bold;">Promote</a>
                                <a class="action-link delete-link" href="club_delete.php?club_id=<?php echo $row['club_id']; ?>" onclick="return confirm('Are you sure you want to delete this club record?')">Delete</a>
                            </td>
                        </tr>
                    <?php $count++; } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="empty-message">You have not registered any clubs here. Start by adding new clubs.</div>
        <?php } ?>
    </div>
</div>
</body>
</html>