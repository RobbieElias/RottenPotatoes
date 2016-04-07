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

$recentlyRated = $db->query('SELECT movieid, name, datereleased, posterurl FROM movie LIMIT 4');

$topActors = $db->query('SELECT actorid, name FROM actor LIMIT 10');
$topDirectors = $db->query('SELECT directorid, name AS name FROM director LIMIT 10');
$topGenres = $db->query('SELECT topicid, description FROM topics LIMIT 10');

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
            <div class="col-md-3 col-sm-6">
                    <div class="thumbnail movie-thumbnail">
                        <a class="movie-poster" href="movie.php?id=<?php echo $movie['movieid'] ?>" style="background-image: url('<?php echo $movie['posterurl'] ?>')"></a>
                        <div class="caption">
                            <h4><a class="movie-title" href="movie.php?id=<?php echo $movie['movieid'] ?>"><?php echo $movie['name'] ?></a></h4>
                            <p><?php echo $movie['datereleased'] ?></p>
                            <input type="text" class="rating" data-size="xs" data-step="1" data-show-clear="false" data-display-only="true" value="4">
                            <div class="rating-label pull-left">4.5/5</div>
                        </div>
                    </div>
            </div>
            <?php } ?>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <h2>Recently Rated</h2>
            </div>
        </div>
        <div class="row">
            <?php foreach ($recentlyRated as $movie) { ?>
            <div class="col-md-3 col-sm-6">
                    <div class="thumbnail movie-thumbnail">
                        <a class="movie-poster" href="movie.php?id=<?php echo $movie['movieid'] ?>" style="background-image: url('<?php echo $movie['posterurl'] ?>')"></a>
                        <div class="caption">
                            <h4><a class="movie-title" href="movie.php?id=<?php echo $movie['movieid'] ?>"><?php echo $movie['name'] ?></a></h4>
                            <p><?php echo $movie['datereleased'] ?></p>
                            <input type="text" class="rating" data-size="xs" data-step="1" data-show-clear="false" data-display-only="true" value="4">
                            <div class="user-rating"><strong>4 Stars</strong> by <a href="profile.php?id=1">John Doe</a></div>
                        </div>
                    </div>
            </div>
            <?php } ?>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-4">
                <h3 class="text-center top-list-title">Top Actors</h3>
                <ul class="list-group top-list">
                    <?php foreach ($topActors as $key => $actor) { ?>
                    <li class="list-group-item">
                        <span class="badge"><?php echo ($key + 1) ?></span>
                        <a href="actor.php?id=<?php echo $actor['actorid'] ?>"><?php echo $actor['name'] ?></a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="col-sm-4">
                <h3 class="text-center top-list-title">Top Directors</h3>
                <ul class="list-group top-list">
                    <?php foreach ($topDirectors as $key => $director) { ?>
                    <li class="list-group-item">
                        <span class="badge"><?php echo ($key + 1) ?></span>
                        <a href="director.php?id=<?php echo $director['directorid'] ?>"><?php echo $director['name'] ?></a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="col-sm-4">
                <h3 class="text-center top-list-title">Top Genres</h3>
                <ul class="list-group top-list">
                    <?php foreach ($topGenres as $key => $genre) { ?>
                    <li class="list-group-item">
                        <span class="badge"><?php echo ($key + 1) ?></span>
                        <a href="genre.php?id=<?php echo $genre['topicid'] ?>"><?php echo $genre['description'] ?></a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php';?>
    <?php include 'includes/scripts.php';?>
  </body>
</html>
