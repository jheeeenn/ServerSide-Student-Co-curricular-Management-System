<?php
session_start();
include("db.php");

$message = "";
$message_type = "";
$reset_link = "";
$email = "";

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($con, trim($_POST['email']));

    if (empty($email)) {
        $message = "Email is required.";
        $message_type = "error";
    } else {
        $check_query = "SELECT * FROM users WHERE email='$email'";
        $check_result = mysqli_query($con, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $token = bin2hex(random_bytes(50));

            $delete_query = "DELETE FROM password_resets WHERE email='$email'";
            mysqli_query($con, $delete_query);

            $insert_query = "INSERT INTO password_resets (email, token) VALUES ('$email', '$token')";
            if (mysqli_query($con, $insert_query)) {
                $reset_link = "http://localhost/student_cocurricular_system/reset_password.php?token=$token";
                $message = "Reset link generated successfully.";
                $message_type = "success";
            } else {
                $message = "Unable to generate the reset link. Please try again.";
                $message_type = "error";
            }
        } else {
            $message = "No account found with that email address.";
            $message_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | JX Student CoCo Hub</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="auth-utility-page">
    <div class="auth-utility-card">
        <h1>Forgot Password</h1>
        <p class="auth-utility-subtext">
            Enter your registered email address to generate a password reset link.
        </p>

        <?php if (!empty($message)) { ?>
            <div class="auth-message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <?php if (empty($reset_link)) { ?>
            <form method="POST" action="" class="auth-form">
                <div class="field-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <button type="submit" name="submit" class="auth-submit secondary">Generate Reset Link</button>
            </form>
        <?php } ?>

        <?php if (!empty($reset_link)) { ?>
            <div class="auth-link-box">
                <a href="<?php echo htmlspecialchars($reset_link); ?>">
                    <?php echo htmlspecialchars($reset_link); ?>
                </a>
            </div>
        <?php } ?>

        <div class="auth-utility-actions">
            <a href="login.php" class="auth-utility-back">Back to Login</a>
        </div>
    </div>
</div>

</body>
</html>