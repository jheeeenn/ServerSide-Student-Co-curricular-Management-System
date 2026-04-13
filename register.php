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
                $new_user_id = mysqli_insert_id($con);

                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['is_admin'] = 0;

                $message = "Registration successful. Redirecting to dashboard...";
                $message_type = "success";

                header("refresh:3;url=dashboard.php");
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
    <title>Register | JX Student CoCo Hub</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="auth-page">
<section class="auth-hero auth-hero-register-simple">
    <div class="auth-bg-orb auth-bg-orb-two"></div>
    <div class="auth-bg-glass auth-bg-glass-two"></div>

    <div class="auth-hero-content auth-hero-content-simple">
        <div class="auth-brand-mark dark-brand">JX Student CoCo Hub</div>

        <h1 class="auth-register-title">Student Co-Curricular Management System</h1>

        <p class="auth-register-lead">
            Create your account here and start managing your co-curricular records in one place.
        </p>
    </div>
</section>

    <section class="auth-panel-wrap">
        <div class="auth-panel">
            <h2>Create account</h2>
            <p class="auth-panel-subtext">Register to access your personal co-curricular management workspace.</p>

            <?php if (!empty($message)) { ?>
                <div class="auth-message <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php } ?>

            <form method="POST" action="" class="auth-form">
                <div class="field-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>

                <div class="field-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="field-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="field-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" name="register" class="auth-submit secondary">Create Account</button>
            </form>

            <div class="auth-links">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </section>
</div>

</body>
</html>