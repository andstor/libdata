<?php
include '../../config/config.php';
include '../../connections/Database.php';

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


if (isset($_POST['delete_id'])) {

    $delete_id = $_POST['delete_id'];

    try {
        $db->begin_transaction();

        // Get user address_id if not null
        $query = "SELECT address_id
                  FROM library_branch
                  WHERE library_branch_id = '$delete_id'";
        $lib_res = $db->select($query);
        $row = $lib_res->fetch_assoc();
        $address_id = $row['address_id'];

        // Delete role(s)
        $query = "DELETE
                  FROM library_branch_staff_assignment
                  WHERE library_branch_library_branch_id = '$delete_id'";
        // Run query
        $is_lbsa_deleted = $db->delete($query);

        // Delete phone(s)
        $query = "DELETE
                  FROM library_branch
                  WHERE library_branch_id = '$delete_id'";
        // Run query
        $is_lib_deleted = $db->delete($query);

        // If user had address_id
        if ($address_id != false) {
            $query = "DELETE
                      FROM address
                      WHERE address_id = '$address_id'
                          AND address_id NOT IN
                              (
                                SELECT u.address_id
                                FROM user AS u
                                WHERE u.address_id IS NOT NULL
                                UNION
                                SELECT u2.address_id
                                FROM library_branch u2
                                WHERE u2.address_id IS NOT NULL
                              )";
            $is_address_deleted = $db->delete($query);
        }


        $db->commit();

    } catch (PDOException $ex) {
        $db->rollBack();
        print_r("rolled back");
    }

    echo $is_lib_deleted;
    exit();
}