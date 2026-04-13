<?php 
include("../db.php");
require("../auth.php");

$base_path = "../"; // Set base path for links in the navbar
$page_title = "Merit Tracker";
$page_subtitle = "Manage your merit contribution records";
$show_cookie_notice = false; 

$user_id = $_SESSION['user_id'];

$status_message = "";
$status_type = "";

if (isset($_GET['status'])) {
    if ($_GET['status'] == "added") {
        $status_message = "Merit record added successfully.";
        $status_type = "success";
    } elseif ($_GET['status'] == "updated") {
        $status_message = "Merit record updated successfully.";
        $status_type = "success";
    } elseif ($_GET['status'] == "deleted") {
        $status_message = "Merit record deleted successfully.";
        $status_type = "success";
    } elseif ($_GET['status'] == "delete_error") {
        $status_message = "Unable to delete merit record. Please try again.";
        $status_type = "error";
    } elseif ($_GET['status'] == "update_error") {
        $status_message = "Unable to update merit record. Please try again.";
        $status_type = "error";
    }
}

// Filters
$search = "";
$filter_type = "";
$sort_order = "DESC";

if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($con, trim($_GET['search']));
}

if (isset($_GET['filter_type'])) {
    $filter_type = mysqli_real_escape_string($con, trim($_GET['filter_type']));
}

if (isset($_GET['sort_order'])) {
    $sort_order = mysqli_real_escape_string($con, trim($_GET['sort_order']));
    if ($sort_order != "ASC" && $sort_order != "DESC") {
        $sort_order = "DESC";
    }
}

// Merit list query
$query = "SELECT * FROM merits WHERE user_id='$user_id'";

if (!empty($search)) {
    $query .= " AND activity_title LIKE '%$search%'";
}

if (!empty($filter_type)) {
    $query .= " AND activity_type = '$filter_type'";
}

$query .= " ORDER BY activity_date $sort_order";

$result = mysqli_query($con, $query);

// Total contribution hours
$total_query = "SELECT SUM(total_hours) AS total_merit_hours FROM merits WHERE user_id='$user_id'";
$total_result = mysqli_query($con, $total_query);

$total_merit_hours = 0;
if ($total_result && mysqli_num_rows($total_result) > 0) {
    $total_row = mysqli_fetch_assoc($total_result);
    if ($total_row['total_merit_hours'] != null) {
        $total_merit_hours = $total_row['total_merit_hours'];
    }
}

include("../partials/header.php");
?>
<?php include("../partials/navbar.php"); ?>

<div class="container">

    <div class="header-box">
        <h2>Merit Tracker Module</h2>
        <p>View and manage your merit contribution records.</p>
    </div>

    <?php if (!empty($status_message)) { ?>
        <div class="message <?php echo $status_type; ?>">
            <?php echo $status_message; ?>
        </div>
    <?php } ?>

    <div class="top-actions">
        <a href="../dashboard.php" class="btn btn-back">Back to Dashboard</a>
        <a href="merit_add.php" class="btn btn-add">Add New Merit Record</a>
    </div>

    <!--Total contribution hours summary -->
    <div class="merit-hours-summary-box">
        <div class="merit-hours-summary-label">Total Contribution Hours:</div>
        <div class="merit-hours-summary-value">
            <?php echo number_format($total_merit_hours, 2); ?> hours
        </div>
    </div>

    <div class="filter-box">
        <form method="GET" action="merit_list.php" class="filter-form">

            <div class="form-group group-search">
                <label for="search">Search Activity Title</label>
                <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Enter activity title">
            </div>

            <div class="form-group group-type">
                <label for="filter_type">Filter by Activity Type</label>
                <select name="filter_type" id="filter_type">
                    <option value="">-- All Types --</option>
                    <option value="Volunteering" <?php if ($filter_type == "Volunteering") echo "selected"; ?>>Volunteering</option>
                    <option value="Community Service" <?php if ($filter_type == "Community Service") echo "selected"; ?>>Community Service</option>
                    <option value="Committee Work" <?php if ($filter_type == "Committee Work") echo "selected"; ?>>Committee Work</option>
                    <option value="Club Service" <?php if ($filter_type == "Club Service") echo "selected"; ?>>Club Service</option>
                    <option value="University Service" <?php if ($filter_type == "University Service") echo "selected"; ?>>University Service</option>
                    <option value="Other" <?php if ($filter_type == "Other") echo "selected"; ?>>Other</option>
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
                <a href="merit_list.php" class="btn btn-reset">Clear Filters</a>
            </div>
        </form>
    </div>

    <div class="table-box">
        <?php if ($result && mysqli_num_rows($result) > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Activity Title</th>
                        <th>Activity Type</th>
                        <th>Organizer</th>
                        <th>Date</th>
                        <th>Total Hours</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $count = 1;
                    while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo htmlspecialchars($row['activity_title']); ?></td>
                            <td><?php echo htmlspecialchars($row['activity_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['organizer']); ?></td>
                            <td><?php echo htmlspecialchars($row['activity_date']); ?></td>
                            <td><?php echo number_format((float)$row['total_hours'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <a class="action-link edit-link" href="merit_edit.php?merit_id=<?php echo $row['merit_id']; ?>">
                                    Edit
                                </a>

                                <a 
                                    href="#"
                                    class="action-link delete-link open-delete-modal"
                                    data-id="<?php echo $row['merit_id']; ?>"
                                    data-title="<?php echo htmlspecialchars($row['activity_title']); ?>"
                                    data-type="<?php echo htmlspecialchars($row['activity_type']); ?>"
                                    data-organizer="<?php echo htmlspecialchars($row['organizer']); ?>"
                                    data-date="<?php echo htmlspecialchars($row['activity_date']); ?>"
                                    data-hours="<?php echo number_format((float)$row['total_hours'], 2); ?>"
                                >
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php 
                    $count++;
                    } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="empty-message">
                No merit records found here. Start by adding a new merit record!
            </div>
        <?php } ?>
    </div>

</div>

<!-- Delete Confirmation Modal -->
<div id="deleteMeritModal" class="delete-modal-overlay">
    <div class="delete-modal-box">
        <div class="delete-modal-header">
            <div class="delete-modal-header-text">
                <h3>Delete Merit Record</h3>
                <p>Please review the record below before deleting it.</p>
            </div>
        </div>

        <div class="delete-modal-warning">
            This action cannot be undone.
        </div>

        <div class="delete-modal-info">
            <div class="delete-modal-info-row">
                <span class="delete-modal-info-label">Activity Title:</span>
                <span class="delete-modal-info-value" id="deleteMeritTitle"></span>
            </div>

            <div class="delete-modal-info-row">
                <span class="delete-modal-info-label">Activity Type:</span>
                <span class="delete-modal-info-value" id="deleteMeritType"></span>
            </div>

            <div class="delete-modal-info-row">
                <span class="delete-modal-info-label">Organizer:</span>
                <span class="delete-modal-info-value" id="deleteMeritOrganizer"></span>
            </div>

            <div class="delete-modal-info-row">
                <span class="delete-modal-info-label">Date:</span>
                <span class="delete-modal-info-value" id="deleteMeritDate"></span>
            </div>

            <div class="delete-modal-info-row">
                <span class="delete-modal-info-label">Total Hours:</span>
                <span class="delete-modal-info-value"><span id="deleteMeritHours"></span> hours</span>
            </div>
        </div>

        <div class="delete-modal-actions">
            <button type="button" class="delete-modal-btn delete-modal-btn-cancel" id="cancelDeleteMerit">Cancel</button>
            <a href="#" class="delete-modal-btn delete-modal-btn-confirm" id="confirmDeleteMerit">Confirm Delete</a>
        </div>
    </div>
</div>

<script>
    // Custom delete confirmation modal
    document.addEventListener("DOMContentLoaded", function () {
        
    // Get modal and buttons
    const modal = document.getElementById("deleteMeritModal");
    const deleteButtons = document.querySelectorAll(".open-delete-modal");
    const cancelBtn = document.getElementById("cancelDeleteMerit");
    const confirmBtn = document.getElementById("confirmDeleteMerit");

    // Get modal text fields
    const titleSpan = document.getElementById("deleteMeritTitle");
    const typeSpan = document.getElementById("deleteMeritType");
    const organizerSpan = document.getElementById("deleteMeritOrganizer");
    const dateSpan = document.getElementById("deleteMeritDate");
    const hoursSpan = document.getElementById("deleteMeritHours");

    // Open modal when Delete is clicked
    deleteButtons.forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();

            // Get data from clicked button
            const meritId = this.getAttribute("data-id");
            const title = this.getAttribute("data-title");
            const type = this.getAttribute("data-type");
            const organizer = this.getAttribute("data-organizer");
            const date = this.getAttribute("data-date");
            const hours = this.getAttribute("data-hours");

            // Show data in modal
            titleSpan.textContent = title;
            typeSpan.textContent = type;
            organizerSpan.textContent = organizer;
            dateSpan.textContent = date;
            hoursSpan.textContent = hours;

            // Set confirm delete link
            confirmBtn.href = "merit_delete.php?merit_id=" + meritId;

            // Show modal
            modal.classList.add("show");
        });
    });

    // Close modal when Cancel is clicked
    cancelBtn.addEventListener("click", function () {
        modal.classList.remove("show");
    });

    // Close modal when clicking outside modal box
    modal.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.classList.remove("show");
        }
    });
});
</script>

</body>
</html>