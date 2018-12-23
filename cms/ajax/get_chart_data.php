<?php include '../../config/config.php'; ?>
<?php include '../../connections/Database.php'; ?>
<?php include '../../helpers/format_helper.php'; ?>

<?php
if (!isset($_SESSION)) session_start();

// Create DB object
$db = new Database;

// Make the script run only if there is a page number posted to this script

$user_id = null;

// Create DB object
$db = new Database;

$query = "SELECT MONTHNAME(bl.loan_date) AS loan_month, COUNT(bl.book_loan_id) AS num_loans
          FROM book_loan bl
            INNER JOIN user u on bl.user_id = u.user_id
          WHERE bl.loan_date >= SUBDATE(CURRENT_DATE, INTERVAL 1 YEAR)
          GROUP BY MONTHNAME(bl.loan_date)";


// Run query
$loans_result = $db->select($query);

$data = array();
$loans = array();
$i = 0;

if ($loans_result !== false) {
    while ($row = $loans_result->fetch_assoc()) {
        //array_push($data, array($data, $row['user_name']));
        $loans[] = $row;
    }
    $data["loans"] = $loans;
}


$query = "SELECT MONTHNAME(r.return_date) AS return_month, COUNT(r.book_loan_id) AS num_returns
          FROM book_return r
          WHERE r.return_date >= SUBDATE( CURRENT_DATE, INTERVAL 1 YEAR)
          GROUP BY MONTHNAME(r.return_date)";

// Run query
$returns_result = $db->select($query);

$returns = array();
$i = 0;

if ($returns_result !== false) {
    while ($row = $returns_result->fetch_assoc()) {
        //array_push($data, array($data, $row['user_name']));
        $returns[] = $row;
    }
    $data["returns"] = $returns;
}


$output = json_encode($data);
header('Content-Type: application/json; charset=UTF-8');

echo $output;
return;

?>
