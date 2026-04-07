<?php
    session_start();
    include("db.php");

    $message = "";
    $message_type = "";
    $email = "";

    if(isset($_COOKIE['remembered_email'])) {
        $email = $_COOKIE['remembered_email'];
    }

    if(isset($_POST['login'])) {
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $password = mysqli_real_escape_string($con, $_POST['password']);


        // check for empty fields 
        if(empty($email)|| empty($password)) {
            $message = "Email and password are required.";
            $message_type = "error";

        }
        else {
            $query = "SELECT * FROM users WHERE email='$email'";
            $result = mysqli_query($con, $query);

            if(mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);

                // verify the password
                if(password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];

                    // set cookie for "Remember Me" functionality
                    if(isset($_POST['remember_email'])) {
                        setcookie('remembered_email', $email, time() + (30 * 24 * 60 * 60)); //  30 days
                    } else {
                        setcookie('remembered_email', '', time() - 3600); // Delete the cookie
                    }

                    header("Location: dashboard.php");
                    exit();
                } else {
                    $message = "Invalid email or password.";
                    $message_type = "error";
                }
            } else {
                $message = "Invalid email or password.";
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
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #c7dcfb;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
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
        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 14px;
            font-weight: bold;
        }
        h2{
            margin-top: 0;
            text-align: center;
            color: #333;
        }
        .error{
            background-color: #f58d96;
            color: #b30000;
            border: 1px solid #ffb3b3;
        }

        .success {
            background-color: #c8e6c9;
            color: #2e7d32;
            border: 1px solid #2e7d32;
        }

        label{
            display: block;
            color: #333;
        }
        input[type="email"],
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
            <h2>User Login</h2>

            

            <?php if(!empty($message)) {?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php } ?>

            <form method="POST" action="">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required><br><br>

                <input type="submit" name="login" value="Login">

                <div style = "margin-top: 15px;">
                    <input type="checkbox" name="remember_email" id="remember_email" 
                    <?php if(isset($_COOKIE['remembered_email']) && $email == $_COOKIE['remembered_email']) echo 'checked'; ?>>
                    <label for="remember_email" style="display: inline; color: #333;">
                        Remember Email</label>
                </div>
            </form>

            <p>Don't have an account? <a href="register.php">Register here</a>.</p>

        </div>
    </div>
</body>
</html>