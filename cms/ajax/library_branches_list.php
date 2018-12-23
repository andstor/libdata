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
    if (isset($_POST['search'])) {
        $search_word = $_POST['search'];
        $search .= " WHERE MATCH(lb.name) AGAINST('" . $search_word . "')";
    }

    $db = new Database;

        $query = "SELECT lb.library_branch_id AS id, lb.name, c.city, r.region, c2.country
                    FROM library_branch AS lb
                    INNER JOIN address a on lb.address_id = a.address_id
                    INNER JOIN city c on a.city_id = c.city_id
                    INNER JOIN region r on c.region_id = r.region_id
                    INNER JOIN country c2 on r.country_id = c2.country_id
                    $search
                    ORDER BY lb.library_branch_id DESC
                    $limit";

// Run query
    $lib_branch_res = $db->select($query);

    $data = array();
    $lib_branch = array();
    $i = 0;

    if ($lib_branch_res !== false) {
        while ($row = $lib_branch_res->fetch_assoc()) {
            //array_push($data, array($data, $row['user_name']));
            $lib_branch[] = $row;
        }
    }
    $data["library_branches"] = $lib_branch;


    $output = json_encode($data);
    header('Content-Type: application/json; charset=UTF-8');

    echo $output;
    return;

} else { // If only count.
    $search = "";

    if (isset($_POST['search'])) {
        $search_word = $_POST['search'];
        $search .= " WHERE MATCH(lb.name) AGAINST('" . $search_word . "')";
    }


    $db = new Database;

    $query = "SELECT COUNT(DISTINCT lb.library_branch_id)
                    FROM library_branch AS lb
                    $search
                    ORDER BY lb.name ASC
                    ";

    // Run query
    $count = $db->select($query)->fetch_row();
    echo $count[0];
    return;

}

?>
