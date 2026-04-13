<?php
    session_start();
    include("db.php");

    $message = "";
    $message_type = "";
    $email = "";
    
    if(isset($_COOKIE['remembered_email'])) {
        $email = $_COOKIE['remembered_email'];
    }

    if (isset($_GET['status']) && $_GET['status'] == "reset_success") {
        $message = "Password reset successful. Please log in with your new password.";
        $message_type = "success";
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
                    
                    $_SESSION['is_admin'] = $user['is_admin']; // Store admin status in session, 0 or 1

                    // set cookie for "Remember Me" functionality
                    if(isset($_POST['remember_email'])) {
                        setcookie('remembered_email', $email, time() + (30 * 24 * 60 * 60), "/"); //  30 days
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
    <title>Login | JX Student CoCo Hub</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="auth-page">
    <!-- Hero section with branding and features -->

    <section class="auth-hero auth-hero-login">
    
    <div class="auth-bg-orb auth-bg-orb-two"></div>
    <div class="auth-bg-orb auth-bg-orb-three"></div>
    <div class="auth-bg-glass auth-bg-glass-one"></div>
    <div class="auth-bg-glass auth-bg-glass-two"></div>
    <div class="auth-bg-grid"></div>

    <div class="auth-hero-content">
        <div class="auth-brand-row">
            
            
            <div class="auth-brand-mark dark-brand">JX Student CoCo Hub</div>
            <div class="auth-mini-line"></div>
        </div>

        <h1 class="auth-login-title">Student Co-Curricular<br>Management System</h1>

        <p class="auth-login-lead">
            One portal to manage your student events, club activities, merit contributions, and achievements.
        </p>

        <div class="auth-hero-highlight">
            <div class="auth-highlight-number">4</div>
            <div class="auth-highlight-text">
                <strong>Unified modules</strong>
                <span>Events, Clubs, Merits, and Achievements connected in one system.</span>
            </div>
        </div>

        <div class="auth-feature-list auth-feature-list-compact">
            <div class="auth-feature-item auth-feature-item-dark">
                <div class="auth-feature-icon">📌</div>
                <div class="auth-feature-text auth-feature-text-dark">
                    <strong>Keep all records organized</strong>
                    <span>Track and update your co-curricular records from a single dashboard.</span>
                </div>
            </div>

            <div class="auth-feature-item auth-feature-item-dark">
                <div class="auth-feature-icon">📊</div>
                <div class="auth-feature-text auth-feature-text-dark">
                    <strong>Monitor your progress</strong>
                    <span>See your activity participation and contribution hours more clearly.</span>
                </div>
            </div>
        </div>
    </div>
</section>

    <section class="auth-panel-wrap">
        <div class="auth-panel">
            <h2>Welcome back</h2>
            <p class="auth-panel-subtext">Sign in to continue managing your co-curricular profile.</p>

            <?php if (!empty($message)) { ?>
                <div class="auth-message <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php } ?>

            <form method="POST" action="" class="auth-form">
                <div class="field-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="field-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="auth-options">
                    <label class="auth-checkbox" for="remember_email">
                        <input type="checkbox" name="remember_email" id="remember_email"
                            <?php if (isset($_COOKIE['remembered_email']) && $email == $_COOKIE['remembered_email']) echo 'checked'; ?>>
                        <span>Remember Email</span>
                    </label>

                    <a class="auth-inline-link" href="forgot_password.php">Forgot Password?</a>
                </div>

                <button type="submit" name="login" class="auth-submit">Login</button>
            </form>

            <div class="auth-links">
                <p>New here? <a href="register.php">Create an account</a></p>
            </div>
        </div>
    </section>
</div>

</body>
</html>