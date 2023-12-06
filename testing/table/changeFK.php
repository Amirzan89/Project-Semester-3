<?php
require_once(__DIR__ . '/../../web/koneksi.php');

$database = koneksi::getInstance();
$conn = $database->getConnection();

$query = "ALTER TABLE `sewa_tempat` ADD CONSTRAINT `TFK` FOREIGN KEY (`id_tempat`) REFERENCES `list_tempat`(`id_tempat`) ON DELETE CASCADE ON UPDATE CASCADE";

$response = [];

try {
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        throw new Exception("Error preparing query: " . $conn->error);
    }

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $response["kode"] = 1;
        $response["pesan"] = "Table structure updated successfully";
    } else {
        $response["kode"] = 0;
        $response["pesan"] = "No changes or update failed";
    }
    
    $stmt->close();
} catch (Exception $e) {
    $response["kode"] = 0;
    $response["pesan"] = $e->getMessage();
    
}
echo json_encode($response);
$stmt->close();
?>