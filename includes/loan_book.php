<?php include '../config/config.php'; ?>
<?php include '../connections/Database.php'; ?>
<?php include '../helpers/format_helper.php'; ?>

<?php
if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['u_id'])) {
    header("Location: error.php?login=false");
}

// Create DB object
$db = new Database;

if (isset($_POST['submit'])) {
    $user_id = $_SESSION['u_id'];

    $title = $publisher_id = $language_id = $language = $price = $library = null;

    $isbn = $_POST['isbn'];
    $library = $_POST['library'];


    $due_date = date('Y-m-d h:i:s', strtotime("+3 weeks"));


//********** BOOK *********
    try {
        $db->begin_transaction();


        // Get number of book
        $query = "SELECT count(book_id) as num_books
          FROM book
          WHERE isbn = '$isbn'";
        // Run query
        $book_res = $db->select($query);
        if ($book_res !== false) {
            $row = $book_res->fetch_assoc();
            $num_books = $row['num_books'];
        }

        // Get first available book_id for rental.
        $query = "SELECT b.book_id
                    FROM book AS b
                      INNER JOIN library_branch AS lb on b.library_branch_id = lb.library_branch_id
                    WHERE isbn = '$isbn' AND b.book_id NOT IN (
                      SELECT bl.book_id
                      FROM book_loan AS bl
                        LEFT JOIN book_return r on bl.book_loan_id = r.book_loan_id
                        INNER JOIN book b2 on bl.book_id = b2.book_id
                      WHERE r.book_return_id IS NULL AND b2.isbn = '$isbn'
                    ) AND lb.name = '$library'";
        // Run query
        $book_res = $db->select($query);
        if ($book_res !== false) {
            $row = $book_res->fetch_array();
            $book_id = $row[0];
        }


        // Insert book authors
        $sql = "INSERT INTO book_loan (book_id, user_id, due_date)  
			    VALUES ('$book_id', '$user_id', '$due_date')";
        // Run query
        $db->insert($sql);


        $db->commit();


    } catch (PDOException $ex) {
        $db->rollBack();
        print_r("rolled back");
    }
    header("Location: ../books.php?status=loansuccess");
    exit();
} else {

    exit();
}