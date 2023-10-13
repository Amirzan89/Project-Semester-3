<?php
// Panggil koneksi database
require_once "../koneksi.php";

if (isset($_GET['id_user'])) {

	$id = $_GET['id_user'];

	// perintah query untuk menghapus data pada tabel is_siswa
	$query = mysqli_query($conn, "DELETE FROM users WHERE id_user='$id'");

	// cek hasil query
	if ($query) {
		// jika berhasil tampilkan pesan berhasil delete data
		header('location: ../dashboard.php');
	} else {
		// jika gagal tampilkan pesan kesalahan
		header('location: ../dashboard.php');
	}	
}						
?>