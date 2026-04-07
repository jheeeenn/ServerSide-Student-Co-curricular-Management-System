<?php

    session_start();
    session_unset();
    session_destroy();

   setcookie('remembered_email', '', time() - 3600, "/"); // Delete the cookie

   //echo "<div class='alert alert-info'>You have been logged out. Redirecting to login page...</div>";
    //header("Refresh:2; url=login.php");
    header("Location: login.php");
    exit();
?>