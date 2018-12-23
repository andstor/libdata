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


// Get genres
$query = "SELECT DISTINCT bg.name
              FROM book_genre AS bg
              ORDER BY bg.name ASC";
$genres = $db->select($query);

// Get genders
$query = "SELECT DISTINCT l.language
              FROM book_language AS l
              ORDER BY l.language ASC";
$language = $db->select($query);

// Get library branch
$query = "SELECT DISTINCT l.name
              FROM library_branch AS l
              ORDER BY l.name ASC";
$libraryBranch = $db->select($query);

// Get authors
$query = "SELECT DISTINCT a.first_name, a.last_name, a.author_id
              FROM author AS a
              ORDER BY a.first_name ASC";
$authors = $db->select($query);

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

    <title>Add book</title>

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
            if ($status === "bookcreated") {
                echo '<div class="alert alert-success" role="alert">Successfully created book.</div>';
            }
            ?>

            <h1 class="page-header">New book
                <small> - add a new book</small>
            </h1>

            <form action="includes/add_book.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-6">

                        <div class="form-group">
                            <label for="inputTitle">Title*</label>
                            <input type="text" id="inputTitle" class="form-control" name="title"
                                   placeholder="Title" required autofocus>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                    <label for="inputISBN">ISBN*</label>
                                    <input id="inputISBN" pattern="^[0-9]{13}$" class="form-control" name="isbn"
                                           aria-describedby="isbnHelp" placeholder="XXXXXXXXXXXXX" required>
                                    <small id="isbnHelp" class="form-text text-muted">ISBN consists of 13 numbers.
                                    </small>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                    <label for="selectLibraryBranch">Library branch*</label>
                                    <?php if ($libraryBranch) : ?>
                                        <select class="selectpicker form-control" id="selectLibraryBranch"
                                                name="libraryBranch"
                                                data-live-search="true" aria-describedby="libraryBranchHelp">
                                            <?php while ($row = $libraryBranch->fetch_assoc()) : ?>
                                                <option><?php echo $row['name']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    <?php else : ?>
                                        <p>No libraryBranchs are defined.</p>
                                    <?php endif; ?>

                                    <small id="libraryBranchHelp" class="form-text text-muted">The library branch where
                                        this book is location.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="additionalFields">
                            <div class="form-group">

                                <div class="row">
                                    <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                        <label for="inputYear">Year</label>
                                        <input type="number" id="inputYear" class="form-control" name="year"
                                               aria-describedby="yearHelp" placeholder="Year">
                                        <small id="yearHelp" class="form-text text-muted">The publication year of the
                                            book.
                                        </small>
                                    </div>

                                    <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                        <label for="inputEdition">Edition</label>
                                        <input type="number" id="inputEdition" class="form-control" name="edition"
                                               aria-describedby="editionHelp" placeholder="Edition">
                                        <small id="editionHelp" class="form-text text-muted">The book edition.</small>

                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                        <label for="inputPrice">Price</label>
                                        <input type="number" id="inputPrice" class="form-control" name="price"
                                               aria-describedby="priceHelp" placeholder="Price">
                                        <small id="priceHelp" class="form-text text-muted">The price of the book.
                                        </small>
                                    </div>

                                    <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                        <label for="inputPages">Pages</label>
                                        <input type="number" id="inputPages" class="form-control"
                                               name="pages" aria-describedby="pagesHelp"
                                               placeholder="Pages">
                                        <small id="pagesHelp" class="form-text text-muted">The number of pages.
                                        </small>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="selectGenre">Book genre</label>
                                <?php if ($genres) : ?>
                                    <select class="selectpicker form-control" id="selectGenre" name="genres[]"
                                            data-live-search="true" aria-describedby="genreHelp" multiple
                                            data-selected-text-format="count > 5" multiple>
                                        <?php while ($row = $genres->fetch_assoc()) : ?>
                                            <option><?php echo $row['name']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                <?php else : ?>
                                    <p>No genres are defined.</p>
                                <?php endif; ?>


                                <small id="genreHelp" class="form-text text-muted">Chose one or more categories.
                                </small>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-xs-6 col-sm-6 col-md-6 nopadding-right">
                                        <label for="inputPublisher">Publisher</label>
                                        <input type="text" id="inputPublisher" class="form-control"
                                               name="publisher"
                                               placeholder="Publisher">
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-md-6 nopadding-left">
                                        <label for="inputLanguage">Book language</label>
                                        <?php if ($language) : ?>
                                            <select class="selectpicker form-control" id="inputLanguage"
                                                    name="language"
                                                    data-live-search="true" aria-describedby="languageHelp">
                                                <option value="">Nothing selected</option>
                                                <?php while ($row = $language->fetch_assoc()) : ?>
                                                    <option><?php echo $row['language']; ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        <?php else : ?>
                                            <p>No languages are defined.</p>
                                        <?php endif; ?>

                                        <small id="languageHelp" class="form-text text-muted">This book's language.
                                        </small>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="additionalFields">
                            <div id="bookImg">
                                <img id="lol" src="../images/book-cover-placeholder.jpg" alt="" width="100%">
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
                            <hr>

                            <div class="form-group">
                                <label for="selectAuthors">Author(s)</label>
                                <select class="selectpicker form-control" id="selectAuthors" name="exAuthorsId[]"
                                        data-live-search="true" multiple data-selected-text-format="count > 3">
                                    <?php if ($authors) : ?>
                                        <?php foreach ($authors as $row) : ?>
                                            <option value="<?php echo $row['author_id']; ?>"><?php echo $row['first_name'] . $row['last_name']; ?></option>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <option disabled="true">No phone types</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div id="authors-container">

                            </div>
                            <div class="form-group">
                                <button type="button" onclick="addAuthorField()" class="btn btn-outline-secondary">
                                    +1 author
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-lg-3">
                        <button class="btn btn-lg btn-success btn-block" name="submit" type="submit"><span
                                    class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add book
                        </button>
                    </div>
                    <div class="col-lg-3">
                        <button class="btn btn-lg btn-danger btn-block" type="button"
                                onclick="location.href='books_list.php'"><span
                                    class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cancel
                        </button>
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
<script src="js/add_books.js" charset="utf-8"></script>
<script src="../node_modules/bootstrap-select/dist/js/bootstrap-select.min.js" charset="utf-8"></script>

</body>
</html>
