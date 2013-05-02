<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by Josh Frandley copyright (C) 2012-2013
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

if (!file_exists("../config.php")) {
    header("Location: ../installer");
}

require_once("../config.php");

$uniquekey = UNIQUE_KEY;

session_start();
if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
    header("Location: login.php");
    exit; 
}

//Get current settings
$currentadminuser = ADMIN_USER;
$currentadminpassword = ADMIN_PASSWORD;
$currentwebsite = WEBSITE;
$currentpathtoscript = PATH_TO_SCRIPT;
$currentcountuniqueonlystate = COUNT_UNIQUE_ONLY_STATE;
$currentcountuniqueonlytime = COUNT_UNIQUE_ONLY_TIME;
$currentadcode = htmlspecialchars_decode(AD_CODE);
$currenttheme = THEME; 

if (isset($_POST["save"])) {
    //Get new settings from POST
    $adminuser = $_POST["adminuser"];
    $adminpassword = $_POST["adminpassword"];
    if ($adminpassword != $currentadminpassword) {
        $adminpassword = sha1($adminpassword);
    }
    $website = $_POST["website"];
    $pathtoscript = $_POST["pathtoscript"];
    $countuniqueonlystate = $_POST["countuniqueonlystate"];
    $countuniqueonlytime = $_POST["countuniqueonlytime"];
    if (isset($_POST["advertcode"])) {
        if (get_magic_quotes_gpc()) {
            $adcode = stripslashes(htmlspecialchars($_POST["advertcode"]));
        } else {
            $adcode = htmlspecialchars($_POST["advertcode"]);
        }
    }
    $theme = $_POST["theme"];

    //Remember previous settings
    if (empty($adcode)) {
        $adcode = $currentadcode;
    }

    $settingsstring = "<?php\n\n//Database Settings\ndefine(\"DB_HOST\", \"" . DB_HOST . "\");\ndefine(\"DB_USER\", \"" . DB_USER . "\");\ndefine(\"DB_PASSWORD\", \"" . DB_PASSWORD . "\");\ndefine(\"DB_NAME\", \"" . DB_NAME . "\");\n\n//Admin Details\ndefine(\"ADMIN_USER\", \"$adminuser\");\ndefine(\"ADMIN_PASSWORD\", \"$adminpassword\");\n\n//Other Settings\ndefine(\"WEBSITE\", \"$website\");\ndefine(\"PATH_TO_SCRIPT\", \"$pathtoscript\");\ndefine(\"COUNT_UNIQUE_ONLY_STATE\", \"$countuniqueonlystate\");\ndefine(\"COUNT_UNIQUE_ONLY_TIME\", \"$countuniqueonlytime\");\ndefine(\"UNIQUE_KEY\", \"$uniquekey\");\ndefine(\"AD_CODE\", \"$adcode\");\ndefine(\"THEME\", \"$theme\");\n\n?>";

    //Write config
    $configfile = fopen("../config.php", "w");
    fwrite($configfile, $settingsstring);
    fclose($configfile);

    //Show updated values
    header("Location: settings.php?updated=true");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>ModernCount &middot; Settings</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php

if (THEME == "default") {
    echo "<link href=\"../resources/bootstrap/css/bootstrap.css\" type=\"text/css\" rel=\"stylesheet\">\n";  
} else {
    echo "<link href=\"//netdna.bootstrapcdn.com/bootswatch/2.3.0/" . THEME . "/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";
}

?>
<style type="text/css">
body {
    padding-top: 60px;
}
</style>
<link href="../resources/bootstrap/css/bootstrap-responsive.css" type="text/css" rel="stylesheet">
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body>
<!-- Nav start -->
<div class="navbar navbar-fixed-top">
<div class="navbar-inner">
<div class="container">
<a class="btw btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</a>
<a class="brand" href="#">ModernCount</a>
<div class="nav-collapse collapse">
<ul class="nav">
<li><a href="index.php">Home</a></li>
<li class="divider-vertical"></li>
<li><a href="add.php">Add</a></li>
<li><a href="#">Edit</a></li>
</ul>
<ul class="nav pull-right">
<li class="active"><a href="settings.php">Settings</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</div>
</div>
</div>
</div>
<!-- Nav end -->
<!-- Content start -->
<div class="container">
<div class="page-header">
<h1>Settings</h1>
</div>
<?php

if (isset($_GET["updated"])) {
    echo "<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><b>Info:</b> Settings updated.</div>";
}

?>
<form method="post">
<fieldset>
<h4>General</h4>
<div>
<label for="adminuser">Admin User</label>
<div class="controls">
<input type="text" id="adminuser" name="adminuser" value="<? echo $currentadminuser; ?>" placeholder="Enter a username..." required>
</div>
</div>
<div>
<label for="adminpassword">Admin Password</label>
<div class="controls">
<input type="password" id="adminpassword" name="adminpassword" value="<? echo $currentadminpassword; ?>" placeholder="Enter a password..." required>
</div>
</div>
<div>
<label for="website">Website</label>
<div class="controls">
<input type="text" id="website" name="website" value="<? echo $currentwebsite; ?>" placeholder="Enter your websites name..." required>
</div>
</div>
<div>
<label for="pathtoscript">Path to Script</label>
<div class="controls">
<input type="text" id="pathtoscript" name="pathtoscript" value="<? echo $currentpathtoscript; ?>" placeholder="Type the path to ModernCount..." pattern="(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-?]*)*\/?" data-validation-pattern-message="Please enter a valid URL" required>
</div>
</div>
<h4>Ad Code</h4>
<p>Show an advert before user can continue to their download. This can be changed on a per download basis.</p>
<div class="alert alert-warning"><b>Warning:</b> On some server configurations using HTML code here may produce errors.</div>
<div>
<div class="controls">
<textarea id="advertcode" name="advertcode" placeholder="Enter a ad code..."><? echo $currentadcode; ?></textarea>
</div>
</div>
<h4>Count Unique Visitors Only</h4>
<p>This settings allows you to make sure an individual users' clicks are only counted once.</p>
<div>
<div class="controls">
<?php
if ($currentcountuniqueonlystate == "Enabled" ) {
    echo "<label class=\"radio\"><input type=\"radio\" id=\"countuniqueonlystateenable\" name=\"countuniqueonlystate\" value=\"Enabled\" checked=\"checked\"> Enabled</label>
          <label class=\"radio\"><input type=\"radio\" id=\"countuniqueonlystatedisable\" name=\"countuniqueonlystate\" value=\"Disabled\"> Disabled</label>";    
} else {
  echo "<label class=\"radio\"><input type=\"radio\" id=\"countuniqueonlystateenable\" name=\"countuniqueonlystate\" value=\"Enabled\"> Enabled</label>
    <label class=\"radio\"><input type=\"radio\" id=\"countuniqueonlystatedisable\" name=\"countuniqueonlystate\" value=\"Disabled\" checked=\"checked\"> Disabled</label>";   
}   
?> 
<div>
<label for="countuniqueonlytime">Time to consider user unique</label>
<div class="controls">
<input type="number" id="countuniqueonlytime" name="countuniqueonlytime" value="<? echo $currentcountuniqueonlytime; ?>" placeholder="Enter a time..." required>
</div>
</div>
</div>  
</div>
<div class="form-actions">
<button type="submit" name="save" class="btn btn-primary">Save Changes</button>
</div>
</fieldset>
</form>
</div>
<!-- Content end -->
<!-- Javascript start -->	
<script src="../resources/jquery.js"></script>
<script src="../resources/bootstrap/js/bootstrap.js"></script>
<!-- Javascript end -->
</body>
</html>
