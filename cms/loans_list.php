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

// Create query
$query = "SELECT DISTINCT g.name
              FROM book_genre AS g
              INNER JOIN book_genre_assignment AS ga ON g.genre_id = ga.book_genre_genre_id
              ORDER BY g.name ASC";
// Run query
$genres = $db->select($query);


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

    <title>Books</title>

    <!-- Bootstrap core CSS -->
    <link href="../libraries/bootstrap-4/css/bootstrap.css" rel="stylesheet">
    <link href="../node_modules/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/common.css" rel="stylesheet">
    <!--    <link rel="stylesheet" href="css/costum.css">-->


</head>

<body>
<?php include 'includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

            <div id="breadcrumb"></div>


            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Loans
                    <small> - list of book loans</small>
                </h1>
            </div>


            <div class="row">

                <div class="col-lg-3 offset-lg-5">
                    <form id="active_loansForm" class="form-group">
                        <select id="active-loans" class="selectpicker form-control" name="active-loans"
                                onchange="filterData()">
                            <option selected value="false">All loans</option>
                            <option value="true">Only active loans</option>
                        </select>
                    </form>
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

            </div><!-- /.row -->

            <hr>

            <?php
            if ($genres === false) {
                echo '<div class="alert alert-info" role="alert"><b>Obs!</b> Her finnes ingen brukerdata enda.</div>';
            }
            ?>

            <div id="booksTableArea" class="table-responsive">
                <table id="booksTable" class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>L-ID</th>
                        <th>ISBN</th>
                        <th>Title</th>
                        <th>User name</th>
                        <th>Loan date</th>
                        <th>Days left</th>
                        <th>Date returned</th>
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
<script src="js/loans_list.js" charset="utf-8"></script>
<script src="../node_modules/bootstrap-select/dist/js/bootstrap-select.min.js" charset="utf-8"></script>

</body>
</html>
