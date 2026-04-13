<?php
include("db.php");
session_start();

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    
    // Check if email exists in the system
    $query = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");
    
    if (mysqli_num_rows($query) > 0) {
        
        // --- LECTURER'S EXACT LOGIC FROM TABLE 8 ---
        $token = bin2hex(random_bytes(50));
        mysqli_query($con, "INSERT INTO password_resets (email, token) VALUES ('$email', '$token')");
        
        $message = "Password reset link: <br><br> <a href='reset_password.php?token=$token' style='color: #007bff; text-decoration: underline;'>Reset Password</a>";
        $message_type = "success";
    } else {
        $message = "Email not found.";
        $message_type = "error";
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

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
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
            <h2>Forgot Password</h2>
            <p class="description">Enter your registered email address to receive a password reset link.</p>
            
            <?php if(!empty($message)) { echo "<div class='message $message_type'>$message</div>"; } ?>

            <form method="POST" action="">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
                
                <button type="submit">Request Reset Link</button>
            </form>
            
            <div class="back-link">
                <a href="login.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>