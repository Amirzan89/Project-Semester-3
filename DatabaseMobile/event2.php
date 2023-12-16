<?php
require('Koneksi.php');
$nama_event = str_replace(['"', "'"], '', $_POST['nama_event']);
$deskripsi = str_replace(['"', "'"], '', $_POST['deskripsi']);
$tempat_event = str_replace(['"', "'"], '', $_POST['tempat_event']);
$tanggal_awal = str_replace(['"', "'"], '', $_POST['tanggal_awal']);
$tanggal_akhir = str_replace(['"', "'"], '', $_POST['tanggal_akhir']);
$link_pendaftaran = str_replace(['"', "'"], '', $_POST['link_pendaftaran']);
$poster_event = str_replace(['"', "'"], '', $_POST['poster_event']);

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