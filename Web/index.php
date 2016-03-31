<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>

<?php

# include the config file
require "config.php";

# include the database class
require "Db.class.php";

# create a database object
$db = new Db();

$test = $db->query('SELECT id, name FROM test');

var_dump($test);

?>


</body>
</html>
