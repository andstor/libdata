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
    if ($_POST['role'] == "All") {
        $where = "";
    } else {
        $role = $_POST['role'];
        $where = " WHERE r.role = '" . $role . "'";
    }

    if (isset($_POST['search'])) {
        if (empty($where)) {
            $where = " WHERE";
        } else {
            $where .= " AND";
        }
        $search = $_POST['search'];
        $where .= " MATCH(u.user_name, u.first_name, u.last_name, u.email) AGAINST('" . $search . "')";
    }


    // TODO switch to inner join
    // Create queryuser_id, user_name, password, first_name, last_name, email, address_id, gender_id, create_time, time_modified
    $query = "SELECT u.user_id, u.user_name, u.first_name, u.last_name, u.email, u.create_time, u.time_modified, r.role
                FROM user AS u
                LEFT JOIN role_assignment AS ra ON u.user_id = ra.user_user_id
                LEFT JOIN role AS r ON ra.role_role_id = r.role_id
                $where
                ORDER BY u.user_id DESC
                $limit";
    // Run query
    $user = $db->select($query);

    /*
    // Create query
    $query = "SELECT * FROM role";
    // Run query
    $role = $db->select($query);
    */


    $data = array();
    $users = array();
    $i = 0;

    if ($user !== false) {
        while ($row = $user->fetch_assoc()) {
            //array_push($data, array($data, $row['user_name']));
            $users[] = $row;
            $i++;
        }

        $data["rows"] = $i;
        $data["users"] = $users;
    }

    $output = json_encode($data);
    header('Content-Type: application/json; charset=UTF-8');

    echo $output;
    return;

} elseif (isset($_POST['role'])) { // If only count.
    $where = "";
    if ($_POST['role'] == "All") {
        $where = "";
    } else {
        $role = $_POST['role'];
        $where = " WHERE r.role = '" . $role . "'";
    }

    if (isset($_POST['search'])) {

        if (empty($where)) {
            $where = " WHERE";
        } else {
            $where .= " AND";
        }
        $search = $_POST['search'];
        $where .= " MATCH(u.user_name, u.first_name, u.last_name, u.email) AGAINST('" . $search . "')";
    }

    //Create query for counting
    // TODO switch to inner join
    $query = "SELECT COUNT(u.user_id)
                FROM user AS u
                LEFT JOIN role_assignment AS ra ON u.user_id = ra.user_user_id
                LEFT JOIN role AS r ON ra.role_role_id = r.role_id
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
