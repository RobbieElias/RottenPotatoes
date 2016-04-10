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

$topMovies = $db->query('SELECT m.movieid, m.name, m.datereleased, m.posterurl, (SELECT coalesce(AVG(w.rating), 0) FROM watches w WHERE w.movieid = m.movieid) rating FROM movie m ORDER BY rating DESC, name LIMIT 8');
$recentlyRated = $db->query('SELECT m.movieid, m.name, m.datereleased, m.posterurl, w.rating, u.userid, u.firstname, u.lastname FROM movie m JOIN watches w ON m.movieid = w.movieid JOIN movieuser u ON w.userid = u.userid WHERE w.rating IS NOT NULL ORDER BY datewatched DESC LIMIT 4');
$topActors = $db->query('SELECT a.actorid, a.name, (SELECT coalesce(AVG(w.rating), 0) FROM watches w JOIN actorplays p ON w.movieid = p.movieid AND p.actorid = a.actorid) rating FROM actor a ORDER BY rating DESC, name LIMIT 10');
$topDirectors = $db->query('SELECT d1.directorid, d1.name, (SELECT coalesce(AVG(w.rating), 0) FROM watches w JOIN directs d2 ON w.movieid = d2.movieid AND d2.directorid = d1.directorid) rating FROM director d1 ORDER BY rating DESC, name LIMIT 10');
$topGenres = $db->query('SELECT t.topicid, t.description, (SELECT COUNT(*) FROM watches w JOIN movietopics m ON w.movieid = m.movieid AND m.topicid = t.topicid WHERE m.topicid = t.topicid) watchcount FROM topics t ORDER BY watchcount DESC, description LIMIT 10');

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
            <p><a class="btn btn-success btn-lg" href="register.php" role="button">Register &raquo;</a></p>
            <p id="login-link" class="visible-xs"><a href="login.php" role="button">Login</a></p>
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
                            <input type="text" class="rating" data-size="xs" data-step="1" data-show-clear="false" data-display-only="true" value="<?php echo round($movie['rating'], 1) ?>">
                            <div class="rating-label pull-left"><?php echo round($movie['rating'], 1) ?>/5</div>
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
                            <input type="text" class="rating" data-size="xs" data-step="1" data-show-clear="false" data-display-only="true" value="<?php echo $movie['rating'] ?>">
                            <div class="user-rating"><strong><?php echo $movie['rating'] ?> Stars</strong> by <a href="profile.php?id=<?php echo $movie['userid'] ?>"><?php echo $movie['firstname'] . ' '. $movie['lastname'] ?></a></div>
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
                <h3 class="text-center top-list-title">Popular Genres</h3>
                <ul class="list-group top-list">
                    <?php foreach ($topGenres as $key => $genre) { ?>
                    <li class="list-group-item">
                        <span class="badge"><?php echo ($key + 1) ?></span>
                        <a href="genres.php?id=<?php echo $genre['topicid'] ?>"><?php echo $genre['description'] ?></a>
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
        </div>
    </div>
    <?php include 'includes/footer.php';?>
    <?php include 'includes/scripts.php';?>
  </body>
</html>
