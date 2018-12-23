<?php include '../../config/config.php'; ?>
<?php include '../../connections/Database.php'; ?>

<?php
// Create DB object
$db = new Database;

// Make the script run only if there is a isbn number posted to this script
if (isset($_POST['isbn'])) {

    // Create DB object
    $db = new Database;


    $isbn = $_POST['isbn'];

    $query = "SELECT b.book_id AS id, bd.isbn, bd.title
              FROM book AS b
              INNER JOIN book_details AS bd ON bd.isbn = b.isbn
              WHERE b.isbn = '$isbn'
              ORDER BY b.book_id DESC";

    $books_res = $db->select($query);

    $books = array();
    if ($books_res !== false) {
        while ($row = $books_res->fetch_assoc()) {
            //array_push($data, array($data, $row['user_name']));
            $books[] = $row;
        }
    }


    $output = json_encode($books);
    header('Content-Type: application/json; charset=UTF-8');

    echo $output;
    return;


} else {
    echo "No pn POSTED";
    die();
}
?>
