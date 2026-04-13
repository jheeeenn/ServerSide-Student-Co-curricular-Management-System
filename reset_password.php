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
    <title>Reset Password | JX Student CoCo Hub</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="auth-utility-page">
    <div class="auth-utility-card">
        <h1>Reset Password</h1>
        <p class="auth-utility-subtext">
            Set a new password for your account.
        </p>

        <?php if (!empty($message)) { ?>
            <div class="auth-message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <?php if ($show_form) { ?>
            <form method="POST" action="" class="auth-form">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="field-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>

                <div class="field-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" name="reset_password" class="auth-submit">Reset Password</button>
            </form>
        <?php } ?>

        <div class="auth-utility-actions">
            <a href="login.php" class="auth-utility-back">Back to Login</a>
        </div>
    </div>
</div>

</body>
</html>