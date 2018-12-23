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

    $search = "";
    if (isset($_POST['search'])) {
        $search_param = $_POST['search'];
        $search .= " WHERE MATCH(bs2.series_name) AGAINST('" . $search_param . "')";
    }


    // Create queryuser_id, user_name, password, first_name, last_name, email, address_id, gender_id, create_time, time_modified
    // Create DB object
    $db = new Database;
    $query = "SELECT DISTINCT
                  bs.book_series_id AS id,
                  bs.series_name,
                  (SELECT COUNT(*)
                   FROM book_series bs5
                     LEFT JOIN  book_series_assignment a5 on bs5.book_series_id = a5.book_series_book_series_id
                   WHERE a5.book_series_book_series_id = bsa.book_series_book_series_id )AS books_in_series
                
                FROM (SELECT bs2.*
                      FROM book_series AS bs2
                        LEFT JOIN book_series_assignment a2 on bs2.book_series_id = a2.book_series_book_series_id
                        $search
                      ORDER BY a2.book_details_isbn DESC
                     ) AS bs
                  LEFT JOIN book_series_assignment bsa on bs.book_series_id = bsa.book_series_book_series_id
                  ORDER BY bs.book_series_id DESC
                  $limit";

    $book_series_res = $db->select($query);


    $data = array();
    $book_series = array();
    $i = 0;

    if ($book_series_res !== false) {
        while ($row = $book_series_res->fetch_assoc()) {
            $book_series[] = $row;
            $i +=1;
        }
        $data["rows"] = $i + 1;
        $data["book_series"] = $book_series;
    }


    $output = json_encode($data);
    header('Content-Type: application/json; charset=UTF-8');

    echo $output;
    return;

} else { // If only count.

    $search="";
    if (isset($_POST['search'])) {
        $search_param = $_POST['search'];
        $search .= " WHERE MATCH(bs.series_name) AGAINST('" . $search_param . "')";
    }

    //Create query for counting
    // TODO switch to inner join
    $query = "SELECT COUNT(DISTINCT bs.book_series_id)
                FROM book_series AS bs
                $search";
    // Run query
    $count = $db->select($query)->fetch_row();
    echo $count[0];
    return;

}
?>
