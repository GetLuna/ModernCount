<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by Josh Frandley copyright (C) 2012-2013
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

$version = "2.0-beta";

if (!file_exists("../config.php")) {
    header("Location: ../installer");
}

require_once("../config.php");

$uniquekey = UNIQUE_KEY;
$currentadminuser = ADMIN_USER;

session_start();
if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
    header("Location: login.php");
    exit; 
}

//Set cookie so we dont constantly check for updates
setcookie("indicationhascheckedforupdates", "checkedsuccessfully", time()+259200);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>ModernCount</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../resources/bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet">
<link href="../resources/datatables/dataTables.bootstrap.css" type="text/css" rel="stylesheet">
<style type="text/css">
body {
    padding-top: 60px;
}
</style>
<link href="../resources/bootstrap/css/bootstrap-responsive.css" type="text/css" rel="stylesheet">
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
<a class="brand" href="index.php">ModernCount</a>
<div class="nav-collapse collapse">
<ul class="nav">
<li class="active"><a href="index.php">Home</a></li>
<li class="divider-vertical"></li>
<li><a href="add.php">Add</a></li>
</ul>
<ul class="nav pull-right">
<li class="dropdown">
<a href="#" class="dropdown-toggle active" data-toggle="dropdown"><?php echo $currentadminuser; ?> <b class="caret"></b></a>
  <ul class="dropdown-menu">
    <li><a href="settings.php">Settings</a></li>
    <li class="active"><a href="changelog.php">Changelog</a></li>
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
<h1>Changelog</h1>
</div>
<h3>Version 2.0.0</h3>
<ul>
	<li>New interface</li>
	<li>New installation</li>
	<li>New remove icon</li>
	<li>New settings panel</li>
	<li>Improved update checker</li>
	<li>Improved table for displaying the data</li>
	<li>"Changelog" is added under the profile link in the menu</li>
    <li>New help message</li>
    <li>It's now possible to install ModernCount on a database that's not protected with a password</li>
</ul>
<h3>Version 1.6.0</h3>
<ul>
	<li>New menu</li>
	<li>Fix bug in update check</li>
	<li>You can't disable the help message anymore</li>
</ul>
<h3>Version 1.5.0</h3>
<ul>
	<li>New interface</li>
    <li>Fixes lots of validation bugs</li>
</ul>
<h3>Version 1.0.0</h3>
<ul>
	<li>Initial release</li>
</ul>
<hr>
<p class="muted pull-right">ModernCount <a href="changelog.php"><?php echo $version; ?></a> &copy; <a href="http://github.com/ModernBB" target="_blank">Studio 384</a> <?php echo date("Y"); ?></p>
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
    is_selected = false;
    $("#downloads input[name=id]").click(function() {
        id = $("#downloads input[name=id]:checked").val();
        is_selected = true;
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
        if (!is_selected) {
            alert("No download selected!");
        } else {
            window.location = "edit.php?id="+ id +"";
        }
    });
    /* End */
    /* Delete */
    $("#delete").click(function() {
        if (!is_selected) {
            alert("No download selected!");
        } else {
            deleteconfirm=confirm("Delete this download?")
            if (deleteconfirm==true) {
                $.ajax({  
                    type: "POST",  
                    url: "actions/worker.php",  
                    data: "action=delete&id="+ id +"",
                    error: function() {  
                        alert("Ajax query failed!");
                    },
                    success: function() {  
                        alert("Download deleted!");
                        window.location.reload();      
                    }	
                });
            } else {
                return false;
            }
        } 
    });
    /* End */
    /* Tracking Link */
    $("#trackinglink").click(function() {
        if (!is_selected) {
            alert("No download selected!");
        } else {
            prompt("Tracking link for selected download. Press Ctrl/Cmd C to copy to the clipboard:", "<?php echo PATH_TO_SCRIPT; ?>/get.php?id="+ id +"");
        } 
    });
    /* End */
});
</script>
<!-- Javascript end -->
</body>
</html>