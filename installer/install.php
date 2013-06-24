<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by Josh Frandley copyright (C) 2012-2013
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

if (!isset($_POST["doinstall"])) {  
    header("Location: index.php");  
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>ModernCount &middot; Installer</title>
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
<a class="brand" href="#">ModernCount</a>
</div>
</div>
</div>
<!-- Nav end -->
<!-- Content start -->
<div class="container">
<div class="page-header">
<h1>Install ModernCount 2.1</h1>
</div>		
<?php

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
$uniquekey = md5(microtime().rand());
$website = $_POST["website"];
$pathtoscript = $_POST["pathtoscript"];

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
define(\"UNIQUE_KEY\", \"$uniquekey\");
define(\"WEBSITE\", \"$website\");
define(\"PATH_TO_SCRIPT\", \"$pathtoscript\");
define(\"AD_CODE\", \"\");
define(\"COUNT_UNIQUE_ONLY_STATE\", \"Disabled\");
define(\"COUNT_UNIQUE_ONLY_TIME\", \"24\");

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
<h2>4. Finish</h2>
<p>ModernCount has been successfully installed. Please delete the "installer" folder from your server, as it poses a potential security risk! You can view your login data by clicking on this button.</p> 
<div id="logindata" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Login data</h3>
  </div>
  <div class="modal-body">
    <ul>  
      <li>User: <? echo $adminuser; ?></li>  
      <li>Password: <? echo $_POST["adminpassword"]; ?></li>  
    </ul>
  </div>
  <div class="modal-footer">
    <a class="btn btn-success" data-dismiss="modal" aria-hidden="true">Close</a>
  </div>
</div>  
<p>
<div class="btn-group">
<a href="#logindata" role="button" class="btn btn-danger" data-toggle="modal">View login</a>
<a href="../admin/login.php" class="btn btn-success">Login</a></p>
</div>
</div>
<!-- Content end -->
<!-- Javascript start -->	
<script src="../resources/jquery.js"></script>
<script src="../resources/bootstrap/js/bootstrap.js"></script>
<!-- Javascript end -->
</body>
</html>