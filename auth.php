<?php
    session_start();

    if(!isset($_SESSION['user_id'])) {
        // Redirect to login page if not authenticated
        
        $project_root = "/student_cocurricular_system";
        header("Location: " . $project_root . "/login.php");
        exit();
    }
?>
