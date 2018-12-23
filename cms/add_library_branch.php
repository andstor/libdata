<?php include '../config/config.php'; ?>
<?php include '../connections/Database.php'; ?>
<?php include '../helpers/format_helper.php'; ?>

<?php
if (!isset($_SESSION)) session_start();

if (isset($_SESSION['u_id'])) {
    if ($_SESSION['u_role'] != 'librarian' && $_SESSION['u_role'] != 'manager' && $_SESSION['u_role'] != 'admin') {
        header("Location: ../error.php?needed_role=librarian");
        exit();
    }
} else {
    header("Location: ../error.php?login=false");
}

// Create DB object
$db = new Database;

$status = null;
if (isset($_GET['status'])) $status = $_GET['status'];

// Get countries
$query = "SELECT DISTINCT c.country
              FROM country AS c
              ORDER BY c.country ASC";
$countries = $db->select($query);

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
    <link rel="icon" href="../../favicon.ico">

    <title>Add user</title>

    <!-- Bootstrap core CSS -->
    <link href="../libraries/bootstrap-4/css/bootstrap.css" rel="stylesheet">
    <link href="../node_modules/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/common.css" rel="stylesheet">
    <link rel="stylesheet" href="css/costum.css">


</head>

<body>

<?php include 'includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

            <div id="breadcrumb"></div>
            <?php
            if ($status === "libexists") {
                echo '<div class="alert alert-danger" role="alert">Library branch already exists.</div>';
            } else if ($status === "success") {
                echo '<div class="alert alert-success" role="alert">Library branch successfully created.</div>';
            }
            ?>
            <h1 class="page-header">New library branch
                <small> - add a new library branch</small>
            </h1>

            <form action="includes/add_library_branch.php" method="POST" enctype="multipart/form-data">
                <div class="row">

                    <div class="col-lg-6">

                        <div class="form-group">
                            <label for="inputName">Library branch name*</label>
                            <input type="text" id="inputName" class="form-control" name="name"
                                   aria-describedby="nameHelp" placeholder="Branch name" required autofocus>
                            <small id="nameHelp" class="form-text text-muted">Name can contain any letters.
                            </small>
                        </div>




                        <hr>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="selectCountry">Country*</label>
                                    <select class="selectpicker form-control " id="selectCountry" name="country" placeholder="Country" required>
                                        <option value="">Country</option>
                                        <?php if ($countries) : ?>
                                        <?php while ($row = $countries->fetch_assoc()) : ?>
                                            <option><?php echo $row['country']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                    <?php else : ?>
                                        <p>No countries yet</p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                    <label for="selectRegion">Region*</label>
                                    <select class="selectpicker form-control" id="selectRegion"
                                            name="region"
                                            data-live-search="true" required>
                                        <option value="">Region</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                    <label for="selectCity">City*</label>
                                    <select class="selectpicker form-control" id="selectCity"
                                            name="city"
                                            data-live-search="true" required>
                                        <option value="">City</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                    <label for="inputPostal">Postal address</label>
                                    <input type="text" id="inputPostal" class="form-control" name="postalRegion"
                                           placeholder="Postal address">
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                    <label for="inputPostalcode">Postal code</label>
                                    <input type="text" id="inputPostalcode" class="form-control" name="postalCode"
                                           placeholder="Postal code">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputAddress1">Address 1*</label>
                            <input type="text" id="inputAddress1" class="form-control" name="address1"
                                   placeholder="Address 1" required>
                        </div>

                        <div class="form-group">
                            <label for="inputAddress2">Address 2</label>
                            <input type="text" id="inputAddress2" class="form-control" name="address2"
                                   placeholder="Address 2">
                        </div>

                        <br>

                        <div class="row">
                            <div class="col-lg-6">
                                <button class="btn btn-lg btn-success btn-block" name="submit" type="submit"><span
                                            class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add library branch
                                </button>
                            </div>
                            <div class="col-lg-6">
                                <button class="btn btn-lg btn-danger btn-block"
                                        onclick="location.href='library_branches_list.php'"><span
                                            class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cancel
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </main>
    </div>
</div>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="../libraries/jquery-3.3.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<script src="../libraries/bootstrap-4/js/bootstrap.js"></script>
<script src="js/breadcrumb.js" charset="utf-8"></script>
<script src="js/add_library_branch.js" charset="utf-8"></script>
<script src="../node_modules/bootstrap-select/dist/js/bootstrap-select.min.js" charset="utf-8"></script>

</body>
</html>
