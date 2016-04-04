<?php

# include the config file
require "./includes/config.php";

# include the database class
require "./includes/Db.class.php";

# create a database object
$db = new Db();

$valid = true;

if (isset($_POST['login'])) {

    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $password = isset($_POST['password']) ? trim($_POST['password']) : null;

    $db->bindMore(array('email' => $email, 'password' => $password));
    $user = $db->row('SELECT userid, firstname FROM movieuser WHERE email = :email AND password = :password');

    if (!empty($user)) {
        $loggedIn = true;
        $_SESSION['userid'] = $user['userid'];
        $_SESSION['firstname'] = $user['firstname'];
        header('Location: index.php');
    }
    else {
        $valid = false;
    }

}

?>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include 'includes/meta.php';?>
    <title>Login - Rotten Potatoes</title>
  </head>
  <body>
    <?php include 'includes/header.php';?>
    <div class="container page-login">
        <h1>Login</h1>
        <form id="loginForm" method="post" action="login.php" data-toggle="validator" role="form">
            <div class="form-group">
                <label class="control-label">Email</label>
                <input id="login-email" type="email" class="form-control" name="email" maxlength="150" required />
                <div class="help-block with-errors"></div>
            </div>

            <div class="form-group">
                <label class="control-label">Password</label>
                <input id="login-password" type="password" class="form-control" name="password" maxlength="20" required />
                <div class="help-block with-errors"></div>
            </div>

            <div class="form-group">
                <input id="btn-login" type="submit" class="btn btn-success" name="login" value="Login" />
            </div>
        </form>
        <?php if (!$valid) echo '<p id="error-message" class="text-danger">Invalid login information.</p>'; ?>
    </div>
    <?php include 'includes/footer.php';?>
    <?php include 'includes/scripts.php';?>
    <script src="js/validator.min.js"></script>
    <script>
    $('#login-email, #login-password').on('focus', function(e) {
        $('#error-message').hide();
    });
    </script>
  </body>
</html>