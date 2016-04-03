<?php

# include the config file
require "./includes/config.php";

# include the database class
require "./includes/Db.class.php";

# create a database object
$db = new Db();

if (isset($_POST['login'])) {

    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $password = isset($_POST['password']) ? trim($_POST['password']) : null;

    $db->bindMore(array('email' => $email, 'password' => $password));
    $user = $db->row('SELECT userid, firstname FROM movieuser WHERE email = :email AND password = :password');

	if (!empty($user)) {
		$loggedIn = true;
		$_SESSION['userid'] = $user['userid'];
		$_SESSION['firstname'] = $user['firstname'];
		header('Location: index.php');
	}

}
else if (isset($_GET['logout'])) {

    // remove all session variables
    session_unset(); 
    session_destroy();
	$loggedIn = false;

}

?>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include 'includes/meta.php';?>
    <title>Home - Rotten Potatoes</title>
  </head>
  <body>
    <?php include 'includes/header.php';?>
    <div class="jumbotron">
        <div class="container">
            <p>
                <span class="glyphicon glyphicon-film" aria-hidden="true"></span>
            </p>
            <h2>Movie recommendations for all you couch <strong>potatoes</strong></h2>
            <?php if (!$loggedIn) { ?>
            <p>
                <a class="btn btn-success btn-lg" href="register.php" role="button">Register &raquo;</a>
            </p>
            <?php } ?>
        </div>
    </div>
    <div class="container">
        <!-- Example row of columns -->
        <div class="row">
            <div class="col-md-4">
                <h2>Heading</h2>
                <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
                <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
            </div>
            <div class="col-md-4">
                <h2>Heading</h2>
                <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
                <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
            </div>
            <div class="col-md-4">
                <h2>Heading</h2>
                <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
                <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php';?>
    <?php include 'includes/scripts.php';?>
  </body>
</html>
