<?php

# include the config file
require "./includes/config.php";

# include the database class
require "./includes/Db.class.php";

# create a database object
$db = new Db();

$userid;
$myProfile = false;

if (!empty($_GET['id']) && ctype_digit($_GET['id'])) {
    $userid = (int)$_GET['id'];
    if ($userid == $_SESSION['userid'])
        $myProfile = true;
} 
else if (!empty($_SESSION['userid'])) {
    $userid = (int)$_SESSION['userid'];
    $myProfile = true;
}
else {
    header('Location: index.php');
    die();
}

// Get user details
$db->bind('userid', $userid);
$user = $db->row('SELECT u.userid, u.firstname, u.lastname, u.city, u.province, u.country, lower(p.agerange) AS lowerrange, upper(p.agerange) AS upperrange, p.gender, p.occupation, p.deviceused FROM movieuser u LEFT JOIN profile p ON u.userid = p.userid WHERE u.userid = :userid');

// Get all watches/ratings
$db->bind('userid', $userid);
$watchedMovies = $db->query('SELECT m.movieid, m.name, w.rating FROM movie m JOIN watches w ON m.movieid = w.movieid WHERE w.userid = :userid ORDER BY w.datewatched DESC LIMIT 10');

// Get user's favorite movies according to rating
$db->bind('userid', $userid);
$favoriteMovies = $db->query('SELECT m.movieid, m.name, m.datereleased, m.posterurl, w.rating FROM movie m JOIN watches w ON m.movieid = w.movieid WHERE w.userid = :userid AND w.rating >= 3 ORDER BY w.rating DESC, name LIMIT 9');

$location;

if (!empty($user)) {
    $location = getLocation($user);
}

$hasInfo = true;
if (empty($location) && empty($user['lowerrange']) && empty($user['gender']) && empty($user['occupation'])) {
    $hasInfo = false;
}

function getLocation($user) {
    $text = '';
    $arr = array();
    if (!empty($user['city']))
        $arr[] = $user['city'];
    if (!empty($user['province']))
        $arr[] = $user['province'];
    if (!empty($user['country']))
        $arr[] = $user['country'];
    foreach ($arr as $key => $val) {
        if ($key > 0)
            $text .= ', ';    
        $text .= $val;
    }
    return $text;
}

?>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include 'includes/meta.php';?>
    <title><?php echo $user['firstname'] ?> - Rotten Potatoes</title>
  </head>
  <body>
    <?php include 'includes/header.php';?>
    <div class="container">
        <?php if (empty($user)) { ?>
        <h3 class="text-danger">User not found.</h3>
        <?php } else { ?>
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-name-title"><?php echo $user['firstname'] . ' ' . $user['lastname'] ?></h1>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="thumbnail">
                            <div class="profile-img-container">
                                <span class="glyphicon glyphicon-user img-circle" aria-hidden="true"></span>
                            </div>
                            <div class="caption">
                                <?php if ($hasInfo) { ?>
                                <table class="table borderless table-profile">
                                    <tbody>
                                        <?php if (!empty($user['gender'])) { ?>
                                        <tr>
                                            <td><strong>Gender:</strong></td>
                                            <td><?php echo $user['gender'] ?></td>
                                        </tr>
                                        <?php 
                                        }
                                        if (!empty($user['lowerrange'])) { ?>
                                        <tr>
                                            <td><strong>Age Group:</strong></td>
                                            <td><?php echo $user['lowerrange'] . '-' . $user['upperrange'] ?></td>
                                        </tr>
                                        <?php 
                                        }
                                        if (!empty($location)) { ?>
                                        <tr>
                                            <td><strong>Location:</strong></td>
                                            <td><?php echo $location ?></td>
                                        </tr>
                                        <?php 
                                        }
                                        if (!empty($user['occupation'])) { ?>
                                        <tr>
                                            <td><strong>Occupation:</strong></td>
                                            <td><?php echo $user['occupation'] ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <?php } else { ?>
                                    <p><strong>Occupation:</strong> Couch Potato</p>
                                    <p>(Information not provided...)</p>
                                <?php 
                                }
                                if ($myProfile) { ?>
                                <a href="account.php?tab=profile" type="button" class="btn btn-default" aria-label="Edit">
                                  <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                </a>
                                <?php } ?>
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
                                            <th>Movie</th>
                                            <th>Rating</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($watchedMovies)) {
                                            foreach ($watchedMovies as $val) { 
                                        ?>
                                            <tr>
                                                <td><a href="movie.php?id=<?php echo $val['movieid'] ?>"><?php echo $val['name'] ?></a></td>
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
                            <div class="panel-heading">Favorites</div>
                            <div class="panel-body">
                                <div class="row">
                                    <?php foreach ($favoriteMovies as $val) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                            <div class="thumbnail movie-thumbnail">
                                                <a class="movie-poster" href="movie.php?id=<?php echo $val['movieid'] ?>" style="background-image: url('<?php echo $val['posterurl'] ?>')"></a>
                                                <div class="caption favorites-container">
                                                    <h4><a class="movie-title" href="movie.php?id=<?php echo $val['movieid'] ?>"><?php echo $val['name'] ?></a></h4>
                                                    <p><?php echo $val['datereleased'] ?></p>
                                                    <p><em><?php if ($myProfile) echo "Your"; else echo $user['firstname'] . "'s"; ?></em> Rating:</p>
                                                    <input type="text" class="rating" data-size="sm" data-step="1" data-show-clear="false" data-display-only="true" value="<?php echo $val['rating'] ?>">
                                                    <div class="rating-label pull-left"><?php echo $val['rating'] ?>/5</div>
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
