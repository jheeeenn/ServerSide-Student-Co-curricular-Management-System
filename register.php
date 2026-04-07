<?php
session_start();
include("db.php");

$message = "";
$message_type = "";

$name = "";
$email = "";

if(isset($_POST['register'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($con, $_POST['confirm_password']);

    // check for empty fields 
    if(empty($name)|| empty($email)|| empty($password) || empty($confirm_password)) {
        $message = "All fields are required.";
        $message_type = "error";
    
    // chekc for password and confrim password matching
    } elseif($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $message_type = "error";
    
    }

    else {
        $check_email_query = "SELECT * FROM users WHERE email='$email'";
        $check_email_result = mysqli_query($con, $check_email_query);

        // email must be unique
        if(mysqli_num_rows($check_email_result) > 0) {
            $message = "Email already exists.";
            $message_type = "error";
    
        } else {
            // hash the password before storing it in db
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert_query = "INSERT INTO users (name, email, password) 
            VALUES ('$name', '$email', '$hashed_password')";

            if(mysqli_query($con, $insert_query)) {
                $message = "Registration successful. You can now log in.";
                //header("Location: login.php");
                //exit();
                $message_type = "success";
                $name = "";
                $email = "";
            } else {
                $message = "Registration failed: " . mysqli_error($con);
                $message_type = "error";
    
            }
        }
    }
    
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #c7dcfb;

            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container{
            width : 100%;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card{
            background-color: #fff;
            width: 400px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.1);

        }

        h2{
            margin-top: 0;
            text-align: center;
            color: #333;
        }
        .message{
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
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

        label{
            font-weight: bold;
            color: #333333;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
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
    <h2>User Registration</h2>

    <?php if(!empty($message)) {?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <form method="POST" action="">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" value = "<?php echo htmlspecialchars($name); ?>"><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value = "<?php echo htmlspecialchars($email); ?>"><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password"><br><br>

        <label for="confirm_password">Confirm Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password"><br><br>

        <input type="submit" name="register" value="Register">
    </form>

    <br>
    <br>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </div>
    </div>
</body>
</html>