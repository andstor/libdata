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

$book_series_id = $_GET['series_id'];
$name = null;


// Get all book_details
$query = "SELECT bs.series_name
          FROM book_series bs
          WHERE bs.book_series_id = '$book_series_id'";
// Run query
$series_res = $db->select($query);
if ($series_res !== false) {
    $row = $series_res->fetch_assoc();
    $name = $row['series_name'];
}


// Get library branch
$query = "SELECT bd.isbn, bd.title, p.publisher, l.language
          FROM book_series_assignment AS bsa
          INNER JOIN book_details bd on bsa.book_details_isbn = bd.isbn
          INNER JOIN publisher p on bd.publisher_id = p.publisher_id
          INNER JOIN book_language l on bd.language_id = l.language_id
          WHERE bsa.book_series_book_series_id = '$book_series_id'
          ORDER BY bd.isbn ASC";
$series_assignment_res = $db->select($query);

/*
if ($book_res !== false) {
    $row = $book_res->fetch_assoc();
    $publisher_id = $row['publisher_id'];
    $language_id = $row['language_id'];
    $title = $row['title'];
    $isbn = $row['isbn'];
}*/

$series_assignment = [];
if ($series_assignment_res !== false) {
    while ($row = $series_assignment_res->fetch_assoc()) {
        $series_assignment[] = $row;
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

            <?php if ($name) : ?>
                <h1 class="page-header">
                    <?php echo $name; ?>
                </h1>
                <br>
            <?php endif; ?>

            <div class="row">

                <div class="col-lg-6">

                    <div class="form-group">
                        <label for="inputName">Book series name</label>
                        <?php if ($name) : ?>
                            <input type="text" id="inputTitle" class="form-control"
                                   name="inputName"
                                   placeholder="Name"
                                   value="<?php echo $name; ?>" readonly>
                        <?php endif; ?>
                    </div>

                    <hr>
                </div>
                <div class="col-lg-12">

                    <div class="form-group">

                        <label for="inputUser">Books</label>
                        <div class="row">

                            <?php if ($series_assignment) : ?>
                                <?php foreach ($series_assignment as $row) : ?>
                                    <hr>
                                    <div class="col-xs-2 col-sm-2 col-md-2 nopadding-right">
                                        <input type="text" id="inputTitle" class="form-control"
                                               name="inputYear"
                                               placeholder="User"
                                               value="<?php echo $row['isbn']; ?>"
                                               readonly>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3 nopadding-right">
                                        <input type="text" id="inputTitle" class="form-control"
                                               name="inputYear"
                                               placeholder="User"
                                               value="<?php echo $row['title']; ?>"
                                               readonly>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3 nopadding-right">
                                        <input type="text" id="inputTitle" class="form-control"
                                               name="inputYear"
                                               placeholder="User"
                                               value="<?php echo $row['publisher']; ?>"
                                               readonly>
                                    </div>
                                    <div class="col-xs-2 col-sm-2 col-md-2 nopadding-right">
                                        <input type="text" id="inputTitle" class="form-control"
                                               name="inputYear"
                                               placeholder="User"
                                               value="<?php echo $row['language']; ?>"
                                               readonly>
                                    </div>
                                    <div class="col-xs-2 col-sm-2 col-md-2 nopadding-left">
                                        <button type="button" class="btn btn-success"
                                                onclick="location.href='view_book_details.php?isbn=<?php echo $row['isbn']; ?>'">
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
