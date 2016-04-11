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
if (!empty($_GET['sort']) && in_array($_GET['sort'], array('rating', 'alpha', 'popularity'))) {
    $sort = $_GET['sort'];
}

$directorcount = $db->single('SELECT COUNT(*) FROM director');
$numPages = (int)ceil($directorcount / 50);
$prevPage = ($page === 1) ? $page : $page - 1;
$nextPage = ($page === $numPages) ? $page : $page + 1;

$title = "Top Rated Directors";
$orderBy = 'ORDER BY rating DESC, moviecount DESC, name';
if ($sort === 'alpha') {
    $title = "Directors";
    $orderBy = 'ORDER BY name';
}
else if ($sort === 'popularity') {
    $title = "Popular Directors";
    $orderBy = 'ORDER BY moviecount DESC, rating DESC, name';
}

$offset = ($page - 1) * 50;
$directors = $db->query('SELECT d1.directorid, d1.name, EXTRACT(year FROM d1.dateofbirth) AS year, (SELECT coalesce(AVG(w.rating), 0) FROM watches w JOIN movie m ON w.movieid = m.movieid JOIN directs d2 ON d2.directorid = d1.directorid AND d2.movieid = m.movieid) AS rating, (SELECT COUNT(*) FROM directs d3 WHERE d3.directorid = d1.directorid) AS moviecount FROM director d1 ' . $orderBy . ' LIMIT 50 OFFSET ' . $offset);

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
    <title>Directors - Rotten Potatoes</title>
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
                        <a href="directors.php?sort=alpha" type="button" class="btn<?php if ($sort === 'alpha') echo ' active'; ?> btn-default">
                            <span class="glyphicon glyphicon-sort-by-alphabet" aria-hidden="true"></span> Alphabetical
                        </a>
                        <a href="directors.php?sort=rating" type="button" class="btn<?php if ($sort === 'rating') echo ' active'; ?> btn-default">
                            <span class="glyphicon glyphicon-star" aria-hidden="true"></span> Rating
                        </a>
                        <a href="directors.php?sort=popularity" type="button" class="btn<?php if ($sort === 'popularity') echo ' active'; ?> btn-default">
                            <span class="glyphicon glyphicon-fire" aria-hidden="true"></span> Popularity
                        </a>
                    </div>
                </div>
                <div class="text-center sort-container visible-xs">
                    <h4>Sort By:</h4>
                    <div class="btn-group text-center" role="group" aria-label="Sort">
                        <a href="directors.php?sort=alpha" type="button" class="btn<?php if ($sort === 'alpha') echo ' active'; ?> btn-default">
                            <span class="glyphicon glyphicon-sort-by-alphabet" aria-hidden="true"></span>
                        </a>
                        <a href="directors.php?sort=rating" type="button" class="btn<?php if ($sort === 'rating') echo ' active'; ?> btn-default">
                            <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
                        </a>
                        <a href="directors.php?sort=popularity" type="button" class="btn<?php if ($sort === 'popularity') echo ' active'; ?> btn-default">
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
                            <th>Name<?php if ($sort === 'alpha') echo '&nbsp;&#9660;'; ?></th>
                            <th class="hidden-xs">Born</th>
                            <th class="text-right">Rating<?php if ($sort === 'rating') echo '&nbsp;&#9660;'; ?></th>
                            <th class="text-right">Movies<?php if ($sort === 'popularity') echo '&nbsp;&#9660;'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($directors as $key => $director) { ?>
                        <tr class="data-row" data-id="<?php echo $director['directorid'] ?>">
                            <td><?php echo (($key + 1) + (($page - 1) * 50)) ?></td>
                            <td><?php echo $director['name'] ?></td>
                            <td class="hidden-xs"><?php echo $director['year'] ?></td>
                            <td class="text-right"><?php echo round($director['rating'], 1) ?> <span class="glyphicon glyphicon-star" aria-hidden="true"></span></td>
                            <td class="text-right"><?php echo $director['moviecount'] ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row text-center">
            <ul class="pagination">
                <?php if ($prevPage !== $page) { ?>
                <li><a href="directors.php<?php echo $queryNoPage . 'page=' . $prevPage ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
                <?php } ?>
                <?php for ($i = 1; $i <= $numPages; $i++) { ?>
                <li <?php if ($i === $page) echo 'class="active"'; ?>><a data-page="<?php echo $i ?>" href="directors.php<?php echo $queryNoPage . 'page=' . $i ?>"><?php echo $i ?></a></li>
                <?php } ?>
                <?php if ($nextPage !== $page) { ?>
                <li><a href="directors.php<?php echo $queryNoPage . 'page=' . $nextPage ?>" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <?php include 'includes/footer.php';?>
    <?php include 'includes/scripts.php';?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.data-row').on('click', function(e) {
                var directorid = $(this).data('id');
                window.location = 'director.php?id=' + directorid;
            });
        });
    </script>
  </body>
</html>