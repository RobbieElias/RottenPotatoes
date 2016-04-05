<?php

# include the config file
require "./includes/config.php";

# include the database class
require "./includes/Db.class.php";

# create a database object
$db = new Db();

if (isset($_GET['logout'])) {

    // remove all session variables
    session_unset(); 
    session_destroy();
	$loggedIn = false;

}

$topMovies = $db->query('SELECT movieid, name, datereleased, posterurl FROM movie LIMIT 8');

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
        <div class="row">
            <div class="col-md-12">
                <h2>Top Movies</h2>
            </div>
        </div>
        <div class="row">
            <?php foreach ($topMovies as $movie) { ?>
            <div class="col-md-3">
                    <div class="thumbnail movie-thumbnail">
                        <a class="movie-poster" href="movie.php?id=<?php echo $movie['movieid'] ?>" style="background-image: url('<?php echo $movie['posterurl'] ?>')"></a>
                        <div class="caption">
                            <h4><a href="movie.php?id=<?php echo $movie['movieid'] ?>"><?php echo $movie['name'] ?></a></h4>
                            <p><?php echo $movie['datereleased'] ?></p>
                            <input id="input-id" type="text" class="rating" data-size="xs" data-step="1" data-show-clear="false" data-display-only="true" value="4">
                            <div class="rating-label pull-left">4.5/5</div>
                        </div>
                    </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php include 'includes/footer.php';?>
    <?php include 'includes/scripts.php';?>
  </body>
</html>
