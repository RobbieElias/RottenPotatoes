<nav class="navbar navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Rotten Potatoes</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="index.php">Home</a></li>
                <?php if (!$loggedIn) { ?>
                <li><a href="#login" data-toggle="modal" data-target="#loginModal">Login</a></li>
                <li><a href="register.php">Register</a></li>
                <?php } else { ?>
                <li role="presentation" class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <?php echo $_SESSION['firstname']; ?>  <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="profile.php">View Profile</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="index.php?logout=true">Logout</a></li> 
                    </ul>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="Login" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Login</h4>
            </div>

            <div class="modal-body">
                <form id="loginForm" method="post" action="index.php" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-xs-3 control-label">Email</label>
                        <div class="col-xs-6">
                            <input id="login-email" type="email" class="form-control" name="email" maxlength="150" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-3 control-label">Password</label>
                        <div class="col-xs-6">
                            <input id="login-password" type="password" class="form-control" name="password" maxlength="20" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-6 col-xs-offset-3">
                            <input id="btn-login" type="submit" class="btn btn-primary" name="login" value="Login" />
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>