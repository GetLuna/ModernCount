<?php

// Copyright Modern Group 2013-2014

session_start();

unset($_SESSION["indication_user"]);

header("Location: login.php?logged_out=true");

exit;

?>