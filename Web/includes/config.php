<?php

# set error reporting to report all errors
error_reporting(E_ALL);

# set the php.ini file to display errors
ini_set('display_errors', true);

session_start();

# Constant for path with query string
define('PATH_QUERY', $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);

$loggedIn = false;

if (!empty($_SESSION['userid']) && !empty($_SESSION['firstname'])) {
	$loggedIn = true;
}

$pageName = basename($_SERVER['PHP_SELF']);

if ($loggedIn) { // disallowed pages when logged in
	if ($pageName === 'register.php' || $pageName === 'login.php') {
		header('Location: index.php');
		die();
	}
}
else { // disallowed pages when logged out
	if ($pageName === 'account.php') {
		header('Location: login.php');
		die();
	}
}

?>