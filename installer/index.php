<?php

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
<link href="../assets/style.css" rel="stylesheet">
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
<div class="row">
    <div class="col-sm-4">
        <h4>Database Settings</h4>
        <div class="form-group">
        <label for="dbhost">Database Host</label>
        <input type="text" class="form-control" id="dbhost" name="dbhost" value="localhost" placeholder="Type your database host" required>
        </div>
        <div class="form-group">
        <label for="dbuser">Database User</label>
        <input type="text" class="form-control" id="dbuser" name="dbuser" placeholder="Type your database user" required>
        </div>
        <div class="form-group">
        <label for="dbpassword">Database Password</label>
        <input type="password" class="form-control" id="dbpassword" name="dbpassword" placeholder="Type your database password" required>
        </div>
        <div class="form-group">
        <label for="dbname">Database Name</label>
        <input type="text" class="form-control" id="dbname" name="dbname" placeholder="Type your database name" required>
        </div>
    </div>
    <div class="col-sm-4">
        <h4>User Details</h4>
        <div class="form-group">
        <label for="user">User</label>
        <input type="text" class="form-control" id="user" name="user" placeholder="Type a username" required>
        </div>
        <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Type an email" required>
        </div>
        <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Type a password" required>
        </div>
        <div class="form-group">
        <label for="passwordconfirm">Confirm Password</label>
        <input type="password" class="form-control" id="passwordconfirm" name="passwordconfirm" placeholder="Type your password again" required>
        <span class="help-block">It is recommended that your password be at least 6 characters long</span>
        </div>
    </div>
    <div class="col-sm-4">
        <h4>Other Settings</h4>
        <div class="form-group">
        <label for="website">Website Name</label>
        <input type="text" class="form-control" id="website" name="website" placeholder="Type your websites name" required>
        </div>
        <div class="form-group">
        <label for="pathtoscript">Path to Script</label>
        <input type="text" class="form-control" id="pathtoscript" name="pathtoscript" value="<?php echo $pathtoscript; ?>" placeholder="Type the path to ModernCount" required>
        </div>
        <input type="hidden" name="install">
        <input type="submit" class="btn btn-default" value="Install">
    </div>
</div>
</form>
<?php
	} else {
?>
	<h2>Installation complete</h2>
    <p>ModernCount has been installed successfully. It's recommended to remove the "installer" folder from your server, as it is a potential security risk. You can now login with the account you just maid.</p>
    <a href="../admin/login.php" class="btn btn-success">Login</a>";
<?php
	}
?>
<footer>
	Copyright <a href="http://studio384.be">Studio 384</a> &middot ModernCount <?php echo $version ?>
</footer>
</div>
<script src="../assets/jquery.min.js"></script>
<script src="../assets/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/nod.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    var metrics = [
        ["#dbhost", "presence", "Database host cannot be empty!"],
        ["#dbuser", "presence", "Database user cannot be empty!"],       
        ["#dbname", "presence", "Database name cannot be empty!"],
        ["#user", "presence", "User name cannot be empty!"],
        ["#email", "email", "Enter a valid email address"],
        ["#password", "presence", "Passwords should be more than 6 characters"],
        ["#passwordconfirm", "same-as: #password", "Passwords do not match!"],
        ["#website", "presence", "Website cannot be empty!"],
    ];
    $("form").nod(metrics);
});
</script>
</body>
</html>