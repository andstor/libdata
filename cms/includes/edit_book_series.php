<?php

use file_upload\FileUploader;

include '../../config/config.php';
include '../../utils/FileUploader.php';
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


if (isset($_POST['submit'])) {

    // Create DB object
    $db = new Database;

    $title = $isbn = $edition = $year = $library_branch = $price =
    $language = $pages = $genres = $publisher = $exAuthorsIds =
    $newAuthorsFirstName = $newAuthorsLastName = null;


    if (isset($_POST['name'])) $name = $_POST['name'];
    if (isset($_POST['book_isbn'])) $book_isbns = $_POST['book_isbn'];


    //Check for empty fields
    if (empty($name) || empty($book_isbns)) {
        header("Location: ../add_book.php?status=empty");
        exit();
    } else {

        //********** BOOK *********
        try {
            $db->begin_transaction();

            //_______________________________________________________________________________________________________
            // Does book already exist?
            $sql = "SELECT bs.book_series_id
					FROM book_series AS bs
					WHERE bs.series_name = '$name'";
            // Run query
            $book_series_res = $db->select($sql);
            if ($book_series_res != false) {
                // Insert into book series assignment
                $row = $book_series_res->fetch_assoc();
                $book_series_id = $row['book_series_id'];

                if (!empty($book_isbns)) {
                    foreach ($book_isbns as $book_isbn) {
                        $sql = "INSERT INTO book_series_assignment (book_details_isbn, book_series_book_series_id)
                        VALUES ('$book_isbn', '$book_series_id')";
                        $db->insert($sql);
                    }
                }
            } else {
                $book_series_id = null;


                //_______________________________________________________________________________________________________
                // Insert the book_description into the database
                $sql = "INSERT INTO book_details (isbn, publisher_id, language_id, title, edition, year, pages, price)
				        VALUES ('$isbn', '$publisher_id', $language_id, '$title', '$edition', '$year', '$pages', '$price')";
                $db->insert($sql);


                // Insert into book
                $sql = "INSERT INTO book (isbn, library_branch_id)
                        SELECT '$isbn', lb.library_branch_id
                        FROM library_branch AS lb
                        WHERE lb.name = '$library_branch'
                ";
                $db->insert($sql);
                $book_id = $db->link->insert_id;
                //_______________________________________________________________________________________________________


                //*****AUTHORS******
                if (!empty($newAuthorsFirstName)) {
                    foreach ($newAuthorsFirstName as $index => $author_firstname) {
                        $author_id = null;
                        $author_lastname = $newAuthorsLastName[$index];

                        $sql = "SELECT a.author_id
                                FROM author AS a
                                WHERE a.first_name = '$author_firstname' AND a.last_name = '$author_lastname'";
                        // Run query
                        $author_res = $db->select($sql);

                        if ($author_res == false) {
                            // Insert book authors
                            $sql = "INSERT INTO author (first_name, last_name)  
                                    VALUES ('$author_firstname', '$author_lastname')";
                            // Run query
                            $db->insert($sql);
                            $author_id = $db->link->insert_id;

                        } else {
                            $row = $author_res->fetch_assoc();
                            $author_id = $row['author_id'];
                        }


                        // Insert book authors
                        $sql = "INSERT INTO author_list (book_details_isbn, author_author_id)  
				                VALUES ('$isbn', '$author_id')";
                        // Run query
                        $db->insert($sql);
                    }
                }


            }

            $db->commit();


            // UPLOAD PROFILE PICTURE

            if (!(!file_exists($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name']))) {
                $file_uploader = new FileUploader();
                $file = $_FILES['image'];
                $file_name = $isbn;
                $file_uploader->upload_img($file, '../../uploads/cover_pictures/', $file_name);
            }

        } catch (PDOException $ex) {
            $db->rollBack();
            print_r("rolled back");
        }
        header("Location: ../add_book.php?status=bookcreated");
        exit();
    }

} else {
    //header("Location: signup.php");
    exit();
}
