<?php

#
# Ceres
# (c) 2020 Simon Strudwick
# 

include 'Config.php';

if (ENABLE_DEVELOPMENT_MODE) {
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');		
}

include 'Page.php';	
include 'ContactPage.php';
include 'utilities.php';
include 'Parsedown.php';

?>
