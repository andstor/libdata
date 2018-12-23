<?php include '../../config/config.php'; ?>
<?php include '../../connections/Database.php'; ?>

<?php
// Create DB object
$db = new Database;

// Make the script run only if there is a isbn number posted to this script
if (isset($_POST['country']) && isset($_POST['region']) ) {

    // Create DB object
    $db = new Database;


    $country = $_POST['country'];
    $region = $_POST['region'];


    $query = "SELECT c.city
              FROM city c
              INNER JOIN region r on c.region_id = r.region_id
              INNER JOIN country c2 on r.country_id = c2.country_id
              WHERE r.region = '$region' AND c2.country = '$country'
              ORDER BY c.city DESC";

    $city_res = $db->select($query);

    $cities = array();
    if ($city_res !== false) {
        while ($row = $city_res->fetch_assoc()) {
            $cities[] = $row;
        }
    }

    $data = array();

    $data["cities"] = $cities;


    $output = json_encode($data);
    header('Content-Type: application/json; charset=UTF-8');

    echo $output;
    return;


} elseif(isset($_POST['country'])) {
// Create DB object
    $db = new Database;


    $country = $_POST['country'];


    $query = "SELECT r.region
              FROM region r
              INNER JOIN country c on r.country_id = c.country_id
              WHERE c.country = '$country'
              ORDER BY c.country ASC";

    $region_res = $db->select($query);

    $regions = array();
    if ($region_res !== false) {
        while ($row = $region_res->fetch_assoc()) {
            //array_push($data, array($data, $row['user_name']));
            $regions[] = $row;
        }
    }


    $data = array();

    $data["regions"] = $regions;

    $output = json_encode($data);
    header('Content-Type: application/json; charset=UTF-8');

    echo $output;
    return;
} else {
    echo "No pn POSTED";
    die();
}
?>
