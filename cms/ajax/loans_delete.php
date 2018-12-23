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

        // Delete book return
        $query = "DELETE
                  FROM book_return
                  WHERE book_loan_id = '$delete_id'";
        // Run query
        $is_return_deleted = $db->delete($query);

        // Delete book return
        $query = "DELETE
                      FROM book_loan
                      WHERE book_loan_id = '$delete_id'";
        // Run query
        $is_loan_deleted = $db->delete($query);

        $db->commit();

    } catch (PDOException $ex) {
        $db->rollBack();
        print_r("rolled back");
    }

    echo $is_loan_deleted;
    exit();
}