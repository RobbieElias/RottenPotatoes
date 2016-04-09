<?php

# include the config file
require "./includes/config.php";

# include the database class
require "./includes/Db.class.php";

# create a database object
$db = new Db();

$page = 1;
if (!empty($_GET['page']) && ctype_digit($_GET['page'])) {
    $page = (int)$_GET['page'];
}
$sort = "rating";
$title = "Top Rated Actors";
if (!empty($_GET['sort']) && in_array($_GET['sort'], array('rating', 'alpha', 'popularity'))) {
    $sort = $_GET['sort'];
}

$actorcount = $db->single('SELECT COUNT(*) FROM actor');
$numPages = (int)ceil($actorcount / 50);
$prevPage = ($page === 1) ? $page : $page - 1;
$nextPage = ($page === $numPages) ? $page : $page + 1;

$orderBy = 'ORDER BY rating DESC';
if ($sort === 'alpha') {
    $title = "Actors";
    $orderBy = 'ORDER BY name';
}
else if ($sort === 'popularity') {
    $title = "Popular Actors";
    $orderBy = 'ORDER BY watchescount DESC';
}
$offset = ($page - 1) * 50;

$actors = $db->query('SELECT a.actorID, a.name, a.dateOfBirth, (SELECT coalesce(AVG(w.rating), 0) 	FROM watches w, movie m, actorPlays p WHERE a.actorID = p.actorID AND p.movieID = w.movieid and w.movieid = m.movieid) AS rating, (SELECT COUNT(*) FROM watches w, movie m, actorPlays p WHERE a.actorID = p.actorID AND p.movieID = w.movieid and w.movieid = m.movieid) AS watchcount FROM actor a ' . $orderBy . ' LIMIT 50 OFFSET ' . $offset);

parse_str($_SERVER['QUERY_STRING'], $queryArray);
unset($queryArray['page']);
$queryNoPage = http_build_query($queryArray);
$queryNoPage = (empty($queryNoPage) ? '?' : '?' . $queryNoPage . '&');

?>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include 'includes/meta.php';?>
    <title>Actors - Rotten Potatoes</title>
  </head>
  <body>
    <?php include 'includes/header.php';?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1><?php echo $title; ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="text-center sort-container hidden-xs">
                    <h4>Sort By:</h4>
                    <div class="btn-group text-center" role="group" aria-label="Sort">
                        <a href="actors.php?sort=alpha" type="button" class="btn<?php if ($sort === 'alpha') echo ' active'; ?> btn-default">
                            <span class="glyphicon glyphicon-sort-by-alphabet" aria-hidden="true"></span> Alphabetical
                        </a>
                        <a href="actors.php?sort=rating" type="button" class="btn<?php if ($sort === 'rating') echo ' active'; ?> btn-default">
                            <span class="glyphicon glyphicon-star" aria-hidden="true"></span> Rating
                        </a>
                        <a href="actors.php?sort=popularity" type="button" class="btn<?php if ($sort === 'popularity') echo ' active'; ?> btn-default">
                            <span class="glyphicon glyphicon-fire" aria-hidden="true"></span> Popularity
                        </a>
                    </div>
                </div>
                <div class="text-center sort-container visible-xs">
                    <h4>Sort By:</h4>
                    <div class="btn-group text-center" role="group" aria-label="Sort">
                        <a href="actors.php?sort=alpha" type="button" class="btn<?php if ($sort === 'alpha') echo ' active'; ?> btn-default">
                            <span class="glyphicon glyphicon-sort-by-alphabet" aria-hidden="true"></span>
                        </a>
                        <a href="actors.php?sort=rating" type="button" class="btn<?php if ($sort === 'rating') echo ' active'; ?> btn-default">
                            <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
                        </a>
                        <a href="actors.php?sort=popularity" type="button" class="btn<?php if ($sort === 'popularity') echo ' active'; ?> btn-default">
                            <span class="glyphicon glyphicon-fire" aria-hidden="true"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Movie<?php if ($sort === 'alpha') echo '&nbsp;&#9660;'; ?></th>
                            <th class="hidden-xs">Year</th>
                            <th class="text-right">Rating<?php if ($sort === 'rating') echo '&nbsp;&#9660;'; ?></th>
                            <th class="text-right">Views<?php if ($sort === 'popularity') echo '&nbsp;&#9660;'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($actors as $key => $actor) { ?>
                        <tr class="actor-row" data-id="<?php echo $actor['actorid'] ?>">
                            <td><?php echo (($key + 1) + (($page - 1) * 50)) ?></td>
                            <td><?php echo $actor['name'] ?></td>
                            <td class="hidden-xs"><?php echo $actor['datereleased'] ?></td>
                            <td class="text-right"><?php echo round($actor['rating'], 1) ?> <span class="glyphicon glyphicon-star" aria-hidden="true"></span></td>
                            <td class="text-right"><?php echo $actor['watchescount'] ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row text-center">
            <ul class="pagination">
                <?php if ($prevPage !== $page) { ?>
                <li><a href="actors.php<?php echo $queryNoPage . 'page=' . $prevPage ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
                <?php } ?>
                <?php for ($i = 1; $i <= $numPages; $i++) { ?>
                <li <?php if ($i === $page) echo 'class="active"'; ?>><a data-page="<?php echo $i ?>" href="actors.php<?php echo $queryNoPage . 'page=' . $i ?>"><?php echo $i ?></a></li>
                <?php } ?>
                <?php if ($nextPage !== $page) { ?>
                <li><a href="actors.php<?php echo $queryNoPage . 'page=' . $nextPage ?>" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <?php include 'includes/footer.php';?>
    <?php include 'includes/scripts.php';?>
    <script type="text/javascript">
        $(document).ready(function() {

            $('.actor-row').on('click', function(e) {
                var movieid = $(this).data('id');
                window.location = 'actor.php?id=' + actorid;
            });

        });
    </script>
  </body>
</html>
