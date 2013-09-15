<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by Josh Frandley copyright (C) 2012-2013
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

if (!file_exists("../config.php")) {
	die("Error: Config file not found! Please reinstall Indication.");
}

require_once("../config.php");

session_start();

unset($_SESSION["indication_user"]);

if (isset($_COOKIE["indication_user_rememberme"])) {
	setcookie("indication_user_rememberme", "", time()-86400);
}

header("Location: login.php?logged_out=true");

exit;

?>