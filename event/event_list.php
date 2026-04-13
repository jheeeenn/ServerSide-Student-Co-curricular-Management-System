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
$query = "SELECT * FROM events WHERE user_id='$user_id'";
if(!empty($search)) {
    $query .= " AND event_title LIKE '%$search%'";
}
if(!empty($filter_type)) {
    $query .= " AND event_type = '$filter_type'";
}

$query .= " ORDER BY event_date $sort_order";

$result = mysqli_query($con, $query);

//$query = "SELECT * FROM events WHERE user_id='$user_id' ORDER BY event_date DESC";
//$result = mysqli_query($con, $query);

include("../partials/header.php");
include("../partials/navbar.php");
?>
    <div class="container">
        <div class="header-box">
                <h2>Events Tracker Module</h2>
                <p>View and manage your events and participation records.</p>

            </div>
            <!-- Status message -->
                <?php if(!empty($status_message)) { ?>
                    <div class="message <?php echo $status_type; ?>">
                        <?php echo $status_message; ?>
                    </div>
                <?php } ?>

            <div class="top-actions">
                <a href="../dashboard.php" class="btn btn-back">Back to Dashboard</a>
                <a href="event_add.php" class="btn btn-add">Add New Event</a>
            </div>

            <!-- Filters -->
            <div class = "filter-box">
                <form method="GET" action="event_list.php" class="filter-form">
                     <div class="form-group group-search">
                        <label for="search">Search Event Title</label>
                        <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Enter event title">
                    </div>
                    
                    <div class="form-group group-type">
                        <label for="filter_type">Filter by Event Type</label>
                        <select name="filter_type" id="filter_type">

                        <option value="">-- All Types --</option>
                        <option value="Workshop" <?php if ($filter_type == "Workshop") 
                            echo "selected"; ?>>Workshop</option>
                        <option value="Competition" <?php if ($filter_type == "Competition") 
                            echo "selected"; ?>>Competition</option>
                        <option value="Talk" <?php if ($filter_type == "Talk") 
                            echo "selected"; ?>>Talk</option>
                        <option value="Seminar" <?php if ($filter_type == "Seminar") 
                            echo "selected"; ?>>Seminar</option>
                        <option value="University Event" <?php if ($filter_type == "University Event") 
                            echo "selected"; ?>>University Event</option>
                        <option value="Other" <?php if ($filter_type == "Other") 
                            echo "selected"; ?>>Other</option>
                
                        </select>
                    </div>

                    <div class="form-group group-sort">
                         <label for="sort_order">Sort by Date</label>
                        <select name="sort_order" id="sort_order">
                            <option value="DESC" <?php if ($sort_order == "DESC") 
                                echo "selected"; ?>>Newest to Oldest</option>
                            <option value="ASC" <?php if ($sort_order == "ASC") 
                                echo "selected"; ?>>Oldest to Newest</option>
                        </select>
                    </div>

                    <div class="form-group group-btn">
                        <button type="submit" class="btn btn-filter">Apply</button>
                    </div>
                    <div class = "form-group group-btn">
                        <a href="event_list.php" class="btn btn-reset">Clear Filters</a>

                    </div>
                </form>
            </div>

            <div class = 'table-box'>
                <?php if(mysqli_num_rows($result) > 0) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>No. </th>
                                <th>Event Title</th>
                                <th>Event Type</th>
                                <th>Organizer</th>
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
                                        <td><?php echo htmlspecialchars($row['event_title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['event_type']); ?></td>
                                        <td><?php echo htmlspecialchars($row['organizer']); ?></td>
                                        <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                                         <td><?php echo htmlspecialchars($row['location']); ?></td>
                                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td>
                                    <a class="action-link edit-link" href="event_edit.php?event_id=
                                        <?php echo $row['event_id']; ?>">
                                        Edit
                                    </a>

                                       <a
                                        href="#"
                                        class="action-link delete-link open-event-delete-modal"
                                        data-id="<?php echo $row['event_id']; ?>"
                                        data-title="<?php echo htmlspecialchars($row['event_title']); ?>"
                                        data-type="<?php echo htmlspecialchars($row['event_type']); ?>"
                                        data-organizer="<?php echo htmlspecialchars($row['organizer']); ?>"
                                        data-date="<?php echo htmlspecialchars($row['event_date']); ?>"
                                        data-location="<?php echo htmlspecialchars($row['location']); ?>"
                                    >
                                        Delete
                                    </a>
                                    </td>
                                </tr>
                            <?php $count++;
                             } ?>

                        </tbody>
                    </table>

                <?php } 
                
                else { ?>
                    <div class="empty-message">
                        No events found here. Start by adding a new event!</div>
                <?php } ?>

            </div>
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
