<?php
require_once(__DIR__ . '/../../web/koneksi.php');
$database = koneksi::getInstance();
$conn = $database->getConnection();
if(isset($_POST['id_user']) && !empty($_POST['id_user'])){
    $query = "UPDATE seniman SET nama_seniman = ? WHERE id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $_POST['nama'], $_POST['id_user']);
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
}else{
    $query = "UPDATE seniman SET tgl_berlaku = '2023-12-31'";
    $stmt = $conn->prepare($query);
    // $stmt->bind_param("si", $_POST['nama'], $_POST['id_user']);
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
}
?>