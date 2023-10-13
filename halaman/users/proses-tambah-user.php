 <?php
// Panggil koneksi database
require_once "../koneksi.php";

if (isset($_POST['simpan'])) {
    // $id = $_POST['id_user'];
    $nama = $_POST['nama'];
    $phone = $_POST['phone'];
    $jenisK = $_POST['jenisK'];
    $tempatL = $_POST['tempatL'];
    $tanggalL = $_POST['tanggalL'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $role = $_POST['role'];
	// perintah query untuk menyimpan data ke tabel 
	$query = mysqli_query($conn, "INSERT INTO users(	id_user,
											 	nama_lengkap,
												no_telpon,
												jenis_kelamin,
                                                tanggal_lahir,
                                                tempat_lahir,
												role,
												email,
												password,
                                                verifikasi)	
										VALUES(	'$id',
												'$nama',
												'$phone',
												'$jenisK',
												'$tempatL',
												'$tanggalL',
												'$role',
												'$email',
                                                '$pass',
                                                '0')");		

	// cek hasil query
	if ($query) {
		// jika berhasil tampilkan pesan berhasil insert data
		header('location: ../dashboard.php');
	} else {
		// jika gagal tampilkan pesan kesalahan
		header('location: ../dashboard.php');
	}						
}
?>