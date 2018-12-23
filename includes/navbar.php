<!-- NAVBAR
================================================== -->
<?php if (!isset($_SESSION)) session_start(); ?>

<nav class="navbar navbar-expand-md bg-white navbar-light border-bottom shadow-sm">
    <a class="navbar-brand" href="index.php">LibData</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="books.php">Books</a>
            </li>

            <?php if (isset($_SESSION['u_id'])) {
                if ($_SESSION['u_role'] == 'librarian' || $_SESSION['u_role'] == 'manager' || $_SESSION['u_role'] == 'admin') {
                    echo '<li class="nav-item">
                            <a class="nav-link" href="cms/index.php">CMS</a>
                          </li>';
                }
            } ?>

        </ul>

        <?php
        if (isset($_SESSION['u_id'])) {
            $username = $_SESSION['u_name'];
            echo '<form class="form-inline mt-2 mt-md-0" id="logoutForm" action="includes/logout.php" method="POST">
                       <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="active_loans.php">Active loans</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="loaning_history.php">History</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="profile.php">' . $username . '</a>
                            </li>
                        </ul>
                        <div class="form-group">
                            <button type="submit" class="btn btn-logout" name="submit">sign out</button>
                        </div>
                      </form>
            ';

        } else {
            echo '<form class="form-inline mt-2 mt-md-0" action="includes/login.php" method="POST">
                    <div class="form-group">
                        <input type="text" name="uid" placeholder="Username or Email" class="form-control">
                        <input type="password" name="pwd" placeholder="Password" class="form-control">
                        <button class="btn btn-outline-success my-2 my-sm-0" name="submit" type="submit">Log in</button>
                    </div>
                    <a class="btn btn-outline-primary" href="signup.php">Sign up</a>
                  </form>
            ';
        }
        ?>

    </div>
</nav>
