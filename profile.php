<?php include 'config/config.php'; ?>
<?php include 'connections/Database.php'; ?>
<?php include 'helpers/format_helper.php'; ?>

<?php
if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['u_id'])) {
    header("Location: error.php?login=false");
    exit();
}

// Create DB object
$db = new Database;


$signup_status = null;

$user_id = $_SESSION['u_id'];

$first_name = $last_name = $email = $line_1 = $line_2 = $role = $gender = $postal_code = $postal_address = $country = false;


// Get user
$query = "SELECT user_id, user_name, first_name, last_name, email
              FROM user
              WHERE user_id = '$user_id'";
// Run query
$users_res = $db->select($query);
if ($users_res !== false) {
    $row = $users_res->fetch_assoc();
    $user_name = $row['user_name'];
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $email = $row['email'];
}

// Get address
$query = "SELECT a.line_1, a.line_2, @address_id := a.address_id
              FROM address a
              INNER JOIN user u on a.address_id = u.address_id
              WHERE u.user_id = '$user_id'";
$address_res = $db->select($query);
if ($address_res !== false) {
    $row = $address_res->fetch_assoc();
    $line_1 = $row['line_1'];
    $line_2 = $row['line_2'];
}


// Get role
$query = "SELECT r.role 
              FROM role r
              INNER JOIN role_assignment ra on r.role_id = ra.role_role_id
              WHERE ra.user_user_id = $user_id";
$role_res = $db->select($query);
if ($role_res !== false) {
    $row = $role_res->fetch_assoc();
    $role = $row['role'];
}

// Get gender
$query = "SELECT g.gender 
              FROM gender g
              INNER JOIN user u on g.gender_id = u.gender_id
              WHERE u.user_id = $user_id";
$gender_res = $db->select($query);
if ($gender_res !== false) {
    $row = $gender_res->fetch_assoc();
    $gender = $row['gender'];
}

// Get country
$query = "SELECT pc.postal_code, pa.postal_address
              FROM postal_code pc
              INNER JOIN postal_address pa on pc.postal_address_id = pa.postal_address_id
              INNER JOIN address a on pc.postal_code_id = a.postal_code_id              
              WHERE a.address_id = @address_id";
$postal_res = $db->select($query);
if ($postal_res !== false) {
    $row = $postal_res->fetch_assoc();
    $postal_code = $row['postal_code'];
    $postal_address = $row['postal_address'];
}

// Get country, region and city
$query = "SELECT c.city, r.region, c2.country
            FROM city c
            INNER JOIN region r on c.region_id = r.region_id
            INNER JOIN country c2 on r.country_id = c2.country_id
            INNER JOIN address a on c.city_id = a.city_id
            WHERE a.address_id = @address_id";
$country_res = $db->select($query);
if ($country_res !== false) {
    $row = $country_res->fetch_assoc();
    $country = $row['country'];
    $region = $row['region'];
    $city = $row['city'];
}


// Get phone
$query = "SELECT p.phone, pt.description
              FROM phone p
              INNER JOIN phone_type pt on p.phone_type = pt.phone_type
              WHERE p.user_id = '$user_id'";
$phone_res = $db->select($query);

$phones = [];
if ($phone_res !== false) {
    while ($row = $phone_res->fetch_assoc()) {
        //array_push($data, array($data, $row['user_name']));
        $phones[] = $row;
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
    <link rel="icon" href="../favicon.ico">

    <title>View user</title>

    <!-- Bootstrap core CSS -->
    <link href="libraries/bootstrap-4/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/common.css" rel="stylesheet">
    <link rel="stylesheet" href="css/common.css">


</head>

<body>

<?php include 'includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

            <div id="breadcrumb"></div>

            <?php if ($user_name && $first_name && $last_name) : ?>
                <h1 class="page-header">
                    <?php echo $first_name . " " . $last_name; ?>
                </h1>
                <h1 class="h2">
                    <small><?php echo $user_name; ?></small>
                </h1>
                <br>
            <?php endif; ?>


                <div class="row">

                    <div class="col-lg-6">

                        <div class="form-group">
                            <label for="inputUsername">Username</label>
                            <?php if ($user_name) : ?>
                                <input type="text" id="inputUsername" class="form-control" name="uid"
                                       placeholder="Username"
                                       value="<?php echo $user_name; ?>" readonly>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="inputFirstname">First name</label>

                                    <?php if ($first_name) : ?>
                                        <input type="text" id="inputFirstname" class="form-control" name="first"
                                               placeholder="First name" value="<?php echo $first_name; ?>" readonly>
                                    <?php endif; ?>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                    <label for="inputLastname">Last name</label>
                                    <?php if ($last_name) : ?>
                                        <input type="text" id="inputLastname" class="form-control" name="last"
                                               placeholder="Last name" value="<?php echo $last_name; ?>" readonly>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail">Email address</label>
                            <?php if ($email) : ?>
                                <input type="email" id="inputEmail" class="form-control" name="email"
                                       placeholder="Email address" value="<?php echo $email; ?>" readonly>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="selectRole">Role</label>
                                    <?php if ($role) : ?>
                                        <input type="text" id="selectRole" class="form-control" name="role"
                                               placeholder="Role" value="<?php echo $role; ?>" readonly>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="inputCountry">Country</label>
                                    <?php if ($country) : ?>
                                        <input type="email" id="inputCountry" class="form-control" name="country"
                                               placeholder="Country" value="<?php echo $country; ?>" readonly>
                                    <?php endif; ?>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                    <label for="inputRegion">Region</label>
                                    <?php if ($region) : ?>

                                        <input type="text" id="inputRegion" class="form-control" name="region"
                                               placeholder="Region" value="<?php echo $region; ?>" readonly>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="inputCountry">City</label>
                                    <?php if ($city) : ?>
                                        <input type="email" id="inputCity" class="form-control" name="city"
                                               placeholder="City" value="<?php echo $city; ?>" readonly>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="inputPostal">Postal address</label>
                                    <?php if ($postal_address) : ?>
                                        <input type="text" id="inputPostal" class="form-control" name="postalRegion"
                                               placeholder="Postal address" value="<?php echo $postal_address; ?>"
                                               readonly>
                                    <?php endif; ?>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                    <label for="inputPostalcode">Postal code</label>
                                    <?php if ($postal_code) : ?>
                                        <input type="text" id="inputPostalcode" class="form-control" name="postalCode"
                                               placeholder="Postal code" value="<?php echo $postal_code; ?>" readonly>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputAddress1">Address 1</label>
                            <?php if ($line_1) : ?>
                                <input type="text" id="inputAddress1" class="form-control" name="address1"
                                       placeholder="Address 1" value="<?php echo $line_1; ?>" readonly>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="inputAddress2">Address 2</label>
                            <?php if ($line_2) : ?>
                                <input type="text" id="inputAddress2" class="form-control" name="address2"
                                       placeholder="Address 2" value="<?php echo $line_2; ?>" readonly>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="selectGender">Gender</label>
                                    <?php if ($gender) : ?>
                                        <input type="text" id="selectGender" class="form-control" name="gender"
                                               placeholder="Gender" value="<?php echo $gender; ?>" readonly>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <?php if ($phones) : ?>
                                    <?php $i = 1 ?>
                                    <?php foreach ($phones as $row) : ?>
                                        <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                            <label for="inputPhone<?php echo $i; ?>">Phone <?php echo $i; ?></label>
                                            <div class="input-group">
                                                <input type="text" id="inputPhone<?php echo $i; ?>" class="form-control"
                                                       aria-label="..."
                                                       placeholder="Phone" name="phone"
                                                       value="<?php echo $row['phone']; ?>" readonly>


                                                <input type="text" id="selectGender" class="form-control" name="gender"
                                                       placeholder="Gender" value="<?php echo $row['description']; ?>"
                                                       readonly>

                                                </select>
                                            </div><!-- /input-group -->
                                        </div>
                                        <?php $i++; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                            </div>
                        </div>

                    </div>
                    <div class="col-lg-6">
                        <div id="profileImg">
                            <?php if (file_exists('uploads/profile_pictures/' . $user_id . '.jpg')) : ?>
                                <img id="lol1" src="uploads/profile_pictures/<?php echo $user_id; ?>.jpg" alt=""
                                     width="100%">
                            <?php else : ?>
                                <img id="lol2" src="images/noavatar.png" alt="" width="100%">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
        </main>
    </div>
</div>
<?php include 'includes/footer.php'; ?>

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
