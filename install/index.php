<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by Josh Frandley copyright (C) 2012-2013
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

//Check if ModernCount has been installed
if (file_exists("../config.php")) {
    die("Information: ModernCount has already been installed! To reinstall the app please delete your config file and run this installer again.");
}

if (isset($_POST["install"])) {

    $dbhost = $_POST["dbhost"];
    $dbuser = $_POST["dbuser"];
    $dbpassword = $_POST["dbpassword"];
    $dbname = $_POST["dbname"];
    $adminuser = $_POST["adminuser"];
    if (empty($_POST["adminpassword"])) {
        die("Error: No admin password set.");
    } else {
        //Salt and hash passwords
        $randsalt = md5(uniqid(rand(), true));
        $salt = substr($randsalt, 0, 3);
        $hashedpassword = hash("sha256", $_POST["adminpassword"]);
        $adminpassword = hash("sha256", $salt . $hashedpassword);
    }
	$website = $_POST["website"];
	$pathtoscript = $_POST["pathtoscript"];
    $version = "3.5.0";
    
    $installstring = "<?php\n\n//Database Settings\ndefine('DB_HOST', " . var_export($dbhost, true) . ");\ndefine('DB_USER', " . var_export($dbuser, true) . ");\ndefine('DB_PASSWORD', " . var_export($dbpassword, true) . ");\ndefine('DB_NAME', " . var_export($dbname, true) . ");\n\n//Admin Details\ndefine('ADMIN_USER', " . var_export($adminuser, true) . ");\ndefine('ADMIN_PASSWORD', " . var_export($adminpassword, true) . ");\ndefine('SALT', " . var_export($salt, true) . ");\n\n//Other Settings\ndefine('WEBSITE', " . var_export($website, true) . ");\ndefine('PATH_TO_SCRIPT', " . var_export($pathtoscript, true) . ");\ndefine('AD_CODE', 'Ad code here...');\ndefine('COUNT_UNIQUE_ONLY_STATE', 'Enabled');\ndefine('COUNT_UNIQUE_ONLY_TIME', '24');\ndefine('IGNORE_ADMIN_STATE', 'Disabled');\ndefine('THEME', 'default');\ndefine('VERSION', " . var_export($version, true) . ");\ndefine('INSTALLED', '1');\n\n?>";
	
    //Check if we can connect
    $con = mysql_connect($dbhost, $dbuser, $dbpassword);
    if (!$con) {
        die("Error: Could not connect to database (" . mysql_error() . "). Check your database settings are correct.");
    }

    //Check if database exists
    $does_db_exist = mysql_select_db($dbname, $con);
    if (!$does_db_exist) {
        die("Error: Database does not exist (" . mysql_error() . "). Check your database settings are correct.");
    }

	//Create Data table
	$createtable = "CREATE TABLE `Data` (
	`name` VARCHAR(100) NOT NULL,
	`id` VARCHAR(25) NOT NULL,
	`url` VARCHAR(200) NOT NULL,
	`count` INT(10) NOT NULL default \"0\",
	`protect` TINYINT(1) NOT NULL default \"0\",
	`password` VARCHAR(200),
	`showads` TINYINT(1) NOT NULL default \"0\",
	PRIMARY KEY (id)
	) ENGINE = MYISAM;";

	//Run query
	mysql_query($createtable);

	//Write Config
	$configfile = fopen("../config.php", "w");
	fwrite($configfile, $installstring);
	fclose($configfile);

	mysql_close($con);
}

//Get path to script
$currenturl = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
$pathtoscriptwithslash = "http://" . substr($currenturl, 0, strpos($currenturl, "installer"));
$pathtoscript = rtrim($pathtoscriptwithslash, "/");	

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>ModernCount &middot; Installation</title>
		<meta name="robots" content="noindex, nofollow">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="../resources/bootstrap/css/bootstrap.min.css" type="text/css" rel="stylesheet">
		<link href="../resources/bootstrap/css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet">
		<style type="text/css">
		body {
			padding-top: 60px;
		}
		@media (max-width: 980px) {
			body {
				padding-top: 0;
			}
		}
		</style>
	</head>
	<body>
		<!-- Nav start -->
		<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="navbar-header">
				<a class="navbar-brand" href="#">ModernCount installer</a>
			</div>
		</nav>
		<!-- Nav end -->
		<!-- Content start -->
		<div class="container">
			<?php
				if (!isset($_POST["install"])) {
			?>	
			<form method="post" autocomplete="off">
				<div class="alert alert-info">
					<h4>Welcome to the ModernCount 4.0 beta installation</h4>
					<p>Welcome and thanks for using ModernCount. To install ModernCount, please fill in all required data in this form, then, click "Install".</p>
				</div>
				<fieldset>
					<div class="row">
						<div class="col-md-4">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">Database Settings</h3>
								</div>
								<div class="panel-body">
									<div class="control-group">
										<label class="control-label" for="dbhost">Database Host</label>
										<input type="text" class="form-control" class="form-control" id="dbhost" name="dbhost" value="localhost" placeholder="Type your database host" required>
										<label class="control-label" for="dbuser">Database User</label>
										<input type="text" class="form-control" id="dbuser" name="dbuser" placeholder="Type your database user" required>
										<label class="control-label" for="dbpassword">Database Password</label>
										<input type="password" class="form-control" id="dbpassword" name="dbpassword" placeholder="Type your database password">
										<label class="control-label" for="dbname">Database Name</label>
										<input type="text" class="form-control" id="dbname" name="dbname" placeholder="Type your database name" required>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">Admin Details</h3>
								</div>
								<div class="panel-body">
									<label class="control-label" for="adminuser">Admin User</label>
									<input type="text" class="form-control" id="adminuser" name="adminuser" placeholder="Type a username" required>
									<label class="control-label" for="adminpassword">Password</label>
									<input type="password" class="form-control" id="adminpassword" name="adminpassword" placeholder="Type a password" required>
									<label class="control-label" for="adminpasswordconfirm">Confirm Password</label>
									<input type="password" class="form-control" id="adminpasswordconfirm" name="adminpasswordconfirm" placeholder="Type your password again" data-validation-match-match="adminpassword" required>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">Admin Details</h3>
								</div>
								<div class="panel-body">
									<label class="control-label" for="website">Website Name</label>
									<input type="text" class="form-control" id="website" name="website" required placeholder="Type your websites name">
									<label class="control-label" for="pathtoscript">Path to Script</label>
									<input type="text" class="form-control" id="pathtoscript" name="pathtoscript" value="<?php echo $pathtoscript; ?>" placeholder="Type the path to ModernCount" required>
								</div>
							</div>
						</div>
					</div>
					<input type="hidden" name="doinstall">
					<div class="alert alert-info">
						<input type="submit" class="btn btn-primary" value="Install">
					</div>
				</fieldset>
			</form>
		<?php
		} else {
			echo "<div class=\"alert alert-success\"><h4 class=\"alert-heading\">Install Complete</h4><p>ModernCount has been successfully installed. Please delete the \"installer\" folder from your server, as it poses a potential security risk!</p><p>Your login details are shown below, please make a note of them.</p><ul><li>User: $adminuser</li><li>Password: <i>Password you set during install</i></li></ul><p><a href=\"../login.php\" class=\"btn btn-success\">Go To Login</a></p></div>";
		}
		?>
		</div>
		<!-- Content end -->
		<!-- Javascript start -->
		<script src="../resources/jquery.min.js"></script>
		<script src="../resources/bootstrap/js/bootstrap.min.js"></script>
		<script src="../resources/validation/jqBootstrapValidation.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			$("input").not("[type=submit]").jqBootstrapValidation();
		});
		</script>
		<!-- Javascript end -->
	</body>
</html>