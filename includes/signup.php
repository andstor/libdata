<?php include '../config/config.php'; ?>
<?php include '../connections/Database.php'; ?>

<?php
session_start();

if (isset($_POST['submit'])) {

    // Create DB object
    $db = new Database;

    $first = $_POST['first'];
    $last = $_POST['last'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $pwd = $_POST['pwd'];


    //Error handlers
    //Check for empty fields
    if (empty($first) || empty($last) || empty($email) || empty($username) || empty($pwd)) {
        header("Location: ../signup.php?signup=empty");
        exit();
    } else {
        //Check if input characters are valid
        if (!preg_match("/^[a-zA-Z]*$/", $first) || !preg_match("/^[a-zA-Z]*$/", $last)) {
            header("Location: ../signup.php?signup=invalid");
            exit();
        } else {
            //Check if email is valid
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: ../signup.php?signup=email");
                exit();
            } else {


                $sql = "SELECT * FROM user WHERE user_name='$username'";
                // Run query
                $result = $db->select($sql);

                if ($result !== false) {
                    header("Location: ../signup.php?signup=usertaken");
                    exit();
                }


                try {
                    $db->begin_transaction();

                    $sql = "SELECT * FROM user WHERE user_name='$username'";
                    // Run query
                    $result = $db->select($sql);

                    //Hashing the password
                    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
                    //Insert the user into the database
                    $sql = "INSERT INTO user (user_name, password, first_name, last_name, email)
                        VALUES ('$username', '$hashedPwd', '$first', '$last','$email');";
                    $db->insert($sql);

                    //Update role

                    $userSQL = "SELECT user_id FROM user WHERE user_name = '$username' LIMIT 1;";

                    $userRes = $db->select($userSQL);
                    $user = $userRes->fetch_assoc();
                    $uid = $user['user_id'];

                    $roleSQL = "SELECT role_id FROM role WHERE role = 'borrower' LIMIT 1;";
                    $roleRes = $db->select($roleSQL);
                    $role = $roleRes->fetch_assoc();
                    $borrowerRoleID = $role['role_id'];

                    $sql = "INSERT INTO role_assignment (user_user_id, role_role_id) 
                        VALUES ('$uid', '$borrowerRoleID');";
                    $db->insert($sql);


                    $db->commit();




                } catch (PDOException $ex) {
                    $db->rollBack();
                    print_r("rolled back");

                }
                header("Location: ../signin.php?signup=success");
                exit();
            }
        }
    }

} else {
    //header("Location: signup.php");
    exit();
}
