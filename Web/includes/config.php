<?php

# set error reporting to report all errors
error_reporting(E_ALL);

# set the php.ini file to display errors
ini_set('display_errors','On');

session_start();

# Constant for path with query string
define('PATH_QUERY', $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);

$loggedIn = false;

if (!empty($_SESSION['userid']) && !empty($_SESSION['firstname'])) {
	$loggedIn = true;
}

?>