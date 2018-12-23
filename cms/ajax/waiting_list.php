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
        $where = " WHERE bd2.isbn IN (
                      SELECT bd3.isbn
                      FROM book_details AS bd3
                        INNER JOIN book_genre_assignment AS ga ON bd3.isbn = ga.book_details_isbn
                        INNER JOIN book_genre AS bg ON ga.book_genre_genre_id = bg.genre_id
                      WHERE bg.name = '" . $genre . "'
                    )";
    }

    $search = "";
    $search_author = "";
    if (isset($_POST['search'])) {
        if (empty($where)) {
            $search = " WHERE";
        } else {
            $search = " AND";
        }
        $search_param = $_POST['search'];
        $search .= " MATCH(bd2.title) AGAINST('" . $search_param . "')";
    }


    // Create queryuser_id, user_name, password, first_name, last_name, email, address_id, gender_id, create_time, time_modified
    // Create DB object
    $db = new Database;
    $query = "SELECT
                  wl.waiting_list_id,
                  bd.isbn,
                  bd.title,
                  (SELECT count(DISTINCT u.user_id)
                    FROM waiting_list wl2
                    INNER JOIN waiting_list_line wll on wl2.waiting_list_id = wll.waiting_list_waiting_list_id
                    INNER JOIN user u on wll.user_user_id = u.user_id
                    WHERE wl2.waiting_list_id = wl.waiting_list_id
                  ) AS num_users,
                  lb.name
                FROM
                  (SELECT DISTINCT bd2.*
                   FROM book_details AS bd2
                   $where $search
                  ) AS bd
                  INNER JOIN waiting_list wl ON bd.isbn = wl.isbn
                  INNER JOIN library_branch lb on wl.library_branch_id = lb.library_branch_id
                  ORDER BY bd.isbn ASC
                  $limit";

    $waiting_list_res = $db->select($query);


    $data = array();
    $waiting_list = array();
    $i = 0;

    if ($waiting_list_res !== false) {
        while ($row = $waiting_list_res->fetch_assoc()) {
            $waiting_list[] = $row;
        }
        $data["rows"] = $i + 1;
        $data["waiting_list"] = $waiting_list;
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
        $where = " WHERE bd.isbn IN (
                      SELECT bd2.isbn
                      FROM book_details AS bd2
                        INNER JOIN book_genre_assignment AS ga ON bd2.isbn = ga.book_details_isbn
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
    $query = "SELECT COUNT(DISTINCT bd.isbn)
                FROM waiting_list AS wl
                LEFT JOIN book_details AS bd ON wl.isbn = bd.isbn
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
