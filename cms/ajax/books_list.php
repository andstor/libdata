<?php include '../../config/config.php'; ?>
<?php include '../../connections/Database.php'; ?>

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
    if ($_POST['genre'] == "All") {
        $where = "";
    } else {
        $genre = $_POST['genre'];
        $where = " WHERE b3.book_id IN (
                      SELECT b2.book_id
                      FROM book_details AS bd
                        INNER JOIN book b2 on bd.isbn = b2.isbn
                        INNER JOIN book_genre_assignment AS ga ON b2.isbn = ga.book_details_isbn
                        INNER JOIN book_genre AS bg ON ga.book_genre_genre_id = bg.genre_id
                      WHERE bg.name = '" . $genre . "'
                    )";
    }

    $search = "";
    $search_author = "";
    if (isset($_POST['search'])) {
        if (empty($where)) {
            $search = $search_author = " WHERE";
        } else {
            $search = $search_author = " AND";
        }
        $search_param = $_POST['search'];
        $search .= " MATCH(bd3.title) AGAINST('" . $search_param . "')";
        $search_author .= " MATCH(bd.title) AGAINST('" . $search_param . "')";
    }


    // Create queryuser_id, user_name, password, first_name, last_name, email, address_id, gender_id, create_time, time_modified
    // Create DB object
    $db = new Database;
    $query = "SELECT DISTINCT b.book_id AS id, bd.isbn, bd.title, l.language, p.publisher, lb.name AS library, l2.waiting_list_id
              FROM (SELECT b3.*
                    FROM book b3
                    INNER JOIN book_details bd3 ON b3.isbn = bd3.isbn
                    $where $search
                    ORDER BY b3.book_id DESC
                    $limit) AS b
              LEFT JOIN book_details AS bd ON b.isbn = bd.isbn
              LEFT JOIN book_language AS l ON bd.language_id = l.language_id
              LEFT JOIN publisher AS p ON bd.publisher_id = p.publisher_id
              INNER JOIN library_branch AS lb ON b.library_branch_id = lb.library_branch_id
              LEFT JOIN waiting_list l2 ON bd.isbn = l2.isbn AND l2.library_branch_id = lb.library_branch_id
              WHERE bd.isbn = b.isbn
              ORDER BY b.book_id DESC";




// Run query
    $book = $db->select($query);

    $query = "SELECT b1.book_id, a.first_name, a.last_name
                FROM (SELECT b3.* 
                      FROM book AS b3
                      INNER JOIN book_details bd ON b3.isbn = bd.isbn
                      $where $search_author
                      ORDER BY b3.book_id DESC 
                      $limit) AS b1
                INNER JOIN author_list AS al ON al.book_details_isbn = b1.isbn
                INNER JOIN author AS a ON al.author_author_id = a.author_id
                ";
// Run query
    $author = $db->select($query);


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


    $authors = array();

    if ($author !== false) {
        while ($row = $author->fetch_assoc()) {
            //array_push($data, array($data, $row['user_name']));
            $authors[] = $row;

        }
        $data["authors"] = $authors;
    }


    $output = json_encode($data);
    header('Content-Type: application/json; charset=UTF-8');

    echo $output;
    return;

} elseif (isset($_POST['genre'])) { // If only count.
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
    $query = "SELECT COUNT(DISTINCT b.book_id)
                FROM book AS b
                LEFT JOIN book_details AS bd ON b.isbn = bd.isbn
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
