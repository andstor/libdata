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
if (isset($_GET['signup'])) $status = $_GET['signup'];

// Create query
$query = "SELECT r.role
              FROM role AS r
              ORDER BY r.role_id DESC";
$roles = $db->select($query);


// Get countries
$query = "SELECT c.country
              FROM country AS c
              ORDER BY c.country ASC";
$countries = $db->select($query);

// Get genders
$query = "SELECT g.gender
              FROM gender AS g
              ORDER BY g.gender ASC";
$genders = $db->select($query);


// Get phoned
$query = "SELECT pt.description
              FROM phone_type AS pt
              ORDER BY pt.description ASC";
$phone_type_res = $db->select($query);

$phone_types = [];
if ($phone_type_res !== false) {
    while ($row = $phone_type_res->fetch_assoc()) {
        //array_push($data, array($data, $row['user_name']));
        $phone_types[] = $row;
    }
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

            <h1 class="page-header">New user
                <small> - add a new user</small>
            </h1>


            <?php
            if ($status === "usertaken") {
                echo '<div class="alert alert-danger" role="alert"><b>Error:</b> that username is already taken.</div>';
            } elseif ($status === "usercreated") {
                echo '<div class="alert alert-success" role="alert">User successfully created.</div>';
            }
            ?>

            <form action="includes/signup.inc.php" method="POST" enctype="multipart/form-data">
                <div class="row">

                    <div class="col-lg-6">

                        <div class="form-group">
                            <label for="inputUsername">Username*</label>
                            <input type="text" id="inputUsername" class="form-control" name="uid"
                                   aria-describedby="usernameHelp" placeholder="Username" required autofocus>
                            <small id="usernameHelp" class="form-text text-muted">Username can contain any letters or
                                numbers, without spaces.
                            </small>
                        </div>


                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="inputFirstname">First name*</label>
                                    <input type="text" id="inputFirstname" class="form-control" name="first"
                                           placeholder="First name" required>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                    <label for="inputLastname">Last name*</label>
                                    <input type="text" id="inputLastname" class="form-control" name="last"
                                           placeholder="Last name" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail">Email address*</label>
                            <input type="email" id="inputEmail" class="form-control" name="email"
                                   aria-describedby="emailHelp" placeholder="Email address" required>
                            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone
                                else.
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail">Confirm email address*</label>
                            <input type="email" id="inputEmail" class="form-control" name="emailConf"
                                   aria-describedby="emailHelp" placeholder="Confirm email address" required>
                            <small id="emailHelp" class="form-text text-muted">Please confirm email.</small>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="inputPassword">Password*</label>
                                    <input type="password" id="inputPassword" name="pwd" class="form-control"
                                           aria-describedby="passwordHelp" placeholder="Password" required>
                                    <small id="passwordHelp" class="form-text text-muted">Password should contain at
                                        least 6 characters.
                                    </small>

                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                    <label for="inputPasswordConfirm">Confirm password*</label>
                                    <input type="password" id="inputPasswordConfirm" name="pwdConf" class="form-control"
                                           aria-describedby="passwordConfHelp" placeholder="Confirm password" required>
                                    <small id="passwordConfHelp" class="form-text text-muted">Please confirm password.
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="selectRole">Role*</label>
                                    <select class="selectpicker form-control" id="selectRole" name="role">
                                        <?php if ($roles) : ?>
                                        <?php while ($row = $roles->fetch_assoc()) : ?>
                                            <option><?php echo $row['role']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                    <small id="roleConfHelp" class="form-text text-muted">Please select a user role.
                                    </small>
                                    <?php else : ?>
                                        <p>No genders yet</p>
                                    <?php endif; ?>
                                </div>
                            </div>
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
                            <label for="inputAddress1">Address 1</label>
                            <input type="text" id="inputAddress1" class="form-control" name="address1"
                                   placeholder="Address 1">
                        </div>

                        <div class="form-group">
                            <label for="inputAddress2">Address 2</label>
                            <input type="text" id="inputAddress2" class="form-control" name="address2"
                                   placeholder="Address 2">
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="selectGender">Gender</label>
                                    <select class="selectpicker form-control" id="selectGender" name="gender">
                                        <option value="">Gender</option>
                                        <?php if ($genders) : ?>
                                        <?php while ($row = $genders->fetch_assoc()) : ?>
                                            <option><?php echo $row['gender']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                    <!--                            <p>No genders yet</p>-->
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="inputPhone1">Phone 1</label>
                                    <div class="input-group">
                                        <input type="text" id="inputPhone1" class="form-control" aria-label="..."
                                               placeholder="Phone 1" name="phone1">
                                        <select class="selectpicker form-control" id="phone1Type" name="phone1Type">
                                            <option value="">Type</option>
                                            <?php if ($phone_types) : ?>
                                                <?php foreach ($phone_types as $row) : ?>
                                                    <option><?php echo $row['description']; ?></option>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <option disabled="true">No phone types</option>
                                            <?php endif; ?>
                                        </select>
                                    </div><!-- /input-group -->
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                    <label for="inputPhone2">Phone 2</label>

                                    <div class="input-group">
                                        <input type="text" class="form-control" aria-label="..." placeholder="Phone 2"
                                               name="phone2">
                                        <select class="selectpicker form-control" id="phone2Type" name="phone2Type">
                                            <option value="">Type</option>
                                            <?php if ($phone_types) : ?>
                                                <?php foreach ($phone_types as $row) : ?>
                                                    <option><?php echo $row['description']; ?></option>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <option disabled="true">No phone types</option>
                                            <?php endif; ?>
                                        </select>
                                    </div><!-- /input-group -->
                                </div>
                            </div>
                        </div>


                        <br>

                        <div class="row">
                            <div class="col-lg-6">
                                <button class="btn btn-lg btn-success btn-block" name="submit" type="submit"><span
                                            class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add user
                                </button>
                            </div>
                            <div class="col-lg-6">
                                <button class="btn btn-lg btn-danger btn-block"
                                        onclick="location.href='users_list.php'"><span
                                            class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cancel
                                </button>
                            </div>
                        </div>
                        <br>

                    </div>
                    <div class="col-lg-6">
                        <div id="profileImg">
                            <img id="lol" src="../images/noavatar.png" alt="" width="100%">
                            <br><br>
                            <div class="input-group">
                                <label class="input-group-btn">
                                    <span class="btn btn-primary">
                                        Browse &hellip;
                                        <input id="upload" type="file" style="display: none;" name="image"
                                               accept="image/*">
                                    </span>
                                </label>
                                <input id="fileName" type="text" class="form-control" readonly>
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
<script src="js/add_user.js" charset="utf-8"></script>
<script src="../node_modules/bootstrap-select/dist/js/bootstrap-select.min.js" charset="utf-8"></script>

</body>
</html>
