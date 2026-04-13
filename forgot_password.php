<?php
session_start();
include("db.php");

$message = "";
$message_type = "";


if (isset($_POST['submit'])){
    $email = mysqli_real_escape_string($con, trim($_POST['email']));

    if(empty($email)) {
        $message = "Email is required.";
        $message_type = "error";
    } else {

        $check_query = "SELECT * FROM users WHERE email='$email'";
        $check_result = mysqli_query($con, $check_query);

        if(mysqli_num_rows($check_result) > 0) {
           $token = bin2hex(random_bytes(50)); // Generate a secure random token

           $delete_query = "DELETE FROM password_resets WHERE email='$email'";
           mysqli_query($con, $delete_query);

            $insert_query = "INSERT INTO password_resets (email, token) VALUES ('$email', '$token')";
            if(mysqli_query($con, $insert_query)) {
                // Send reset email
                $reset_link = "http://localhost/student_cocurricular_system/reset_password.php?token=$token";
                $message = "Reset link generated:<br><a href='$reset_link'>$reset_link</a>";
                $message_type = "success";
            }
            else{
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
    <title>Forgot Password</title>
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
            word-wrap: break-word;
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
        input[type="email"] {
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
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h2>Forgot Password</h2>

        <!-- Display message if exists -->
        <?php if (!empty($message)) { ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <form method="POST" action="">
            <label for="email">Enter your registered email:</label>
            <input type="email" name="email" id="email" required>

            <input type="submit" name="submit" value="Generate Reset Link">
        </form>

        <p><a href="login.php">Back to Login</a></p>
    </div>
</div>

</body>
</html>
