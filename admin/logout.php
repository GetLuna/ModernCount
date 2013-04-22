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

$uniquekey = UNIQUE_KEY;

session_start();

unset($_SESSION["is_logged_in_" . $uniquekey . ""]);

if (isset($_COOKIE["indicationrememberme_" . $uniquekey . ""])) {
	setcookie("indicationrememberme_" . $uniquekey . "", "", time()-86400);
}

header("Location: login.php?logged_out=true");

exit;

?>