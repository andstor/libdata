<!-- NAVBAR
================================================== -->
<?php if (!isset($_SESSION)) session_start(); ?>


<nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="index.php">Library CMS</a>
    <ul class="navbar-nav mr-auto">
        <li class="nav-item">
            <a class="nav-link mr-sm-2 mr-0" href="../index.php">Go to front page »</a>
        </li>
    </ul>
    <form class="form-inline ml-auto" id="logoutForm" action="../includes/logout.php" method="POST">
        <?php
        if (isset($_SESSION['u_id'])) {
            $session_user_id = $_SESSION['u_id'];
            $session_firstname = $_SESSION['u_first'];
            $session_lastname = $_SESSION['u_last'];
            echo '
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link mr-sm-2 mr-0" href="view_user.php?user_id=' . $session_user_id . ' " id="user">' . $session_firstname . " " . $session_lastname . '</a>
                        </li>
                    </ul>
                    <div class="form-group mr-sm-2">
                        <button class="btn btn-link my-2 my-sm-0 text-white-50 navbar-btn" type="submit" name="submit">log out</button>
                    </div>
             ';
        } else {
            header('../../signin.php');
        }
        ?>
    </form>
</nav>