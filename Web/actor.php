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

// Get actor details
$db->bind('actorid', $actorid);
$actor = $db->row('SELECT d.actorid, d.name, d.dateofbirth FROM actor d WHERE d.actorid = :actorid');

// Get movies
$db->bind('actorid', $actorid);
$movies = $db->query('SELECT m.movieid, m.name, m.datereleased, m.posterurl, p.role, (SELECT coalesce(AVG(w.rating), 0) FROM watches w WHERE w.movieid = m.movieid AND w.rating IS NOT NULL) AS rating FROM movie m JOIN actorplays p ON m.movieid = p.movieid JOIN actor a ON p.actorid = a.actorid WHERE p.actorid = :actorid ORDER BY rating DESC, name LIMIT 20');

?>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include 'includes/meta.php';?>
    <title><?php echo (!empty($actor)) ? $actor['name'] : 'Actor' ?> - Rotten Potatoes</title>
  </head>
  <body>
    <?php include 'includes/header.php';?>
    <div class="container">
        <?php if (empty($actor)) { ?>
        <h3 class="text-danger">Actor not found.</h3>
        <?php } else { ?>
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-name-title"><?php echo $actor['name'] ?><br><span class="small">Actor<?php echo $actor['dateofbirth'] ?></span></h1>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Movies
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <?php foreach ($movies as $movie) { ?>
                            <div class="col-md-3 col-sm-6">
                                    <div class="thumbnail movie-thumbnail">
                                        <a class="movie-poster" href="movie.php?id=<?php echo $movie['movieid'] ?>" style="background-image: url('<?php echo $movie['posterurl'] ?>')"></a>
                                        <div class="caption">
                                            <h4><a class="movie-title" href="movie.php?id=<?php echo $movie['movieid'] ?>"><?php echo $movie['name'] ?></a></h4>
                                            <p><?php echo $movie['datereleased'] ?></p>
                                            <p><strong>Role:</strong> <?php echo (!empty($movie['role'])) ? $movie['role'] : 'N/A' ?></p>
                                            <input type="text" class="rating" data-size="xs" data-step="1" data-show-clear="false" data-display-only="true" value="<?php echo round($movie['rating'], 1) ?>">
                                            <div class="rating-label pull-left"><?php echo round($movie['rating'], 1) ?>/5</div>
                                        </div>
                                    </div>
                            </div>
                            <?php } ?>
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