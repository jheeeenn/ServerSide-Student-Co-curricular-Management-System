<?php
// partials/header.php
if (!isset($base_path)) {
    $base_path = "";

}

// Set default page title and subtitle if not already set
if (!isset($page_title)) {
    $page_title = "JX Student CoCo Hub";
}
if (!isset($page_subtitle)) {
    $page_subtitle = "";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JX Student CoCo Hub</title>

    <!-- Link to the external css -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/style.css">

</head>
<body>

<?php if (isset($_COOKIE['remembered_email']) && isset($show_cookie_notice) && $show_cookie_notice == true) 
    { ?>
    <div class="top-notification">
        Welcome back! Cookie preferences are active.
    </div>
<?php } ?>

<?php if (empty($hide_topbar)) { ?>
<div class="topbar">
    <h1><?php echo htmlspecialchars($page_title); ?></h1>
    <?php if (!empty($page_subtitle)) { ?>
        <p><?php echo htmlspecialchars($page_subtitle); ?></p>
    <?php } ?>
</div>
<?php } ?>