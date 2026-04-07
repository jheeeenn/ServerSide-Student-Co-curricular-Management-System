<?php 
include("../db.php");
require("../auth.php");

$base_path = "../"; // Set base path for links in the navbar
$page_title = "Events Tracker";
$page_subtitle = "Manage your events and participation records";
$show_cookie_notice = false; 

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM events WHERE user_id='$user_id' ORDER BY event_date DESC";
$result = mysqli_query($con, $query);

include("../partials/header.php");
include("../partials/navbar.php");
?>
    <div class="container">
            <div class="header-box">
                <h2>Events Tracker Module</h2>
                <p>View and manage your events and participation records.</p>

            </div>

            <div class="top-actions">
                <a href="../dashboard.php" class="btn btn-back">Back to Dashboard</a>
                <a href="event_add.php" class="btn btn-add">Add New Event</a>
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

                                    <a class="action-link delete-link" href="event_delete.php?event_id=
                                        <?php echo $row['event_id']; ?>" 
                                        onclick="return confirm('Are you sure you want to delete this event record?')">
                                        Delete</a>
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
    </body>
</html>
