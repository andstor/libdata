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
    if (empty($name)) {
        header("Location: ../add_book_series.php?status=empty");
        exit();
    } else {

        //********** BOOK *********
        try {
            $db->begin_transaction();


            $book_series_id = null;


            // Insert into book
            $sql = "INSERT INTO book_series (series_name)
                    VALUES ('$name')";
            $db->insert($sql);
            $book_series_id = $db->link->insert_id;


            if (!empty($book_isbns)) {
                foreach ($book_isbns as $book_isbn) {
                    $sql = "INSERT INTO book_series_assignment (book_details_isbn, book_series_book_series_id)
                        VALUES ('$book_isbn', '$book_series_id')";
                    $db->insert($sql);
                }
            }


            $db->commit();


            // UPLOAD PROFILE PICTURE
/*
            if (!(!file_exists($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name']))) {
                $file_uploader = new FileUploader();
                $file = $_FILES['image'];
                $file_name = $isbn;
                $file_uploader->upload_img($file, '../../uploads/cover_pictures/', $file_name);
            }*/

        } catch (PDOException $ex) {
            $db->rollBack();
            print_r("rolled back");
        }
        header("Location: ../add_book_series.php?status=seriescreated");
        exit();
    }

} else {
    //header("Location: signup.php");
    exit();
}
