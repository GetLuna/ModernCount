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

$username = ADMIN_USER;
$password = ADMIN_PASSWORD;
$salt = SALT;

session_start();

//If cookie is set, skip login
if (isset($_COOKIE["indication_user_rememberme"])) {
    $_SESSION["indication_user"] = $username;
}

if (isset($_POST["password"]) && isset($_POST["username"])) {
    $hashedpassword = hash("sha256", $salt . hash("sha256", $_POST["password"]));
    if ($hashedpassword == $password && $_POST["username"] == $username) {
        $_SESSION["indication_user"] = $username;
            if (isset($_POST["rememberme"])) {
                setcookie("indication_user_rememberme", $username, time()+1209600);
            }
    } else {
        header("Location: login.php?login_error=true");
    }
} 

if (!isset($_SESSION["indication_user"])) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Indication &middot; Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<link href="../resources/bootstrap/css/bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="../resources/bootstrap/css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet">
<style type="text/css">
body {
    padding-top: 60px;
    background-color: #f5f5f5;
}
.form-signin {
    max-width: 300px;
    padding: 19px 29px 29px;
    margin: 0 auto 20px;
    background-color: #fff;
    border: 1px solid #e5e5e5;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
    -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
    box-shadow: 0 1px 2px rgba(0,0,0,.05);
}
.form-signin .form-signin-heading, .form-signin .checkbox {
    margin-bottom: 10px;
}
.form-signin input[type="text"], .form-signin input[type="password"] {
    font-size: 16px;
    height: auto;
    margin-bottom: 5px;
    padding: 7px 9px;
}
</style>
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body>
<!-- Content start -->
<div class="container">
<form class="form-signin" method="post">
<fieldset>
<h2 class="form-signin-heading text-center">Indication</h2>
<?php 
if (isset($_GET["login_error"])) {
    echo "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>Incorrect username or password.</div>";
} elseif (isset($_GET["logged_out"])) {
    echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>Successfully logged out.</div>";
}
?>
<div class="control-group">
<label class="control-label" for="username">Username</label>
<div class="controls">
<input type="text" id="username" name="username" class="input-block-level" placeholder="Username...">
</div>
</div>
<div class="control-group">
<label class="control-label" for="password">Password</label>
<div class="controls">
<input type="password" id="password" name="password" class="input-block-level" placeholder="Password...">
</div>
</div>
<div class="control-group">
<div class="controls">
<label class="checkbox">
<input type="checkbox" id="rememberme" name="rememberme"> Remember Me
</label>
</div>
</div>
<button type="submit" class="btn pull-right">Login</button>
</fieldset>
</form>
</div>
<!-- Content end -->
<!-- Javascript start -->
<script src="../resources/jquery.min.js"></script>
<script src="../resources/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("#username").focus();
});
</script>
<!-- Javascript end -->
</body>
</html>
<?php
} else {
    header("Location: index.php");
    exit;
}
?>