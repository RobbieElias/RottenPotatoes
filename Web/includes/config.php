<?php

# set error reporting to report all errors
error_reporting(E_ALL);

# set the php.ini file to display errors
ini_set('display_errors','On');

session_start();

$loggedIn = false;
$userid = null;
$firstname = '';

if (!empty($_SESSION['userid']) && !empty($_SESSION['firstname'])) {
	$loggedIn = true;
	$userid = $_SESSION['userid'];
	$userid = $_SESSION['firstname'];
}

?>