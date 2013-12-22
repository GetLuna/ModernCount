<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("../config.php")) {
	header('Location: ../installer');
	exit;
}

require_once("../config.php");

session_start();

session_unset("indication_user");

if (isset($_COOKIE["indication_user_rememberme"])) {
	setcookie("indication_user_rememberme", "", time()-86400);
}

header("Location: login.php?logged_out=true");

exit;

?>