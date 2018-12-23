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

$waiting_list_id = $_GET['list_id'];
$isbn = $title = $publisher_id = $language_id = $price = $library = null;


// Get all book_details
$query = "SELECT *
          FROM book_details
          INNER JOIN waiting_list l on book_details.isbn = l.isbn
          WHERE l.waiting_list_id = '$waiting_list_id'";
// Run query
$book_res = $db->select($query);
if ($book_res !== false) {
    $row = $book_res->fetch_assoc();
    $publisher_id = $row['publisher_id'];
    $language_id = $row['language_id'];
    $title = $row['title'];
    $edition = $row['edition'];
    $year = $row['year'];
    $pages = $row['pages'];
    $price = $row['price'];
    $isbn = $row['isbn'];
}


// Get library branch
$query = "SELECT l.name
          FROM library_branch AS l
          INNER JOIN waiting_list l2 on l.library_branch_id = l2.library_branch_id
          WHERE l2.waiting_list_id = '$waiting_list_id'
          ORDER BY l.name ASC";
// Run query
$library_res = $db->select($query);
if ($library_res !== false) {
    $row = $library_res->fetch_assoc();
    $library = $row['name'];
}


// Get users
$query = "SELECT u.first_name, u.last_name, u.user_id
              FROM user AS u
              INNER JOIN waiting_list_line wll on u.user_id = wll.user_user_id
              WHERE wll.waiting_list_waiting_list_id = '$waiting_list_id'
              ORDER BY u.first_name ASC";
$users_res = $db->select($query);

$users = [];
if ($users_res !== false) {
    while ($row = $users_res->fetch_assoc()) {
        $users[] = $row;
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

    <title>View book</title>

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

            <?php if ($title && $isbn) : ?>
                <h1 class="page-header">
                    <?php echo $title; ?>
                </h1>
                <h1 class="h2">
                    <small><?php echo $isbn; ?></small>
                </h1>
                <br>
            <?php endif; ?>

                <div class="row">

                    <div class="col-lg-6">

                        <div class="form-group">
                            <label for="inputTitle">Title*</label>
                            <?php if ($title) : ?>
                                <input type="text" id="inputTitle" class="form-control"
                                       name="inputTitle"
                                       placeholder="Title"
                                       value="<?php echo $title; ?>" readonly>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">

                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="inputISBN">ISBN*</label>
                                    <?php if ($isbn) : ?>
                                        <input type="text" id="inputISBN" class="form-control"
                                               name="inputISBN"
                                               placeholder="ISBN"
                                               value="<?php echo $isbn; ?>" readonly>
                                    <?php endif; ?>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                    <label for="selectLibraryBranch">Library branch</label>
                                    <?php if ($library) : ?>
                                        <input type="text" id="selectLibraryBranch" class="form-control"
                                               name="libraryBranch"
                                               placeholder="Library branch"
                                               value="<?php echo $library; ?>" readonly>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="form-group">

                            <label for="inputUser">Users</label>
                            <div class="row">

                            <?php if ($users) : ?>
                                <?php foreach ($users as $row) : ?>
                                        <div class="col-xs-10 col-sm-10 col-md-10 nopadding-right">
                                            <input type="text" id="inputTitle" class="form-control"
                                                   name="inputYear"
                                                   placeholder="User"
                                                   value="<?php echo $row['first_name'] . " " . $row['last_name']; ?>"
                                                   readonly>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 nopadding-left">
                                            <button type="button" class="btn btn-success"
                                                    onclick="location.href='view_user.php?user_id=<?php echo $row['user_id']; ?>'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                     class="feather feather-eye">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>
                                        </div>
                                    <br><br>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div id="bookImg">
                            <img src="../uploads/cover_pictures/<?php echo $isbn; ?>.jpg"
                                 onerror="replaceMissingImages(this);" alt="" width="100%">
                            <br>
                            <br>
                        </div>
                    </div>
                </div>
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
<script src="js/common.js"></script>
<!--<script src="js/add_books.js" charset="utf-8"></script>-->
<script src="../node_modules/bootstrap-select/dist/js/bootstrap-select.min.js" charset="utf-8"></script>

</body>
</html>
