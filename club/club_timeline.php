<?php 
include("../db.php");
require("../auth.php");

$base_path = "../"; 
$page_title = "My Club Journey";
$show_cookie_notice = false; 

// --- LECTURER'S THEME LOGIC ---
if (isset($_POST['toggle_theme'])) {
    $_SESSION['theme_mode'] = (isset($_SESSION['theme_mode']) && $_SESSION['theme_mode'] == 'dark') ? 'light' : 'dark';
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
$theme = isset($_SESSION['theme_mode']) ? $_SESSION['theme_mode'] : 'light';

$user_id = $_SESSION['user_id'];

// Order ASC to show oldest first in the timeline
$query = "SELECT * FROM clubs WHERE user_id='$user_id' ORDER BY join_date ASC";
$result = mysqli_query($con, $query);

$club_data = [];
while($row = mysqli_fetch_assoc($result)) {
    array_push($club_data, $row);
}

include("../partials/header.php");
include("../partials/navbar.php");
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
    
    :root {
        --bg-main: <?php echo ($theme == 'dark') ? '#0B1437' : '#F4F7FE'; ?>;
        --bg-card: <?php echo ($theme == 'dark') ? '#111C44' : '#FFFFFF'; ?>;
        --text-primary: <?php echo ($theme == 'dark') ? '#FFFFFF' : '#2B3674'; ?>;
        --text-secondary: <?php echo ($theme == 'dark') ? '#CBD5E1' : '#A3AED0'; ?>;
        --border-color: <?php echo ($theme == 'dark') ? '#2B3674' : '#E0E5F2'; ?>;
        --line-color: <?php echo ($theme == 'dark') ? '#8C3FFF' : '#4318FF'; ?>;
        --badge-bg: <?php echo ($theme == 'dark') ? 'rgba(255, 152, 0, 0.2)' : '#FFF0B3'; ?>;
        --badge-text: <?php echo ($theme == 'dark') ? '#FFB74D' : '#B37D00'; ?>;
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
    
    .btn-back { 
        display: inline-flex; 
        align-items: center; 
        gap: 6px; 
        background: var(--bg-card); 
        color: var(--text-primary); 
        padding: 10px 20px; 
        border-radius: 12px; 
        text-decoration: none; 
        font-weight: 600; 
        font-size: 14px; 
        border: 1px solid var(--border-color); 
        margin-bottom: 30px; 
        transition: 0.3s; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
    }
    
    .btn-back:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }

    /* --- TIMELINE CSS --- */
    .timeline { 
        position: relative; 
        max-width: 800px; 
        margin: 0 auto; 
        padding: 40px 0; 
    }
    
    .timeline::after {
        content: ''; 
        position: absolute; 
        width: 4px; 
        background-color: var(--line-color);
        top: 0; bottom: 0; 
        left: 50%; 
        margin-left: -2px; 
        border-radius: 2px;
    }

    .timeline-container { 
        padding: 10px 40px; 
        position: relative; 
        background-color: inherit; 
        width: 50%; 
        box-sizing: border-box; 
    }
    
    .timeline-left { left: 0; }
    .timeline-right { left: 50%; }

    /* Standard Timeline Dot */
    .timeline-container::after {
        content: ''; 
        position: absolute; 
        width: 20px; height: 20px; 
        right: -13px;
        background-color: var(--bg-main); 
        border: 4px solid #16C098; 
        top: 25px; 
        border-radius: 50%; 
        z-index: 1;
    }
    
    /* Highlighted dot for progressions */
    .timeline-container.progression::after {
        border-color: #FF9800; 
        background-color: var(--badge-bg);
    }

    .timeline-right::after { left: -15px; }

    .timeline-content {
        padding: 25px; 
        background: var(--bg-card); 
        position: relative; 
        border-radius: 16px;
        border: 1px solid var(--border-color); 
        box-shadow: 0 5px 20px rgba(0,0,0,0.05); 
        transition: 0.3s;
    }
    
    .timeline-content:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
    }

    .time-header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
    .time-date { font-weight: 700; color: #16C098; font-size: 14px; }
    
    .progression-badge { 
        background: var(--badge-bg); 
        color: var(--badge-text); 
        font-size: 10px; 
        font-weight: 700; 
        padding: 4px 8px; 
        border-radius: 8px; 
        text-transform: uppercase; 
        letter-spacing: 0.5px; 
        border: 1px solid var(--badge-text); 
    }

    .time-title { font-size: 20px; font-weight: 800; color: var(--text-primary); margin: 0 0 5px 0; }
    .time-role { font-size: 14px; color: #4318FF; font-weight: 600; margin-bottom: 10px; }
    
    .timeline-edit-link { 
        font-size: 13px; color: var(--text-secondary); text-decoration: none; 
        font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: 0.3s; 
    }
    .timeline-edit-link:hover { color: var(--line-color); }

    @media screen and (max-width: 600px) {
        .timeline::after { left: 31px; }
        .timeline-container { width: 100%; padding-left: 70px; padding-right: 25px; }
        .timeline-container::after { left: 18px; }
        .timeline-right { left: 0%; }
    }
</style>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <a href="club_list.php" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Back to Dashboard
        </a>
        <form method="POST" style="margin: 0;">
            <button type="submit" name="toggle_theme" style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary); border-radius: 8px; padding: 8px 12px; cursor: pointer;">
                <?php echo ($theme == 'dark') ? '☀️ Theme' : '🌙 Theme'; ?>
            </button>
        </form>
    </div>
    
    <div style="text-align: center; margin-bottom: 40px; background: var(--bg-card); padding: 30px; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
        <h2 style="color: var(--text-primary); font-weight: 800; font-size: 28px; margin:0;">Journey Timeline</h2>
        <p style="color: var(--text-secondary); margin-top: 5px;">A visual history of your journey.</p>
    </div>

    <?php if(count($club_data) > 0) { ?>
        <div class="timeline">
            <?php 
            $is_left = true;
            $seen_clubs = array(); 

            foreach($club_data as $row) { 
                $position_class = $is_left ? "timeline-left" : "timeline-right";
                $is_left = !$is_left; 

                // Check if student was already in this club to mark as an updated role
                $clean_club_name = strtolower(trim($row['club_name']));
                $is_progression = false;

                if (in_array($clean_club_name, $seen_clubs)) {
                    $is_progression = true; 
                    $position_class .= " progression"; 
                } else {
                    array_push($seen_clubs, $clean_club_name); 
                }
            ?>
                <div class="timeline-container <?php echo $position_class; ?>">
                    <div class="timeline-content">
                        <div class="time-header-row">
                            <div class="time-date"><?php echo date("F Y", strtotime($row['join_date'])); ?></div>
                            <?php if($is_progression) { ?>
                                <div class="progression-badge">Role Update</div>
                            <?php } ?>
                        </div>
                        
                        <h3 class="time-title"><?php echo htmlspecialchars($row['club_name']); ?></h3>
                        <div class="time-role"><?php echo htmlspecialchars($row['role']); ?></div>
                        
                        <a href="club_edit.php?club_id=<?php echo $row['club_id']; ?>" class="timeline-edit-link">
                            Edit Record <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div style="text-align:center; background: var(--bg-card); padding: 50px; border-radius: 16px; border: 1px solid var(--border-color); max-width: 600px; margin: 40px auto; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
            <div style="font-size: 45px; margin-bottom: 15px;">📭</div>
            <h3 style="color: var(--text-primary); margin: 0 0 10px 0; font-size: 22px;">No Journey Data Yet</h3>
            <p style="color: var(--text-secondary); margin: 0 0 25px 0;">You haven't added any club records.</p>
            <a href="club_add.php" style="background: #4318FF; color: white; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 600; display: inline-block; transition: 0.3s;">+ Add Your First Club</a>
        </div>
    <?php } ?>

</div>

</body>
</html>