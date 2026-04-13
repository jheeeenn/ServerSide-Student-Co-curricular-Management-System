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


// $query = "
//     SELECT 
//         users.user_id,
//         users.name,
//         users.email,
//         users.created_at,
//         users.is_admin,
//         COUNT(events.event_id) AS total_events
//     FROM users
//     LEFT JOIN events ON users.user_id = events.user_id
//     GROUP BY users.user_id, users.name, users.email, users.created_at, users.is_admin
//     ORDER BY users.user_id ASC
// ";

$query = "
    SELECT 
        users.user_id,
        users.name,
        users.email,
        users.created_at,
        users.is_admin,

        -- Total Events
        (SELECT COUNT(*) 
         FROM events e 
         WHERE e.user_id = users.user_id) AS total_events,

        -- Total Achievements
        (SELECT COUNT(*) 
         FROM achievements a 
         WHERE a.user_id = users.user_id) AS total_achievements

    FROM users
    ORDER BY users.user_id ASC
";


$result = mysqli_query($con, $query);

include("../partials/header.php");
include("../partials/navbar.php");

?>
    <div class="container">
        <div class="header-box">
            <h2>Admin User Overview</h2>
            <p>View all registered users and their basic activity summaries.</p>

        </div>

        <div class="table-box">
            <?php if($result && mysqli_num_rows($result) > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Total Events</th>
                        <th>Total Clubs</th>
                        <th>Total Merits</th>
                        <th>Total Achievements</th>
                        <th>Registered At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $count = 1;
                    while($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $count; ?></td>
                            <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo ($row['is_admin'] == 1) ? "Admin" : "User"; ?></td>
                            <td><?php echo htmlspecialchars($row['total_events']); ?></td>
                            <td>0</td>
                            <td>0</td>
                            <td><?php echo htmlspecialchars($row['total_achievements']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        </tr>
                    <?php 
                    $count++;
                    } 
                    ?>
                </tbody>
            </table>

            <?php } else { ?>
                <div class="empty-message">
                    <p>No users found.</p>
                </div>
            <?php } ?>
        </div>
    </div>

</body>
</html>
