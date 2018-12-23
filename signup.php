<?php if (!isset($_SESSION)) session_start(); ?>

<?php

$status = null;
if (isset($_GET['signup'])) $status = $_GET['signup'];



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
    <link href="css/signin.css" rel="stylesheet">
    <link href="css/sticky-footer.css" rel="stylesheet">


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="libraries/jquery-3.3.1.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
            integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
            crossorigin="anonymous"></script>
    <script src="libraries/bootstrap-4/js/bootstrap.js"></script>
    <![endif]-->
</head>

<body>
<!-- NAVBAR
================================================== -->
<?php include 'includes/navbar.php'; ?>


<!-- Page Content -->
<div class="container">

    <form class="form-signin" action="includes/signup.php" method="POST">
        <h2 class="form-signin-heading">Register user</h2>
        <br>
        <?php
        if ($status === "usertaken") {
            echo '<div class="alert alert-danger" role="alert">Username already taken.</div>';
        } elseif ($status === "invalid") {
            echo '<div class="alert alert-danger" role="alert">Passwords did not match.</div>';
        } elseif ($status === "empty") {
            echo '<div class="alert alert-warning" role="alert">Please fill inn all fields.</div>';
        } elseif ($status === "email") {
            echo '<div class="alert alert-danger" role="alert">Email is not valid.</div>';
        }

        ?>
        <div class="form-group">
            <label for="inputUsername">Username</label>
            <input type="text" id="inputUsername" class="form-control" name="username" aria-describedby="usernameHelp"
                   placeholder="Username" required>
            <small id="usernameHelp" class="form-text text-muted">Username can contain any letters or numbers, without
                spaces.
            </small>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                    <label for="inputFirstname">First name</label>
                    <input type="text" id="inputFirstname" class="form-control" name="first" placeholder="First name"
                           required>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                    <label for="inputLastname">Last name</label>
                    <input type="text" id="inputLastname" class="form-control" name="last" placeholder="Last name"
                           required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="inputEmail">Email address</label>
            <input type="email" id="inputEmail" class="form-control" name="email" aria-describedby="emailHelp"
                   placeholder="Email adsress" required autofocus>
            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                    <label for="inputPassword">Password</label>
                    <input type="password" id="inputPassword" name="pwd" class="form-control"
                           aria-describedby="passwordHelp" placeholder="Passwoed" required>
                    <small id="passwordHelp" class="form-text text-muted">Password should contain at least 6
                        characters.
                    </small>

                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                    <label for="inputPasswordConfirm">Confirm password passord</label>
                    <input type="password" id="inputPasswordConfirm" name="pwdConf" class="form-control"
                           aria-describedby="passwordConfHelp" placeholder="Confirm password" required>
                    <small id="passwordConfHelp" class="form-text text-muted">Please confirm password.</small>

                </div>
            </div>
        </div>

        <button class="btn btn-lg btn-primary btn-block" name="submit" type="submit">Register</button>
    </form>


    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>


</div>
<!-- /container -->

</body>

</html>
