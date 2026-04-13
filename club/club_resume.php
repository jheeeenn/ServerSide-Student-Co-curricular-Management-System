<?php 
include("../db.php");
require("../auth.php");

$base_path = "../"; 
$page_title = "Resume";
$show_cookie_notice = false; 

// --- LECTURER'S THEME LOGIC ---
if (isset($_POST['toggle_theme'])) {
    $_SESSION['theme_mode'] = (isset($_SESSION['theme_mode']) && $_SESSION['theme_mode'] == 'dark') ? 'light' : 'dark';
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
$theme = isset($_SESSION['theme_mode']) ? $_SESSION['theme_mode'] : 'light';

$user_id = $_SESSION['user_id'];

// Get all clubs ordered by newest first
$query = "SELECT * FROM clubs WHERE user_id='$user_id' ORDER BY join_date DESC";
$result = mysqli_query($con, $query);

$total_leadership = 0;
$grouped_clubs = []; // Array to group roles by club name

while($row = mysqli_fetch_assoc($result)) {
    // Count Leadership roles
    $role_lower = strtolower($row['role']);
    if (strpos($role_lower, 'president') !== false || strpos($role_lower, 'vice') !== false || strpos($role_lower, 'secretary') !== false || strpos($role_lower, 'treasurer') !== false || strpos($role_lower, 'director') !== false) {
        $total_leadership = $total_leadership + 1;
    }

    // Grouping Logic by Club Name
    $club_name_clean = trim($row['club_name']);
    $group_key = strtolower($club_name_clean); 

    if (!isset($grouped_clubs[$group_key])) {
        $grouped_clubs[$group_key] = [
            'display_name' => $club_name_clean,
            'roles' => []
        ];
    }
    
    array_push($grouped_clubs[$group_key]['roles'], $row);
}

include("../partials/header.php");
include("../partials/navbar.php");
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
    
    @media print {
        @page { 
            margin: 0; 
        }
        body { 
            padding: 1.5cm; 
            zoom: 0.9; 
        }
    }
    
    :root {
        --bg-main: <?php echo ($theme == 'dark') ? '#0B1437' : '#F4F7FE'; ?>;
        --bg-card: <?php echo ($theme == 'dark') ? '#111C44' : '#FFFFFF'; ?>;
        --text-primary: <?php echo ($theme == 'dark') ? '#FFFFFF' : '#2B3674'; ?>;
        --text-secondary: <?php echo ($theme == 'dark') ? '#CBD5E1' : '#A3AED0'; ?>;
        --border-color: <?php echo ($theme == 'dark') ? '#2B3674' : '#E0E5F2'; ?>;
    }

    body { 
        background-color: var(--bg-main) !important; 
        transition: 0.3s ease; 
        background-image: <?php echo ($theme == 'dark') 
            ? "url(\"data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Cfilter id='g'%3E%3CfeGaussianBlur stdDeviation='1.5' result='b'/%3E%3CfeMerge%3E%3CfeMergeNode in='b'/%3E%3CfeMergeNode in='SourceGraphic'/%3E%3C/feMerge%3E%3C/filter%3E%3C/defs%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.035' filter='url(%23g)'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")" 
            : "url(\"data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%234318ff' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")"; 
        ?> !important; 
    }

    .container, .container * { font-family: 'Poppins', sans-serif; }
    
    .btn-action { 
        display: inline-flex; 
        align-items: center; 
        gap: 6px; 
        padding: 10px 20px; 
        border-radius: 12px; 
        text-decoration: none; 
        font-weight: 600; 
        font-size: 14px; 
        border: none; 
        cursor: pointer; 
        transition: 0.3s; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
    }
    
    .btn-action:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    .btn-back { background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--border-color); }
    .btn-print { background: #4318FF; color: white; margin-left: 10px; }

    /* The Resume Paper Layout */
    .resume-paper {
        background: #FFFFFF; 
        color: #000000;
        max-width: 800px; 
        margin: 40px auto; 
        padding: 60px;
        border-radius: 8px; 
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }

    .resume-header { border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; text-align: center; }
    .resume-title { font-size: 28px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin: 0; }
    .resume-subtitle { font-size: 14px; color: #555; margin-top: 5px; }

    .summary-box { background: #f8f9fa; border: 1px solid #ddd; padding: 15px; border-radius: 8px; margin-bottom: 35px; text-align: center; font-weight: 600; font-size: 14px; }

    .org-block { margin-bottom: 35px; }
    .org-title { font-size: 20px; font-weight: 800; margin: 0 0 15px 0; border-bottom: 1px solid #eee; padding-bottom: 5px; color: #111; }
    
    .role-item { margin-bottom: 15px; padding-left: 15px; border-left: 2px solid #4318FF; }
    .role-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 5px; }
    .role-name { font-size: 16px; font-weight: 700; margin: 0; color: #222; }
    .role-date { font-size: 13px; color: #555; font-weight: 600; font-style: italic; }
    .role-desc { font-size: 14px; line-height: 1.6; color: #333; text-align: justify; margin-top: 5px; }

    @media print {
        body { background: white !important; }
        .sidebar, .navbar, .top-controls { display: none !important; }
        .resume-paper { box-shadow: none !important; margin: 0 !important; padding: 0 !important; max-width: 100% !important; }
    }
    
</style>

<div class="container">
    <div class="top-controls" style="text-align: center; margin-bottom: 20px;">
        <a href="club_list.php" class="btn-action btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Back to Dashboard
        </a>
        <button onclick="window.print()" class="btn-action btn-print">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Save as PDF / Print
        </button>
    </div>

    <div class="resume-paper">
        <div class="resume-header">
            <h1 class="resume-title">CLUBS & SOCIETIES RECORD</h1>
            <div class="resume-subtitle">Official Portfolio of Organizational Leadership & Affiliations</div>
        </div>

        <div class="summary-box">
            Total Unique Affiliations: <?php echo count($grouped_clubs); ?> &nbsp; | &nbsp; Leadership Roles Held: <?php echo $total_leadership; ?>
        </div>

        <?php 
        if(count($grouped_clubs) > 0) {
            foreach($grouped_clubs as $club_group) { 
        ?>
            <div class="org-block">
                <h3 class="org-title"><?php echo htmlspecialchars($club_group['display_name']); ?></h3>
                
                <?php 
                foreach($club_group['roles'] as $role_record) { 
                ?>
                    <div class="role-item">
                        <div class="role-header">
                            <div class="role-name"><?php echo htmlspecialchars($role_record['role']); ?></div>
                            <div class="role-date">Started: <?php echo date("F Y", strtotime($role_record['join_date'])); ?></div>
                        </div>
                        <div class="role-desc">
                            <?php echo nl2br(htmlspecialchars($role_record['description'])); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php 
            } 
        } else {
            echo "<p style='text-align:center; font-style:italic;'>No clubs records found.</p>";
        }
        ?>
    </div>
</div>

</body>
</html>