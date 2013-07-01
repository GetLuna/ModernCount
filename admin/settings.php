<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

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
$currentadcode = htmlspecialchars_decode(AD_CODE);
$currentcountuniqueonlystate = COUNT_UNIQUE_ONLY_STATE;
$currentcountuniqueonlytime = COUNT_UNIQUE_ONLY_TIME;
$currentignoreadminstate = IGNORE_ADMIN_STATE; 
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
    if (isset($_POST["advertcode"])) {
        if (get_magic_quotes_gpc()) {
            $adcode = stripslashes(htmlspecialchars($_POST["advertcode"]));
        } else {
            $adcode = htmlspecialchars($_POST["advertcode"]);
        }
    }
    $countuniqueonlystate = $_POST["countuniqueonlystate"];
    $countuniqueonlytime = $_POST["countuniqueonlytime"];
    $ignoreadminstate = $_POST["ignoreadminstate"];
    $theme = $_POST["theme"];

    //Remember previous settings
    if (empty($adcode)) {
        $adcode = $currentadcode;
    }

    $settingsstring = "<?php\n\n//Database Settings\ndefine('DB_HOST', '" . DB_HOST . "');\ndefine('DB_USER', '" . DB_USER . "');\ndefine('DB_PASSWORD', '" . DB_PASSWORD . "');\ndefine('DB_NAME', '" . DB_NAME . "');\n\n//Admin Details\ndefine('ADMIN_USER', " . var_export($adminuser, true) . ");\ndefine('ADMIN_PASSWORD', " . var_export($adminpassword, true) . ");\n\n//Other Settings\ndefine('UNIQUE_KEY', " . var_export($uniquekey, true) . ");\ndefine('WEBSITE', " . var_export($website, true) . ");\ndefine('PATH_TO_SCRIPT', " . var_export($pathtoscript, true) . ");\ndefine('AD_CODE', " . var_export($adcode, true) . ");\ndefine('COUNT_UNIQUE_ONLY_STATE', " . var_export($countuniqueonlystate, true) . ");\ndefine('COUNT_UNIQUE_ONLY_TIME', " . var_export($countuniqueonlytime, true) . ");\ndefine('IGNORE_ADMIN_STATE', " . var_export($ignoreadminstate, true) . ");\ndefine('THEME', " . var_export($theme, true) . ");\n\n?>";

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
            echo "<link href=\"//netdna.bootstrapcdn.com/bootswatch/2.3.1/" . THEME . "/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";
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
							<li><a href="edit.php">Edit</a></li>
						</ul>
						<ul class="nav pull-right">
							<li class="active" class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $currentadminuser; ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li class="active"><a href="settings.php">Settings</a></li>
									<li><a href="changelog.php">Changelog</a></li>
									<li class="divider"></li>
									<li><a href="logout.php">Logout</a></li>
								</ul>
							</li>
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
			<form method="post" autocomplete="off">
				<fieldset>
					<!-- Settings nav -->
					<ul class="nav nav-tabs" id="settings">
						<li class="active"><a href="#general">General</a></li>
						<li><a href="#visitors">Unique visitors</a></li>
						<li><a href="#ad">Ad code</a></li>
						<li><a href="#moderncount">ModernCount</a></li>
					</ul>
                    <!-- End settings nav -->
                    <div class="tab-content">
						<div class="tab-pane active" id="general">
							<h4>Admin details</h4>
							<div class="control-group">
								<label class="control-label" for="adminuser">Admin User</label>
								<div class="controls">
									<input type="text" id="adminuser" name="adminuser" value="<?php echo $currentadminuser; ?>" placeholder="Enter a username" required />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="adminpassword">Admin Password</label>
								<div class="controls">
									<input type="password" id="adminpassword" name="adminpassword" value="<?php echo $currentadminpassword; ?>" placeholder="Enter a password" required />
								</div>
							</div>
							<h4>Website settings</h4>
							<div class="control-group">
								<label class="control-label" for="website">Website</label>
								<div class="controls">
									<input type="text" id="website" name="website" value="<?php echo $currentwebsite; ?>" placeholder="Enter your websites name" required />
								</div>
							</div>
<div class="control-group">
								<label class="control-label" for="pathtoscript">Path to script</label>
								<div class="controls">
									<input type="text" id="pathtoscript" name="pathtoscript" value="<?php echo $currentpathtoscript; ?>" placeholder="Type the path to ModernCount"  required />
								</div>
							</div>
						</div>
						<div class="tab-pane" id="visitors">
							<h4>Count unique visitors only</h4>
							<p>This settings allows you to make sure an individual user's clicks are only counted once.</p>
							<div class="control-group">
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
								</div>  
							</div>
							<div class="control-group">
								<label class="control-label" for="countuniqueonlytime">Time to consider a user unique</label>
								<div class="controls">
                                	<input type="number" id="countuniqueonlytime" name="countuniqueonlytime" value="<?php echo $currentcountuniqueonlytime; ?>" placeholder="Enter a time" required />
								</div>
							</div>
							<h4>Ignore admin</h4>
							<p>This settings prevents downloads being counted when you are logged in to Indication.</p>
							<div class="control-group">
								<div class="controls">
<?php
if ($currentignoreadminstate == "Enabled" ) {
    echo "<label class=\"radio\"><input type=\"radio\" id=\"ignoreadminstateenable\" name=\"ignoreadminstate\" value=\"Enabled\" checked=\"checked\"> Enabled</label>
    <label class=\"radio\"><input type=\"radio\" id=\"ignoreadminstatedisable\" name=\"ignoreadminstate\" value=\"Disabled\"> Disabled</label>";    
} else {
    echo "<label class=\"radio\"><input type=\"radio\" id=\"ignoreadminstateenable\" name=\"ignoreadminstate\" value=\"Enabled\"> Enabled</label>
    <label class=\"radio\"><input type=\"radio\" id=\"ignoreadminstatedisable\" name=\"ignoreadminstate\" value=\"Disabled\" checked=\"checked\"> Disabled</label>";   
}   
?> 
								</div>
							</div>
						</div>
						<div class="tab-pane" id="ad">
                            <h4>Ad code</h4>
                            <p>Show an advert before user can continue to their download. This can be changed on a per download basis.</p>
                            <div class="alert alert-warning"><b>Warning:</b> On some server configurations using HTML code here may produce errors.</div>
                                <div class="control-group">
                                    <div class="controls">
                                        <textarea id="advertcode" name="advertcode" style="width:600px;height:200px;" placeholder="Enter a ad code"><?php echo $currentadcode; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="moderncount">
                                <h4>Theme</h4>
                                <div class="control-group">
                                    <label class="control-label" for="theme">Select a theme</label>
                                <div class="controls">
<?php
$themes = array("default", "amelia", "cerulean", "cosmo", "cyborg", "flatly", "journal", "readable", "simplex", "slate", "spacelab", "spruce", "superhero", "united");

echo "<select id=\"theme\" name=\"theme\">";
foreach ($themes as $value) {
    if ($value == $currenttheme) {
        echo "<option value=\"$value\" selected=\"selected\">". ucfirst($value) . "</option>";
    } else {
        echo "<option value=\"$value\">". ucfirst($value) . "</option>";
    }
}
echo "</select>";
?>
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
        <script src="../resources/validation/jqBootstrapValidation.js"></script>
        <script type="text/javascript">
        $(document).ready(function() {
            $("input").not("[type=submit]").jqBootstrapValidation();
        });
        </script>
        
        <script>
        $('#settings a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
        })
        </script>
        <!-- Javascript end -->
    </body>
</html>