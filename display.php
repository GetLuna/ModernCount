<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by Josh Frandley copyright (C) 2012-2013
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

if (!file_exists("config.php")) {
    die("Error: Config file not found! Please contact the site administrator.");
}

require_once("config.php");
require_once("includes/common.php");

//Connect to database
@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Error: Could not connect to database (" . mysql_error() . "). Check your database settings are correct.");
}

//Check database exists
$does_db_exist = mysql_select_db(DB_NAME, $con);
if (!$does_db_exist) {
    die("Error: Database does not exist (" . mysql_error() . "). Check your database settings are correct.");
}

if (isset($_GET["id"])) {
    $id = mysql_real_escape_string($_GET["id"]);
} else {
    die("Error: ID cannot be blank.");
}

//If ID exists, show count or else die
$showinfo = mysql_query("SELECT count FROM Data WHERE id = \"$id\"");
$showresult = mysql_fetch_assoc($showinfo);
if ($showresult != 0) {
    echo $showresult["count"];
} else {
    die("Error: ID does not exist.");
}

mysql_close($con);

?>