<?php
require_once(__DIR__ . '/../../web/koneksi.php');
$database = koneksi::getInstance();
$conn = $database->getConnection();
$pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
$query = "UPDATE users SET password = '$pass' WHERE id_user = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_POST['id_user']);
$stmt->execute();
if ($stmt->affected_rows > 0) {
    $stmt->close();
    $response["kode"] = 1;
    $response["pesan"] = "Lanjut";
} else {
    $response["kode"] = 0;
    $response["pesan"] = "Akun Belum Terdaftar";
}
echo json_encode($response);
?>