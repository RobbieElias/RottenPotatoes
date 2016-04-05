<?php 

# include the config file
require "./includes/config.php"; 

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'includes/meta.php';?>
    <title>About - Rotten Potatoes</title>
    <style>
        .team-title {
            margin: 0 0 20px 0;
            font-weight: bold;
        }
        .team-container img {
            width: 200px;
            height: 200px;
            margin: 0 auto;
        }
    </style>
</head>

<body>

    <?php include 'includes/header.php';?>
    <div class="jumbotron">
        <div class="container">
            <p>
                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
            </p>
            <h2>What is <strong>Rotten Potatoes?</strong></h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
        </div>
    </div>

    <!-- About the Members (in a grid) -->
    <div class="container team-container">
        <h2 class="text-center team-title">The Team</h2>
        <div class="row text-center">
            <div class="col-sm-4">
                <img src="http://www.incrediblesnaps.com/wp-content/uploads/2013/05/38-Animal-Portrait-Photography-6.jpg" class="img-responsive img-circle" alt="Image">
                <h4 class="margin">Isaac Shannon</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            </div>
            <div class="col-sm-4">
                <img src="http://1aike31wshtt3k0e9u2nxtwz.wpengine.netdna-cdn.com/wp-content/uploads/2011/01/animal-portraits_alex-castro.jpg" class="img-responsive img-circle" alt="Image">
                <h4 class="margin">Robbie Elias</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            </div>
            <div class="col-sm-4">
                <img src="http://webneel.com/daily/sites/default/files/images/daily/05-2013/15-animal-portrait-photography.jpg" class="img-responsive img-circle" alt="Image">
                <h4 class="margin">Jesse Desjardins</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php';?>
    <?php include 'includes/scripts.php';?>

</body>

</html>