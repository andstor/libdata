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
        $query = "SELECT isbn
                  FROM book
                  WHERE book_id = '$delete_id'";
        $book_res = $db->select($query);
        $row = $book_res->fetch_assoc();
        $isbn = $row['isbn'];


        // Get book
        $query = "SELECT COUNT(b.isbn) AS number_isbn
                  FROM book AS b
                  WHERE b.isbn = '$isbn'
                ";
        $num_book_res = $db->select($query);
        $row = $num_book_res->fetch_assoc();
        $num_books = $row['number_isbn'];


        if ($num_books <= 1) {
////////////////////////////////////////////////////////////////////

            // Get publisher_id
            $sql = "SELECT bd.publisher_id
                FROM book_details AS bd
                WHERE bd.isbn = '$isbn'";
            // Run query
            $publisher_res = $db->select($sql);
            $row = $book_res->fetch_assoc();
            $publisher_id = $row['publisher_id'];


            // Get authors_id
            $sql = "SELECT al.author_author_id
                FROM author_list AS al
                WHERE al.author_author_id NOT IN (
                  SELECT DISTINCT author_author_id
                  FROM book_details AS bd
                    INNER JOIN author_list a3 on bd.isbn = a3.book_details_isbn
                  WHERE bd.isbn IS NOT NULL
                        AND bd.isbn <> '$isbn'
                )";
            // Run query
            $authors_res = $db->select($sql);

            $authors = array();
            if ($authors_res !== false) {
                while ($row = $authors_res->fetch_assoc()) {
                    $authors[] = $row['author_author_id'];
                }
            }

            $author_ids = implode("','",$authors);

////////////////////////////////////////////////////////////////////

            // Delete book_genre_assignments
            $query = "DELETE
                      FROM book_genre_assignment
                      WHERE book_details_isbn IN ('$isbn')";
            $is_genre_assignment_deleted = $db->delete($query);

            // Delete author_list entries
            $query = "DELETE
                      FROM author_list
                      WHERE book_details_isbn = '$isbn'";
            $is_author_list_deleted = $db->delete($query);

            // Delete author(s)
            $query = "DELETE
                      FROM author
                      WHERE author_id IN ('$author_ids')";
            // Run query
            $is_authors_deleted = $db->delete($query);

            // Delete book_loan(s)
            $query = "DELETE
                      FROM book_series_assignment
                      WHERE book_details_isbn = '$isbn'";
            // Run query
            $is_bool_series_assignment_deleted = $db->delete($query);


            // Delete book_loan(s)
            $query = "DELETE
                      FROM waiting_list_line
                      WHERE waiting_list_waiting_list_id IN (SELECT wl.waiting_list_id FROM waiting_list wl WHERE wl.isbn = '$isbn')";
            // Run query
            $is_waiting_list_line_deleted = $db->delete($query);

            // Delete book_loan(s)
            $query = "DELETE
                      FROM waiting_list
                      WHERE isbn = '$isbn'";
            // Run query
            $is_waiting_list_deleted = $db->delete($query);

            // Delete book_loan(s)
            $query = "DELETE
                      FROM book_return
                      WHERE book_loan_id IN (SELECT bl.book_loan_id FROM book_loan bl WHERE bl.book_id = '$delete_id')";
            // Run query
            $is_book_return_deleted = $db->delete($query);

            // Delete book_loan(s)
            $query = "DELETE
                      FROM book_loan
                      WHERE book_id = '$delete_id'";
            // Run query
            $is_book_loan_deleted = $db->delete($query);

            // Delete book
            $query = "DELETE
                      FROM book
                      WHERE book_id = '$delete_id'";
            // Run query
            $is_book_deleted = $db->delete($query);


            // Delete book details.
            if ($isbn != false) {
                $query = "DELETE
                          FROM book_details
                          WHERE isbn = '$isbn'";
                $is_book_details_deleted = $db->delete($query);
            }


            // Delete publisher
            $query = "DELETE
                      FROM p
                      USING publisher AS p
                      WHERE p.publisher_id = '$publisher_id'
                        AND p.publisher_id NOT IN (
                           SELECT bd.publisher_id
                           FROM book_details AS bd
                           WHERE bd.isbn IS NOT NULL
                           AND bd.isbn <> '$isbn'
                           )";
            // Run query
            $is_user_deleted = $db->delete($query);


        } else {
            // Delete book
            $query = "DELETE
                      FROM book
                      WHERE book_id = '$delete_id'";
            // Run query
            $is_book_deleted = $db->delete($query);
        }

        $db->commit();

    } catch (PDOException $ex) {
        $db->rollBack();
        print_r("rolled back");
    }

    echo $is_book_deleted;
    exit();
}