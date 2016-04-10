<?php

# include the config file
require "./includes/config.php";

# include the database class
require "./includes/Db.class.php";

# create a database object
$db = new Db();

if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
    header('Location: index.php');
    die();
}

$movieid = (int)$_GET['id'];

$isEdit = false;
if (!empty($_GET['action'])) {
    $isEdit = true;
}

// Handle POST actions
$messageDirector;
$messageGenre;

if (!empty($_POST) && $loggedIn) {

    $userid = $_SESSION['userid'];
    $success = false;

    if (!empty($_POST['add-director'])) {
        if (!empty($_POST['name'])) {
            $name = trim($_POST['name']);
            $db->bind('name', $name);
            $directorid = $db->single('SELECT directorid FROM director WHERE name = :name');
            if (empty($directorid)) {
                $db->bind('name', $name);
                $insert = $db->query('INSERT INTO director (name) VALUES (:name)');
                if ($insert > 0) {
                    $directorid = $db->lastInsertId('director_directorid_seq');
                }
            } 
            if (!empty($directorid)) {
                try {
                    $db->bindMore(array("movieid"=>$movieid));
                    $insert = $db->query('INSERT INTO directs (movieid, directorid) VALUES (:movieid, ' . $directorid . ')'); // wouldn't let me bind directorid for some reason...
                    if ($insert > 0)
                        $success = true;
                } catch (PDOException $e) {}
            }
        }
        
        if (!$success) {
            $messageDirector = "Director could not be added.";
        }
    } else if (!empty($_POST['add-genre'])) {
        if (!empty($_POST['topicid'])) {
            $topicid = $_POST['topicid'];

            $db->bindMore(array("movieid"=>$movieid,"topicid"=>$topicid));
            $insert = $db->query('INSERT INTO movietopics (movieid, topicid) VALUES (:movieid, :topicid)');
            if ($insert > 0)
                $success = true;
        }
        
        if (!$success) {
            $messageGenre = "Genre could not be added.";
        }
    } else if (!empty($_POST['delete-directorid'])) {
        $directorid = $_POST['delete-directorid'];

        $db->bind("directorid",$directorid);
        $hasMovies = $db->single('SELECT 1 FROM directs WHERE directorid = :directorid');

        $db->bindMore(array("movieid"=>$movieid,"directorid"=>$directorid));
        $delete = $db->query('DELETE FROM directs WHERE movieid = :movieid AND directorid = :directorid');
        if ($delete > 0) {
            $success = true;
        }

        // Delete the director completely if he/she doesn't have any movies
        if (!empty($hasMovies)) {
            $db->bind("directorid",$directorid);
            $db->query('DELETE FROM director WHERE directorid = :directorid');
        }
        
        if (!$success) {
            $messageDirector = "Director could not be deleted.";
        }
    } else if (!empty($_POST['delete-topicid'])) {
        $topicid = $_POST['delete-topicid'];

        $db->bindMore(array("movieid"=>$movieid,"topicid"=>$topicid));
        $delete = $db->query('DELETE FROM movietopics WHERE movieid = :movieid AND topicid = :topicid');
        if ($delete > 0) {
            $success = true;
        }
        
        if (!$success) {
            $messageGenre = "Genre could not be deleted.";
        }
    } else if (!empty($_POST['watched'])) {
        $db->bindMore(array("userid"=>$userid,"movieid"=>$movieid));
        $insert = $db->query('INSERT INTO watches (userid, movieid) VALUES (:userid, :movieid)');
    } else if (!empty($_POST['user-rating'])) {
        $rating = $_POST['user-rating'];
        $db->bindMore(array("userid"=>$userid,"movieid"=>$movieid));

        // check if watches entry exists or not
        $temp = $db->single('SELECT 1 FROM watches WHERE userid = :userid AND movieid = :movieid');
        if (empty($temp)) {
            $db->bindMore(array("userid"=>$userid,"movieid"=>$movieid,"rating"=>$rating));
            $insert = $db->query('INSERT INTO watches (userid, movieid, rating) VALUES (:userid, :movieid, :rating)');
        }
        else {
            $db->bindMore(array("rating"=>$rating,"userid"=>$userid,"movieid"=>$movieid));
            $update = $db->query('UPDATE watches SET rating = :rating WHERE userid = :userid AND movieid = :movieid');
        }
    } else if (!empty($_POST['remove-user-rating'])) {
        $db->bindMore(array("userid"=>$userid,"movieid"=>$movieid));
        $delete = $db->query('DELETE FROM watches WHERE userid = :userid AND movieid = :movieid');
    }
}

// Get movie details
$db->bind('movieid', $movieid);
$movie = $db->row('SELECT m.movieid, m.name, m.datereleased, m.posterurl, (SELECT AVG(w1.rating) FROM watches w1 WHERE w1.movieid = m.movieid AND w1.rating IS NOT NULL) AS rating, (SELECT COUNT(*) FROM watches w2 WHERE w2.movieid = m.movieid AND w2.rating IS NOT NULL) AS ratingscount FROM movie m WHERE m.movieid = :movieid');

// Get actors in the movie
$db->bind('movieid', $movieid);
$actors = $db->query('SELECT a.actorid, a.name, p.role FROM actor a JOIN actorplays p ON a.actorid = p.actorid AND p.movieid = :movieid');

// Get directors
$db->bind('movieid', $movieid);
$directors = $db->query('SELECT d1.directorid, d1.name FROM director d1 JOIN directs d2 ON d2.directorid = d1.directorid AND d2.movieid = :movieid');
$directorsTitle = "Director";
$directorsText = "";
if (!empty($directors)) {
    foreach ($directors as $key => $director) {
        if ($isEdit) {
            $directorsText .= '<form method="POST" id="form_delete_director_' . $director['directorid'] . '">' .
                                    $director['name'] . '(<a href="#" onclick="document.getElementById(\'form_delete_director_' .
                                    $director['directorid'] . '\').submit();">Delete</a>)' .
                                    '<input type="hidden" name="delete-directorid" value="' . $director['directorid'] . '" />' .
                              '</form>';
        }
        else {
            if ($key > 0)
                $directorsText .= ', ';
            $directorsText .= '<a href="director.php?id=' . $director['directorid'] . '">' . $director['name'] . '</a>';
        }
    }
    if (count($directors) > 1) {
        $directorsTitle = "Directors";
    }
}
else {
    $directorsText = "-";
}

// Get genres
$db->bind('movieid', $movieid);
$genres = $db->query('SELECT t.topicid, t.description FROM topics t JOIN movietopics m ON t.topicid = m.topicid AND m.movieid = :movieid ORDER BY description');
$genresTitle = "Genre";
$genresText = "";
if (!empty($genres)) {
    foreach ($genres as $key => $genre) {
        if ($isEdit) {
            $genresText .= '<form method="POST" id="form_delete_genre_' . $genre['topicid'] . '">' .
                                $genre['description'] . '(<a href="#" onclick="document.getElementById(\'form_delete_genre_' .
                                $genre['topicid'] . '\').submit();">Delete</a>)' .
                                '<input type="hidden" name="delete-topicid" value="' . $genre['topicid'] . '" />' .
                            '</form>';
        }
        else {
            if ($key > 0)
                $genresText .= ', ';
            $genresText .= '<a href="genres.php?id=' . $genre['topicid'] . '">' . $genre['description'] . '</a>';
        }
    }
    if (count($genres) > 1) {
        $genresTitle = "Genres";
    }
}
else {
    $genresText = "-";
}

// Get studio(s)
$db->bind('movieid', $movieid);
$studios = $db->query('SELECT s1.studioid, s1.name FROM studio s1 JOIN sponsors s2 ON s1.studioid = s2.studioid AND s2.movieid = :movieid');
$studiosTitle = "Studio";
$studiosText = "";
if (!empty($studios)) {
    foreach ($studios as $key => $studio) {
        if ($key > 0)
            $studiosText .= ', ';
        $studiosText .= '<a href="studios.php?id=' . $studio['studioid'] . '">' . $studio['name'] . '</a>';
    }
    if (count($studios) > 1) {
        $studiosTitle = "Studios";
    }
}
else {
    $studiosText = "-";
}

$db->bind('movieid', $movieid);
$availableGenres = $db->query('SELECT t.topicid, t.description FROM topics t WHERE NOT EXISTS (SELECT 1 FROM movietopics m WHERE m.topicid = t.topicid AND m.movieid = :movieid) ORDER BY description');


// Get user rating (watches table)
$watched = false;
$rating = null;
if ($loggedIn) {
    $userid = $_SESSION['userid'];
    $db->bindMore(array("userid"=>$userid,"movieid"=>$movieid));
    $row = $db->row('SELECT datewatched, rating FROM watches WHERE userid = :userid AND movieid = :movieid');
    if (!empty($row)) {
        $watched = true;
        if (!empty($row['rating'])) {
            $rating = $row['rating'];
        }
    }
}

// Get all users watches/ratings
$tempid = ($loggedIn) ? $userid : -1;
$db->bindMore(array("userid"=>$tempid,"movieid"=>$movieid));
$userWatches = $db->query('SELECT u.userid, u.firstname, u.lastname, w.rating FROM movieuser u JOIN watches w ON u.userid = w.userid WHERE u.userid != :userid AND w.movieid = :movieid ORDER BY w.datewatched DESC LIMIT 15');

// Get related movies
$topicid = -1;
if (!empty($genres)) {
    $movieGenre = $genres[array_rand($genres)]; // get 1 genre associated to the movie
    if (!empty($movieGenre))
        $topicid = $movieGenre['topicid'];
}
$db->bindMore(array("topicid"=>$topicid,"movieid"=>(int)$movieid));
$relatedMovies = $db->query('SELECT m.movieid, m.name, m.datereleased, m.posterurl, t.topicid FROM movie m JOIN movietopics t ON m.movieid = t.movieid AND m.movieid != :movieid ORDER BY t.topicid = :topicid DESC, random() LIMIT 6');

?>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include 'includes/meta.php';?>
    <title><?php echo (!empty($movie)) ? $movie['name'] : 'Movie' ?> - Rotten Potatoes</title>
  </head>
  <body>
    <?php include 'includes/header.php';?>
    <div class="container">
        <?php if (empty($movie)) { ?>
        <h3 class="text-danger">Movie not found.</h3>
        <?php } else { ?>
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-name-title"><?php echo $movie['name'] ?></h1>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                <div class="col-sm-12">
                    <div class="thumbnail movie-thumbnail">
                        <a href="movie.php?id=<?php echo $movie['movieid'] ?>"><img src="<?php echo $movie['posterurl'] ?>"></a>
                        <div class="caption">
                            <h3 class="potato-rate">Potato-rate</h3>
                                <span class="glyphicon glyphicon-star single-star pull-left" aria-hidden="true"></span>
                            <div class="rating-label-single"><?php echo round($movie['rating'], 1) ?>/5 (<?php echo $movie['ratingscount'] ?> Ratings)</div>
                            <?php if ($loggedIn) { ?>
                                <?php if (empty($rating)) { ?>
                                <form method="POST" id="watch-movie-form" class="user-rating-actions">
                                    <div>
                                        <button id="btn-rate" type="button" class="btn btn-warning btn-lg" aria-label="Star">
                                          <span class="glyphicon glyphicon-star" aria-hidden="true"></span> Rate
                                        </button>
                                        <button id="btn-watch" type="button" class="btn btn-success btn-lg" aria-label="Star" <?php if ($watched) echo 'disabled="disabled"'; ?>>
                                          <span class="glyphicon glyphicon-<?php if ($watched) echo 'ok'; else echo 'plus'; ?>" aria-hidden="true"></span> Watched
                                        </button>
                                    </div>
                                    <input type="hidden" name="watched" value="true">
                                </form>
                                <?php } ?>
                            <form method="POST" id="user-rating-container" style="display: <?php if (empty($rating)) echo 'none'; else echo 'block'; ?>;">
                                <?php if (empty($rating)) { ?>
                                <input id="user-rating" name="user-rating" type="text" class="rating" data-size="xs" data-step="1" data-show-clear="false" data-min="0" data-max="5">
                                <?php } else { ?>
                                <hr>
                                <p class="your-rating">Your Rating:</p>
                                <input id="user-rating" name="user-rating" type="text" class="rating" data-size="xs" data-step="1" data-show-clear="false" data-display-only="true" value="<?php echo $rating ?>" data-min="0" data-max="5">
                                <div class="rating-label pull-left"><?php echo $rating ?>/5 (<a href="#" onclick="document.getElementById('remove-user-rating').submit()">Remove</a>)</div>
                                <?php } ?>
                            </form>
                            <?php } ?>
                            <form method="POST" id="remove-user-rating">
                                <input type="hidden" name="remove-user-rating" value="true">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Recently Watched</div>
                        <div class="panel-body contains-table">
                            <table class="table">
                                <thead> 
                                    <tr>
                                        <th>Name</th>
                                        <th>Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (!empty($userWatches)) {
                                        foreach ($userWatches as $val) { 
                                    ?>
                                        <tr>
                                            <td><a href="profile.php?id=<?php echo $val['userid'] ?>"><?php echo $val['firstname'] . ' ' . $val['lastname'] ?></a></td>
                                            <td>
                                            <?php if (!empty($val['rating'])) { ?>
                                                <?php echo $val['rating'] ?> <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
                                            <?php } else { ?>
                                            <span>-</span>
                                            <?php } ?>                                                
                                            </td>
                                        </tr>
                                    <?php 
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="2"><em>None</em></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">Movie Info
                                <?php if($loggedIn && !$isEdit) { ?>
                                <a href="<?php echo PATH_QUERY ?>&action=edit" type="button" class="btn btn-default btn-edit-movie-info pull-right" aria-label="Edit">
                                  <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                </a>
                                <?php } else if ($loggedIn && $isEdit) { ?>
                                <a href="movie.php?id=<?php echo $movieid ?>" type="button" class="btn btn-default btn-edit-movie-info pull-right" aria-label="Done">
                                  <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                </a>
                                <?php } ?>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-2 col-xs-4">
                                        <div class="info-margin"><strong><?php echo $directorsTitle ?>:</strong></div>
                                    </div>
                                    <div class="col-lg-4 col-xs-8">
                                        <div class="info-margin"><?php echo $directorsText ?></div>
                                        <?php if ($isEdit) { ?>
                                        <form class="form-movie-add info-margin" method="POST">
                                            <div class="row">
                                                <div class="col-xs-8 left">
                                                    <input type="text" class="form-control" name="name" placeholder="Director Name" required>
                                                </div>
                                                <div class="col-xs-4 right">
                                                    <input type="submit" class="btn btn-default" name="add-director" value="Add">
                                                </div>
                                            </div>
                                            <?php if (!empty($messageDirector)) echo '<p class="text-danger">' . $messageDirector . '</p>'; ?>
                                        </form>
                                        <?php } ?>                                        
                                    </div>
                                    <div class="col-lg-2 col-xs-4">
                                        <div class="info-margin"><strong><?php echo $genresTitle ?>:</strong></div>
                                    </div>
                                    <div class="col-lg-4 col-xs-8">
                                        <div class="info-margin"><?php echo $genresText ?></div>
                                        <?php if ($isEdit) { ?>
                                        <form class="form-movie-add info-margin" method="POST">
                                            <div class="row">
                                                <div class="col-xs-8 left">
                                                    <select class="form-control" name="topicid">
                                                        <option value="">-- Genre --</option>
                                                        <?php foreach ($availableGenres as $genre) { ?>
                                                            <option value="<?php echo $genre['topicid'] ?>"><?php echo $genre['description'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-xs-4 right">
                                                    <input type="submit" class="btn btn-default" name="add-genre" value="Add">
                                                </div>
                                            </div>
                                            <?php if (!empty($messageGenre)) echo '<p class="text-danger">' . $messageGenre . '</p>'; ?>
                                        </form>
                                        <?php } ?> 
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2 col-xs-4 info-margin">
                                        <div><strong>Year:</strong></div>
                                    </div>
                                    <div class="col-lg-4 col-xs-8 info-margin">
                                        <div><?php echo $movie['datereleased'] ?></div>
                                    </div>
                                    <div class="col-lg-2 col-xs-4 info-margin">
                                        <div><strong><?php echo $studiosTitle ?>:</strong></div>
                                    </div>
                                    <div class="col-lg-4 col-xs-8 info-margin">
                                        <div><?php echo $studiosText ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">Main Cast</div>
                            <div class="panel-body">
                                <div class="row">
                                    <?php foreach ($actors as $actor) { ?>
                                    <div class="col-sm-6 col-md-4">
                                        <p class="actor-name"><a href="actor.php?id=<?php echo $actor['actorid'] ?>"><?php echo $actor['name'] ?></a><br /><span>as <?php echo (empty($actor['role'])) ? 'N/A' : $actor['role'] ?></span></p>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">Related Movies</div>
                            <div class="panel-body">
                                <div class="row">
                                    <?php foreach ($relatedMovies as $val) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                            <div class="thumbnail movie-thumbnail">
                                                <a class="movie-poster" href="movie.php?id=<?php echo $val['movieid'] ?>" style="background-image: url('<?php echo $val['posterurl'] ?>')"></a>
                                                <div class="caption">
                                                    <h4><a class="movie-title" href="movie.php?id=<?php echo $val['movieid'] ?>"><?php echo $val['name'] ?></a></h4>
                                                    <p><?php echo $val['datereleased'] ?></p>
                                                </div>
                                            </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php include 'includes/footer.php';?>
    <?php include 'includes/scripts.php';?>
    <script type="text/javascript">
        $('#btn-rate').on('click', function(e) {
            e.preventDefault();
            $('#user-rating-container').show();
        });
        $('#btn-watch').on('click', function(e) {
            e.preventDefault();
            $('#watch-movie-form').submit();
        });
        $('#user-rating').on('rating.change', function() {
            $('#user-rating-container').submit();
        });
    </script>
  </body>
</html>
