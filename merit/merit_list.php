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
 $current_page = "merit";?>

<?php include("../partials/navbar.php"); ?>


<div class="container module-shell merit-shell">

    <?php
        $total_records = 0;
        $event_merit_count = 0;
        $club_merit_count = 0;
        $event_merit_percent = 0;
        $club_merit_percent = 0;    

        $stats_query = "SELECT event_id, club_id FROM merits WHERE user_id='$user_id'";
        $stats_result = mysqli_query($con, $stats_query);

        if ($stats_result && mysqli_num_rows($stats_result) > 0) {
            while ($stats_row = mysqli_fetch_assoc($stats_result)) {
                $total_records++;

                if (!empty($stats_row['event_id'])) {
                    $event_merit_count++;
                }

                if (!empty($stats_row['club_id'])) {
                    $club_merit_count++;
                }
            }
        }

        if ($total_records > 0) {
            $event_merit_percent = round(($event_merit_count / $total_records) * 100);
            $club_merit_percent = round(($club_merit_count / $total_records) * 100);
        }
    ?>

    <section class="module-hero module-hero-merit">
        <div class="module-hero-main">
            <div class="module-hero-icon merit-accent-soft">⏳</div>
            <div class="module-hero-text-wrap">
                <h2>Merit Tracker</h2>
                <p>Record and manage your contribution hours for volunteering, service, and co-curricular activities.</p>
            </div>
        </div>

        <div class="module-hero-actions">
            <a href="merit_add.php" class="module-btn module-btn-primary merit-accent-btn">+ Add Merit Record</a>
        </div>
    </section>

    <?php if (!empty($status_message)) { ?>
        <div class="message <?php echo $status_type; ?> module-status-message">
            <?php echo $status_message; ?>
        </div>
    <?php } ?>

    <section class="module-stats-grid">
        <div class="module-stat-card">
            <div class="module-stat-icon merit-accent-soft">📝</div>
            <div>
                <h3><?php echo $total_records; ?></h3>
                <p>Total Activities</p>
            </div>
        </div>

        <div class="module-stat-card">
            <div class="module-stat-icon merit-accent-soft">🕒</div>
            <div>
                <h3><?php echo number_format((float)$total_merit_hours, 2); ?></h3>
                <p>Total Contribution Hours</p>
            </div>
        </div>

        <div class="module-stat-card merit-linkage-mini-card">
            <div class="merit-linkage-mini-arc merit-linkage-event-arc" style="--p: <?php echo $event_merit_percent; ?>;"></div>
            <div class="merit-linkage-mini-text">
                <h3><?php echo $event_merit_count; ?></h3>
                <p>Event Merit</p>
            </div>
        </div>

        <div class="module-stat-card merit-linkage-mini-card">
            <div class="merit-linkage-mini-arc merit-linkage-club-arc" style="--p: <?php echo $club_merit_percent; ?>;"></div>
            <div class="merit-linkage-mini-text">
                <h3><?php echo $club_merit_count; ?></h3>
                <p>Club Merit</p>
            </div>
        </div>
    </section>

    <section class="module-filter-card">
        <form method="GET" action="merit_list.php" class="module-filter-form">
            <div class="module-filter-full">
                <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search activity title...">
            </div>

            <div>
                <select name="filter_type" id="filter_type">
                    <option value="">All Types</option>
                    <option value="Volunteering" <?php if ($filter_type == "Volunteering") echo "selected"; ?>>Volunteering</option>
                    <option value="Community Service" <?php if ($filter_type == "Community Service") echo "selected"; ?>>Community Service</option>
                    <option value="Committee Work" <?php if ($filter_type == "Committee Work") echo "selected"; ?>>Committee Work</option>
                    <option value="Club Service" <?php if ($filter_type == "Club Service") echo "selected"; ?>>Club Service</option>
                    <option value="University Service" <?php if ($filter_type == "University Service") echo "selected"; ?>>University Service</option>
                    <option value="Other" <?php if ($filter_type == "Other") echo "selected"; ?>>Other</option>
                </select>
            </div>

            <div>
                <select name="sort_order" id="sort_order">
                    <option value="DESC" <?php if ($sort_order == "DESC") echo "selected"; ?>>Newest to Oldest</option>
                    <option value="ASC" <?php if ($sort_order == "ASC") echo "selected"; ?>>Oldest to Newest</option>
                </select>
            </div>

            <div class="module-filter-actions">
                <button type="submit" class="module-btn module-btn-primary merit-accent-btn">Filter</button>
                <a href="merit_list.php" class="module-btn module-btn-secondary">Reset</a>
            </div>
        </form>
    </section>

    <section class="module-content-card">
        <?php if ($result && mysqli_num_rows($result) > 0) { ?>
            <div class="module-table-wrap">
                <table class="module-table">
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
                                <td class="module-title-cell"><?php echo htmlspecialchars($row['activity_title']); ?></td>
                                <td><span class="module-tag merit-tag"><?php echo htmlspecialchars($row['activity_type']); ?></span></td>
                                <td><?php echo htmlspecialchars($row['organizer']); ?></td>
                                <td><?php echo htmlspecialchars($row['activity_date']); ?></td>
                                <td><span class="merit-hours-badge"><?php echo number_format((float)$row['total_hours'], 2); ?> hrs</span></td>
                                <td class="module-description-cell"><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>
                                    <div class="module-action-links">
                                        <a class="module-action-link module-action-edit" href="merit_edit.php?merit_id=<?php echo $row['merit_id']; ?>">Edit</a>
                                        <a
                                            href="#"
                                            class="module-action-link module-action-delete open-delete-modal"
                                            data-id="<?php echo $row['merit_id']; ?>"
                                            data-title="<?php echo htmlspecialchars($row['activity_title']); ?>"
                                            data-type="<?php echo htmlspecialchars($row['activity_type']); ?>"
                                            data-organizer="<?php echo htmlspecialchars($row['organizer']); ?>"
                                            data-date="<?php echo htmlspecialchars($row['activity_date']); ?>"
                                            data-hours="<?php echo number_format((float)$row['total_hours'], 2); ?>"
                                        >
                                            Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                        $count++;
                        } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="module-empty-state">
                <div class="module-empty-icon merit-accent-text">⏳</div>
                <h3>No merit records found</h3>
                <p>Start by adding your first merit contribution record.</p>
                <a href="merit_add.php" class="module-btn module-btn-primary merit-accent-btn">Add Merit Record</a>
            </div>
        <?php } ?>
    </section>

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