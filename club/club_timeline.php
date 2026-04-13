<?php 
include("../db.php");
require("../auth.php");

$base_path = "../"; 
$page_title = "Club Tracker";
$page_subtitle = "Visual Timeline of your Club Memberships";
$show_cookie_notice = false; 

if (isset($_POST['toggle_theme'])) {
    $_SESSION['theme_mode'] = (isset($_SESSION['theme_mode']) && $_SESSION['theme_mode'] == 'dark') ? 'light' : 'dark';
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
$theme = isset($_SESSION['theme_mode']) ? $_SESSION['theme_mode'] : 'light';

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM clubs WHERE user_id='$user_id' ORDER BY join_date ASC";
$result = mysqli_query($con, $query);

include("../partials/header.php");
include("../partials/navbar.php");
?>

<div style="position: absolute; top: 18px; right: 30px; z-index: 9999;">
    <form method="POST" style="margin: 0;">
        <button type="submit" name="toggle_theme" class="btn" style="background-color: #343a40; color: white; border: 1px solid #fff;">Toggle Theme</button>
    </form>
</div>

<style>
    .header-box { background-color: #ffffff !important; }

    * { box-sizing: border-box; }
    .timeline { position: relative; max-width: 1200px; margin: 0 auto; }
    .timeline::after { content: ''; position: absolute; width: 6px; background-color: white; top: 0; bottom: 0; left: 50%; margin-left: -3px; }
    .timeline .container { padding: 10px 40px; position: relative; background-color: inherit; width: 50%; margin: 0; }
    .timeline .container::after { content: ''; position: absolute; width: 25px; height: 25px; right: -17px; background-color: white; border: 4px solid #FF9F55; top: 15px; border-radius: 50%; z-index: 1; }
    .left { left: 0; }
    .right { left: 50%; }
    .left::before { content: " "; height: 0; position: absolute; top: 22px; width: 0; z-index: 1; right: 30px; border: medium solid white; border-width: 10px 0 10px 10px; border-color: transparent transparent transparent white; }
    .right::before { content: " "; height: 0; position: absolute; top: 22px; width: 0; z-index: 1; left: 30px; border: medium solid white; border-width: 10px 10px 10px 0; border-color: transparent white transparent transparent; }
    .right::after { left: -16px !important; }
    .content { padding: 20px 30px; background-color: white; position: relative; border-radius: 6px; box-shadow: 0 4px 14px rgba(0,0,0,0.08); }

    @media screen and (max-width: 600px) {
      .timeline::after { left: 31px; }
      .timeline .container { width: 100%; padding-left: 70px; padding-right: 25px; }
      .timeline .container::before { left: 60px; border: medium solid white; border-width: 10px 10px 10px 0; border-color: transparent white transparent transparent; }
      .left::after, .right::after { left: 15px !important; }
      .right { left: 0%; }
    }

    <?php if ($theme == 'dark') { ?>
    body { background-image: none !important; background-color: #121212 !important; }
    .header-box { background-color: #1e1e1e !important; color: #ffffff !important; border: 1px solid #333 !important; }
    h2, h3, p { color: #ffffff !important; }
    .content { background-color: #1e1e1e !important; border: 1px solid #333 !important; color: #fff !important; }
    .timeline::after { background-color: #333 !important; }
    .left::before { border-color: transparent transparent transparent #1e1e1e !important; }
    .right::before { border-color: transparent #1e1e1e transparent transparent !important; }
    .timeline .container::after { background-color: #121212 !important; border-color: #555 !important; }
    .glow-text { color: #e0e0e0 !important; text-shadow: 0px 0px 8px rgba(255, 255, 255, 0.5); }
    <?php } ?>
</style>

<div class="container">
    <div class="header-box">
        <h2 style="margin-top: 0;">Journey Timeline</h2>
        <p>A chronological history of your clubs journey.</p>
        <div style="clear: both;"></div>
    </div>

    <div class="top-actions" style="overflow: auto; margin-bottom: 20px;">
        <div style="float: left;">
            <a href="club_list.php" class="btn btn-back">Back to Clubs List</a>
        </div>
    </div>

    <?php if(mysqli_num_rows($result) > 0) { ?>
        <div class="timeline">
            <?php 
            $is_left = true;
            while($row = mysqli_fetch_assoc($result)) { 
                $side = $is_left ? "left" : "right";
                $is_left = !$is_left;
            ?>
              <div class="container <?php echo $side; ?>">
                <div class="content">
                  <h2 style="margin-top: 0; color: #17a2b8;"><?php echo date("Y", strtotime($row['join_date'])); ?></h2>
                  <p><strong><?php echo htmlspecialchars($row['club_name']); ?></strong><br>
                  <?php echo htmlspecialchars($row['role']); ?></p>
                  <p class="glow-text"><?php echo htmlspecialchars($row['description']); ?></p>
                </div>
              </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="header-box" style="text-align: center; margin-top: 20px;">
            <p>No history to display yet. Add some clubs first!</p>
        </div>
    <?php } ?>
</div>
</body>
</html>