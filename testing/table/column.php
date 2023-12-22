<?php
require_once(__DIR__.'/../../web/koneksi.php');

try {
  $database = koneksi::getInstance();
  $conn = $database->getConnection();

  $query = "DESCRIBE `seniman`";
  $result = mysqli_query($conn, $query);

  if ($result) {
    $resultArray = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($resultArray);
  } else {
    throw new Exception('Query failed');
  }
} catch (Exception $e) {
  echo json_encode(['error' => $e->getMessage()]);
}
