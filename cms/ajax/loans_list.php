<?php include '../../config/config.php'; ?>
<?php include '../../connections/Database.php'; ?>
<?php include '../../helpers/format_helper.php'; ?>

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
    if (!empty($_POST['search'])) {
        $search_param = $_POST['search'];
        $search .= " MATCH(bd.title) AGAINST('" . $search_param . "')";
    }


    $db = new Database;
    if ($_POST['active_loans'] == 'true') {
        if (!empty($_POST['search'])) $search = " AND" . $search;

        $query = "SELECT bl.book_loan_id AS loan_id,
                         bd.isbn,
                         bd.title,
                         u.user_name AS full_name,
                         bl.loan_date,
                         (SELECT DATEDIFF(bl.due_date, CURRENT_TIMESTAMP)) AS days_left,
                         r.return_date
                    FROM book_loan bl
                    INNER JOIN book b on bl.book_id = b.book_id
                    INNER JOIN book_details bd on b.isbn = bd.isbn
                    INNER JOIN user u on bl.user_id = u.user_id
                    LEFT JOIN book_return r on bl.book_loan_id = r.book_loan_id
                    WHERE r.book_loan_id IS NULL
                    $search
                    ORDER BY bl.loan_date DESC 
                    $limit";
    } else {
        if (isset($_POST['search'])) $search = " WHERE" . $search;

        $query = "SELECT bl.book_loan_id AS loan_id,
                         bd.isbn,
                         bd.title,
                         u.user_name AS full_name,
                         bl.loan_date, due_date,
                         (CASE
                            WHEN r.return_date IS NULL THEN (SELECT DATEDIFF(bl.due_date, CURRENT_TIMESTAMP)) 
                            ELSE NULL  
                         END) AS days_left,
                         r.return_date
                    FROM book_loan bl
                    INNER JOIN book b on bl.book_id = b.book_id
                    INNER JOIN book_details bd on b.isbn = bd.isbn
                    INNER JOIN user u on bl.user_id = u.user_id
                    LEFT JOIN book_return r on bl.book_loan_id = r.book_loan_id
                    $search
                    ORDER BY bl.loan_date DESC
                    $limit";
    }

// Run query
    $loans_res = $db->select($query);

    $data = array();
    $loans = array();
    $i = 0;

    if ($loans_res !== false) {
        while ($row = $loans_res->fetch_assoc()) {
            //array_push($data, array($data, $row['user_name']));
            $loans[] = $row;
        }
    }
    $data["rows"] = $i + 1;
    $data["loans"] = $loans;

    foreach ($data["loans"] AS $key => $value) {
        if ($value["loan_date"] == '0000-00-00 00:00:00' || $value["loan_date"] == null) {
            $data["loans"][$key]["loan_date"] = "";
        } else {
            $data["loans"][$key]["loan_date"] = formatDate($value["loan_date"]);
        }

        if ($value["return_date"] == '0000-00-00 00:00:00' || $value["return_date"] == null) {
            $data["loans"][$key]["return_date"] = "";
        } else {
            $data["loans"][$key]["return_date"] = formatDate($value["return_date"]);
        }
    }

    $output = json_encode($data);
    header('Content-Type: application/json; charset=UTF-8');

    echo $output;
    return;

} elseif (isset($_POST['active_loans'])) { // If only count.
    $where = "";
    $search = "";
    if (!empty($_POST['search'])) {
        $search_param = $_POST['search'];
        $search .= " MATCH(bd.title) AGAINST('" . $search_param . "')";
    }


    $db = new Database;

    if ($_POST['active_loans'] == 'true') {
        if (!empty($_POST['search'])) $search = " AND" . $search;

        $query = "SELECT COUNT(DISTINCT bl.book_loan_id)
                    FROM book_loan bl
                    INNER JOIN book b on bl.book_id = b.book_id
                    INNER JOIN book_details bd on b.isbn = bd.isbn
                    INNER JOIN user u on bl.user_id = u.user_id
                    LEFT JOIN book_return r on bl.book_loan_id = r.book_loan_id
                    WHERE r.book_loan_id IS NULL
                    $search
                    ORDER BY bl.loan_date ASC
                    ";
    } else {
        if (isset($_POST['search'])) $search = " WHERE" . $search;

        $query = "SELECT COUNT(DISTINCT bl.book_loan_id)
                    FROM book_loan bl
                    INNER JOIN book b on bl.book_id = b.book_id
                    INNER JOIN user u on bl.user_id = u.user_id
                    INNER JOIN book_details bd on b.isbn = bd.isbn
                    LEFT JOIN book_return r on bl.book_loan_id = r.book_loan_id
                    $search
                    ORDER BY bl.loan_date ASC
                    ";
    }

    // Run query
    $count = $db->select($query)->fetch_row();
    echo $count[0];
    return;

} else {
    echo "No pn POSTED";
    die();
}
?>
