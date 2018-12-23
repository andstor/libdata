<?php if (!isset($_SESSION)) session_start(); ?>

<?php
$error_message = null;

if (isset($_GET['needed_role'])) {
    $error_message = "You need minimum the permission role \"" . $_GET['needed_role'] . "\" to access that resource.";
}

if (isset($_GET['login'])) {
    $error_message = "You need to be logged in.";
    header("Location: signin.php");
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
            <div class="col col-auto">
                <h1 class="text-center">Error :(</h1>
                <?php
                if (!$error_message == null) {
                    echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
                }
                ?>
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
