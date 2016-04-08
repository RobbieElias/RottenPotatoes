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

$actorid = (int)$_GET['id']; 

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

    if (!empty($_POST['add-movie'])) {
        if (!empty($_POST['name'])) {
            $name = trim($_POST['name']);
            $db->bind('name', $name);
            $movieid = $db->single('SELECT moveieid FROM movie WHERE name = :name');
            if (empty($movieid)) {
                $db->bind('name', $name);
                $insert = $db->query('INSERT INTO movie(name) VALUES (:name)');
                if ($insert > 0) {
                    $movieid = $db->lastInsertId('movie_movieid_seq');
                }
            }
            if (!empty($movieid)) {
                try {
                    $db->bindMore(array("actorid"=>$actorid));
                    $insert = $db->query('INSERT INTO actorPlays (movieid, actorid) VALUES (:actorid, ' . $movieid . ')'); // wouldn't let me bind directorid for some reason...
                    if ($insert > 0)
                        $success = true;
                } catch (PDOException $e) {}
            }
        }
        
        if (!$success) {
            $messageMovie = "Movie could not be added.";
        }
    } else if (!empty($_POST['delete-movieid'])) {
        $movieid = $_POST['delete-movieid'];

        $db->bindMore(array("movieid"=>$movieid,"actorid"=>$actorid));
        $delete = $db->query('DELETE FROM actorPlays WHERE movieid = :movieid AND actorid = :arid');
        if ($delete > 0) {
            $success = true;
        }
        
        if (!$success) {
            $messageDirector = "Movie could not be deleted.";
        }
    }
}

// Get actor details
$db->bind('actorid', $actorid);
$director = $db->row('SELECT a.actorID, a.name, a.dateOfBirth, m.posterurl FROM actor d WHERE a.actorid = :actorid');

// Get movies
$db->bind('actorid', $actorid);
$movies = $db->query('SELECT m.movieid, m.name, m.datereleased, m.posterurl, (SELECT AVG(w1.rating) FROM watches w1 WHERE w1.movieid = m.movieid AND w1.rating IS NOT NULL) AS rating, (SELECT COUNT(*) FROM watches w2 WHERE w2.movieid = m.movieid AND w2.rating IS NOT NULL) AS ratingscount FROM movie m, actor a, actorPlays p WHERE a.actorID = :actorid AND a.actorID = p.actorID AND m.movieid = p.movieid');
$moviesTitle = "Movies";
$moviesText = "";
if (!empty($movies)) {
    foreach ($movies as $key => $movie) {
        if ($isEdit) {
            $moviesText .= '<form method="POST" id="form_delete_movie_' . $movie['movieid'] . '">' .
                                    $movie['name'] . '(<a href="#" onclick="document.getElementById(\'form_delete_movie_' .
                                    $movie['movieid'] . '\').submit();">Delete</a>)' .
                                    '<input type="hidden" name="delete-movieid" value="' . $movie['movieid'] . '" />' .
                              '</form>';
        }
        else {
            if ($key > 0)
                $moviesText .= ', ';
            $moviesText .= '<a href="movies.php?id=' . $movie['movieid'] . '">' . $movie['name'] . '</a>';
        }
    }
    if (count($movies) > 1) {
        $moviesTitle = "Movies";
    }
}
else {
    $moviesText = "-";
}
?>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include 'includes/meta.php';?>
    <title>Actor - Rotten Potatoes</title>
  </head>
  <body>
    <?php include 'includes/header.php';?>
    <div class="container">
		<?php if (empty($actor)) { ?>
        <h3 class="text-danger">Actor not found.</h3>
        <?php } else { ?>
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-name-title"><?php echo $actor['name'] ?></h1>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-4">
                <div class="row">
					<div class="col-sm-12">
						<a href="actor.php?id=<?php echo $actor['actorid'] ?>"><h1><?php echo $actor['name'] ?></h1></a>
					</div>
				</div>
			</div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">Actor Info
                                <?php if($loggedIn && !$isEdit) { ?>
                                <a href="<?php echo PATH_QUERY ?>&action=edit" type="button" class="btn btn-default btn-edit-movie-info pull-right" aria-label="Edit">
                                  <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                </a>
                                <?php } else if ($loggedIn && $isEdit) { ?>
                                <a href="actor.php?id=<?php echo $actorid ?>" type="button" class="btn btn-default btn-edit-movie-info pull-right" aria-label="Done">
                                  <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                </a>
                                <?php } ?>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-2 col-xs-4">
                                        <div class="info-margin"><strong><?php echo $moviesTitle ?>:</strong></div>
                                    </div>
                                    <div class="col-lg-4 col-xs-8">
									<?php foreach ($movies as $key => $movie) {?>
                                        <div class="info-margin"><?php echo $moviesText ?></div>
                                        <?php if ($isEdit) { ?>
                                        <form class="form-movie-add info-margin" method="POST">
                                            <div class="row">
                                                <div class="col-xs-8 left">
                                                    <input type="text" class="form-control" name="name" placeholder="Movie Title" required>
                                                </div>
                                                <div class="col-xs-4 right">
                                                    <input type="submit" class="btn btn-default" name="add-Movie" value="Add">
                                                </div>
                                            </div>
                                            <?php if (!empty($messageMovie)) echo '<p class="text-danger">' . $messageMovie . '</p>'; ?>
                                        </form>
                                        <?php } ?>  
									<?php }?>
                                    </div>
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