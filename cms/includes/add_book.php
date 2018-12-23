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


    if (isset($_POST['title'])) $title = $_POST['title'];
    if (isset($_POST['isbn'])) $isbn = $_POST['isbn'];
    if (isset($_POST['edition'])) $edition = $_POST['edition'];
    if (isset($_POST['year'])) $year = $_POST['year'];
    if (isset($_POST['libraryBranch'])) $library_branch = $_POST['libraryBranch'];
    if (isset($_POST['price'])) $price = $_POST['price'];
    if (isset($_POST['language'])) $language = $_POST['language'];
    if (isset($_POST['pages'])) $pages = $_POST['pages'];
    if (isset($_POST['genres'])) $genres = $_POST['genres'];
    if (isset($_POST['publisher'])) $publisher = $_POST['publisher'];

    if (isset($_POST['exAuthorsId'])) $exAuthorsIds = $_POST['exAuthorsId'];
    if (isset($_POST['newAuthorFirstName'])) $newAuthorsFirstName = $_POST['newAuthorFirstName'];
    if (isset($_POST['newAuthorLastName'])) $newAuthorsLastName = $_POST['newAuthorLastName'];

    //Check for empty fields
    if (empty($title) || empty($isbn) || empty($library_branch)) {
        header("Location: ../add_book.php?status=empty");
        exit();
    } else {

        //********** BOOK *********
        try {
            $db->begin_transaction();

            //_______________________________________________________________________________________________________
            // Does book already exist?
            $sql = "SELECT bd.isbn
					FROM book_details AS bd
					WHERE bd.isbn = '$isbn'";
            // Run query
            $book_details_res = $db->select($sql);
            if ($book_details_res != false) {
                // Insert into book
                $sql = "INSERT INTO book (isbn, library_branch_id)
                        SELECT '$isbn', lb.library_branch_id
                        FROM library_branch AS lb
                        WHERE lb.name = '$library_branch'";
                $db->insert($sql);
                $book_id = $db->link->insert_id;

            } else {
                $language_id = null;
                $publisher_id = null;
                $book_id = null;


                // Get language_id
                $sql = "SELECT l.language_id
                        FROM book_language AS l
                        WHERE l.language = '$language'";
                // Run query
                $language_res = $db->select($sql);
                if ($language_res !== false) {
                    $row = $language_res->fetch_assoc();
                    $language_id = addslashes($row['language_id']);
                } else {
                    $language_id = "NULL";
                }


                if ($publisher == null) {

                    $publisher_id = "NULL";
                } else {

                    // Get/set publisher_id
                    $sql = "SELECT p.publisher_id
                        FROM publisher AS p
                        WHERE p.publisher = '$publisher'";
                    // Run query
                    $publisher_res = $db->select($sql);

                    if ($publisher_res == false) {
                        //Insert the country into the database
                        $sql = "INSERT INTO publisher (publisher)
							VALUES ('$publisher')";
                        $db->insert($sql);
                        $publisher_id = $db->link->insert_id;
                    } else {
                        $row = $publisher_res->fetch_assoc();
                        $publisher_id = $row['publisher_id'];
                    }
                    $publisher_id = addslashes($publisher_id);
                }


                if ($edition == null) {
                    $edition = "NULL";
                } else {
                    $edition = addslashes($edition);
                }

                if ($year == null) {
                    $year = "NULL";
                } else {
                    $year = addslashes($year);
                }

                if ($pages == null) {
                    $pages = "NULL";
                } else {
                    $pages = addslashes($pages);
                }

                if ($price == null) {
                    $price = "NULL";
                } else {
                    $price = addslashes($price);
                }



                //_______________________________________________________________________________________________________
                // Insert the book_description into the database
                $sql = "INSERT INTO book_details (isbn, publisher_id, language_id, title, edition, year, pages, price)
				        VALUES ('$isbn', $publisher_id, $language_id, '$title', $edition, $year, $pages, $price)";
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


                // Insert genre(s)
                if (!empty($genres)) {
                    foreach ($genres as $genre) {
                        // Insert book genre_assignment
                        $sql = "INSERT INTO book_genre_assignment (book_details_isbn, book_genre_genre_id)  
                                SELECT '$isbn', g.genre_id 
                                  FROM book_genre AS g
                                  WHERE g.name = '$genre'";
                        // Run query
                        $db->insert($sql);
                    }
                }


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


                if (!empty($exAuthorsIds) && is_array($exAuthorsIds)) {
                    foreach ($exAuthorsIds as $exAuthorsId) {
                        $sql = "INSERT INTO author_list (book_details_isbn, author_author_id)  
				                VALUES ('$isbn', '$exAuthorsId')";
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
        }
        header("Location: ../add_book.php?status=bookcreated");
        exit();
    }

} else {
    //header("Location: signup.php");
    exit();
}
