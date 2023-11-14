<?php
require_once(__DIR__.'/../web/koneksi.php');
require_once(__DIR__.'/../web/authenticate.php');
require_once(__DIR__.'/../env.php');
loadEnv();
$database = koneksi::getInstance();
$conn = $database->getConnection();
$userAuth = authenticate($_POST, [
    'uri' => $_SERVER['REQUEST_URI'],
    'method' => $_SERVER['REQUEST_METHOD'
    ]
], $conn);
if ($userAuth['status'] == 'error') {
    header('Location: /login.php');
} else {
    $userAuth = $userAuth['data'];
    if(!in_array($userAuth['role'],['super admin','admin tempat'])){
        echo "<script>alert('Anda bukan admin tempat !')</script>";
        echo "<script>window.location.href = '/dashboard.php';</script>";
        exit();
    }
    $tPath = ($_SERVER['APP_ENV'] == 'local') ? '' : $_SERVER['APP_FOLDER'];
    $csrf = $GLOBALS['csrf'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Disporabudpar - Nganjuk</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="<?php echo $tPath; ?>/public/assets/img/landing-page/favicon.png" rel="icon">
    <link href="<?php echo $tPath; ?>/public/assets/img/landing-page/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <!-- <link href="https://fonts.gstatic.com" rel="preconnect"> -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">
    <!-- Vendor CSS Files -->
    <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo $tPath; ?>/public/assets/vendor/simple-datatables/style.css" rel="stylesheet">


    <!-- Template Main CSS File -->
    <link href="<?php echo $tPath; ?>/public/assets/css/tempat.css" rel="stylesheet">
</head>

<body>
    <script>
        var csrfToken = "<?php echo $csrf ?>";
        var email = "<?php echo $userAuth['email'] ?>";
        var idUser = "<?php echo $userAuth['id_user'] ?>";
        var number = "<?php echo $userAuth['number'] ?>";
        var role = "<?php echo $userAuth['role'] ?>";
    </script>
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
        <?php include(__DIR__.'/../header.php');
        ?>
    </header><!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            <?php
            $nav = 'tempat';
            include(__DIR__.'/../sidebar.php');
            ?>
        </ul>
    </aside><!-- End Sidebar-->

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Formulir Peminjaman</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="/tempat.php">Kelola Tempat</a></li>
                    <li class="breadcrumb-item active">Formulir Peminjaman</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->


        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Formulir Peminjaman Tempat</h5>

                            <!-- Multi Columns Form -->
                            <form class="row g-3" method="POST" action="proses-tambah-tempat.php">
                                <div class="col-md-12">
                                    <label for="nama_peminjam" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama_peminjam">
                                </div>
                                <div class="col-md-12">
                                    <label for="nik_sewa" class="form-label">Nomor Induk Kependudukan</label>
                                    <input type="text" class="form-control" id="nik_sewa">
                                </div>
                                <div class="col-md-12">
                                    <label for="instansi" class="form-label">Instansi</label>
                                    <input type="text" class="form-control" id="instansi">
                                </div>
                                <div class="col-md-12">
                                    <label for="nama_kegiatan_sewa" class="form-label">Nama Kegiatan</label>
                                    <input type="text" class="form-control" id="nama_kegiatan_sewa">
                                </div>
                                <div class="col-md-9">
                                    <label for="nama_organisasi" class="form-label">Nama Tempat</label>
                                    <input type="text" class="form-control" id="nama_organisasi">
                                </div>
                                <div class="col-md-3">
                                    <label for="jumlah_peserta" class="form-label">Jumlah Peserta</label>
                                    <input type="number" class="form-control" id="jumlah_peserta">
                                </div>
                                <div class="col-md-6">
                                    <label for="tgl_awal_peminjaman" class="form-label">Tanggal Awal</label>
                                    <input type="date" class="form-control" id="tgl_awal_peminjaman">
                                </div>
                                <div class="col-md-6">
                                    <label for="tgl_akhir_peminjaman" class="form-label">Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="tgl_akhir_peminjaman">
                                </div>
                                <div class="col-md-6">
                                    <label for="waktu_awal_peminjaman" class="form-label">Waktu Awal</label>
                                    <input type="time" class="form-control" id="waktu_awal_peminjaman">
                                </div>
                                <div class="col-md-6">
                                    <label for="waktu_akhir_peminjaman" class="form-label">Waktu Akhir</label>
                                    <input type="time" class="form-control" id="waktu_akhir_peminjaman">
                                </div>
                                <div class="col-md-12 ">
                                    <label for="deskripsi_sewa_tempat" class="form-label">Deskripsi Kegiatan</label>
                                    <textarea class="form-control" id="deskripsi_sewa_tempat"
                                        style="height: 100px;"></textarea>
                                </div>
                                <div class="col-12">
                                    <label for="surat_keterangan" class="form-label">Surat Keterangan</label>
                                    <input type="file" class="form-file-input form-control" id="surat_keterangan">
                                </div>
                            </form>

                            <br><br>

                            <div class="col-lg-12 col-md-4">
                                <div class="card success-card revenue-card">
                                    <div class="card-body">
                                        <h6><strong>DENGAN PENGAJUAN FORMULIR INI, ANDA MENYETUJUI HAL- HAL BERIKUT
                                                :</strong></h6>
                                        <br>
                                        <h6>
                                            <ol start="1">
                                                <li>Peminjaman tempat ini bersifat gratis dan tidak dipungut biaya.
                                                </li>
                                                <li>Harus memiliki surat keterangan dari desa atau instansi.</li>
                                                <li>Mengajukan surat permohonan peminjaman minimal 1 hari sebelum tempat
                                                    digunakan.</li>
                                                <li>Persetujuan atau penolakan surat permohonan peminjaman dapat dilihat
                                                    paling lambat 1 x 24 jam setelah surat permohonan diajukan.</li>
                                                <li>Surat peminjaman tempat ini berlaku satu kali pemakaian tempat yang
                                                    dipinjam.</li>
                                                <li>Dilarang menggunakan tempat untuk kegiatan yang bertentangan dengan
                                                    kepribadian Bangsa Indonesia.</li>
                                                <li>Tidak melanggar tata tertib dan bertentangan dengan norma-norma
                                                    agama.</li>
                                                <li>Ruangan atau tempat yang dipinjam untuk kegiatan harus dijaga dan
                                                    dipelihara dengan sebaik-baiknya.</li>
                                            </ol>
                                        </h6>
                                    </div>
                                </div>
                            </div>

        </section>


    </main><!-- End #main -->
    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <?php include(__DIR__.'/../footer.php');
        ?>
    </footer>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $tPath; ?>/public/assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="<?php echo $tPath; ?>/public/assets/vendor/tinymce/tinymce.min.js"></script>

    <!-- Template Main JS File -->
    <script src="<?php echo $tPath; ?>/public/assets/js/main.js"></script>

</body>

</html>