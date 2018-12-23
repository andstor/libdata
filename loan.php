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



$isbn = $_GET['isbn'];
$title = $publisher_id = $language_id = $language = $price = $library = null;


// Get number of book
$query = "SELECT count(book_id) as num_books
          FROM book
          WHERE isbn = '$isbn'";
// Run query
$book_res = $db->select($query);
if ($book_res !== false) {
    $row = $book_res->fetch_assoc();
    $num_books = $row['num_books'];
}


// Get all book_details
$query = "SELECT *
          FROM book_details
          WHERE isbn = '$isbn'";
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
}

// Get first available book_id for rental.
$query = "SELECT DISTINCT lb.name
          FROM book AS b
          INNER JOIN library_branch AS lb on b.library_branch_id = lb.library_branch_id
          WHERE isbn = '$isbn' 
          AND b.book_id NOT IN (
              SELECT bl.book_id
              FROM book_loan AS bl
              LEFT JOIN book_return r on bl.book_loan_id = r.book_loan_id
              INNER JOIN book b2 on bl.book_id = b2.book_id
              WHERE r.book_return_id IS NULL AND b2.isbn = '$isbn'
          )";
// Run query

$libraryBranch = $db->select($query);
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
    <link href="node_modules/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="libraries/bootstrap-4/css/bootstrap.css" rel="stylesheet">

    <title>LibData</title>


<body>
<!-- NAVBAR
================================================== -->
<?php include 'includes/navbar.php'; ?>


<main role="main">
    <section class="jumbotron m-0 ">
        <div class="container">
            <form action="includes/loan_book.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="isbn" value="<?php echo $isbn; ?>">
            <div class="col-lg-11 offset-lg-1">

                <h1 class="jumbotron-heading">Complete loan of</h1>
                <h1 class="jumbotron-heading">"<?php echo $title; ?>"</h1>

                    <div class="row">
                        <div class="col-lg-4">
                            <br>
                            <label class="lead " for="selectLibraryBranch">Select a library branch</label>
                            <?php if ($libraryBranch) : ?>
                                <select class="selectpicker form-control" id="selectLibraryBranch"
                                        name="library"
                                        data-live-search="true" aria-describedby="libraryBranchHelp">
                                    <?php while ($row = $libraryBranch->fetch_assoc()) : ?>
                                        <option><?php echo $row['name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            <?php else : ?>
                                <p>No libraryBranchs are defined.</p>
                            <?php endif; ?>

                        </div><!-- /.col-lg-6 -->
                    </div>

                <br>

                <p class="lead text-muted">The book needs to be returned within 3 three weeks (<?php echo date('Y-m-d', strtotime("+3 weeks")); ?>).
                    <br>
                    Please contact us for an extended loaning period. Happy reading!</p>
                <div class="row">
                <div class="col-lg-3"><button class="btn btn-outline-primary btn-block" name="submit" type="submit">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Complete
                    </button>
                    </div>
                <div class="col-lg-3"><button class="btn btn-outline-secondary btn-block" type="button"
                                              onclick="location.href='books.php'">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Cancel
                    </button></div>
                </div>
            </div>
            </form>
        </div>
    </section>
    <!-- FOOTER -->
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
    <link rel="icon" href="../favicon.ico">

    <title>Index</title>

    <!-- Bootstrap core CSS -->
    <link href="libraries/bootstrap-4/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/index.css" rel="stylesheet">
    <link href="css/common.css" rel="stylesheet">
    <script src="node_modules/bootstrap-select/dist/js/bootstrap-select.min.js" charset="utf-8"></script>

</head>

</html>
