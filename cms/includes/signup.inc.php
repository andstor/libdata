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


    $username = $_POST['uid'];
    $first = $_POST['first'];
    $last = $_POST['last'];
    $email = $_POST['email'];
    $emailConf = $_POST['emailConf'];
    $pwd = $_POST['pwd'];
    $pwdConf = $_POST['pwdConf'];
    $role = $_POST['role'];

    $country = $_POST['country'];
    $region = $_POST['region'];
    $city = $_POST['city'];
    $postal_address = $_POST['postalRegion'];
    $postal_code = (int)$_POST['postalCode'];
    $address1 = $_POST['address1'];
    $address2 = $_POST['address2'];

    $gender = $_POST['gender'];
    $phone1 = $_POST['phone1'];
    $phone1Type = $_POST['phone1Type'];
    $phone2 = $_POST['phone2'];
    $phone2Type = $_POST['phone2Type'];
    //$optNewsletter = $__POST['optNewsletter'];

    //Error handlers
    //Check for empty fields
    if (empty($first) || empty($last) || empty($email) || empty($username) || empty($pwd) || empty($role)) {
        header("Location: ../add_user.php?signup=empty");
        exit();
    } elseif ($pwd !== $pwdConf || $email !== $emailConf) {
        header("Location: ../add_user.php?signup=noMatch");
        exit();
    } else {
        //Check if input characters are valid
        if (!preg_match("/^[a-zA-Z]*$/", $first) || !preg_match("/^[a-zA-Z]*$/", $last)) {
            header("Location: ../add_user.php?signup=invalid");
            exit();
        } else {
            //Check if email is valid
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: ../add_user.php?signup=email");
                exit();
            } else {

                //**********USER*********

                $sql = "SELECT * FROM user WHERE user_name='$username'";
                // Run query
                $result = $db->select($sql);

                if ($result !== false) {
                    header("Location: ../add_user.php?signup=usertaken");
                    exit();
                } else {
                    try {
                        $db->begin_transaction();
                        //Hashing the password
                        $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
                        //Insert the user into the database
                        $sql = "INSERT INTO user (first_name, last_name, email, user_name, password)
								VALUES ('$first', '$last', '$email', '$username', '$hashedPwd')";
                        // Run query
                        $db->insert($sql);

                        $inserted_user_id = $db->link->insert_id;


                        // Insert role
                        // *** ADDRESS ***
                        $sql = "INSERT INTO role_assignment (user_user_id, role_role_id) 
								SELECT '$inserted_user_id', r.role_id
								FROM role AS r
								WHERE r.role = '$role';
								";
                        // Run query
                        $db->insert($sql);

//******************************************************************************

                        if (!empty($country) &&
                            !empty($region) &&
                            !empty($city) &&
                            !empty($address1)) {
//                            header("Location: ../signup.php?signup=notempty" . $country . "::" . $region . "::" . $postal_address . "::" . $postal_code . "::" . $address1 . "::" . $address2 . "::");

                            $address_id = null;
                            $country_id = null;
                            $postal_address_id = null;
                            $postal_code_id = null;


                            // Address
                            $sql = "SELECT *, @address_id := address_id 
                                    FROM address a
                                    INNER JOIN city c on a.city_id = c.city_id
                                    LEFT JOIN postal_code pc on a.postal_code_id = pc.postal_code_id
                                    LEFT JOIN postal_address pa on pc.postal_address_id = pa.postal_address_id
                                    WHERE line_1 = '$address1'
                                        AND line_2 = '$address2'
                                        AND c.city = '$city'
                                        AND pc.postal_code = '$postal_code'
                                        AND pa.postal_address = '$postal_address'";
                            $address_res = $db->select($sql);


                            if (!$address_res == false) { // If the address already exists
                                $sql = "UPDATE user
									SET address_id = @address_id
									WHERE user_id = '$inserted_user_id'";
                                $db->update($sql);
                            } else {

                                // Country, region and city
                                $sql = "SELECT c2.city_id 
                                    FROM country c
                                    INNER JOIN region r on c.country_id = r.country_id
                                    INNER JOIN city c2 on r.region_id = c2.region_id  
                                    WHERE c.country = '$country'
                                      AND r.region = '$region'
                                      AND c2.city = '$city'";
                                $crc_res = $db->select($sql);
                                $row = $crc_res->fetch_assoc();
                                $city_id = $row['city_id'];


                                // *** ADDRESS ***
                                $sql = "INSERT INTO address (line_1, line_2, city_id)
									VALUES ('$address1', '$address2', '$city_id')";
                                // Run query
                                $db->insert($sql);
                                $address_id = $db->link->insert_id;

                                //Update the user address_id
                                $sql = "UPDATE user
									SET address_id = '$address_id'
									WHERE user_id = '$inserted_user_id'";
                                // Run query
                                $db->update($sql);
                            }

                            if (!empty($postal_code) && !empty($postal_address)) {

                                // postal_address
                                $sql = "SELECT postal_address_id FROM postal_address 
                                    WHERE postal_address = '$postal_address'";
                                $postal_address_res = $db->select($sql);

                                // postal_code
                                $sql = "SELECT postal_code_id
                                        FROM postal_code
                                        INNER JOIN postal_address pa on postal_code.postal_address_id = pa.postal_address_id
                                        WHERE postal_code = '$postal_code'
                                        AND pa.postal_address = '$postal_address'";
                                $postal_code_res = $db->select($sql);


                                // POSTAL ADDRESS and CODE
                                if ($postal_address_res != false && $postal_code_res != false) {
                                    $row = $postal_address_res->fetch_assoc();
                                    $postal_address_id = $row['postal_address_id'];

                                    $row = $postal_code_res->fetch_assoc();
                                    $postal_code_id = $row['postal_code_id'];

                                } else {
                                    //Insert the country into the database
                                    $sql = "INSERT INTO postal_address (postal_address)
									VALUES ('$postal_address')";
                                    $db->insert($sql);
                                    $postal_address_id = $db->link->insert_id;

                                    $sql = "INSERT INTO postal_code (postal_code, postal_address_id)
									VALUES ('$postal_code', '$postal_address_id')";
                                    $db->insert($sql);
                                    $postal_code_id = $db->link->insert_id;
                                }

//                                throw new Exception("okok");


                                $sql = "UPDATE address
									SET postal_code_id = '$postal_code_id'
									WHERE address_id = '$address_id'";
                                // Run query
                                $db->update($sql);

                            }
                        }

                        if (!empty($gender)) {
                            $sql = "UPDATE user
									SET gender_id = (SELECT gender_id
									                 FROM gender
									                 WHERE gender = '$gender' 
									                )
									WHERE user_id = '$inserted_user_id'";
                            // Run query
                            $db->update($sql);
                        }

                        if (!empty($phone1) && !empty($phone1Type)) {
                            //Update the user address_id
                            $sql = "INSERT INTO phone (user_id, phone, phone_type)
									SELECT '$inserted_user_id', '$phone1', pt.phone_type
									FROM phone_type AS pt
									WHERE pt.description = '$phone1Type'";
                            // Run query
                            $db->update($sql);
                        }

                        if (!empty($phone2) && !empty($phone2Type)) {
                            //Update the user address_id
                            $sql = "INSERT INTO phone (user_id, phone, phone_type)
									SELECT '$inserted_user_id', '$phone2', pt.phone_type
									FROM phone_type AS pt
									WHERE pt.description = '$phone2Type'";
                            // Run query
                            $db->update($sql);
                        }

                        $db->commit();


                        // UPLOAD PROFILE PICTURE

                        if (!(!file_exists($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name']))) {
                            $file_uploader = new FileUploader();
                            $file = $_FILES['image'];
                            $file_name = $inserted_user_id;
                            $file_uploader->upload_img($file, '../../uploads/profile_pictures/', $file_name);
                        }

                    } catch (PDOException $ex) {
                        $db->rollBack();
                        print_r("rolled back");
                    }
                    header("Location: ../add_user.php?signup=usercreated");

                    exit();
                }
            }
        }
    }
} else {
    header("Location: add_user.php?help=yes");
    exit();
}
