<?php include '../config/config.php'; ?>
<?php include '../connections/Database.php'; ?>

<?php
if (!isset($_SESSION)) session_start();

// Create DB object
$db = new Database;

// Make the script run only if there is a page number posted to this script
if (isset($_POST['pn'])) {
    $limit = "";
    if ($_POST['pn'] != 'All') {

        $rpp = preg_replace('#[^0-9]#', '', $_POST['rpp']);
        $last = preg_replace('#[^0-9]#', '', $_POST['last']);
        $pn = preg_replace('#[^0-9]#', '', $_POST['pn']);
        // This makes sure the page number isn't below 1, or more than our $last page
        if ($pn < 1) {
            $pn = 1;
        } elseif ($pn > $last) {
            $pn = $last;
        }
        // This sets the range of rows to query for the chosen $pn
        $limit = 'LIMIT ' . ($pn - 1) * $rpp . ',' . $rpp;
    }

    $where = "";
    if ($_POST['genre'] != "All") {

        $genre = $_POST['genre'];
        $where = " WHERE bd2.isbn IN (
                      SELECT DISTINCT bd.isbn
                      FROM book_details AS bd
                      INNER JOIN book_genre_assignment AS ga ON bd.isbn = ga.book_details_isbn
                      INNER JOIN book_genre AS bg ON ga.book_genre_genre_id = bg.genre_id
                      WHERE bg.name = '" . $genre . "'
                    )";
    }


    $where_count = "";
    if ($_POST['library'] != "All") {
        if (empty($where)) {
            $where .= " WHERE";
        } else {
            $where .= " AND";
        }
        $library_branch = $_POST['library'];
        $where .= " bd2.isbn IN (
                      SELECT DISTINCT b.isbn
                      FROM library_branch AS lb
                        INNER JOIN book b on lb.library_branch_id = b.library_branch_id
                      WHERE lb.name = '" . $library_branch . "'
                    )";

        $where_count = " AND b2.library_branch_id IN (
                      SELECT DISTINCT lb.library_branch_id
                      FROM library_branch AS lb
                       INNER JOIN book b on lb.library_branch_id = b.library_branch_id
                      WHERE lb.name = '" . $library_branch . "'
                      )";
    }


    $search = "";
    if (isset($_POST['search'])) {
        if (empty($where)) {
            $where .= " WHERE";
        } else {
            $where .= " AND";
        }
        $search_param = $_POST['search'];
        $where .= " MATCH(bd2.title) AGAINST('" . $search_param . "')";
    }


    // Create queryuser_id, user_name, password, first_name, last_name, email, address_id, gender_id, create_time, time_modified
    // Create DB object
    $db = new Database;
    $query = "SELECT DISTINCT
                  bd.isbn,
                  bd.title,
                  l.language,
                  p.publisher,
                  (SELECT count(DISTINCT b2.book_id)
                   FROM book AS b2
                   WHERE b2.isbn = bd.isbn
                   $where_count 
                   AND b2.book_id NOT IN (
                      SELECT bl.book_id
                      FROM book_loan AS bl
                      LEFT JOIN book_return r on bl.book_loan_id = r.book_loan_id
                      INNER JOIN book b3 on bl.book_id = b3.book_id
                      WHERE r.book_return_id IS NULL
                      )
                  ) AS num_books
                FROM (SELECT DISTINCT bd2.*
                      FROM book_details AS bd2
                      $where
                      ORDER BY bd2.isbn ASC
                      $limit) AS bd
                  LEFT JOIN book_language AS l ON bd.language_id = l.language_id
                  LEFT JOIN publisher AS p ON bd.publisher_id = p.publisher_id
                ORDER BY bd.isbn ASC";


// Run query
    $book = $db->select($query);

    $data = array();
    $books = array();
    $i = 0;

    if ($book !== false) {
        while ($row = $book->fetch_assoc()) {
            //array_push($data, array($data, $row['user_name']));
            $books[] = $row;
        }
        $data["rows"] = $i + 1;
        $data["books"] = $books;
    }



    $output = json_encode($data);
    header('Content-Type: application/json; charset=UTF-8');

    echo $output;
    return;

} elseif (isset($_POST['genre']) && isset($_POST['library'])) { // If only count.
    $where = "";
    if ($_POST['genre'] == "All") {
        $where = "";
    } else {
        $genre = $_POST['genre'];
        $where = " WHERE b.book_id IN (
                     SELECT b2.book_id
                     FROM book_details AS bd
                       INNER JOIN book b2 on bd.isbn = b2.isbn
                       INNER JOIN book_genre_assignment AS ga ON b2.isbn = ga.book_details_isbn
                       INNER JOIN book_genre AS bg ON ga.book_genre_genre_id = bg.genre_id
                     WHERE bg.name = '" . $genre . "'
                   )";
    }

    if ($_POST['library'] == "All") {
        $where .= "";
    } else {
        if (empty($where)) {
            $where = " WHERE";
        } else {
            $where .= " AND";
        }
        $library_branch = $_POST['library'];
        $where .= " lb.name = '" . $library_branch . "'";
    }


    if (isset($_POST['search'])) {
        if (empty($where)) {
            $where = " WHERE";
        } else {
            $where .= " AND";
        }
        $search = $_POST['search'];
        $where .= " MATCH(bd.title) AGAINST('" . $search . "')";
    }

    //Create query for counting
    // TODO switch to inner join
    $query = "SELECT COUNT(DISTINCT bd.isbn)
                FROM book AS b
                LEFT JOIN book_details AS bd ON b.isbn = bd.isbn
                LEFT JOIN library_branch AS lb ON b.library_branch_id = lb.library_branch_id
                $where";
    // Run query
    $count = $db->select($query)->fetch_row();
    echo $count[0];
    return;

} else {
    echo "No pn POSTED";
    die();
}
?>
