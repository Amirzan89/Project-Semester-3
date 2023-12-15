<?php
require('Koneksi.php');

$nama_pengirim = str_replace(['"', "'"], '', $_POST['nama_pengirim']);
$status = str_replace(['"', "'"], '', $_POST['status']);
$id_user = str_replace(['"', "'"], '', $_POST['id_user']);
$response = array();
$sql = "SELECT id_detail FROM detail_events ORDER BY id_detail DESC LIMIT 1";
$result = $konek->query($sql);
if ($result->num_rows == 1) {
    $id = $result->fetch_assoc()['id_detail'];
}else{
    $sql = "SHOW TABLE STATUS LIKE 'detail_events'";
    $result1 = $konek->query($sql);
    if ($result1->num_rows == 1) {
        $id = $result1->fetch_assoc()['Auto_increment'];
    }
}
date_default_timezone_set('Asia/Jakarta');
$created_at = date('Y-m-d H:i:s');
if(isset($_POST['id_detail']) && !empty($_POST['id_detail'])){
    $sql = "INSERT INTO events (nama_pengirim, created_at, updated_at, status, id_user, id_detail)  VALUES ('$nama_pengirim', '$created_at', '$created_at','$status', '$id_user', '". str_replace(['"', "'"], '', $_POST['id_detail']) ."')";
}else{
    $sql = "INSERT INTO events (nama_pengirim, created_at, updated_at, status, id_user, id_detail)  VALUES ('$nama_pengirim', '$created_at', '$created_at','$status', '$id_user', '". $id."')";
}
if ($konek->query($sql) === TRUE) {
    $response["kode"] = 1;
    $response["pesan"] = "Data telah berhasil dimasukkan.";
} else {
    $response["kode"] = 2;
    $response["pesan"] = "Error: " . $sql . "<br>" . $konek->error;
}

$konek->close();

echo json_encode($response);
?>