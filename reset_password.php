<?php
include("db.php");
session_start();

$message = "";
$message_type = "";
$show_form = false;
$email = "";

// --- LECTURER'S EXACT LOGIC FROM TABLE 9 ---
if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($con, $_GET['token']);
    $query = mysqli_query($con, "SELECT email FROM password_resets WHERE token='$token'");
    
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        $email = $row['email'];
        $show_form = true; 
    } else {
        $message = "Invalid or expired reset token.";
        $message_type = "error";
    }
} else {
    $message = "No reset token provided.";
    $message_type = "error";
}

// Handle the form submission to update the password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password'])) {
    $new_password = mysqli_real_escape_string($con, $_POST['new_password']);
    
    // MD5 is used for consistency with the existing system as per lecturer notes
    $hashed_password = md5($new_password);
    
    // Update the users table
    mysqli_query($con, "UPDATE users SET password='$hashed_password' WHERE email='$email'");
    
    // Delete the used token
    mysqli_query($con, "DELETE FROM password_resets WHERE email='$email'");
    
    // Redirect to login page with success status
    header("Location: login.php?status=reset_success");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #c7dcfb;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background: #ffffff;
            width: 400px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            margin-top: 0;
            text-align: center;
            color: #333;
        }

        p.description {
            text-align: center;
            color: #555;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .error {
            background-color: #f58d96;
            color: #b30000;
            border: 1px solid #ffb3b3;
        }

        .success {
            background-color: #c8e6c9;
            color: #2e7d32;
            border: 1px solid #2e7d32;
        }

        label {
            display: block;
            color: #333;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 16px;
            border: 1px solid #cccccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #28a745; 
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Create New Password</h2>
            
            <?php if(!empty($message)) { echo "<div class='message $message_type'>$message</div>"; } ?>

            <?php if($show_form) { ?>
                <p class="description">Enter your new password below for <b><?php echo htmlspecialchars($email); ?></b>.</p>
                <form method="POST" action="">
                    <label for="new_password">New Password:</label>
                    <input type="password" name="new_password" id="new_password" required minlength="6">
                    
                    <button type="submit">Confirm Reset</button>
                </form>
            <?php } ?>
            
            <div class="back-link">
                <a href="login.php">Return to Login</a>
            </div>
        </div>
    </div>
</body>
</html>