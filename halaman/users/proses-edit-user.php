<?php
// Panggil koneksi database
require_once "../koneksi.php";

if (isset($_POST['simpan'])) {
    if (isset($_POST['id_user'])) {
        $id = $_POST['id_user'];
        $nama = $_POST['nama'];
        $phone = $_POST['phone'];
        $jenisK = $_POST['jenisK'];
        $tempatL = $_POST['tempatL'];
        $tanggalL = $_POST['tanggalL'];
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        $role = $_POST['role'];
    
        // perintah query untuk menyimpan data ke tabel 
        $query = mysqli_query($conn, "UPDATE users SET nama_lengkap = '$nama', 
                                                        no_telpon = '$phone', 
                                                        jenis_kelamin = '$jenisK',
                                                        tanggal_lahir = '$tanggalL', 
                                                        tempat_lahir = '$tempatL', 
                                                        role = '$role',
                                                        email = '$email',
                                                        password = '$pass' 
                                                        WHERE id_user = '$id' ");		
    
        // cek hasil query
        if ($query) {
            // jika berhasil tampilkan pesan berhasil insert data
            header('location: ../dashboard.php');
        } else {
            // jika gagal tampilkan pesan kesalahan
            header('location: ../dashboard.php');
        }				
    }
    		
}
?>