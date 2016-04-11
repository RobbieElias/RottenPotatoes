<nav class="navbar navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php"><img src="images/logo.png" alt="Rotten Potatoes"></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="index.php">Home</a></li>
                <li><a class="hidden-sm" href="movies.php">Movies</a></li>
                <li><a class="hidden-sm" href="actors.php">Actors</a></li>
                <li role="presentation" class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        More  <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="visible-sm" href="movies.php">Movies</a></li>
                        <li><a class="visible-sm" href="actors.php">Actors</a></li>
                        <li><a href="directors.php">Directors</a></li>
                        <li><a href="genres.php">Genres</a></li>
                        <li><a href="studios.php">Studios</a></li>
                    </ul>
                </li>
                <?php if (!$loggedIn) { ?>
                <li><a href="login.php">Login</a></li>
                <?php } else { ?>
                <li role="presentation" class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <?php echo $_SESSION['firstname']; ?>  <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="profile.php">View Profile</a></li>
                        <li><a href="account.php">Account Settings</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="index.php?logout=true">Logout</a></li> 
                    </ul>
                </li>
                <?php } ?>
            </ul>
            <form id="searchForm" class="navbar-form navbar-right navbar-input-group" role="search" method="GET" action="search.php">
                <div class="input-group">
                    <input id="search-term" type="text" class="form-control" name="term" placeholder="Search Movies, Actors, ..." value="<?php if (!empty($_GET['term'])) echo $_GET['term']; ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                    <div class="input-group-btn">
                        <button id="nav-search" type="submit" class="btn btn-md btn-default" aria-label="Search">
                            <span class="glyphicon glyphicon-search"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</nav>