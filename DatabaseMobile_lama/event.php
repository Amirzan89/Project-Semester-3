<?php
require('Koneksi.php');


$nama_event = $_POST['nama_event'];
$deskripsi = $_POST['deskripsi'];
$tempat_event = $_POST['tempat_event'];
$tanggal_awal = $_POST['tanggal_awal'];
$tanggal_akhir= $_POST['tanggal_akhir'];
$link_pendaftaran= $_POST['link_pendaftaran'];
$poster_event = $_POST['poster_event'];

$poster_event = $_FILES['poster_event'];
$posterDir = __DIR__.'/uploads/events/';

// Mengunggah gambar poster
$posterFileName = $posterDir . basename($poster_event['name']);
move_uploaded_file($poster_event['tmp_name'], $posterFileName);

$sql = "INSERT INTO detail_events (nama_event, deskripsi, tempat_event, tanggal_awal, tanggal_akhir, link_pendaftaran, poster_event)  VALUES ('$nama_event', '$deskripsi', '$tempat_event', '$tanggal_awal', '$tanggal_akhir', '$link_pendaftaran', '" . '/'.basename($poster_event['name']) . "')";

$response = array();
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