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
require_once("includes/common.php");

$uniquekey = UNIQUE_KEY;
$currentadminuser = ADMIN_USER;

session_start();
if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
    header("Location: login.php");
    exit; 
}

//Set cookie so we dont constantly check for updates
setcookie("indicationhascheckedforupdates", "checkedsuccessfully", time()+0);

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>ModernCount</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        if (THEME == "default") {
            echo "<link href=\"../resources/bootstrap/css/bootstrap.css\" type=\"text/css\" rel=\"stylesheet\">\n";  
        } else {
            echo "<link href=\"//netdna.bootstrapcdn.com/bootswatch/2.3.1/" . THEME . "/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";
        } ?>
        <link href="../resources/datatables/dataTables.bootstrap.css" type="text/css" rel="stylesheet">
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
							<li class="active"><a href="index.php"><?php echo $lang_common['Home'] ?></a></li>
							<li class="divider-vertical"></li>
							<li><a href="add.php"><?php echo $lang_common['Add'] ?></a></li>
							<li><a href="edit.php"><?php echo $lang_common['Edit'] ?></a></li>
						</ul>
						<ul class="nav pull-right">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $currentadminuser; ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="settings.php"><?php echo $lang_common['Settings'] ?></a></li>
									<li><a href="changelog.php"><?php echo $lang_common['Changelog'] ?></a></li>
									<li class="divider"></li>
									<li><a href="logout.php"><?php echo $lang_common['Logout'] ?></a></li>
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
				<h1>All downloads</h1>
			</div>		
			<noscript><div class="alert alert-info"><h4 class="alert-heading">Information</h4><p>Please enable JavaScript to use Indication. For instructions on how to do this, see <a href="http://www.activatejavascript.org" target="_blank">here</a>.</p></div></noscript>
<?php

@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>Could not connect to database (" . mysql_error() . "). Check your database settings are correct.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

$does_db_exist = mysql_select_db(DB_NAME, $con);
if (!$does_db_exist) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>Database does not exist (" . mysql_error() . "). Check your database settings are correct.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

$getdownloads = mysql_query("SELECT * FROM Data");

//Update checking
if (!isset($_COOKIE["indicationhascheckedforupdates"])) {
    $remoteversion = file_get_contents("https://raw.github.com/ModernBB/ModernCount/master/version.txt");
    if (preg_match("/^[0-9.-]{1,}$/", $remoteversion)) {
        if (VERSION < $remoteversion) {
            echo "<div class=\"alert\"><h4 class=\"alert-heading\">Update to ModernCount v$remoteversion</h4><a href=\"https://github.com/ModernBB/ModernCount/compare/$version...$remoteversion\" target=\"_blank\">ModernCount v$remoteversion</a> is available. <a href=\"https://studio384.be/moderncount.php/\" target=\"_blank\">Click here to download the latest version</a>.</div>";
        }
    }
}

echo "<table id=\"downloads\" class=\"table table-striped table-bordered table-condensed\">
<thead>
<tr>
<th style=\"width:20px;\"></th>
<th>Name</th>
<th>URL</th>
<th>ID</th>
<th style=\"width:75px;\">Count</th>
</tr></thead><tbody>";

while($row = mysql_fetch_assoc($getdownloads)) {
    echo "<tr>";
    echo "<td><input name=\"id\" type=\"radio\" value=\"" . $row["id"] . "\"></td>";
    echo "<td>" . $row["name"] . "</td>";
    echo "<td>" . $row["url"] . "</td>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td>" . $row["count"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";

?>
			<div class="btn-group">
			    <button id="edit" class="btn btn-success">Edit</button>
			    <button id="delete" class="btn btn-success">Delete</button>
			    <button id="trackinglink" class="btn btn-success">Show tracking link</button>
			</div>
			<br /><br />
            <!-- Delete dialog start -->
			<div id="deleteconfirmdialog" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="dcdheader" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 id="dcdheader">Confirm delete</h3>
				</div>
				<div class="modal-body">
					<p>Are you sure you want to delete the selected download?</p>
				</div>
				<div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button id="deleteconfirm" class="btn btn-danger">Delete</button>
				</div>
			</div>
            <!-- Delete dialog end, no id dialog start -->
			<div id="noidselecteddialog" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="nisdheader" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 id="nisdheader">Error</h3>
				</div>
				<div class="modal-body">
					<p>No ID selected.</p>
				</div>
				<div class="modal-footer">
					<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				</div>
			</div>
            <!-- No id dialog end -->
			<div class="well well-small">
<?php

$getnumberofdownloads = mysql_query("SELECT COUNT(id) FROM Data");
$resultnumberofdownloads = mysql_fetch_assoc($getnumberofdownloads);
echo "<i class=\"icon-download\"></i> <b>" . $resultnumberofdownloads["COUNT(id)"] . "</b> items and ";

$gettotalnumberofdownloads = mysql_query("SELECT SUM(count) FROM Data");
$resulttotalnumberofdownloads = mysql_fetch_assoc($gettotalnumberofdownloads);
if ($resulttotalnumberofdownloads["SUM(count)"] > "1") {
    echo " <b>" . $resulttotalnumberofdownloads["SUM(count)"] . "</b> total downloads";
} else {
    echo " <b>0</b> total downloads";
}

mysql_close($con);

?>
			</div>
            
			<footer>
				<p class="muted pull-right">ModernCount <a href="changelog.php"><?php echo VERSION; ?></a> &copy; <a href="http://github.com/ModernBB" target="_blank">Studio 384</a> <?php echo date("Y"); ?>
			</footer>
		</div>
        <!-- Content end -->
        <!-- Javascript start -->	
        <script src="../resources/jquery.js"></script>
        <script src="../resources/bootstrap/js/bootstrap.js"></script>
        <script src="../resources/datatables/jquery.dataTables.js"></script>
        <script src="../resources/datatables/dataTables.bootstrap.js"></script>
        <script type="text/javascript">
        $(document).ready(function() {
            /* Table selection */
            id_selected = false;
            $("#downloads input[name=id]").click(function() {
                id = $("#downloads input[name=id]:checked").val();
                id_selected = true;
            });
            /* End */
            /* Datatables */
            $("#downloads").dataTable({
                "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
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
                    $("#noidselecteddialog").modal("show");    
                }
            });
            /* End */
            /* Show Delete Dialog */
            $("#delete").click(function() {
                if (id_selected == true) {
                    $("#deleteconfirmdialog").modal("show");
                } else {
                    $("#noidselecteddialog").modal("show");    
                }
            });
            /* End */
            /* Delete worker */
            $("#deleteconfirm").click(function() {
                $("#deleteconfirmdialog").modal("hide");
                $.ajax({  
                    type: "POST",  
                    url: "actions/worker.php",  
                    data: "action=delete&id="+ id +"",
                    error: function() {  
                        alert("Ajax query failed!");
                    },
                    success: function() {  
                        window.location.reload();    
                    }	
                });
            });
            /* End */
            /* Tracking Link */
            $("#trackinglink").click(function() {
                if (!id_selected) {
                    $("#noidselecteddialog").modal("show");
                } else {
                    prompt("Tracking link for selected download. Press copy to the clipboard:", "<?php echo PATH_TO_SCRIPT; ?>/get.php?id="+ id +"");
                } 
            });
            /* End */
        });
        </script>
        <!-- Javascript end -->
	</body>
</html>