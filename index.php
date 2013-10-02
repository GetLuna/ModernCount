<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by Josh Frandley copyright (C) 2012-2013
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Attempt to load the configuration file config.php
if (file_exists('config.php'))
	require 'config.php';
 
if (defined(INSTALL))
	header("Location: admin");
else
	header("Location: install/index.php");

exit;

?>
