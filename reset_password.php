<?php
session_start();
include("db.php");

$message = "";
$message_type = "";
$show_form = false;
$email = "";
$token = "";


if(isset($_GET['token'])){
    $token = mysqli_real_escape_string($con, $_GET['token']);

    $query = "SELECT * FROM password_resets WHERE token='$token'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $email = $row['email'];
        $show_form = true;
    }

    else {
        $message = "Invalid or expired token.";
        $message_type = "error";
    }
}
else{
    $message = "No token provided.";
    $message_type = "error";
}


if (isset($_POST['reset_password'])) {

    $token = mysqli_real_escape_string($con, $_POST['token']);
    $new_password = mysqli_real_escape_string($con, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($con, $_POST['confirm_password']);


    $query = "SELECT * FROM password_resets WHERE token='$token'";
    $result = mysqli_query($con, $query);

    if(mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $email = $row['email'];

        // Validate new password and confirmation
        // check for empty fields
        if(empty($new_password) || empty($confirm_password)) {
            $message = "Both password fields are required.";
            $message_type = "error";
            $show_form = true;
        } 
        // 
        elseif ($new_password !== $confirm_password) {
            $message = "Passwords do not match.";
            $message_type = "error";
            $show_form = true;

        } 
        
        else {
            // Update the user's password in the database
            // Hash the new password before storing it in db 
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $update_query = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
            if(mysqli_query($con, $update_query)) {
                // Delete the token after successful reset
                $delete_query = "DELETE FROM password_resets WHERE email='$email'";
                mysqli_query($con, $delete_query);

                header("Location: login.php?status=reset_success");
                exit();
            } else {
                $message = "Unable to reset password.";
                $message_type = "error";
                $show_form = true;
            }
        }
    } else {
        $message = "Invalid or expired token.";
        $message_type = "error";
    }
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
            width: 420px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-top: 0;
            text-align: center;
            color: #333;
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
        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>


<body>
    <div class="container">
        <div class="card">
            <h2>Reset Password</h2>

            <?php if(!empty($message)) { ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php } ?>

            <?php if($show_form) { ?>
                <form method="POST" action="">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>

                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>

                    <input type="submit" name="reset_password" value="Reset Password">
                </form>
            <?php } ?>

            <p><a href="login.php">Back to Login</a></p>
        </div>
    </div>
</body>
</html>