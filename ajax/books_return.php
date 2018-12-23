<?php
include '../config/config.php';
include '../connections/Database.php';

if (!isset($_SESSION)) session_start();

if (isset($_SESSION['u_id'])) {
} else {
    header("Location: ../error.php?login=false");
}

// Create DB object
$db = new Database;

if (isset($_POST['loan_id'])) {

    $loan_id = $_POST['loan_id'];
    $is_book_returned = false;
    try {
        $db->begin_transaction();

            // Delete book
            $query = "INSERT INTO book_return (book_loan_id)
                      VALUES ('$loan_id')";
            // Run query
            $is_book_returned = $db->insert($query);

        $db->commit();

    } catch (PDOException $ex) {
        $db->rollBack();
        print_r("rolled back");
    }

    echo $is_book_deleted;
    exit();
}