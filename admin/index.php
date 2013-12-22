<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

$version = "4.5dev";

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

//Set cookie so we dont constantly check for updates
setcookie("indicationhascheckedforupdates", "checkedsuccessfully", time()+604800);

@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Error: Could not connect to database (" . mysql_error() . "). Check your database settings are correct.");
} else {
    $does_db_exist = mysql_select_db(DB_NAME, $con);
    if (!$does_db_exist) {
        die("Error: Database does not exist (" . mysql_error() . "). Check your database settings are correct.");
    }
}

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
        <title>Indication</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
if ($resultgetusersettings["theme"] == "default") {
    echo "<link href=\"../resources/bootstrap/css/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";  
} else {
    echo "<link href=\"//netdna.bootstrapcdn.com/bootswatch/2.3.2/" . $resultgetusersettings["theme"] . "/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";
}
?>
        <link href="../resources/bootstrap/css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet">
        <link href="../resources/datatables/jquery.dataTables-bootstrap.min.css" type="text/css" rel="stylesheet">
        <link href="../resources/bootstrap-notify/css/bootstrap-notify.min.css" type="text/css" rel="stylesheet">
        <style type="text/css">
        body {
            padding-top: 60px;
        }
        @media (max-width: 980px) {
            body {
                padding-top: 0;
            }
        }
		@media all and (min-width: 1200px) {
			.row {
				margin-left: -15px;
			}
		}
        </style>
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
    </head>
    <body>
        <!-- Nav start -->
        <div class="navbar navbar-default navbar-fixed-top">
        	<div class="container">
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
        <!-- Nav end -->
        <!-- Content start -->
        <div class="container">
            <div class="page-header">
                <h1>All Downloads</h1>
            </div>
            <div class="notifications top-right"></div>		
            <noscript>
            	<div class="alert alert-info">
                    <h4 class="alert-heading">Information</h4>
                    <p>Please enable JavaScript to use Indication. For instructions on how to do this, see <a href="http://www.activatejavascript.org" target="_blank">here</a>.</p>
                </div>
            </noscript>
<?php

//Update checking
if (!isset($_COOKIE["indicationhascheckedforupdates"])) {
    $remoteversion = file_get_contents("https://raw.github.com/joshf/Indication/master/version.txt");
    if (preg_match("/^[0-9.-]{1,}$/", $remoteversion)) {
        if ($version < $remoteversion) {
            echo "<div class=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><h4 class=\"alert-heading\">Update</h4><p>Indication <a href=\"https://github.com/joshf/Indication/releases/$remoteversion\" target=\"_blank\">$remoteversion</a> is available. <a href=\"https://github.com/joshf/Indication#updating\" target=\"_blank\">Click here to update</a>.</p></div>";
        }
    }
}

$getdownloads = mysql_query("SELECT * FROM `Data`");

echo "<table id=\"downloads\" class=\"table table-striped table-bordered table-condensed\">
<thead>
<tr>
<th></th>
<th>Name</th>
<th class=\"hidden-phone\">URL</th>
<th>Count</th>
</tr></thead><tbody>";

while($row = mysql_fetch_assoc($getdownloads)) {
    echo "<tr>";
    echo "<td><input name=\"id\" type=\"radio\" value=\"" . $row["id"] . "\"></td>";
    echo "<td>" . $row["name"] . "</td>";
    echo "<td class=\"hidden-phone\">" . $row["url"] . "</td>";
    echo "<td>" . $row["count"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";

?>
            <div class="btn-group">
                <button id="edit" class="btn btn-default">Edit</button>
                <button id="delete" class="btn btn-default">Delete</button>
                <button id="trackinglink" class="btn btn-default">Copy Tracking Link</button>
            </div>
            <br>
            <br>
            <div class="alert alert-info">   
                <b>Info:</b> To edit, delete or show the tracking link for a download please select the radio button next to it.  
            </div>
            <div class="well">
<?php

$getnumberofdownloads = mysql_query("SELECT COUNT(id) FROM `Data`");
$resultgetnumberofdownloads = mysql_fetch_assoc($getnumberofdownloads);
echo "<span class=\"glyphicon glyphicon-list-alt\"></span> <b>" . $resultgetnumberofdownloads["COUNT(id)"] . "</b> items<br>";

$gettotalnumberofdownloads = mysql_query("SELECT SUM(count) FROM `Data`");
$resultgettotalnumberofdownloads = mysql_fetch_assoc($gettotalnumberofdownloads);
if (is_null($resultgettotalnumberofdownloads["SUM(count)"])) {
    echo "<span class=\"glyphicon glyphicon-download\"></span> <b>0</b> total downloads";
} else {
    echo "<span class=\"glyphicon glyphicon-download\"></span> <b>" . $resultgettotalnumberofdownloads["SUM(count)"] . "</b> total downloads";
}

mysql_close($con);

?>
            </div>
            <hr>
            <p class="muted pull-right">Indication <?php echo $version; ?> &copy; <a href="http://github.com/joshf" target="_blank">Josh Fradley</a> <?php echo date("Y"); ?>. Themed by <a href="http://twitter.github.com/bootstrap/" target="_blank">Bootstrap</a>.</p>
        </div>
        <!-- Content end -->
        <!-- Javascript start -->
        <script src="../resources/jquery.min.js"></script>
        <script src="../resources/bootstrap/js/bootstrap.min.js"></script>
        <script src="../resources/datatables/jquery.dataTables.min.js"></script>
        <script src="../resources/datatables/jquery.dataTables-bootstrap.min.js"></script>
        <script src="../resources/bootstrap-notify/js/bootstrap-notify.min.js"></script>
        <script src="../resources/bootbox/bootbox.min.js"></script>
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
			/* Table selection */
			id_selected = false;
			$("#downloads input[name=id]").click(function() {
				id = $("#downloads input[name=id]:checked").val();
				id_selected = true;
			});
			/* End */
			/* Datatables */
			$("#downloads").dataTable({
				"sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
				"sPaginationType": "bootstrap",
				"aoColumnDefs": [{
					"bSortable": false,
					"aTargets": [0]
				}]
			});
			$.extend($.fn.dataTableExt.oStdClasses, {
				"sSortable": "header",
				"sWrapper": "dataTables_wrapper form-inline"
			});
			/* End */  
			/* Edit */
			$("#edit").click(function() {
				if (id_selected == true) {
					window.location = "edit.php?id="+ id +"";
				} else {
					show_notification("info", "info-sign", "No ID selected!");
				}
			});
			/* End */
			/* Delete */
			$("#delete").click(function() {
				if (id_selected == true) {
					bootbox.confirm("Are you sure you want to delete the selected download?", "No", "Yes", function(result) {
						if (result == true) {
							$.ajax({
								type: "POST",
								url: "actions/worker.php",
								data: "action=delete&id="+ id +"",
								error: function() {
									show_notification("error", "warning-sign", "Ajax query failed!");
								},
								success: function() {
									show_notification("success", "ok", "Download deleted!", true);
								}
							});
						}
					});
				} else {
					show_notification("info", "info-sign", "No ID selected!");
				}
			});
			/* End */
			/* Show tracking Link */
			$("#trackinglink").click(function() {
				if (id_selected == true) {
					bootbox.prompt("Tracking Link", "Cancel", "Ok", null, "<?php echo PATH_TO_SCRIPT; ?>/get.php?id="+ id +"");
					/* Select form automatically (For Firefox) */
					$(".input-block-level").select();
				} else {
					show_notification("info", "info-sign", "No ID selected!");
				}
			});
			/* End */
		});
        </script>
		<!-- Javascript end -->
    </body>
</html>