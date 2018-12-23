<?php include '../config/config.php'; ?>
<?php include '../connections/Database.php'; ?>
<?php if (!isset($_SESSION)) session_start(); ?>

<?php

if (isset($_POST['submit'])) {
	// Create DB object
	$db = new Database;

	$uid = $_POST['uid'];
	$pwd = $_POST['pwd'];

	//Error handlers
	//Check if inputs are empty
	if (empty($uid) || empty($pwd)) {
		header("Location: ../index.php?login=empty");
		exit();

	} else {
		$sql = "SELECT * 
                FROM user 
                INNER JOIN role_assignment a on user.user_id = a.user_user_id
                INNER JOIN role r on a.role_role_id = r.role_id
                WHERE user_name='$uid' OR email='$uid' LIMIT 1;
                ";
		// Run query
		$result = $db->select($sql);

		if ($result != false) {
            $row = $result->fetch_assoc();
			$hashedPwdCheck = password_verify($pwd, $row['password']);
			if ($hashedPwdCheck == false) {
				header("Location: ../error.php?password=nomatch");
				exit();
			} elseif ($hashedPwdCheck == true) {
				//Log in the user here
				$_SESSION['u_id'] = $row['user_id'];
				$_SESSION['u_first'] = $row['first_name'];
				$_SESSION['u_last'] = $row['last_name'];
				$_SESSION['u_email'] = $row['email'];
				$_SESSION['u_name'] = $row['user_name'];
				$_SESSION['u_role'] = $row['role'];
				header("Location: ../index.php?login=success");
				exit();
			}
		} else {
            header("Location: ../signin.php?login=nomatch");
        }
	}
} else {
	header("Location: ../index.php?login=error");
	exit();
}
