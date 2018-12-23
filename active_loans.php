<?php include 'config/config.php'; ?>
<?php include 'connections/Database.php'; ?>
<?php include 'helpers/format_helper.php'; ?>

<?php
if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['u_id'])) {
    header("Location: error.php?login=false");
    exit();
}

$db = new Database;


$status = null;
if (isset($_GET['status'])) $status = $_GET['status'];


$books = $genres = null;
// Get genres
$query = "SELECT *
          FROM book AS b
          INNER JOIN book_details detail on b.isbn = detail.isbn
          ORDER BY b.book_id ASC";
$books = $db->select($query);


// Create query
$query = "SELECT DISTINCT g.name
              FROM book_genre AS g
              LEFT JOIN book_genre_assignment AS ga ON g.genre_id = ga.book_genre_genre_id
              ORDER BY g.name ASC";
// Run query
$genres = $db->select($query);

// Create query
$query = "SELECT DISTINCT lb.name
              FROM library_branch AS lb
              ORDER BY lb.name ASC";
// Run query
$libraries = $db->select($query);

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

    <title>Books</title>

    <!-- Bootstrap core CSS -->
    <link href="libraries/bootstrap-4/css/bootstrap.css" rel="stylesheet">
    <link href="node_modules/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/common.css" rel="stylesheet">

</head>

<body>
<?php include 'includes/navbar.php'; ?>


<main role="main">
    <section class="jumbotron text-center m-0 p-4">
        <div class="container">
            <?php
            if ($status === "loansuccess") {
                echo '<div class="alert alert-success" role="alert">Successfully loaned book.</div>';
            }
            ?>

            <h1 class="jumbotron-heading">Active loans</h1>
            <br>
            <div class="row">

                <div class="col-lg-3">
                    <form id="librarySelectForm" class="form-group m-0">
                        <select id="librarySelect" class="selectpicker form-control" data-live-search="true" name="library"
                                onchange="filterData()">
                            <option selected="selected" value="All">All libraries</option>

                            <?php if ($libraries) : ?>
                                <?php while ($row = $libraries->fetch_assoc()) : ?>

                                    <option value="<?php echo $row['name']; ?>">
                                        <?php echo $row['name']; ?></option>

                                <?php endwhile; ?>
                            <?php else : ?>
                                <p>No genres defined yet</p>
                            <?php endif; ?>
                        </select>
                    </form>
                </div>
                <div class="col-lg-3">
                    <form id="genreSelectForm" class="form-group m-0">
                        <select id="genreSelect" class="selectpicker form-control" data-live-search="true" name="genre"
                                onchange="filterData()">
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
                    </form>
                </div><!-- /.col-lg-6 -->


                <div class="col-lg-6">
                    <div class="input-group">
                        <input id="search-input" type="text" class="form-control"
                               placeholder="Search for books...">
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
        </div>
    </section>


    <div class="album py-5 bg-light">
        <div class="container">


            <div class="row" id="gridContainer"></div>
            <div id="booksTableArea" class="table-responsive">
                <table id="booksTable" class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>B-ID</th>
                        <th>ISBN</th>
                        <th>Title</th>
                        <th>Days left</th>
                        <th>Time due</th>
                        <th>Library</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>


                </table>
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
                            <option selected>4</option>
                            <option>8</option>
                            <option>24</option>
                            <option>50</option>
                            <option>100</option>
                            <option>All</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>


</main>

<?php include 'includes/footer.php'; ?>


<!-- Bootstrap core JavaScript
  ================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="libraries/jquery-3.3.1.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<script src="libraries/bootstrap-4/js/bootstrap.js"></script>
<script src="js/active_loans.js"></script>
<script src="node_modules/bootstrap-select/dist/js/bootstrap-select.min.js" charset="utf-8"></script>

</body>
</html>