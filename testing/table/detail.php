<?php
require_once(__DIR__.'/../../web/koneksi.php');
$database = koneksi::getInstance();
$conn = $database->getConnection();
$query = mysqli_query($conn, "SELECT * FROM detail_events");
if ($query) {
    $resultArray = mysqli_fetch_all($query, MYSQLI_ASSOC);
    echo json_encode($resultArray);
} else {
    echo json_encode(['error' => 'Query failed']);
}
?>