<?php

// Copyright Modern Group 2013-2014

require_once("../assets/version.php");

if (!file_exists("../config.php")) {
    header("Location: ../installer");
    exit;
}

require_once("../config.php");

session_start();
if (!isset($_SESSION["_user"])) {
    header("Location: login.php");
    exit; 
}

//Set cookie so we dont constantly check for updates
setcookie("updatecheck", time(), time()+604800);

@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Error: Could not connect to database (" . mysql_error() . "). Check your database settings are correct.");
} else {
    $does_db_exist = mysql_select_db(DB_NAME, $con);
    if (!$does_db_exist) {
        die("Error: Database does not exist (" . mysql_error() . "). Check your database settings are correct.");
    }
}

$getusersettings = mysql_query("SELECT `user` FROM `Users` WHERE `id` = \"" . $_SESSION["_user"] . "\"");
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
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ModernCount</title>
<link rel="apple-touch-icon" href="../assets/icon.png">
<link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="../assets/datatables/css/dataTables.bootstrap.min.css" rel="stylesheet">
<link href="../assets/bootstrap-notify/css/bootstrap-notify.min.css" rel="stylesheet">
<style type="text/css">
body {
    padding-top: 30px;
    padding-bottom: 30px;
}
/* Fix weird notification appearance */
a.close.pull-right {
    padding-left: 10px;
}
/* Slim down the actions column */
tr td:last-child {
    width: 64px;
    white-space: nowrap;
}
</style>
</head>
<body>
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
<div class="container">
<div class="navbar-header">
<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
<span class="sr-only">Toggle navigation</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="#">ModernCount</a>
</div>
<div class="navbar-collapse collapse">
<ul class="nav navbar-nav">
<li class="active"><a href="index.php">Home</a></li>
<li><a href="add.php">Add</a></li>
<li><a href="edit.php">Edit</a></li>
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
<div class="container">
<div class="page-header">
<h1>Downloads for <?php echo WEBSITE; ?></h1>
</div>
<div class="notifications top-right"></div>	
<?php

echo "<noscript><div class=\"alert alert-info\"><h4 class=\"alert-heading\">Information</h4><p>Please enable JavaScript to use ModernCount. For instructions on how to do this, see <a href=\"http://www.activatejavascript.org\" class=\"alert-link\" target=\"_blank\">here</a>.</p></div></noscript>";

//Update checking
if (!isset($_COOKIE["updatecheck"])) {
    $remoteversion = file_get_contents("https://raw.github.com/ModernBB/ModernCount/master/version.txt");
    if (version_compare($version, $remoteversion) < 0) {            
        echo "<div class=\"alert alert-warning\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><h4 class=\"alert-heading\">Update</h4><p>ModernCount <a href=\"https://github.com/joshf/ModernCount/releases/$remoteversion\" class=\"alert-link\" target=\"_blank\">$remoteversion</a> is available. <a href=\"https://github.com/joshf/ModernCount#updating\" class=\"alert-link\" target=\"_blank\">Click here for instructions on how to update</a>.</p></div>";
    }
} 

$getdownloads = mysql_query("SELECT * FROM `Data`");

echo "<table id=\"downloads\" class=\"table table-bordered table-hover table-condensed\">
<thead>
<tr>
<th class=\"col-md-4 col-xs-6\">Name</th>
<th class=\"hidden-xs col-md-6\">URL</th>
<th class=\"col-md-1 col-xs-3\">Count</th>
<th class=\"col-md-1 col-xs-3\">Actions</th>
</tr></thead><tbody>";

while($row = mysql_fetch_assoc($getdownloads)) {
    echo "<tr>";
    echo "<td>" . $row["name"] . "</td>";
    echo "<td class=\"hidden-xs\">" . $row["url"] . "</td>";
    echo "<td>" . $row["count"] . "</td>";
    echo "<td><div class=\"btn-group\"><a href=\"edit.php?id=" . $row["id"] . "\" class=\"btn btn-default btn-xs\" role=\"button\"><span class=\"glyphicon glyphicon-edit\"></span></a><button type=\"button\" class=\"trackinglink btn btn-default btn-xs\" data-id=\"" . $row["id"] . "\"><span class=\"glyphicon glyphicon-share-alt\"></span></button><button type=\"button\" class=\"delete btn btn-default btn-xs\" data-id=\"" . $row["id"] . "\"><span class=\"glyphicon glyphicon-trash\"></span></button></div></td>";
    echo "</tr>";
}
echo "</tbody></table>";

?>
<div class="well">
<?php

$getnumberofdownloads = mysql_query("SELECT COUNT(id) FROM `Data`");
$resultgetnumberofdownloads = mysql_fetch_assoc($getnumberofdownloads);
echo "<i class=\"glyphicon glyphicon-list-alt\"></i> <b>" . $resultgetnumberofdownloads["COUNT(id)"] . "</b> items and ";

$gettotalnumberofdownloads = mysql_query("SELECT SUM(count) FROM `Data`");
$resultgettotalnumberofdownloads = mysql_fetch_assoc($gettotalnumberofdownloads);
if (is_null($resultgettotalnumberofdownloads["SUM(count)"])) {
    echo "<i class=\"glyphicon glyphicon-download\"></i> <b>0</b> total downloads";
} else {
    echo "<i class=\"glyphicon glyphicon-download\"></i> <b>" . $resultgettotalnumberofdownloads["SUM(count)"] . "</b> total downloads";
}

mysql_close($con);

?>
</div>
<hr>
<div class="footer">
ModernCount <?php echo $version; ?> &copy; <a href="http://github.com/ModernBB" target="_blank">ModernBB Group</a> <?php echo date("Y"); ?>
</div>
</div>
<script src="../assets/jquery.min.js"></script>
<script src="../assets/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/datatables/js/jquery.dataTables.min.js"></script>
<script src="../assets/datatables/js/dataTables.bootstrap.min.js"></script>
<script src="../assets/bootbox.min.js"></script>
<script src="../assets/bootstrap-notify/js/bootstrap-notify.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    /* Set Up Notifications */
    var show_notification = function(type, icon, text, reload) {
        $(".top-right").notify({
            type: type,
            transition: "fade",
            icon: icon,
            message: {
                text: text
            },
            onClosed: function() {
                if (reload == true) {
                    window.location.reload();
                }
            }
        }).show();
    };
    /* End */
    /* Datatables */
    $("#downloads").dataTable({
        "aoColumns": [
            null,
            null,
            null,
            {"bSortable": false}
        ]
    });
    /* End */
    /* Delete */
    $("table").on("click", ".delete", function() {
        var id = $(this).data("id");
        bootbox.confirm("Are you sure you want to delete this download?", function(result) {
            if (result == true) {
                $.ajax({
                    type: "POST",
                    url: "actions/worker.php",
                    data: "action=delete&id="+ id +"",
                    error: function() {
                        show_notification("danger", "warning-sign", "Ajax query failed!");
                    },
                    success: function() {
                        show_notification("success", "ok", "Download deleted!", true);
                    }
                });
            }
        });
    });
    /* End */
    /* Show tracking Link */
    $("table").on("click", ".trackinglink", function() {
        var id = $(this).data("id");
        bootbox.prompt({
            title: "Tracking Link",
            value: "<?php echo PATH_TO_SCRIPT; ?>/get.php?id=" + id + "",
            callback: function(result) {
                /* This has to be here for some reason */
            }
        });
    });
    /* End */
});
</script>
</body>
</html>