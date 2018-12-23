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


    $name = $_POST['name'];

    $country = $_POST['country'];
    $region = $_POST['region'];
    $city = $_POST['city'];
    $postal_address = $_POST['postalRegion'];
    $postal_code = (int)$_POST['postalCode'];
    $address1 = $_POST['address1'];
    $address2 = $_POST['address2'];

    //Check for empty fields
    if (empty($name) ||
        empty($country) ||
        empty($region) ||
        empty($city) ||
        empty($address1)) {
        header("Location: ../signup.php?signup=empty");
        exit();
    } else {
        //Check if input characters are valid
        if (!preg_match("/^[a-zA-Z\s]*$/", $name)) {
            header("Location: ../add_library_branch.php?status=invalidname");
            exit();
        } else {

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

            $sql = "SELECT * 
                    FROM library_branch
                    INNER JOIN address a on library_branch.address_id = a.address_id
                    WHERE library_branch.name = '$name'
                    AND a.address_id = @address_id";
            // Run query
            $result = $db->select($sql);

            if ($result !== false) {
                header("Location: ../add_library_branch.php?status=libexists");
                exit();
            } else {
                try {
                    $db->begin_transaction();

                    $address_id = null;
                    $lib_branch_id = null;
                    $country_id = null;
                    $postal_address_id = null;
                    $postal_code_id = null;


                    if (!$address_res == false) { // If the address already exists
                        // *** ADDRESS ***
                        $sql = "INSERT INTO library_branch (address_id, name)
									VALUES (@address_id, '$name')";
                        // Run query
                        $db->insert($sql);
                        $lib_branch_id = $db->link->insert_id;

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

                        $sql = "INSERT INTO library_branch (address_id, name)
									VALUES ($address_id, '$name')";
                        // Run query
                        $db->insert($sql);
                        $lib_branch_id = $db->link->insert_id;
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


                        $sql = "UPDATE address
									SET postal_code_id = '$postal_code_id'
									WHERE address_id = '$address_id'";
                        // Run query
                        $db->update($sql);

                    }


                    $db->commit();


                } catch (PDOException $ex) {
                    $db->rollBack();
                    print_r("rolled back");
                }
                header("Location: ../add_library_branch.php?status=success");
                exit();
            }
        }
    }
} else {
    header("Location: ../add_library_branch.php?help=yes");
    exit();
}
