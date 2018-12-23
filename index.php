<?php include 'config/config.php'; ?>
<?php include 'connections/Database.php'; ?>
<?php include 'helpers/format_helper.php'; ?>

<?php
if (!isset($_SESSION)) session_start();

$db = new Database;


$status = null;
if (isset($_GET['status'])) $status = $_GET['status'];


// Get number of book
$query = "SELECT count(book_id) as num_books
          FROM book";
// Run query
$book_res = $db->select($query);
if ($book_res !== false) {
    $row = $book_res->fetch_assoc();
    $num_books = $row['num_books'];
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

    <title>LibData</title>


<body>
<!-- NAVBAR
================================================== -->
<?php include 'includes/navbar.php'; ?>


<main role="main">

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div id="jumbo-background">
        <div class="jumbotron" id="jumbo-img">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4" id="jumbo-card">
                        <h1 class="display-3">LibData</h1>
                        <p class="h3">Welcome to LibData, the online library. We have <?php if ($num_books != false) echo $num_books; ?>
                            books in stack!</p>

                        <br>
                        <p><a class="btn btn-outline-warning btn-lg" href="books.php" role="button">Loan books »</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Example row of columns -->
        <div class="row">

            <div class="col-md-4"  id="books-img">
                <h2><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layers"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                    Huge book collection</h2>
                <p>We have a massive collection of books available, and new books are constantly added to the collection.
                    Our current stack across all library branches houses a total
                    of <?php if ($num_books != false) echo $num_books; ?> books!</p>
                <p><a class="btn btn-outline-secondary" href="books.php" role="button">Browse collection »</a></p>
            </div>
            <div class="col-md-4" id="call-img">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-users">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Contact us
                </h2>
                <p>If there is anything we can help you with, please feel free to contact us. We don't bite ;)</p>
                <p><a class="btn btn-outline-secondary" href="#" role="button">Contact »</a></p>
            </div>
            <div class="col-md-4" id="db-img">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-database">
                        <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                        <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                        <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                    </svg>
                    Reliable DB
                </h2>
                <p>We utilize one of the most robust, state of the art, database structure to ensure data consistency. Take a look!</p>
                <p><a class="btn btn-outline-secondary" href="lib_db.php" role="button">View DB »</a></p>
            </div>
        </div>
    </div> <!-- /container -->
    <!-- FOOTER -->
    <hr>
    <?php include 'includes/footer.php'; ?>

</main>


<!-- Bootstrap core JavaScript
  ================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="libraries/jquery-3.3.1.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<script src="libraries/bootstrap-4/js/bootstrap.js"></script>
</body>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Index</title>

    <!-- Bootstrap core CSS -->
    <link href="libraries/bootstrap-4/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/index.css" rel="stylesheet">
    <link href="css/common.css" rel="stylesheet">
</head>

</html>
