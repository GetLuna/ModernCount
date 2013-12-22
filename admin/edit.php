<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("../config.php")) {
	header('Location: ../installer');
	exit;
}

require_once("../config.php");

session_start();
if (!isset($_SESSION["indication_user"])) {
    header("Location: login.php");
    exit; 
}

//Connect to database
@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Error: Could not connect to database (" . mysql_error() . "). Check your database settings are correct.");
}

mysql_select_db(DB_NAME, $con);

$getusersettings = mysql_query("SELECT `user`, `theme` FROM `Users` WHERE `id` = \"" . $_SESSION["indication_user"] . "\"");
if (mysql_num_rows($getusersettings) == 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}
$resultgetusersettings = mysql_fetch_assoc($getusersettings);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Indication &middot; Edit</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../resources/bootstrap/css/bootstrap.min.css" type="text/css" rel="stylesheet">
        <link href="../resources/bootstrap/css/moderncount.css" type="text/css" rel="stylesheet">
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
	</head>
    <body>
        <!-- Nav start -->
        <div class="navbar navbar-default navbar-fixed-top">
            <div class="navbar-inner">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">Indication</a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-left">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="add.php">Add</a></li>
                        <li class="active"><a href="edit.php">Edit</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $resultgetusersettings["user"]; ?> <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="settings.php">Settings</a></li>
                                <li><a href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Nav end -->
        <!-- Content start -->
        <div class="container">
            <div class="page-header">
                <h1>Edit</h1>
            </div>
<?php

//Error display
if (isset($_GET["error"])) {
    $error = $_GET["error"];
    if ($error == "emptyfields") {
        echo "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><h4 class=\"alert-heading\">Error</h4><p>One or more fields were left empty.</p></div>";
    } elseif ($error == "emptypassword") {
        echo "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><h4 class=\"alert-heading\">Error</h4><p>Empty password.</p></div>";
    }
}

if (!isset($_GET["id"])) {
	$getids = mysql_query("SELECT `id`, `name` FROM `Data`");
    if (mysql_num_rows($getids) != 0) {
        echo "<form action=\"edit.php\" method=\"get\"><fieldset><div class=\"control-group\"><label class=\"control-label\" for=\"id\">Select a download to edit</label><div class=\"controls\"><select id=\"id\" class=\"form-control\" name=\"id\">";
        while($row = mysql_fetch_assoc($getids)) {
            echo "<option value=\"" . $row["id"] . "\">" . ucfirst($row["name"]) . "</option>";
        }
        echo "</select></div></div><div class=\"form-actions\"><button type=\"submit\" class=\"btn btn-primary\">Edit</button></div></fieldset></form>";
    } else {
        echo "<div class=\"alert alert-info\"><h4 class=\"alert-heading\">Information</h4><p>No downloads available to edit.</p><p><a class=\"btn btn-info\" href=\"javascript:history.go(-1)\">Go Back</a></p></div>";
    }
} else {

?>
<?php

$idtoedit = mysql_real_escape_string($_GET["id"]);

//Check if ID exists
$doesidexist = mysql_query("SELECT `id` FROM `Data` WHERE `id` = \"$idtoedit\"");
if (mysql_num_rows($doesidexist) == 0) {
    echo "<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>ID does not exist.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div>";
} else {

?>
<form action="actions/edit.php" method="post" autocomplete="off">
<fieldset>
<?php

$getidinfo = mysql_query("SELECT * FROM `Data` WHERE `id` = \"$idtoedit\"");
$getidinforesult = mysql_fetch_assoc($getidinfo);
    
echo "<div class=\"control-group\"><label class=\"control-label\" for=\"name\">Name</label><div class=\"controls\"><input type=\"text\" class=\"form-control\" id=\"name\" name=\"name\" value=\"" . $getidinforesult["name"] . "\" placeholder=\"Type a name...\" pattern=\"([0-9A-Za-z-\\.@:%_\+~#=\s]+)\" required></div></div>";
echo "<div class=\"control-group\"><label class=\"control-label\" for=\"id\">ID</label><div class=\"controls\"><input type=\"text\" class=\"form-control\" id=\"id\" name=\"id\" value=\"" . $getidinforesult["id"] . "\" placeholder=\"Type an ID...\" pattern=\"([0-9A-Za-z-\\.@:%_\+~#=]+)\" required></div></div>";
echo "<div class=\"control-group\"><label class=\"control-label\" for=\"url\">URL</label><div class=\"controls\"><input type=\"text\" class=\"form-control\" id=\"url\" name=\"url\" value=\"" . $getidinforesult["url"] . "\" placeholder=\"Type a URL...\" pattern=\"(http|ftp|https)://[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:/~+#-]*[\w@?^=%&amp;/~+#-])?\" required></div></div>";
echo "<div class=\"control-group\"><label class=\"control-label\" for=\"count\">Count</label><div class=\"controls\"><input type=\"number\" class=\"form-control\" id=\"count\" name=\"count\" value=\"" . $getidinforesult["count"] . "\" placeholder=\"Type a count...\" min=\"0\" required></div></div>";

echo "<div class=\"control-group\"><div class=\"controls\"><label class=\"checkbox\">";
    
//Check if we should show ads
$checkifadsshow = mysql_query("SELECT `showads` FROM `Data` WHERE `id` = \"$idtoedit\"");
$checkifadsshowresult = mysql_fetch_assoc($checkifadsshow); 
if ($checkifadsshowresult["showads"] == "1") { 
    echo "<input type=\"checkbox\" id=\"showadsstate\" name=\"showadsstate\" checked=\"checked\"> Show ads";
} else {
    echo "<input type=\"checkbox\" id=\"showadsstate\"  name=\"showadsstate\"> Show ads";
}

echo "</label></div></div><div class=\"control-group\"><div class=\"controls\"><label class=\"checkbox\">";
    
//Check if download is protected
$checkifprotected = mysql_query("SELECT `protect` FROM `Data` WHERE `id` = \"$idtoedit\"");
$checkifprotectedresult = mysql_fetch_assoc($checkifprotected); 
if ($checkifprotectedresult["protect"] == "1") { 
    echo "<input type=\"checkbox\" id=\"passwordprotectstate\" name=\"passwordprotectstate\" checked=\"checked\"> Enable password protection";
} else {
    echo "<input type=\"checkbox\" id=\"passwordprotectstate\" name=\"passwordprotectstate\"> Enable password protection";
}

mysql_close($con);

?>
                            </label>
                        </div>
                    </div>
                    <div id="passwordentry" style="display: none;">
                        <div class="control-group">
                            <label class="control-label" for="password">Password</label>
                            <div class="controls">
                                <input type="password" id="password" name="password" placeholder="Type a password...">
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <input type="hidden" name="idtoedit" value="<?php echo $idtoedit; ?>" />
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </fieldset>
            </form>
<?php
}
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
            $("#passwordprotectstate").click(function() {
                if ($("#passwordprotectstate").prop("checked") == true) {
                    $("#password").prop("required", true);
                    $("#passwordentry").show("fast");
                } else {
                    $("#passwordentry").hide("fast");
                    $("#password").prop("required", false);
                }
            });
            $("input").not("[type=submit]").jqBootstrapValidation(); 
        });
        </script>
        <!-- Javascript end -->
    </body>
</html>