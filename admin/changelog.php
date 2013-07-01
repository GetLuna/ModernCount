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

?>
<!DOCTYPE html>
<html lang="en">
	<head>
        <meta charset="utf-8">
        <title>ModernCount &middot; Changelog</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        if (THEME == "default") {
            echo "<link href=\"../resources/bootstrap/css/bootstrap.css\" type=\"text/css\" rel=\"stylesheet\">\n";  
        } else {
            echo "<link href=\"//netdna.bootstrapcdn.com/bootswatch/2.3.1/" . THEME . "/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";
        }
        ?>
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
					<a class="brand" href="#">ModernCount</a>
					<div class="nav-collapse collapse">
						<ul class="nav">
							<li><a href="index.php">Home</a></li>
							<li class="divider-vertical"></li>
							<li><a href="add.php">Add</a></li>
							<li><a href="edit.php">Edit</a></li>
						</ul>
						<ul class="nav pull-right">
							<li class="active" class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $currentadminuser; ?> <b class="caret"></b></a>
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
            <h3>Version 3.1.0</h3>
            <ul>
                <li>Enhanced database</li>
                <li>IDs are now displayed in the index</li>
                <li>You can now search on IDs</li>
                <li>Improved display of record table</li>
                <li>Fix broken tracking link copy</li>
                <li>Improved display of succeed message when adding and editing</li>
                <li>Improved code readability</li>
                <li>Fixes 20 HTML issues</li>
            </ul>
            <h3>Version 3.0.1</h3>
            <ul>
                <li>Fix issue that cause the default theme to be displayed in changelog even if it's not the theme in settings</li>
                <li>Remove unneeded scripts on some pages</li>
                <li>Fix spelling issue on index</li>
                <li>Small design changes</li>
            </ul>
            <h3>Version 3.0.0</h3>
            <ul>
                <li>The Settings page won't display auto-correct menu's</li>
                <li>It's not longer possible to add a negative count</li>
                <li>URLs will no longer convert to lowercase</li>
                <li>Cleaned up settings interface</li>
                <li>Improved error messages</li>
                <li>Improved installer, for a more secure installation</li>
                <li>New "Javascript isn't enabled" warning</li>
                <li>Don't launch installer if config doesn't exist when running get.php</li>
                <li>Focus is now automatic set on login</li>
                <li>Rewrite of get.php</li>
                <li>Improved details view for adding and editing downloads</li>
                <li>New standard settings</li>
                <li>Ingor downloads by admins</li>
                <li>Updated data validation</li>
                <li>Improved editions, deletion on index</li>
                <li>Small interface improvements</li>
                <li>New pagination</li>
                <li>4 minor improvements</li>
                <li>5 other bugfixes</li>
                <li>2 security improvements</li>
            </ul>
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
                <li>The "Ad code" boxes are bigger</li>
                <li>You can now choose to see 5, 40 or 75 records on the admin index</li>
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
            
			<footer>
				<p class="muted pull-right">ModernCount <a href="changelog.php"><?php echo VERSION; ?></a> &copy; <a href="http://github.com/ModernBB" target="_blank">Studio 384</a> <?php echo date("Y"); ?>
			</footer>
        </div>
        <!-- Content end -->
        <!-- Javascript start -->	
        <script src="../resources/jquery.js"></script>
        <script src="../resources/bootstrap/js/bootstrap.js"></script>
        <!-- Javascript end -->
	</body>
</html>