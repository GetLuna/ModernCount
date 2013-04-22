<!DOCTYPE html>
<!-- Indication, Copyright Josh Fradley (http://github.com/joshf/Indication) -->
<html lang="en">
<head>
<meta charset="utf-8">
<title>Indication &middot; Installer</title>
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../resources/bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet">
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
<a class="brand" href="#">Indication</a>
</div>
</div>
</div>
<!-- Nav end -->
<!-- Content start -->
<div class="container">
<div class="page-header">
<h1>Installer</h1>
</div>		
<?php

if (!isset($_POST["doinstall"])) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Install Failed</h4><p>Error: Installer called directly.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

//Get new settings from POST
$dbhost = $_POST["dbhost"];
$dbuser = $_POST["dbuser"];
$dbpassword = $_POST["dbpassword"];
$dbname = $_POST["dbname"];
$adminuser = $_POST["adminuser"];
if (empty($_POST["adminpassword"])) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Install Failed</h4><p>Error: No admin password set.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
} else {
    $adminpassword = sha1($_POST["adminpassword"]);
}
$website = $_POST["website"];
$pathtoscript = $_POST["pathtoscript"];
$uniquekey = str_shuffle("abcdefghijklmnopqrstuvwxyz123456789");

$installstring = "<?php

//Database Settings
define(\"DB_HOST\", \"$dbhost\");
define(\"DB_USER\", \"$dbuser\");
define(\"DB_PASSWORD\", \"$dbpassword\");
define(\"DB_NAME\", \"$dbname\");

//Admin Details
define(\"ADMIN_USER\", \"$adminuser\");
define(\"ADMIN_PASSWORD\", \"$adminpassword\");

//Other Settings
define(\"WEBSITE\", \"$website\");
define(\"PATH_TO_SCRIPT\", \"$pathtoscript\");
define(\"COUNT_UNIQUE_ONLY_STATE\", \"Disabled\");
define(\"COUNT_UNIQUE_ONLY_TIME\", \"24\");
define(\"UNIQUE_KEY\", \"$uniquekey\");
define(\"AD_CODE\", \"\");
define(\"THEME\", \"default\");

?>";

//Check if we can connect
@$con = mysql_connect($dbhost, $dbuser, $dbpassword);
if (!$con) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Install Failed</h4><p>Error: Could not connect to database (" . mysql_error() . "). Check your database settings are correct.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

//Check if database exists
$does_db_exist = mysql_select_db($dbname, $con);
if (!$does_db_exist) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Install Failed</h4><p>Error: Database does not exist (" . mysql_error() . "). Check your database settings are correct.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

//Create Data table
$createtable = "CREATE TABLE Data (
name VARCHAR(100) NOT NULL,
id VARCHAR(25) NOT NULL,
url VARCHAR(200) NOT NULL,
count INT(10) NOT NULL default \"0\",
protect TINYINT(1) NOT NULL default \"0\",
password VARCHAR(200),
showads TINYINT(1) NOT NULL default \"0\",
PRIMARY KEY (id)
) ENGINE = MYISAM;";

//Run query
mysql_query($createtable);

//Write Config
$configfile = fopen("../config.php", "w");
fwrite($configfile, $installstring);
fclose($configfile);

mysql_close($con);

?>
<div class="alert alert-success"><h4 class="alert-heading">Install Complete</h4><p>Indication has been successfully installed. Please delete the "installer" folder from your server, as it poses a potential security risk!</p>
<p><a href="../admin/login.php" class="btn btn-success">Go To Login</a></p>
</div>
</div>
<!-- Content end -->
<!-- Javascript start -->	
<script src="../resources/jquery.js"></script>
<script src="../resources/bootstrap/js/bootstrap.js"></script>
<!-- Javascript end -->
</body>
</html>