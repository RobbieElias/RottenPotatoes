<?php

# include the config file
require "./includes/config.php";

# include the database class
require "./includes/Db.class.php";

# create a database object
$db = new Db();

$term;
if (!empty($_GET['term'])) {
    $term = $_GET['term'];
}
else {
    header('Location: index.php');
    die();
}

$termSql = "%" . strtolower($term) . "%";
$db->bindMore(array('t1'=>$termSql,'t2'=>$termSql,'t3'=>$termSql,'t4'=>$termSql,'t5'=>$termSql));
$results = $db->query("SELECT * FROM ((SELECT m.movieid AS id, m.name, 'Movie' AS type FROM movie m WHERE lower(m.name) LIKE :t1) UNION ALL " .
                      "(SELECT a.actorid AS id, a.name, 'Actor' AS type FROM actor a WHERE lower(a.name) LIKE :t2) UNION ALL " .
                      "(SELECT d.directorid AS id, d.name, 'Director' AS type FROM director d WHERE lower(d.name) LIKE :t3) UNION ALL " .
                      "(SELECT t.topicid AS id, t.description AS name, 'Genre' AS type FROM topics t WHERE lower(t.description) LIKE :t4) UNION ALL " .
                      "(SELECT s.studioid AS id, s.name, 'Studio' AS type FROM studio s WHERE lower(s.name) LIKE :t5)) " .
                      "results ORDER BY name LIMIT 50");

?>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include 'includes/meta.php';?>
    <title>Search - Rotten Potatoes</title>
  </head>
  <body>
    <?php include 'includes/header.php';?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>Search Results for "<?php echo $term ?>":</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <?php if (count($results) > 0) { ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $key => $result) { ?>
                        <tr class="table-row" data-id="<?php echo $result['id'] ?>" data-type="<?php echo $result['type'] ?>">
                            <td><?php echo ($key + 1) ?></td>
                            <td><?php echo $result['type'] ?></td>
                            <td><?php echo $result['name'] ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                <h4 class="text-danger">No results found.</h4>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php';?>
    <?php include 'includes/scripts.php';?>
    <script type="text/javascript">
        $(document).ready(function() {

            $('.table-row').on('click', function(e) {
                var id = $(this).data('id');
                var type = $(this).data('type');
                if (type === 'Movie')
                    window.location = 'movie.php?id=' + id;
                else if (type === 'Actor')
                    window.location = 'actor.php?id=' + id;
                else if (type === 'Director')
                    window.location = 'director.php?id=' + id;
                else if (type === 'Genre')
                    window.location = 'genres.php?id=' + id;
                else if (type === 'Studio')
                    window.location = 'studios.php?id=' + id;
            });

        });
    </script>
  </body>
</html>
