<?php
require_once(__DIR__.'/../../web/koneksi.php');
$database = koneksi::getInstance();
$conn = $database->getConnection();

$idUser = $_POST['id_user'];

// Use prepared statement to prevent SQL injection
$stmt[0] = $conn->prepare("DELETE FROM surat_advis WHERE id_user = ?");
$stmt[0]->bind_param('i', $idUser);

if ($stmt[0]->execute()) {
    $stmt[0] = $conn->prepare("DELETE FROM seniman WHERE id_user = ?");
    $stmt[0]->bind_param('i', $idUser);
    
    if ($stmt[0]->execute()) {
        // If deletion is successful
        $response = ['status' => 'success', 'message' => 'Record deleted successfully'];
        echo json_encode($response);
    } else {
        // If deletion fails
        $response = ['status' => 'error', 'message' => 'Delete query failed'];
        echo json_encode($response);
    }
} else {
    // If deletion fails
    $response = ['status' => 'error', 'message' => 'Delete query failed'];
    echo json_encode($response);
}

$stmt[0]->close();
$conn->close();
?>
