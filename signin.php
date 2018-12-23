<?php if (!isset($_SESSION)) session_start(); ?>

<?php

$status = null;
if (isset($_GET['signup'])) $status = $_GET['signup'];


$login_status = null;


if (isset($_GET['login'])) {
    $login_status = "The username or password did not match any registered user.";
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>Signin Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="libraries/bootstrap-4/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/common.css" rel="stylesheet">
</head>

<?php include 'includes/navbar.php'; ?>


<main role="main">
    <!-- Page Content -->
    <br>
    <div class="container">
        <div class="row justify-content-md-center">
            <div class="col col-lg-5">
                <?php
                if (!$login_status == null) {
                    echo '<div class="alert alert-danger" role="alert">' . $login_status . '</div>';
                }
                ?>
                <?php
                if ($status === "success") {
                    echo '<div class="alert alert-success" role="alert">Successfully created user account.</div>';
                }
                ?>
                <h2 class="form-signin-heading">Please sign in</h2>
                <form class="form" action="includes/login.php" method="POST">
                    <div class="form-group">
                        <label for="inputUsername" class="sr-only">Username or Email</label>
                        <input type="text" id="inputUsername" name="uid" class="form-control" placeholder="Username or Email" required
                               autofocus>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword" class="sr-only">Password</label>
                        <input type="password" id="inputPassword" name="pwd" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="remember-me"> Remember me
                            </label>
                        </div>
                    </div>
                    <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Sign in</button>
                </form>
            </div>
        </div>
    </div>
    <br>
    <!-- /container -->
    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
</main>


<!-- /.container -->


<!-- Bootstrap core JavaScript
  ================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="libraries/jquery-3.3.1.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<script src="libraries/bootstrap-4/js/bootstrap.js"></script>
</body>
</html>
