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

    <title>Add book series</title>

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
            if ($status === "seriescreated") {
                echo '<div class="alert alert-success" role="alert">Successfully created book series.</div>';
            }
            ?>

            <h1 class="page-header">New book series
                <small> - add a new book series</small>
            </h1>

            <form action="includes/add_book_series.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-6">

                        <div class="form-group">
                            <label for="inputName">Series name*</label>
                            <input type="text" id="inputName" class="form-control" name="name"
                                   placeholder="Name" required autofocus>
                        </div>


                    </div>
                </div>

                <hr>
                <div class="col-lg-12">
                    <h3>Add books to series</h3>
                    <div id="books-container"></div>
                </div>
                <br>

                <div class="row">
                    <hr>
                    <div class="col-lg-3 offset-lg-5">
                        <div id="genreSelectForm" class="form-group">
                            <select id="genreSelect" class="selectpicker form-control" data-live-search="true"
                                    name="genre" onchange="filterData()">
                                <option selected="selected" value="All">All genres</option>

                                <?php if ($genres) : ?>
                                    <?php while ($row = $genres->fetch_assoc()) : ?>

                                        <option value="<?php echo $row['name']; ?>">
                                            <?php echo $row['name']; ?></option>

                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <p>No genres defined yet</p>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div><!-- /.col-lg-6 -->


                    <div class="col-lg-4">
                        <div class="input-group">
                            <input id="search-input" type="text" class="form-control"
                                   placeholder="Search by title...">
                            <span class="input-group-btn">
                      <button id="btn-search" class="btn btn-default" type="button" onclick="filterData()"><svg
                                  xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11"
                                                                                                 r="8"></circle><line
                                      x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></button>
                    </span>
                        </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->

                </div>
                <br>
                <div class="row">
                    <div class="col">
                        <div id="booksTableArea" class="table-responsive">
                            <table id="booksTable" class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>ISBN</th>
                                    <th>Title</th>
                                    <th>Language</th>
                                    <th>Publisher</th>
                                    <th class="text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-auto">
                        <nav aria-label="Page navigation">
                            <ul id="pagination_controls" class="pagination">

                            </ul>
                        </nav>
                    </div>


                    <div class="col-lg-2 ">
                        <div class="form-group">
                            <select class="form-control" id="limitRecords">
                                <option selected>5</option>
                                <option>10</option>
                                <option>25</option>
                                <option>50</option>
                                <option>100</option>
                                <option>All</option>
                            </select>
                        </div>
                    </div>
                </div>
                <br><br>
                <div class="row">
                    <div class="col-lg-3">
                        <button id="submitBtn" class="btn btn-lg btn-success btn-block" name="submit" type="submit"><span
                                    class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add book series
                        </button>
                    </div>
                    <div class="col-lg-3">
                        <button class="btn btn-lg btn-danger btn-block" type="button"
                                onclick="location.href='book_series_list.php'"><span
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
<script src="js/add_book_series.js" charset="utf-8"></script>
<script src="../node_modules/bootstrap-select/dist/js/bootstrap-select.min.js" charset="utf-8"></script>

</body>
</html>
