<!DOCTYPE html>
<html lang="en">
  <head>
	<?php include 'includes/meta.php';?>
    <title>About - Rotten Potatoes</title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link href="http://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="test/css">
	<style>
    body {
        font: 20px Montserrat, sans-serif;
        line-height: 1.8;
        color: #f5f6f7;
    }
    p {font-size: 16px;}
    .margin {margin-bottom: 45px;}
    .bg-1 { 
        background-color: #FF8040; /* Mango Orange */
        color: #ffffff;
    }
    .bg-2 { 
        background-color: #ffffff; /* White */
        color: #555555;
    }
    .container-fluid {
        padding-top: 70px;
        padding-bottom: 70px;
    }
	
    </style>
  </head>
<body>

<?php include 'includes/header.php';?>

<!-- About Rotten Potatoes -->
<div class="container-fluid bg-1 text-left">
  <h3 class="margin">What is Rotten Potatoes?</h3>
  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p>
</div>

<!-- About the Members (in a grid) -->
<div class="container-fluid bg-2 text-left">    
  <h3 class="margin">The Team</h3><br>
  <div class="row text-center">
    <div class="col-sm-4">
	  <img src="images/isaac.jpg" class="img-responsive img-circle margin" style="width:100%" alt="Image">
      <h4 class="margin">Isaac Shannon</h4>
	  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
    </div>
    <div class="col-sm-4"> 
	  <img src="images/robbie.jpg" class="img-responsive img-circle margin" style="width:100%" alt="Image">
      <h4 class="margin">Robbie Elias</h4>
	  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
    </div>
    <div class="col-sm-4"> 
	  <img src="images/jesse.jpg" class="img-responsive img-circle margin" style="width:100%" alt="Image">
      <h4 class="margin">Jesse Desjardins</h4>
	  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
    </div>
  </div>
</div>

<?php include 'includes/footer.php';?>
<?php include 'includes/scripts.php';?>

</body>
</html>
