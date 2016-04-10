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
            <p>Rotten Potatoes is a movie recommendation website that was built by the three uOttawa students below. This application was completed as the final group project for the <strong>CSI2132</strong> course.</p>
        </div>
    </div>

    <!-- About the Members (in a grid) -->
    <div class="container team-container">
        <h2 class="text-center team-title">The Team</h2>
        <div class="row text-center">
            <div class="col-sm-4">
                <img src="images/portrait_isaac.jpg" class="img-responsive img-circle" alt="Image">
                <h4 class="margin">Isaac Shannon</h4>
                <p>Foot model, popular Kazhak pop singer, veteran of three world wars and cup stacking world record holder.</p>
            </div>
            <div class="col-sm-4">
                <img src="images/portrait_robbie.jpg" class="img-responsive img-circle" alt="Image">
                <h4 class="margin">Robbie Elias</h4>
                <p>Computer Science student at uOttawa, graduated Heritage College in Computer Science.</p>
            </div>
            <div class="col-sm-4">
                <img src="images/portrait_jesse.jpg" class="img-responsive img-circle" alt="Image">
                <h4 class="margin">Jesse Desjardins</h4>
                <p>Professional amateur, 4-time Grammy winning didgeridoo player, and master procrastinator.</p>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php';?>
    <?php include 'includes/scripts.php';?>

</body>

</html>