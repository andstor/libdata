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

    $waiting_list_id = $waiting_list_line_id = $library = null;

    $isbn = $_POST['isbn'];
    $library = $_POST['library'];




//********** BOOK *********
    try {
        $db->begin_transaction();


        // Waiting list
        $sql = "SELECT wl.waiting_list_id FROM waiting_list wl
                WHERE wl.isbn = '$isbn' 
                AND wl.library_branch_id = (
                                            SELECT library_branch.library_branch_id
                                            FROM library_branch
                                            WHERE name = '$library'
                                            )";
        $waiting_list_res = $db->select($sql);

        if ($waiting_list_res == false) {
            //Insert the country into the database
            $sql = "INSERT INTO waiting_list (isbn, library_branch_id)
					SELECT '$isbn', library_branch_id
					FROM library_branch
					WHERE library_branch_id = (
                                            SELECT library_branch.library_branch_id
                                            FROM library_branch
                                            WHERE name = '$library'
                                            )";
            $db->insert($sql);
            $waiting_list_id = $db->link->insert_id;
        } else {
            $row = $waiting_list_res->fetch_assoc();
            $waiting_list_id = $row['waiting_list_id'];
        }



        // Waiting list line
        $sql = "SELECT wll.waiting_list_waiting_list_id
                FROM waiting_list_line wll
                WHERE wll.waiting_list_waiting_list_id = '$waiting_list_id'
                AND wll.user_user_id = '$user_id'";
        $waiting_list_line_res = $db->select($sql);


        if ($waiting_list_line_res != false) {
            header("Location: ../books.php?status=waitingalready");
            exit();
        } else {
            //Insert the country into the database
            $sql = "INSERT INTO waiting_list_line (waiting_list_waiting_list_id, user_user_id) 
					SELECT '$waiting_list_id', $user_id";
            $db->insert($sql);
            $waiting_list_line_id = $db->link->insert_id;
        }



        $db->commit();


    } catch (PDOException $ex) {
        $db->rollBack();
        print_r("rolled back");
    }
    header("Location: ../books.php?status=waitingsuccess");
    exit();
} else {

    exit();
}