<?php

// Copyright Modern Group 2013-2014

session_start();

unset($_SESSION["_user"]);

header("Location: login.php?logged_out=true");

exit;

?>