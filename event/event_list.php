<?php 
include("../db.php");
require("../auth.php");

$base_path = "../"; // Set base path for links in the navbar
$page_title = "Events Tracker";
$page_subtitle = "Manage your events and participation records";
$show_cookie_notice = false; 

$user_id = $_SESSION['user_id'];


$status_message = "";
$status_type = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == "added") {
        $status_message = "Event added successfully.";
        $status_type = "success";
    } elseif ($_GET['status'] == "updated") {
        $status_message = "Event updated successfully.";
        $status_type = "success";
    } elseif ($_GET['status'] == "deleted") {
        $status_message = "Event deleted successfully.";
        $status_type = "success";
    } elseif ($_GET['status'] == "delete_error") {
        $status_message = "Unable to delete event. Please try again.";
        $status_type = "error";
    }
    elseif ($_GET['status'] == "update_error") {
        $status_message = "Unable to update event. Please try again.";
        $status_type = "error";
    }
}

// flters
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

// build the query 
//$query = "SELECT * FROM events WHERE user_id='$user_id'";
$query = "
    SELECT events.*, clubs.club_name AS related_club_name
    FROM events
    LEFT JOIN clubs ON events.club_id = clubs.club_id
    WHERE events.user_id='$user_id'
";


if(!empty($search)) {
    $query .= " AND events.event_title LIKE '%$search%'";
}
if(!empty($filter_type)) {
    $query .= " AND events.event_type = '$filter_type'";
}

$query .= " ORDER BY events.event_date $sort_order";

$result = mysqli_query($con, $query);

//$query = "SELECT * FROM events WHERE user_id='$user_id' ORDER BY event_date DESC";
//$result = mysqli_query($con, $query);

include("../partials/header.php");
$current_page = "event";
include("../partials/navbar.php");
?>
<div class="auth-bg-glass auth-bg-glass-one"></div>
<div class="auth-bg-glass auth-bg-glass-two"></div>
<div class="container module-shell event-shell">

    <?php
        $total_events = 0;
        $upcoming_events = 0;
        $linked_club_count = 0;
        $club_link_percent = 0;

        $stats_query = "SELECT event_date, club_id FROM events WHERE user_id='$user_id'";
        $stats_result = mysqli_query($con, $stats_query);

        if ($stats_result && mysqli_num_rows($stats_result) > 0) {
            $today = date("Y-m-d");

            while ($stats_row = mysqli_fetch_assoc($stats_result)) {
                $total_events++;

                if ($stats_row['event_date'] >= $today) {
                    $upcoming_events++;
                }

                if (!empty($stats_row['club_id'])) {
                    $linked_club_count++;
                }
            }
        }

        if ($total_events > 0) {
            $club_link_percent = round(($linked_club_count / $total_events) * 100);
        }
    ?>

    <section class="module-hero module-hero-event">
        <div class="module-hero-main">
            <div class="module-hero-icon event-accent-soft">📅</div>
            <div class="module-hero-text-wrap">
                <h2>Event Tracker</h2>
                <p>Record and manage your programme, workshop, talk, and event participation.</p>
            </div>
        </div>

        <div class="module-hero-actions">
            <a href="event_add.php" class="module-btn module-btn-primary event-accent-btn">+ Add Event</a>
        </div>
    </section>

    <?php if(!empty($status_message)) { ?>
        <div class="message <?php echo $status_type; ?> module-status-message">
            <?php echo $status_message; ?>
        </div>
    <?php } ?>

    <section class="module-stats-grid">
        <div class="module-stat-card">
            <div class="module-stat-icon event-accent-soft">🗓️</div>
            <div>
                <h3><?php echo $total_events; ?></h3>
                <p>Total Events</p>
            </div>
        </div>

        <div class="module-stat-card">
            <div class="module-stat-icon event-accent-soft">📌</div>
            <div>
                <h3><?php echo $upcoming_events; ?></h3>
                <p>Upcoming Events</p>
            </div>
        </div>

        <div class="module-stat-card event-linkage-mini-card">
            <div class="event-linkage-mini-arc" style="--p: <?php echo $club_link_percent; ?>;"></div>
            <div class="event-linkage-mini-text">
                <h3><?php echo $club_link_percent; ?>%</h3>
                <p>Linked Club</p>
            </div>
        </div>

    </section>

    <section class="module-filter-card">
        <form method="GET" action="event_list.php" class="module-filter-form module-filter-form-event">
            <div class="module-filter-full">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search event title...">
            </div>

            <div>
                <select name="filter_type">
                    <option value="">All Categories</option>
                    <option value="Workshop" <?php if ($filter_type == "Workshop") echo "selected"; ?>>Workshop</option>
                    <option value="Competition" <?php if ($filter_type == "Competition") echo "selected"; ?>>Competition</option>
                    <option value="Talk" <?php if ($filter_type == "Talk") echo "selected"; ?>>Talk</option>
                    <option value="Seminar" <?php if ($filter_type == "Seminar") echo "selected"; ?>>Seminar</option>
                    <option value="University Event" <?php if ($filter_type == "University Event") echo "selected"; ?>>University Event</option>
                    <option value="Other" <?php if ($filter_type == "Other") echo "selected"; ?>>Other</option>
                </select>
            </div>

            <div>
                <select name="sort_order">
                    <option value="DESC" <?php if ($sort_order == "DESC") echo "selected"; ?>>Newest to Oldest</option>
                    <option value="ASC" <?php if ($sort_order == "ASC") echo "selected"; ?>>Oldest to Newest</option>
                </select>
            </div>

            <div class="module-filter-actions">
                <button type="submit" class="module-btn module-btn-primary event-accent-btn">Filter</button>
                <a href="event_list.php" class="module-btn module-btn-secondary">Reset</a>
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
                            <th>Event Title</th>
                            <th>Type</th>
                            <th>Organizer</th>
                            <th>Related Club</th>
                            <th>Date</th>
                            <th>Location</th>
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
                                <td class="module-title-cell"><?php echo htmlspecialchars($row['event_title']); ?></td>
                                <td><span class="module-tag event-tag"><?php echo htmlspecialchars($row['event_type']); ?></span></td>
                                <td><?php echo htmlspecialchars($row['organizer']); ?></td>

                                <td>
                                    <?php if (!empty($row['related_club_name'])) { ?>
                                        <span class="module-tag club-tag"><?php echo htmlspecialchars($row['related_club_name']); ?></span>
                                    <?php } else { ?>
                                        <span class="event-unlinked-badge">Not linked</span>
                                    <?php } ?>
                                </td>

                                <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td class="module-description-cell"><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>
                                    <div class="module-action-links">
                                        <a class="module-action-link module-action-edit" href="event_edit.php?event_id=<?php echo $row['event_id']; ?>">Edit</a>
                                        <a class="module-action-link module-action-generate-merit" href="../merit/merit_add.php?event_id=<?php echo $row['event_id']; ?>">Generate Merit</a>
                                        <a
                                            href="#"
                                            class="module-action-link module-action-delete open-event-delete-modal"
                                            data-id="<?php echo $row['event_id']; ?>"
                                            data-title="<?php echo htmlspecialchars($row['event_title']); ?>"
                                            data-type="<?php echo htmlspecialchars($row['event_type']); ?>"
                                            data-organizer="<?php echo htmlspecialchars($row['organizer']); ?>"
                                            data-date="<?php echo htmlspecialchars($row['event_date']); ?>"
                                            data-location="<?php echo htmlspecialchars($row['location']); ?>"
                                        >
                                            Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php $count++; } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="module-empty-state">
                <div class="module-empty-icon event-accent-text">📅</div>
                <h3>No events found</h3>
                <p>Start by adding your first event record.</p>
                <a href="event_add.php" class="module-btn module-btn-primary event-accent-btn">Add Event</a>
            </div>
        <?php } ?>
    </section>
</div>

<div id="deleteEventModal" class="delete-modal-overlay">
    <div class="delete-modal-box">
        <div class="delete-modal-header">
            <div class="delete-modal-header-text">
                <h3>Delete Event Record</h3>
                <p>Please review the event details before deleting it.</p>
            </div>
        </div>

        <div class="delete-modal-warning">
            This action cannot be undone.
        </div>

        <div class="delete-modal-info">
            <div class="delete-modal-info-row">
                <span class="delete-modal-info-label">Title:</span>
                <span class="delete-modal-info-value" id="deleteEventTitle"></span>
            </div>
            <div class="delete-modal-info-row">
                <span class="delete-modal-info-label">Type:</span>
                <span class="delete-modal-info-value" id="deleteEventType"></span>
            </div>
            <div class="delete-modal-info-row">
                <span class="delete-modal-info-label">Organizer:</span>
                <span class="delete-modal-info-value" id="deleteEventOrganizer"></span>
            </div>
            <div class="delete-modal-info-row">
                <span class="delete-modal-info-label">Date:</span>
                <span class="delete-modal-info-value" id="deleteEventDate"></span>
            </div>
            <div class="delete-modal-info-row">
                <span class="delete-modal-info-label">Location:</span>
                <span class="delete-modal-info-value" id="deleteEventLocation"></span>
            </div>
        </div>

        <div class="delete-modal-actions">
            <button type="button" class="delete-modal-btn delete-modal-btn-cancel" id="cancelDeleteEvent">Cancel</button>
            <a href="#" class="delete-modal-btn delete-modal-btn-confirm" id="confirmDeleteEvent">Confirm Delete</a>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("deleteEventModal");
    const buttons = document.querySelectorAll(".open-event-delete-modal");
    const cancelBtn = document.getElementById("cancelDeleteEvent");
    const confirmBtn = document.getElementById("confirmDeleteEvent");

    const titleSpan = document.getElementById("deleteEventTitle");
    const typeSpan = document.getElementById("deleteEventType");
    const organizerSpan = document.getElementById("deleteEventOrganizer");
    const dateSpan = document.getElementById("deleteEventDate");
    const locationSpan = document.getElementById("deleteEventLocation");

    buttons.forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();

            const eventId = this.getAttribute("data-id");
            titleSpan.textContent = this.getAttribute("data-title");
            typeSpan.textContent = this.getAttribute("data-type");
            organizerSpan.textContent = this.getAttribute("data-organizer");
            dateSpan.textContent = this.getAttribute("data-date");
            locationSpan.textContent = this.getAttribute("data-location");

            confirmBtn.href = "event_delete.php?event_id=" + eventId;
            modal.classList.add("show");
        });
    });

    cancelBtn.addEventListener("click", function () {
        modal.classList.remove("show");
    });

    modal.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.classList.remove("show");
        }
    });
});
</script>

    </body>
</html>
