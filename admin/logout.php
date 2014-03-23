<?php

session_start();

unset($_SESSION["indication_user"]);

header("Location: login.php?logged_out=true");

exit;

?>