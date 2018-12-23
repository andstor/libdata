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

<main role="main" class="">

    <div class="container">

        <div class="d-flex align-items-center p-3 my-3 text-white-50 bg-blue rounded box-shadow">
            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none"
                 stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="feather feather-database mr-2">
                <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
            </svg>
            <div class="lh-100">
                <h6 class="mb-0 text-white lh-100">LibData database</h6>
            </div>
        </div>

        <div class="my-3 p-3 bg-white rounded box-shadow">
            <h6 class="border-bottom border-gray pb-2 mb-0">DB schema</h6>
            <div class="media text-muted pt-3">
                <img data-src="" class="mr-2 rounded" src="images/db.svg" data-holder-rendered="true" width="100%">
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>

</main>

<!-- /END THE FEATURETTES -->


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
    <link href="css/offcanvas.css" rel="stylesheet">
    <link href="css/common.css" rel="stylesheet">
</head>

</html>
