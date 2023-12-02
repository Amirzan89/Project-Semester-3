<?php
require_once(__DIR__ . '/../../web/koneksi.php');

$database = koneksi::getInstance();
$conn = $database->getConnection();

// Drop the 'tgl_selesai' column
$queryDropColumn = "ALTER TABLE surat_advis DROP COLUMN tgl_selesai";
$stmtDropColumn = $conn->prepare($queryDropColumn);
$stmtDropColumn->execute();

// Rename 'tgl_awal' column to 'tgl_advis'
$queryRenameColumn = "ALTER TABLE surat_advis CHANGE COLUMN tgl_awal tgl_advis DATE NOT NULL";
$stmtRenameColumn = $conn->prepare($queryRenameColumn);
$stmtRenameColumn->execute();

$response = [];

if ($stmtDropColumn->affected_rows > 0 || $stmtRenameColumn->affected_rows > 0) {
    $response["kode"] = 1;
    $response["pesan"] = "Table structure updated successfully";
} else {
    $response["kode"] = 0;
    $response["pesan"] = "No changes or update failed";
}

$stmtDropColumn->close();
$stmtRenameColumn->close();

echo json_encode($response);
?>