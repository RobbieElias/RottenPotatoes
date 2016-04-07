<?php

# include the config file
require "./includes/config.php";

# include the database class
require "./includes/Db.class.php";

# create a database object
$db = new Db();

$topicid;
if (!empty($_GET['id']) && ctype_digit($_GET['id'])) {
    $topicid = (int)$_GET['id'];
}

$topics = $db->query('SELECT t.topicid, t.description, (SELECT COUNT(*) FROM movietopics m WHERE m.topicid = t.topicid) AS moviecount FROM topics t ORDER BY t.description');

$title = 'Genres';
$page = 1;
$moviecount = 0;
$numPages = 1;
$prevPage = 1;
$nextPage = 1;
$topic;
$movies;
$sort = "rating";

if (!empty($topicid)) {

    $topic = findTopicid($topicid, $topics);

    if (!empty($topic)) {
        $title = $topic['description'] . ' Movies';

        if (!empty($_GET['page']) && ctype_digit($_GET['page'])) {
            $page = (int)$_GET['page'];
        }
        $offset = ($page - 1) * 50;

        if (!empty($_GET['sort']) && in_array($_GET['sort'], array('rating', 'alpha', 'popularity'))) {
            $sort = $_GET['sort'];
        }

        $orderBy = 'ORDER BY rating DESC, name';
        if ($sort === 'alpha') {
            $orderBy = 'ORDER BY name';
        }
        else if ($sort === 'popularity') {
            $orderBy = 'ORDER BY watchescount DESC, name';
        }

        $db->bind('topicid', $topicid);
        $movies = $db->query('SELECT m.movieid, m.name, m.datereleased, (SELECT coalesce(AVG(w.rating), 0) FROM watches w WHERE w.movieid = m.movieid) AS rating, (SELECT COUNT(*) FROM watches w WHERE w.movieid = m.movieid) AS watchescount FROM movie m JOIN movietopics t ON m.movieid = t.movieid AND t.topicid = :topicid ' . $orderBy . ' LIMIT 50 OFFSET ' . $offset);

        $moviecount = $topic['moviecount'];
        $numPages = (int)ceil($moviecount / 50);
        $prevPage = ($page === 1) ? $page : $page - 1;
        $nextPage = ($page === $numPages) ? $page : $page + 1;
    }

}

parse_str($_SERVER['QUERY_STRING'], $queryArray);
unset($queryArray['page']);
$queryNoPage = http_build_query($queryArray);
$queryNoPage = (empty($queryNoPage) ? '?' : '?' . $queryNoPage . '&');

function findTopicid($id, $array) {
   foreach ($array as $key => $val) {
       if ($val['topicid'] === $id) {
           return $val;
       }
   }
   return null;
}

?>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include 'includes/meta.php';?>
    <title><?php echo $title ?> - Rotten Potatoes</title>
  </head>
  <body>
    <?php include 'includes/header.php';?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <?php echo $title ?>
                    <?php if (!empty($topic)) { ?>
                    <span class="title-view-all">(<a href="genres.php">All Genres</a>)</span>
                    <?php } ?>
                </h1>
            </div>
        </div>
        <?php if (empty($topic)) { ?>
        <div class="row">
            <?php foreach ($topics as $key => $topic) { ?>
            <div class="col-xs-4 text-center genre-container">
                <a href="genres.php?id=<?php echo $topic['topicid'] ?>"><?php echo $topic['description'] ?></a><br>
                <span><?php echo $topic['moviecount'] ?> movies</span>
            </div>
            <?php } ?>
        </div>
        <?php } else if (empty($movies)) { ?>
        <div class="row">
            <div class="col-xs-12">
                <h3 class="text-danger">This genre doesn't have any movies yet.</h3>
            </div>
        </div>
        <?php } else { ?>
        <div class="row">
            <div class="col-xs-12">
                <div class="text-center sort-container hidden-xs">
                    <h4>Sort By:</h4>
                    <div class="btn-group text-center" role="group" aria-label="Sort">
                        <a href="genres.php?<?php echo 'id=' . $topicid . '&' ?>sort=alpha" type="button" class="btn<?php if ($sort === 'alpha') echo ' active'; ?> btn-default">
                            <span class="glyphicon glyphicon-sort-by-alphabet" aria-hidden="true"></span> Alphabetical
                        </a>
                        <a href="genres.php?<?php echo 'id=' . $topicid . '&' ?>sort=rating" type="button" class="btn<?php if ($sort === 'rating') echo ' active'; ?> btn-default">
                            <span class="glyphicon glyphicon-star" aria-hidden="true"></span> Rating
                        </a>
                        <a href="genres.php?<?php echo 'id=' . $topicid . '&' ?>sort=popularity" type="button" class="btn<?php if ($sort === 'popularity') echo ' active'; ?> btn-default">
                            <span class="glyphicon glyphicon-fire" aria-hidden="true"></span> Popularity
                        </a>
                    </div>
                </div>
                <div class="text-center sort-container visible-xs">
                    <h4>Sort By:</h4>
                    <div class="btn-group text-center" role="group" aria-label="Sort">
                        <a href="genres.php?<?php echo 'id=' . $topicid . '&' ?>sort=alpha" type="button" class="btn<?php if ($sort === 'alpha') echo ' active'; ?> btn-default">
                            <span class="glyphicon glyphicon-sort-by-alphabet" aria-hidden="true"></span>
                        </a>
                        <a href="genres.php?<?php echo 'id=' . $topicid . '&' ?>sort=rating" type="button" class="btn<?php if ($sort === 'rating') echo ' active'; ?> btn-default">
                            <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
                        </a>
                        <a href="genres.php?<?php echo 'id=' . $topicid . '&' ?>sort=popularity" type="button" class="btn<?php if ($sort === 'popularity') echo ' active'; ?> btn-default">
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
                            <th>Rating<?php if ($sort === 'rating') echo '&nbsp;&#9660;'; ?></th>
                            <th>Movie<?php if ($sort === 'alpha') echo '&nbsp;&#9660;'; ?></th>
                            <th class="hidden-xs">Year</th>
                            <th class="text-right">Views<?php if ($sort === 'popularity') echo '&nbsp;&#9660;'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movies as $key => $movie) { ?>
                        <tr class="movie-row" data-id="<?php echo $movie['movieid'] ?>">
                            <td><?php echo (($key + 1) + (($page - 1) * 50)) ?></td>
                            <td><span class="glyphicon glyphicon-star" aria-hidden="true"></span> <?php echo round($movie['rating'], 1) ?></td>
                            <td><?php echo $movie['name'] ?></td>
                            <td class="hidden-xs"><?php echo $movie['datereleased'] ?></td>
                            <td class="text-right"><?php echo $movie['watchescount'] ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row text-center">
            <ul class="pagination">
                <?php if ($prevPage !== $page) { ?>
                <li><a href="genres.php<?php echo $queryNoPage . 'page=' . $prevPage ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
                <?php } ?>
                <?php for ($i = 1; $i <= $numPages; $i++) { ?>
                <li <?php if ($i === $page) echo 'class="active"'; ?>><a data-page="<?php echo $i ?>" href="genres.php<?php echo $queryNoPage . 'page=' . $i ?>"><?php echo $i ?></a></li>
                <?php } ?>
                <?php if ($nextPage !== $page) { ?>
                <li><a href="genres.php<?php echo $queryNoPage . 'page=' . $nextPage ?>" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
                <?php } ?>
            </ul>
        </div>
        <?php } ?>
    </div>
    <?php include 'includes/footer.php';?>
    <?php include 'includes/scripts.php';?>
    <script type="text/javascript">
        $(document).ready(function() {

            $('.movie-row').on('click', function(e) {
                var movieid = $(this).data('id');
                window.location = 'movie.php?id=' + movieid;
            });

        });
    </script>
  </body>
</html>
