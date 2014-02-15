<?php

// Copyright Modern Group 2013-2014

//Check if ModernCount has been installed
if (file_exists("../config.php")) {
    die("Information: ModernCount has already been installed! To reinstall the app please delete your config file and run this installer again.");
}

require_once("../assets/version.php");

if (isset($_POST["install"])) {

    $dbhost = $_POST["dbhost"];
    $dbuser = $_POST["dbuser"];
    $dbpassword = $_POST["dbpassword"];
    $dbname = $_POST["dbname"];
    $user = $_POST["user"];
    $email = $_POST["email"];
    if (empty($_POST["password"])) {
        die("Error: No  password set.");
    } else {
        //Salt and hash passwords
        $randsalt = md5(uniqid(rand(), true));
        $salt = substr($randsalt, 0, 3);
        $hashedpassword = hash("sha256", $_POST["password"]);
        $password = hash("sha256", $salt . $hashedpassword);
    }
	$website = $_POST["website"];
	$pathtoscript = $_POST["pathtoscript"];
    
    //Second salt for password protection
    $randsalt2 = md5(uniqid(rand(), true));
    $salt2 = substr($randsalt2, 0, 3);
    
    $installstring = "<?php\n\n//Database Settings\ndefine('DB_HOST', " . var_export($dbhost, true) . ");\ndefine('DB_USER', " . var_export($dbuser, true) . ");\ndefine('DB_PASSWORD', " . var_export($dbpassword, true) . ");\ndefine('DB_NAME', " . var_export($dbname, true) . ");\n\n//Other Settings\ndefine('SALT', " . var_export($salt2, true) . ");\ndefine('WEBSITE', " . var_export($website, true) . ");\ndefine('PATH_TO_SCRIPT', " . var_export($pathtoscript, true) . ");\ndefine('AD_CODE', 'Ad code here');\ndefine('COUNT_UNIQUE_ONLY_STATE', 'Enabled');\ndefine('COUNT_UNIQUE_ONLY_TIME', '24');\ndefine('IGNORE_ADMIN_STATE', 'Disabled');\ndefine('VERSION', " . var_export($version, true) . ");\n\n?>";

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
	$createdatatable = "CREATE TABLE `Data` (
	`name` VARCHAR(100) NOT NULL,
	`id` VARCHAR(25) NOT NULL,
	`url` VARCHAR(200) NOT NULL,
	`count` INT(10) NOT NULL default \"0\",
	`protect` TINYINT(1) NOT NULL default \"0\",
	`password` VARCHAR(200),
	`showads` TINYINT(1) NOT NULL default \"0\",
	PRIMARY KEY (id)
	) ENGINE = MYISAM;";
    
    mysql_query($createdatatable);
    
    //Create Users table
    $createuserstable = "CREATE TABLE `Users` (
    `id` smallint(10) NOT NULL AUTO_INCREMENT,
    `user` varchar(20) NOT NULL,
    `password` varchar(200) NOT NULL,
    `salt` varchar(3) NOT NULL,
    `email` varchar(100) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=MyISAM;";
    
    mysql_query($createuserstable);
    
    //Add user
    mysql_query("INSERT INTO Users (user, password, salt, email)
    VALUES (\"$user\",\"$password\",\"$salt\",\"$email\")");

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
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ModernCount &middot; Installer</title>
<meta name="robots" content="noindex, nofollow">
<link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<style type="text/css">
body {
    padding-top: 30px;
    padding-bottom: 30px;
}
</style>
</head>
<body>
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
<div class="container">
<div class="navbar-header">
<a class="navbar-brand" href="#">ModernCount</a>
</div>
</div>
</div>
<div class="container">
<div class="page-header">
<h1>Installer</h1>
</div>
<?php
if (!isset($_POST["install"])) {
?>	
<form role="form" method="post" autocomplete="off">
<h4>Database Settings</h4>
<div class="form-group">
<label for="dbhost">Database Host</label>
<input type="text" class="form-control" id="dbhost" name="dbhost" value="localhost" placeholder="Type your database host" />
</div>
<div class="form-group">
<label for="dbuser">Database User</label>
<input type="text" class="form-control" id="dbuser" name="dbuser" placeholder="Type your database user" />
</div>
<div class="form-group">
<label for="dbpassword">Database Password</label>
<input type="password" class="form-control" id="dbpassword" name="dbpassword" placeholder="Type your database password" />
</div>
<div class="form-group">
<label for="dbname">Database Name</label>
<input type="text" class="form-control" id="dbname" name="dbname" placeholder="Type your database name" />
</div>
<h4>User Details</h4>
<div class="form-group">
<label for="user">User</label>
<input type="text" class="form-control" id="user" name="user" placeholder="Type a username" />
</div>
<div class="form-group">
<label for="email">Email</label>
<input type="email" class="form-control" id="email" name="email" placeholder="Type an email" />
</div>
<div class="form-group">
<label for="password">Password</label>
<input type="password" class="form-control" id="password" name="password" placeholder="Type a password" />
</div>
<div class="form-group">
<label for="passwordconfirm">Confirm Password</label>
<input type="password" class="form-control" id="passwordconfirm" name="passwordconfirm" placeholder="Type your password again" />
<span class="help-block">It is recommended that your password be at least 6 characters long</span>
</div>
<h4>Other Settings</h4>
<div class="form-group">
<label for="website">Website Name</label>
<input type="text" class="form-control" id="website" name="website" placeholder="Type your websites name" />
</div>
<div class="form-group">
<label for="pathtoscript">Path to Script</label>
<input type="text" class="form-control" id="pathtoscript" name="pathtoscript" value="<?php echo $pathtoscript; ?>" placeholder="Type the path to ModernCount" />
</div>
<input type="hidden" name="install">
<input type="submit" class="btn btn-default" value="Install">
</form>
<?php
} else {
    echo "<div class=\"alert alert-success\"><h4 class=\"alert-heading\">Install Complete</h4><p>ModernCount has been successfully installed. Please delete the \"installer\" folder from your server, as it poses a potential security risk!</p><p>Your login details are shown below, please make a note of them.</p><ul><li>User: $user</li><li>Password: <i>Password you set during install</i></li></ul><p><a href=\"../admin/login.php\" class=\"btn btn-success\">Go To Login</a></p></div>";
}
?>
</div>
<script src="../assets/jquery.min.js"></script>
<script src="../assets/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>