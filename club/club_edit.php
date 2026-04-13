<?php
include("../db.php");
require("../auth.php");

$base_path = "../"; 
$page_title = "Edit Club Record";
$show_cookie_notice = false;

// --- LECTURER'S THEME LOGIC ---
if (isset($_POST['toggle_theme'])) {
    $_SESSION['theme_mode'] = (isset($_SESSION['theme_mode']) && $_SESSION['theme_mode'] == 'dark') ? 'light' : 'dark';
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
$theme = isset($_SESSION['theme_mode']) ? $_SESSION['theme_mode'] : 'light';

$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "";

if(!isset($_GET['club_id'])) {
    header("Location: club_list.php?status=error");
    exit();
}

$club_id = mysqli_real_escape_string($con, $_GET['club_id']);

$select_query = "SELECT * FROM clubs WHERE club_id='$club_id' AND user_id='$user_id'";
$select_result = mysqli_query($con, $select_query);

if(mysqli_num_rows($select_result) != 1) {
    header("Location: club_list.php?status=error");
    exit();
}

$row = mysqli_fetch_assoc($select_result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        $club_name = mysqli_real_escape_string($con, trim($_POST['club_name']));
        $role = mysqli_real_escape_string($con, trim($_POST['role']));
        $join_date = mysqli_real_escape_string($con, trim($_POST['join_date']));
        $description = mysqli_real_escape_string($con, trim($_POST['description']));

        // --- SERVER-SIDE DATA VALIDATION ---
        $errors = [];
        $current_date = date("Y-m-d");
        $min_realistic_date = date("Y", strtotime("-10 years")) . "-01-01"; 

        if (empty($club_name) || strlen($club_name) > 100) { 
            $errors[] = "Club name must be between 1 and 100 characters."; 
        }
        if (empty($role) || strlen($role) > 50) { 
            $errors[] = "Role must be between 1 and 50 characters."; 
        }
        if (empty($join_date) || $join_date > $current_date || $join_date < $min_realistic_date) { 
            $errors[] = "Please select a realistic join date (between " . date("Y", strtotime("-10 years")) . " and today)."; 
        }
        if (strlen($description) > 1000) { 
            $errors[] = "Description must be under 1000 characters."; 
        }

        if (empty($errors)) {
            $update_query = "UPDATE clubs SET 
                             club_name='$club_name', role='$role', join_date='$join_date', description='$description' 
                             WHERE club_id='$club_id' AND user_id='$user_id'";

            if (mysqli_query($con, $update_query)) {
                header("Location: club_list.php?status=updated");
                exit();
            } else {
                $message = "Database Error: " . mysqli_error($con);
                $message_type = "error";
            }
        } else {
            $message = implode("<br>", $errors);
            $message_type = "error";
        }
    }
}

include("../partials/header.php");
include("../partials/navbar.php");
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
    
    :root {
        --bg-main: <?php echo ($theme == 'dark') ? '#0B1437' : '#F4F7FE'; ?>;
        --bg-card: <?php echo ($theme == 'dark') ? '#111C44' : '#FFFFFF'; ?>;
        --bg-glass: <?php echo ($theme == 'dark') ? 'rgba(17, 28, 68, 0.95)' : 'rgba(255, 255, 255, 0.95)'; ?>;
        --bg-input: <?php echo ($theme == 'dark') ? '#0B1437' : '#F4F7FE'; ?>;
        --text-primary: <?php echo ($theme == 'dark') ? '#FFFFFF' : '#2B3674'; ?>;
        --text-secondary: <?php echo ($theme == 'dark') ? '#CBD5E1' : '#A3AED0'; ?>;
        --border-color: <?php echo ($theme == 'dark') ? '#2B3674' : '#E0E5F2'; ?>;
        --shadow-color: <?php echo ($theme == 'dark') ? 'rgba(0,0,0,0.25)' : 'rgba(0,0,0,0.04)'; ?>;
        --icon-color: <?php echo ($theme == 'dark') ? '#8F9BBA' : '#A3AED0'; ?>;
    }

    body { 
        background-color: var(--bg-main) !important; 
        transition: background-color 0.3s ease; 
        background-image: <?php echo ($theme == 'dark') 
            ? "url(\"data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Cfilter id='g'%3E%3CfeGaussianBlur stdDeviation='1.5' result='b'/%3E%3CfeMerge%3E%3CfeMergeNode in='b'/%3E%3CfeMergeNode in='SourceGraphic'/%3E%3C/feMerge%3E%3C/filter%3E%3C/defs%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.035' filter='url(%23g)'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")" 
            : "url(\"data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%234318ff' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")"; 
        ?> !important; 
    }

    .container, .container * { font-family: 'Poppins', sans-serif; }
    
    .form-card { 
        background: var(--bg-glass) !important; 
        backdrop-filter: blur(10px);
        border-radius: 24px !important; 
        box-shadow: 0px 15px 40px var(--shadow-color) !important; 
        border: 1px solid var(--border-color);
        max-width: 650px;
        margin: 50px auto;
        padding: 45px;
        transition: background 0.3s, border-color 0.3s;
    }
    
    .btn-back { 
        border-radius: 10px !important; 
        transition: 0.3s; 
        background: var(--bg-input); 
        color: var(--text-primary); 
        border: 1px solid var(--border-color); 
        padding: 10px 18px; 
        text-decoration: none; 
        display: inline-flex; 
        align-items: center; 
        gap: 6px; 
        font-size: 13px; 
        font-weight: 600; 
        margin-bottom: 30px;
    }
    .btn-back:hover { transform: translateY(-2px); box-shadow: 0 5px 15px var(--shadow-color); }
    
    .form-row { display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; }
    .form-group { flex: 1; min-width: 250px; margin-bottom: 0; }
    .form-group.full-width { width: 100%; margin-bottom: 20px;}
    
    label { color: var(--text-primary) !important; font-weight: 600 !important; font-size: 13px; display: block; margin-bottom: 8px; }
    
    .input-wrapper { position: relative; width: 100%; }
    .input-icon { 
        position: absolute; 
        left: 16px; 
        top: 50%; 
        transform: translateY(-80%); 
        margin-top: -1.5px; 
        width: 18px; 
        height: 18px; 
        color: var(--icon-color); 
        transition: 0.3s; 
        pointer-events: none;
    }
    
    .form-input { 
        width: 100%; 
        border-radius: 12px !important; 
        border: 1px solid var(--border-color) !important; 
        background-color: var(--bg-input) !important; 
        padding: 14px 14px 14px 45px !important; 
        color: var(--text-primary); 
        font-size: 14px; 
        transition: 0.3s; 
        box-sizing: border-box;
    }
    
    textarea.form-input { 
        min-height: 120px; 
        resize: vertical;
    }
    
    .form-input:focus { 
        outline: none; 
        border-color: #4318FF !important; 
        background-color: var(--bg-card) !important; 
        box-shadow: 0 0 0 3px rgba(67, 24, 255, 0.15); 
    }
    .form-input:focus + .input-icon { color: #4318FF; }

    .btn-submit { background: #4318FF; color: white; cursor: pointer; border: none; padding: 16px; border-radius: 12px; font-weight: 600; width: 100%; font-size: 15px; transition: 0.3s; margin-top: 10px; display: flex; justify-content: center; align-items: center; gap: 8px; }
    .btn-submit:hover { background: #3311DB; box-shadow: 0px 8px 20px rgba(67, 24, 255, 0.25); transform: translateY(-2px); }
</style>

<div class="container">
    <div class="form-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <a href="club_list.php" class="btn-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Back to List
            </a>
            
            <form method="POST" style="margin: 0;">
                <button type="submit" name="toggle_theme" style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary); border-radius: 8px; padding: 8px 12px; cursor: pointer;">
                    <?php echo ($theme == 'dark') ? '☀️ Theme' : '🌙 Theme'; ?>
                </button>
            </form>
        </div>

        <h2 style="color: var(--text-primary); font-weight: 800; margin-top: 0; font-size: 26px;">Edit Club Record</h2>
        <p style="color: var(--text-secondary); margin-bottom: 35px; font-size: 14px;">Update your affiliation details and leadership roles.</p>

        <?php if($message != "") { ?>
            <div class="message <?php echo $message_type; ?>" style="border-radius: 10px; margin-bottom: 25px; padding: 12px; background: #ffebee; color: #c62828; border: 1px solid #ffcdd2;">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Club/Society Name</label>
                    <div class="input-wrapper">
                        <input type="text" name="club_name" class="form-input" value="<?php echo htmlspecialchars($row['club_name']); ?>" maxlength="100" required>
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                </div>

                <div class="form-group">
                    <label>Your Role</label>
                    <div class="input-wrapper">
                        <input type="text" name="role" class="form-input" value="<?php echo htmlspecialchars($row['role']); ?>" maxlength="50" required>
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                </div>
            </div>

            <div class="form-group full-width">
                <label>Date Joined</label>
                <div class="input-wrapper">
                    <input type="date" name="join_date" class="form-input" style="color-scheme: <?php echo $theme; ?>;" value="<?php echo htmlspecialchars($row['join_date']); ?>" max="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y', strtotime('-10 years')) . '-01-01'; ?>" required>
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
            </div>

            <div class="form-group full-width">
                <label>Description & Achievements</label>
                <textarea name="description" class="form-input" maxlength="1000"><?php echo htmlspecialchars($row['description']); ?></textarea>
            </div>

            <button type="submit" name="submit" class="btn-submit">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Update Record
            </button>
        </form>
    </div>
</div>

</body>
</html>