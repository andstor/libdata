<?php include 'config/config.php'; ?>
<?php include 'connections/Database.php'; ?>
<?php include 'helpers/format_helper.php'; ?>

<?php
if (!isset($_SESSION)) session_start();


// Create DB object
$db = new Database;


$isbn = $_GET['isbn'];
$title = $publisher_id = $publisher= $language_id = $language = $price = $num_books_left = $library = null;


$query = "SELECT count(DISTINCT b2.book_id) AS num_books
                   FROM book AS b2
                   WHERE b2.isbn = '$isbn'
                   AND b2.book_id NOT IN (
                      SELECT bl.book_id
                      FROM book_loan AS bl
                      LEFT JOIN book_return r on bl.book_loan_id = r.book_loan_id
                      INNER JOIN book b3 on bl.book_id = b3.book_id
                      WHERE r.book_return_id IS NULL
                      )";
// Run query
$boks_left_res = $db->select($query);
if ($boks_left_res !== false) {
    $row = $boks_left_res->fetch_assoc();
    $num_books_left = $row['num_books'];
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


// Get publisher
$query = "SELECT p.publisher
          FROM publisher AS p
          WHERE p.publisher_id = '$publisher_id'
          ORDER BY p.publisher ASC";
// Run query
$publisher_res = $db->select($query);
if ($publisher_res !== false) {
    $row = $publisher_res->fetch_assoc();
    $publisher = $row['publisher'];
}

// Get language
$query = "SELECT l.language
          FROM book_language AS l
          WHERE l.language_id = '$language_id'
          ORDER BY l.language ASC";
// Run query
$language_res = $db->select($query);
if ($language_res !== false) {
    $row = $language_res->fetch_assoc();
    $language = $row['language'];
}


// Get book genres
$query = "SELECT bg.name
          FROM book_genre_assignment bga
          INNER JOIN book_genre bg on bga.book_genre_genre_id = bg.genre_id  
          WHERE bga.book_details_isbn = '$isbn'
          ORDER BY bg.name ASC";
// Run query
$genres_res = $db->select($query);

$genres = [];
if ($genres_res !== false) {
    while ($row = $genres_res->fetch_assoc()) {
        //array_push($data, array($data, $row['user_name']));
        $genres[] = $row;
    }
}

// Get library branch
$query = "SELECT l.name
              FROM library_branch AS l
              INNER JOIN book b on l.library_branch_id = b.library_branch_id
              WHERE b.isbn = '$isbn'
              GROUP BY l.library_branch_id
              ORDER BY l.name ASC";
$library_branch_res = $db->select($query);
$libraries = [];
if ($library_branch_res !== false) {
    while ($row = $library_branch_res->fetch_assoc()) {
        //array_push($data, array($data, $row['user_name']));
        $libraries[] = $row;
    }
}


// Get authors
$query = "SELECT a.first_name, a.last_name
              FROM author AS a
              INNER JOIN author_list a2 on a.author_id = a2.author_author_id
              WHERE a2.book_details_isbn = '$isbn'
              ORDER BY a.first_name ASC";
$authors_res = $db->select($query);

$authors = [];
if ($authors_res !== false) {
    while ($row = $authors_res->fetch_assoc()) {
        $authors[] = $row;
    }
}


$series_name = null;
$book_series_id = null;
// Get language
$query = "SELECT bs.book_series_id, bs.series_name
          FROM book_series AS bs
          INNER JOIN book_series_assignment a on bs.book_series_id = a.book_series_book_series_id
          WHERE  a.book_details_isbn = '$isbn'
          ORDER BY bs.book_series_id ASC";
// Run query
$book_series_id_res = $db->select($query);
if ($book_series_id_res !== false) {
    $row = $book_series_id_res->fetch_assoc();
    $book_series_id = $row['book_series_id'];
    $series_name = $row['series_name'];
}

if ($book_series_id != null) {
// Get series assignment
    $query = "SELECT bs.book_series_id, bs.series_name, a.book_details_isbn, b.isbn, b.title, p.publisher
              FROM book_series AS bs
              INNER JOIN book_series_assignment a on bs.book_series_id = a.book_series_book_series_id
              INNER JOIN book_details b on a.book_details_isbn = b.isbn
              INNER JOIN publisher p on b.publisher_id = p.publisher_id
              WHERE bs.book_series_id = '$book_series_id'
              ORDER BY b.year ASC";
    $series_res = $db->select($query);
    $series = [];
    if ($series_res !== false) {
        while ($row = $series_res->fetch_assoc()) {
            //array_push($data, array($data, $row['user_name']));
            $series[] = $row;
        }
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

    <title>View book</title>

    <!-- Bootstrap core CSS -->
    <link href="libraries/bootstrap-4/css/bootstrap.css" rel="stylesheet">

    <link href="node_modules/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/common.css" rel="stylesheet">
    <link rel="stylesheet" href="css/common.css">


</head>

<body>

<?php include 'includes/navbar.php'; ?>

<div class="container">
    <div class="row">

        <main role="main" class="col-md-9 ml-sm-auto col-lg-12 px-4">

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

            <form action="includes/add_book.php" method="POST" enctype="multipart/form-data">
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
                                    <?php if ($libraries) : ?>
                                        <ul>
                                            <?php foreach ($libraries as $row) : ?>
                                                <li><?php echo $row['name']; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>

                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="form-group">

                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="inputYear">Year</label>
                                    <?php if ($year) : ?>
                                        <input type="text" id="inputTitle" class="form-control"
                                               name="inputYear"
                                               placeholder="Year"
                                               value="<?php echo $year; ?>" readonly>
                                    <?php endif; ?>
                                </div>

                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                    <label for="inputEdition">Edition</label>
                                    <?php if ($edition) : ?>
                                        <input type="text" id="inputEdition" class="form-control"
                                               name="inputEdition"
                                               placeholder="Edition"
                                               value="<?php echo $edition; ?>" readonly>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="inputPrice">Price</label>
                                    <?php if ($price) : ?>
                                        <input id="inputPrice" class="form-control"
                                               name="inputPrice"
                                               placeholder="Price"
                                               value="<?php echo $price; ?>" readonly>
                                    <?php endif; ?>
                                </div>

                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                    <label for="inputPages">Pages</label>
                                    <?php if ($pages) : ?>
                                        <input id="inputPages" class="form-control"
                                               name="inputPages"
                                               placeholder="Pages"
                                               value="<?php echo $pages; ?>" readonly>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">

                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="inputPublisher">Publisher</label>
                                    <?php if ($publisher) : ?>
                                        <input type="text" id="inputPublisher" class="form-control"
                                               name="inputPublisher"
                                               placeholder="Publisher"
                                               value="<?php echo $publisher; ?>" readonly>
                                    <?php endif; ?>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                    <label for="inputLanguage">Book language</label>
                                    <?php if ($language) : ?>
                                        <input type="text" id="inputLanguage" class="form-control"
                                               name="inputLanguage"
                                               placeholder="Language"
                                               value="<?php echo $language; ?>" readonly>
                                    <?php endif; ?>

                                    <small id="languageHelp" class="form-text text-muted">This book's language.
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="selectGenre">Book genre(s)</label>
                            <div class="row">
                                <?php if ($genres) : ?>
                                    <?php $i = 1; ?>

                                    <?php foreach ($genres as $row) : ?>
                                        <?php if ($i == 1 || $i == ceil((count($genres) / 2) + 1)) : ?>
                                            <div class="col-xs-6 col-sm-6 col-md-6 nopadding">
                                            <ul>
                                        <?php endif; ?>

                                        <li><?php echo $row['name']; ?></li>
                                        <?php $i++; ?>

                                        <?php if ($i == count($genres) + 1 || $i == ceil((count($genres) / 2) + 1)) : ?>
                                            </ul></div>
                                        <?php endif; ?>

                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="selectAuthors">Author(s)</label>

                            <div class="row">
                                <?php if ($authors) : ?>
                                    <?php $i = 1; ?>

                                    <?php foreach ($authors as $row) : ?>
                                        <?php if ($i == 1 || $i == ceil((count($authors) / 2) + 1)) : ?>
                                            <div class="col-xs-6 col-sm-6 col-md-6 nopadding">
                                            <ul>
                                        <?php endif; ?>

                                        <li><?php echo $row['first_name'] . " " . $row['last_name']; ?></li>
                                        <?php $i++; ?>

                                        <?php if ($i == count($authors) + 1 || $i == ceil((count($authors) / 2) + 1)) : ?>
                                            </ul></div>
                                        <?php endif; ?>

                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <?php if (file_exists('uploads/cover_pictures/' . $isbn . '.jpg')) : ?>
                            <img id="bookImg" src="uploads/cover_pictures/<?php echo $isbn; ?>.jpg" alt="">
                        <?php else : ?>
                            <img id="bookImg" src="images/book-cover-placeholder.jpg" alt="">
                        <?php endif; ?>
                        <hr>
                        <div class="form-group">

                            <div class="row">
                                <div class="col-lg-5 offset-lg-1">

                                    <?php if ($num_books_left > 0): ?>
                                        <button class="btn btn-outline-success btn-block" name="submit" type="button"
                                                onclick="location.href='loan.php?isbn=<?php echo $isbn; ?>'"><span
                                                    class="glyphicon glyphicon-plus" aria-hidden="true"></span> Loan
                                            book
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-outline-success btn-block" name="submit" type="button"
                                                onclick="location.href='waiting_list.php?isbn=<?php echo $isbn; ?>'"><span
                                                    class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                            Subscribe to waiting list
                                        </button>
                                    <?php endif; ?>

                                </div>
                                <div class="col-lg-5">
                                    <button class="btn btn-outline-secondary btn-block" type="button"
                                            onclick="location.href='contact_us.php'"><span
                                                class="glyphicon glyphicon-remove" aria-hidden="true"></span> Contact us
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (!empty($series)) : ?>
                    <hr>
                    <br>
                    <h3>Books in same series - <?php echo $series_name; ?></h3>
                    <br>
                    <div class="row">
                        <?php foreach ($series as $row) : ?>

                            <div class="col-md-2">
                                <div class="card mb-3 box-shadow">
                                    <img id="book_cover' + book + '" class="card-img-top"
                                         src="uploads/cover_pictures/<?php echo $row['isbn']; ?>.jpg"
                                         alt="Card image cap" onerror="replaceMissingImages(this);"
                                         onclick="location.href='view_book_details.php?isbn=<?php echo $row['isbn']; ?>'"
                                         style="cursor: pointer">
                                    <div class="card-body">
                                        <p class="card-text"><?php echo $row['title']; ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </form>
        </main>
    </div>
</div>
<hr>
<?php include 'includes/footer.php'; ?>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="libraries/jquery-3.3.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<script src="libraries/bootstrap-4/js/bootstrap.js"></script>
<script src="js/breadcrumb.js" charset="utf-8"></script>
<!--<script src="js/add_books.js" charset="utf-8"></script>-->
<script src="node_modules/bootstrap-select/dist/js/bootstrap-select.min.js" charset="utf-8"></script>

</body>
</html>
