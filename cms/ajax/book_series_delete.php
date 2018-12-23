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

        // Get book
        $query = "SELECT book_series_id
                  FROM book_series
                  WHERE book_series_id = '$delete_id'";
        $book_res = $db->select($query);
        $row = $book_res->fetch_assoc();
        $book_series_id = $row['book_series_id'];


        // Delete book_genre_assignments
        $query = "DELETE
                  FROM book_series_assignment
                  WHERE book_series_book_series_id = '$book_series_id'";
        $is_book_series_assignment_deleted = $db->delete($query);

        // Delete book
        $query = "DELETE
                  FROM book_series
                  WHERE book_series_id = '$book_series_id'";
        // Run query
        $is_book_series_deleted = $db->delete($query);

        $db->commit();

    } catch (PDOException $ex) {
        $db->rollBack();
        print_r("rolled back");
    }

    echo $is_book_series_deleted;
    exit();
}