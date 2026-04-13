<?php 
include("../db.php");
require("../auth.php");

$base_path = "../"; 
$page_title = "Clubs & Societies";
$page_subtitle = "Manage your club memberships and roles";
$show_cookie_notice = false; 

// --- LECTURER'S THEME LOGIC ---
if (isset($_POST['toggle_theme'])) {
    $_SESSION['theme_mode'] = (isset($_SESSION['theme_mode']) && $_SESSION['theme_mode'] == 'dark') ? 'light' : 'dark';
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
$theme = isset($_SESSION['theme_mode']) ? $_SESSION['theme_mode'] : 'light';

$user_id = $_SESSION['user_id'];

$status_message = "";
$status_type = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == "added") { 
        $status_message = "Club record added successfully."; 
        $status_type = "success"; 
    } else if ($_GET['status'] == "updated") { 
        $status_message = "Club record updated successfully."; 
        $status_type = "success"; 
    } else if ($_GET['status'] == "deleted") { 
        $status_message = "Club record deleted successfully."; 
        $status_type = "success"; 
    } else if ($_GET['status'] == "error") { 
        $status_message = "An error occurred. Please try again."; 
        $status_type = "error"; 
    }
}

$sort_order = "DESC";
if (isset($_GET['sort_order'])) { 
    $sort_order = $_GET['sort_order']; 
}

$role_filter = "";
if (isset($_GET['role_filter'])) { 
    $role_filter = $_GET['role_filter']; 
}

$leader_keywords = ['president', 'vice', 'secretary', 'treasurer', 'director', 'chair', 'head', 'manager', 'coordinator', 'captain', 'lead', 'auditor', 'executive', 'officer'];

// Find the newest date for each club to determine the "Active" status accurately
$truth_query = "SELECT club_name, join_date FROM clubs WHERE user_id='$user_id'";
$truth_result = mysqli_query($con, $truth_query);
$club_newest_dates = [];
$absolute_unique_clubs = [];

while($t_row = mysqli_fetch_assoc($truth_result)) {
    $clean_name = strtolower(trim($t_row['club_name']));
    $t_timestamp = strtotime($t_row['join_date']);
    
    if (!in_array($clean_name, $absolute_unique_clubs)) {
        array_push($absolute_unique_clubs, $clean_name);
        $club_newest_dates[$clean_name] = $t_timestamp;
    } else {
        if ($t_timestamp > $club_newest_dates[$clean_name]) {
            $club_newest_dates[$clean_name] = $t_timestamp;
        }
    }
}

// Build the query based on filters
$query = "SELECT * FROM clubs WHERE user_id='$user_id'";

if ($role_filter == 'leader' || $role_filter == 'member') {
    $sql_conditions = [];
    foreach ($leader_keywords as $keyword) {
        if ($role_filter == 'leader') { 
            array_push($sql_conditions, "role LIKE '%$keyword%'"); 
        } else { 
            array_push($sql_conditions, "role NOT LIKE '%$keyword%'"); 
        }
    }
    
    if ($role_filter == 'leader') { 
        $query .= " AND (" . implode(" OR ", $sql_conditions) . ")"; 
    } else { 
        $query .= " AND (" . implode(" AND ", $sql_conditions) . ")"; 
    }
}

$query .= " ORDER BY join_date $sort_order";
$result = mysqli_query($con, $query);

$club_data = [];
$total_leadership = 0;
$total_cumulative_days = 0;
$current_timestamp = time();
$filtered_unique_clubs = [];

while($row = mysqli_fetch_assoc($result)) {
    array_push($club_data, $row);
    
    $role_lower = strtolower($row['role']);
    $is_leader = false;
    
    foreach ($leader_keywords as $keyword) {
        if (strpos($role_lower, $keyword) !== false) {
            $is_leader = true;
            break; 
        }
    }
    
    if ($is_leader) { 
        $total_leadership = $total_leadership + 1; 
    }
    
    $join_timestamp = strtotime($row['join_date']);
    if ($join_timestamp <= $current_timestamp) {
        $time_difference = $current_timestamp - $join_timestamp;
        $days_active = floor($time_difference / (60 * 60 * 24));
        $total_cumulative_days = $total_cumulative_days + $days_active;
    }

    $clean_name = strtolower(trim($row['club_name']));
    if (!in_array($clean_name, $filtered_unique_clubs)) {
        array_push($filtered_unique_clubs, $clean_name);
    }
}

// Calculate Statistics
$total_clubs = count($absolute_unique_clubs); 

$exp_years = floor($total_cumulative_days / 365);
$exp_months = floor(($total_cumulative_days % 365) / 30);

$experience_text = "";
if ($exp_years > 0) { 
    $experience_text .= $exp_years . " Year";
    if ($exp_years > 1) {
        $experience_text .= "s ";
    } else {
        $experience_text .= " ";
    }
}
if ($exp_months > 0) { 
    $experience_text .= $exp_months . " Month";
    if ($exp_months > 1) {
        $experience_text .= "s";
    }
}
if (trim($experience_text) == "") { 
    $experience_text = "Just Started"; 
}

$leader_pct = 0;
$member_pct = 0;
if (count($club_data) > 0) {
    $calc_pct = round(($total_leadership / count($club_data)) * 100);
    $leader_pct = $calc_pct;
    $member_pct = 100 - $leader_pct;
}

include("../partials/header.php");
include("../partials/navbar.php");
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
    
    :root {
        --bg-main: <?php echo ($theme == 'dark') ? '#0B1437' : '#F4F7FE'; ?>;
        --bg-card: <?php echo ($theme == 'dark') ? '#111C44' : '#FFFFFF'; ?>;
        --bg-glass: <?php echo ($theme == 'dark') ? 'rgba(17, 28, 68, 0.95)' : 'rgba(255, 255, 255, 0.95)'; ?>;
        --bg-footer: <?php echo ($theme == 'dark') ? 'rgba(11, 20, 55, 0.5)' : 'rgba(244, 247, 254, 0.5)'; ?>;
        --bg-input: <?php echo ($theme == 'dark') ? '#0B1437' : '#F4F7FE'; ?>;
        --text-primary: <?php echo ($theme == 'dark') ? '#FFFFFF' : '#2B3674'; ?>;
        --text-secondary: <?php echo ($theme == 'dark') ? '#CBD5E1' : '#A3AED0'; ?>;
        --text-body: <?php echo ($theme == 'dark') ? '#E2E8F0' : '#4A5568'; ?>;
        --border-color: <?php echo ($theme == 'dark') ? '#2B3674' : '#E0E5F2'; ?>;
        --shadow-color: <?php echo ($theme == 'dark') ? 'rgba(0,0,0,0.25)' : 'rgba(0,0,0,0.04)'; ?>;
        --empty-bg: <?php echo ($theme == 'dark') ? 'rgba(255, 255, 255, 0.03)' : 'rgba(67, 24, 255, 0.02)'; ?>;
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
    
    .header-box, .filter-box, .distribution-bar-wrapper, .stat-card { 
        background: var(--bg-glass) !important; 
        backdrop-filter: blur(10px); 
        border-radius: 16px !important; 
        box-shadow: 0px 5px 20px var(--shadow-color) !important; 
        border: 1px solid var(--border-color); 
        transition: background 0.3s, border-color 0.3s; 
    }
    
    .btn { 
        border-radius: 10px !important; 
        font-weight: 600; transition: 0.3s; 
        padding: 10px 18px !important; 
        font-size: 13px; border:none; 
        cursor:pointer;
    }
    
    .btn-filter { background-color: #4318FF !important; color: white !important;}
    .btn-filter:hover { background-color: #3311DB !important; }
    
    .form-input { 
        border-radius: 10px !important; 
        border: 1px solid var(--border-color) !important; 
        background-color: var(--bg-input) !important; 
        color: var(--text-primary) !important; 
        font-size: 13px; 
        padding: 10px 15px !important; 
        width: 100%; 
        box-sizing: border-box; 
        transition: 0.3s;
    }
    .form-input:focus { 
        outline: none; 
        border-color: #4318FF !important; 
        box-shadow: 0 0 0 3px rgba(67, 24, 255, 0.2); 
    }

    .theme-toggle { 
        background: var(--bg-card); 
        border: 1px solid var(--border-color); 
        color: var(--text-primary); 
        width: 45px; height: 45px; 
        border-radius: 12px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 20px; 
        cursor: pointer; 
        transition: 0.3s; 
        box-shadow: 0px 5px 15px var(--shadow-color); 
        margin: 0; 
    }
    .theme-toggle:hover { transform: translateY(-2px); }

    .stats-container { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
    .stat-card { flex: 1; min-width: 180px; padding: 20px; display: flex; align-items: center; gap: 15px; }
    .stat-icon { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .stat-info h4 { margin: 0; font-size: 12px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;}
    .stat-info p { margin: 0; font-size: 20px; color: var(--text-primary); font-weight: 800; line-height: 1.2;}

    .dist-title { font-size: 13px; color: var(--text-primary); font-weight: 700; margin-bottom: 10px; display: flex; justify-content: space-between; }
    .dist-track { width: 100%; height: 12px; background: var(--bg-input); border-radius: 10px; display: flex; overflow: hidden; }
    .dist-leader { background: linear-gradient(90deg, #4318FF, #8C3FFF); height: 100%; transition: width 1s; }
    .dist-member { background: linear-gradient(90deg, #A3AED0, #E0E5F2); height: 100%; transition: width 1s; }

    .club-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
    .club-card { background: var(--bg-card); border-radius: 16px; overflow: hidden; box-shadow: 0px 5px 15px var(--shadow-color); transition: 0.3s ease; display: flex; flex-direction: column; border: 1px solid var(--border-color); position: relative;}
    .club-card:hover { transform: translateY(-5px); box-shadow: 0px 15px 30px rgba(67, 24, 255, 0.1); }

    .card-cover { height: 95px; position: relative; overflow: hidden; display: flex; align-items: center; padding: 0 20px; color: white; justify-content: space-between;}
    .club-card:nth-child(4n+1) .card-cover { background: linear-gradient(135deg, #4318FF, #8C3FFF); }
    .club-card:nth-child(4n+2) .card-cover { background: linear-gradient(135deg, #16C098, #00A67E); }
    .club-card:nth-child(4n+3) .card-cover { background: linear-gradient(135deg, #FF7B54, #FF5252); }
    .club-card:nth-child(4n+4) .card-cover { background: linear-gradient(135deg, #00B4DB, #0083B0); }

    .cover-title { font-size: 19px; font-weight: 700; z-index: 2; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-shadow: 0px 2px 4px rgba(0,0,0,0.3); }
    .cover-watermark { position: absolute; top: -10px; right: 5px; font-size: 80px; font-weight: 800; color: rgba(255, 255, 255, 0.15); line-height: 1; user-select: none; }

    .status-badge { z-index: 2; padding: 4px 8px; border-radius: 6px; font-size: 10px; font-weight: 700; text-transform: uppercase; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
    .status-active { background: #16C098; color: white; }
    .status-past { background: rgba(255,255,255,0.2); color: white; backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.4); }

    .card-content { padding: 20px; flex: 1; display: flex; flex-direction: column; background: var(--bg-card); }
    .badges-row { display: flex; gap: 8px; margin-bottom: 12px; flex-wrap: wrap; }
    .badge { padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    
    .role-member { background: var(--bg-input); color: #4318FF; border: 1px solid var(--border-color); }
    .role-leader { background: linear-gradient(135deg, #FFF9E6, #FFF0B3); color: #B37D00; border: 1px solid #FFE680; }
    .time-badge { background: var(--bg-input); color: var(--text-secondary); border: 1px dashed var(--border-color); }

    .club-desc { font-size: 13px; color: var(--text-body); margin-bottom: 20px; line-height: 1.6; flex: 1; max-height: 75px; overflow-y: auto; padding-right: 5px; }
    .club-desc::-webkit-scrollbar { width: 4px; }
    .club-desc::-webkit-scrollbar-track { background: transparent; }
    .club-desc::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 4px; }

    .skills-container { margin-bottom: 15px; display: flex; flex-wrap: wrap; gap: 6px; min-height: 22px;}
    .skill-tag { font-size: 10.5px; background: transparent; border: 1px solid var(--border-color); color: var(--text-secondary); padding: 3px 8px; border-radius: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;}

    .milestone-container { margin-bottom: 15px; }
    .milestone-labels { display: flex; justify-content: space-between; font-size: 11px; color: var(--text-secondary); font-weight: 600; margin-bottom: 5px;}
    .progress-track { width: 100%; height: 6px; background: var(--bg-input); border-radius: 10px; overflow: hidden; }
    .progress-fill { height: 100%; background: linear-gradient(90deg, #4318FF, #16C098); border-radius: 10px; transition: width 1s ease-out; }

    .card-footer { display: flex; justify-content: space-between; align-items: center; background: var(--bg-footer); margin: 0 -20px -20px -20px; padding: 12px 20px; border-top: 1px solid var(--border-color); }
    .join-date { font-size: 12px; color: var(--text-secondary); font-weight: 600; display: flex; align-items: center; gap: 5px; }
    .action-group { display: flex; gap: 8px; }
    .icon-btn { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: 0.2s; text-decoration: none; background: var(--bg-card); border: 1px solid var(--border-color); }
    .icon-edit { color: #4318FF; } .icon-edit:hover { background: #4318FF; color: white; border-color: #4318FF; }
    .icon-delete { color: #E31A1A; } .icon-delete:hover { background: #E31A1A; color: white; border-color: #E31A1A; }

    .empty-card { border: 2px dashed #4318FF; border-radius: 16px; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 30px 20px; text-align: center; background: var(--empty-bg); transition: 0.3s; text-decoration: none; min-height: 200px; }
    .empty-card:hover { background: rgba(67, 24, 255, 0.05); transform: translateY(-5px); border-color: #3311DB; }
    .empty-icon { font-size: 32px; background: var(--bg-card); color: var(--text-primary); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0px 5px 15px rgba(67, 24, 255, 0.1); margin-bottom: 10px; transition: 0.3s; }
</style>

<div class="container">
    <div class="header-box" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 20px 25px;">
        <div>
            <h2 style="color: var(--text-primary); margin-bottom: 5px; font-weight: 700; font-size: 24px;">🏅 Clubs & Societies</h2>
            <p style="color: var(--text-secondary); margin: 0; font-size: 14px;">Manage your club affiliations and track your journey.</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="club_timeline.php" style="background: #16C098; color: white; padding: 12px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 14px; box-shadow: 0 5px 15px rgba(22, 192, 152, 0.2); transition: 0.3s; display: flex; align-items: center; gap: 6px;">📍 View Timeline</a>
            <a href="club_resume.php" style="background: #2B3674; color: white; padding: 12px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 14px; box-shadow: 0 5px 15px rgba(43, 54, 116, 0.2); transition: 0.3s; display: flex; align-items: center; gap: 6px;">📄 Generate Resume</a>
            
            <form method="POST" style="margin: 0;">
                <button type="submit" name="toggle_theme" class="theme-toggle" id="themeIcon">
                    <?php echo ($theme == 'dark') ? '☀️' : '🌙'; ?>
                </button>
            </form>
        </div>
    </div>

    <?php if(!empty($status_message)) { ?>
        <div class="message <?php echo $status_type; ?>" style="border-radius: 10px; font-size: 13px;">
            <?php echo $status_message; ?>
        </div>
    <?php } ?>

    <div class="stats-container">
        <div class="stat-card" style="border: 1px solid #4318FF22; box-shadow: 0 4px 15px #4318FF11;">
            <div class="stat-icon" style="background: #E8E5FF; color: #4318FF;">⏳</div>
            <div class="stat-info">
                <h4 style="color: #4318FF;">Total Experience</h4>
                <p style="color: var(--text-primary); font-size: 18px;"><?php echo $experience_text; ?></p>
            </div>
        </div>
        <div class="stat-card" style="border: 1px solid #16C09822; box-shadow: 0 4px 15px #16C09811;">
            <div class="stat-icon" style="background: #E5F8F3; color: #16C098;">🏫</div>
            <div class="stat-info">
                <h4 style="color: #16C098;">Unique Clubs</h4> 
                <p style="color: var(--text-primary);"><?php echo $total_clubs; ?></p>
            </div>
        </div>
        <div class="stat-card" style="border: 1px solid #B37D0022; box-shadow: 0 4px 15px #B37D0011;">
            <div class="stat-icon" style="background: #FFF0B3; color: #B37D00;">💼</div>
            <div class="stat-info">
                <h4 style="color: #B37D00;">Leadership Roles</h4>
                <p style="color: var(--text-primary);"><?php echo $total_leadership; ?></p>
            </div>
        </div>
    </div>

    <?php if(count($club_data) > 0) { ?>
    <div class="distribution-bar-wrapper" style="padding: 20px; margin-bottom: 25px;">
        <div class="dist-title">
            <span>Role Distribution (Filtered Records)</span>
            <span><span style="color: #4318FF;">💼 <?php echo $leader_pct; ?>% Leaders</span> &nbsp;|&nbsp; <span style="color: #A3AED0;">👤 <?php echo $member_pct; ?>% Members</span></span>
        </div>
        <div class="dist-track">
            <div class="dist-leader" style="width: <?php echo $leader_pct; ?>%;"></div>
            <div class="dist-member" style="width: <?php echo $member_pct; ?>%;"></div>
        </div>
    </div>
    <?php } ?>

    <div class="filter-box" style="padding: 20px;">
        <form method="GET" action="club_list.php" class="filter-form" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
            
            <div class="form-group group-search" style="flex: 2; min-width: 200px;">
                <label style="color: var(--text-body); font-weight: 700;">Live Search</label>
                <input type="text" id="liveSearchInput" class="form-input" onkeyup="liveSearch()" placeholder="Type to filter instantly...">
            </div>
            
            <div class="form-group group-type" style="flex: 1.5; min-width: 150px;">
                <label style="color: var(--text-body); font-weight: 700;">Filter by Role</label>
                <select name="role_filter" class="form-input">
                    <option value="">All Roles</option>
                    <option value="leader" <?php if ($role_filter == "leader") echo "selected"; ?>>Leadership Only</option>
                    <option value="member" <?php if ($role_filter == "member") echo "selected"; ?>>Members Only</option>
                </select>
            </div>

            <div class="form-group group-sort" style="flex: 1.5; min-width: 150px;">
                 <label style="color: var(--text-body); font-weight: 700;">Sort by Date</label>
                <select name="sort_order" class="form-input">
                    <option value="DESC" <?php if ($sort_order == "DESC") echo "selected"; ?>>Newest First</option>
                    <option value="ASC" <?php if ($sort_order == "ASC") echo "selected"; ?>>Oldest First</option>
                </select>
            </div>

            <div class="form-group group-btn" style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-filter" style="margin: 0;">Apply Sort</button>
                <a href="club_list.php" class="btn" style="margin: 0; background: var(--bg-input); color: var(--text-primary); border: 1px solid var(--border-color); display:flex; align-items:center;">Clear</a>
            </div>
        </form>
    </div>

    <div class="club-grid" id="clubGrid">
        
        <a href="club_add.php" class="empty-card searchable-card">
            <div class="empty-icon">➕</div>
            <h3 style="color: #4318FF; font-size: 18px; font-weight: 700; margin-bottom: 5px;">Register New</h3>
            <p style="color: var(--text-secondary); font-size: 13px; margin: 0; line-height: 1.4;">Add your latest affiliation.</p>
        </a>

        <?php if(count($club_data) > 0) { ?>
            <?php foreach($club_data as $row) { 
                
                $role_lower = strtolower(htmlspecialchars($row['role']));
                $is_leader = false;
                
                foreach ($leader_keywords as $keyword) {
                    if (strpos($role_lower, $keyword) !== false) {
                        $is_leader = true;
                        break; 
                    }
                }
                
                $role_class = "role-member"; $role_icon = "👤";
                if ($is_leader) { $role_class = "role-leader"; $role_icon = "👑"; }

                $join_timestamp = strtotime($row['join_date']);
                $time_active = "New"; $progress_percent = 0; $current_year = 1;
                
                if ($join_timestamp <= $current_timestamp) {
                    $time_difference = $current_timestamp - $join_timestamp;
                    $total_days_active = floor($time_difference / (60 * 60 * 24));
                    
                    $years = floor($total_days_active / 365);
                    $months = floor(($total_days_active % 365) / 30);
                    
                    $time_active = "";
                    if ($years > 0) { $time_active .= $years . " yr "; }
                    if ($months > 0) { $time_active .= $months . " mo"; }
                    if ($time_active == "") { $time_active = $total_days_active . " d"; }
                    if ($total_days_active == 0) { $time_active = "Today"; }

                    $current_year = floor($total_days_active / 365) + 1;
                    $days_into_year = $total_days_active % 365;
                    $progress_percent = ($days_into_year / 365) * 100;
                    if ($progress_percent < 5) { $progress_percent = 5; } 
                }

                $club_name_clean = preg_replace('/[^a-zA-Z]/', '', $row['club_name']);
                $club_initial = "C";
                if (strlen($club_name_clean) > 0) { $club_initial = strtoupper(substr($club_name_clean, 0, 1)); }

                // Check active role against the background Truth query
                $is_active_role = false;
                $clean_name_match = strtolower(trim($row['club_name']));
                if (isset($club_newest_dates[$clean_name_match]) && $join_timestamp == $club_newest_dates[$clean_name_match]) {
                    $is_active_role = true;
                }

                $skills = [];
                $text_check = strtolower($row['club_name'] . " " . $row['description'] . " " . $row['role']);
                
                if($is_leader) { array_push($skills, 'Leadership'); }
                if(strpos($text_check, 'bodybuilding') !== false || strpos($text_check, 'gym') !== false || strpos($text_check, 'sport') !== false || strpos($text_check, 'badminton') !== false || strpos($text_check, 'fitness') !== false) { array_push($skills, 'Sports & Fitness'); }
                if(strpos($text_check, 'computer') !== false || strpos($text_check, 'tech') !== false || strpos($text_check, 'science') !== false || strpos($text_check, 'engineering') !== false || strpos($text_check, 'math') !== false || strpos($text_check, 'robot') !== false) { array_push($skills, 'Academic & Tech'); }
                if(strpos($text_check, 'music') !== false || strpos($text_check, 'dance') !== false || strpos($text_check, 'art') !== false || strpos($text_check, 'culture') !== false || strpos($text_check, 'language') !== false) { array_push($skills, 'Arts & Culture'); }
                if(strpos($text_check, 'volunteer') !== false || strpos($text_check, 'community') !== false || strpos($text_check, 'rotaract') !== false) { array_push($skills, 'Community Service'); }
                if(strpos($text_check, 'business') !== false || strpos($text_check, 'entrepreneur') !== false || strpos($text_check, 'finance') !== false || strpos($text_check, 'treasurer') !== false) { array_push($skills, 'Business & Finance'); }

                if(count($skills) == 0) { array_push($skills, 'General Member'); }

                $unique_skills = [];
                foreach ($skills as $skill) {
                    if (!in_array($skill, $unique_skills)) { array_push($unique_skills, $skill); }
                }
                $final_skills = [];
                if (isset($unique_skills[0])) { $final_skills[] = $unique_skills[0]; }
                if (isset($unique_skills[1])) { $final_skills[] = $unique_skills[1]; }
            ?>
                <div class="club-card searchable-card">
                    <div class="card-cover">
                        <div class="cover-watermark"><?php echo $club_initial; ?></div>
                        <h3 class="cover-title search-title"><?php echo htmlspecialchars($row['club_name']); ?></h3>
                        
                        <?php if($is_active_role) { ?>
                            <div class="status-badge status-active">Active Role</div>
                        <?php } else { ?>
                            <div class="status-badge status-past">Past Role</div>
                        <?php } ?>
                    </div>
                    
                    <div class="card-content">
                        <div class="badges-row">
                            <div class="badge <?php echo $role_class; ?>">
                                <?php echo $role_icon; ?> <span class="search-role"><?php echo htmlspecialchars($row['role']); ?></span>
                            </div>
                            <div class="badge time-badge" title="Calculated from your join date">
                                ⏱️ <?php echo trim($time_active); ?>
                            </div>
                        </div>
                        
                        <div class="club-desc">
                            <?php echo htmlspecialchars($row['description']); ?>
                        </div>

                        <div class="skills-container">
                            <?php foreach($final_skills as $skill) { ?>
                                <span class="skill-tag"><?php echo $skill; ?></span>
                            <?php } ?>
                        </div>

                        <div class="milestone-container" title="Progress towards your Year <?php echo $current_year; ?> anniversary!">
                            <div class="milestone-labels">
                                <span>Year <?php echo $current_year - 1; ?></span>
                                <span style="color: #4318FF;">Year <?php echo $current_year; ?></span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width: <?php echo $progress_percent; ?>%;"></div>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <div class="join-date">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:3px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                Started: <?php echo date("d M Y", strtotime($row['join_date'])); ?>
                            </div>
                            <div class="action-group">
                                <a href="club_edit.php?club_id=<?php echo $row['club_id']; ?>" class="icon-btn icon-edit" title="Edit Record">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>
                                <a href="club_delete.php?club_id=<?php echo $row['club_id']; ?>" class="icon-btn icon-delete" title="Delete Record" onclick="return confirm('Are you sure you want to permanently delete this club record?')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

    </div>
</div>

<script>
    function liveSearch() {
        var input = document.getElementById('liveSearchInput').value.toLowerCase();
        var cards = document.getElementsByClassName('searchable-card');
        
        for (var i = 0; i < cards.length; i++) {
            if (cards[i].classList.contains('empty-card')) continue;
            
            var title = cards[i].querySelector('.search-title').innerText.toLowerCase();
            var role = cards[i].querySelector('.search-role').innerText.toLowerCase();
            
            if (title.indexOf(input) > -1 || role.indexOf(input) > -1) {
                cards[i].style.display = "";
            } else {
                cards[i].style.display = "none";
            }
        }
    }

    window.onload = function() {
        if (window.history.replaceState) {
            var currentUrl = window.location.href;
            if (currentUrl.indexOf('status=') > -1) {
                var cleanUrl = currentUrl.substring(0, currentUrl.indexOf('?'));
                window.history.replaceState({}, document.title, cleanUrl);
            }
        }
    };
</script>

<br><br>
</body>
</html>