<?php
require_once('../web/koneksi.php');
require_once('../web/authenticate.php');
$database = koneksi::getInstance();
$conn = $database->getConnection();
$userAuth = authenticate($_POST, [
    'uri' => $_SERVER['REQUEST_URI'],
    'method' => $_SERVER['REQUEST_METHOD']
], $conn);
if ($userAuth['status'] == 'error') {
    header('Location: /login.php');
} else {
    $userAuth = $userAuth['data'];
    if (isset($_GET['id_seniman']) && !empty($_GET['id_seniman'])) {
        $id = $_GET['id_seniman'];
        $sql = mysqli_query($conn, "SELECT id_seniman, nik, nomor_induk, nama_seniman, jenis_kelamin, tempat_lahir, DATE_FORMAT(tanggal_lahir, '%d %M %Y') AS tanggal_lahir, alamat_seniman, no_telpon, nama_organisasi, jumlah_anggota, status, catatan FROM seniman WHERE id_seniman = '$id'");
        $seniman = mysqli_fetch_assoc($sql);
    } else {
        header('Location: /seniman.php');
    }
}
$csrf = $GLOBALS['csrf'];
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
    <link href="/public/img/icon/utama/logo.png" rel="icon">

    <!-- Google Fonts -->
    <!-- <link href="https://fonts.gstatic.com" rel="preconnect"> -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">
    <!-- Vendor CSS Files -->
    <link href="/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="/public/assets/vendor/simple-datatables/style.css" rel="stylesheet">


    <!-- Template Main CSS File -->
    <link href="/public/assets/css/nomor-induk.css" rel="stylesheet">

</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
        <?php include('../header.php');
        ?>
    </header><!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            <?php
            $nav = 'seniman';
            include('../sidebar.php');
            ?>
        </ul>
    </aside><!-- End Sidebar-->

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Formulir Pendaftaran</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="/seniman.php">Kelola Seniman</a></li>
                    <?php if ($seniman['status'] == 'diajukan' || $seniman['status'] == 'proses') { ?>
                        <li class="breadcrumb-item"><a href="/seniman/pengajuan.php">Pengajuan Nomer Induk
                                Seniman</a></li>
                    <?php } else if ($seniman['status'] == 'diterima' || $seniman['status'] == 'ditolak') { ?>
                            <li class="breadcrumb-item"><a href="/seniman/riwayat.php">Riwayat Nomer Induk Seniman</a>
                            </li>
                    <?php } ?>
                    <li class="breadcrumb-item active">Detail event</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"></h5>
                            <!-- Multi Columns Form -->
                            <form class="row g-3">
                                <div class="col-md-12">
                                    <label for="nik" class="form-label">Nomor Induk Seniman</label>
                                    <input type="text" class="form-control" id="nik" readonly value="<?php echo $seniman['nomor_induk']?>">
                                </div>
                                <div class="col-md-12">
                                    <label for="nik" class="form-label">Nomor Induk Kependudukan</label>
                                    <input type="text" class="form-control" id="nik" readonly value="<?php echo $seniman['nik']?>">
                                </div>
                                <div class="col-md-12">
                                    <label for="nama_seniman" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama_seniman" readonly value="<?php echo $seniman['nama_seniman']?>">
                                </div>
                                <div class="col-mb-3 mt-0">
                                    <label for="jenis_kelamin" class="col-md-12 pt-3 col-form-label">Jenis
                                        Kelamin</label>
                                    <div class="col-md-6">
                                        <select class="form-select" aria-label="Default select example" disabled>
                                            <?php if($seniman['jenis_kelamin'] == 'laki-laki'){ ?>
                                                <option value="laki-laki" selected="selected">Laki-laki</option>
                                            <?php }else if($seniman['jenis_kelamin'] === 'perempuan'){ ?>
                                                <option value="perempuan" selected="selected">Perempuan</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label for="tempat_lahir" class="form-label">Tempat lahir</label>
                                    <input type="text" class="form-control" id="tempat_lahir" readonly value="<?php echo $seniman['tempat_lahir']?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="tanggal_lahir" class="form-label">Tanggal lahir</label>
                                    <input type="text" class="form-control" id="tanggal_lahir" readonly value="<?php echo $seniman['tanggal_lahir']?>">
                                </div>
                                <div class="col-md-12 ">
                                    <label for="alamat_seniman" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="alamat_seniman" placeholder="Masukkan Alamat"
                                        style="height: 100px;" readonly><?php echo $seniman['alamat_seniman']?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label for="no_telpon" class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="no_telpon" readonly value="<?php echo $seniman['no_telpon']?>">
                                </div>
                                <div class="col-md-8">
                                    <label for="nama_organisasi" class="form-label">Nama Organisasi</label>
                                    <input type="text" class="form-control" id="nama_organisasi" readonly value="<?php echo $seniman['nama_organisasi']?>" >
                                </div>
                                <div class="col-md-4">
                                    <label for="jumlah_anggota" class="form-label">Jumlah Anggota</label>
                                    <input type="number" class="form-control" id="jumlah_anggota" readonly value="<?php echo $seniman['jumlah_anggota']?>" >
                                </div>
                                <div class="col-12">
                                    <label for="surat_keterangan" class="form-label">Surat Keterangan Desa</label>
                                    <input type="file" class="form-file-input form-control" id="surat_keterangan">
                                </div>
                                <div class=" col-12">
                                    <label for="ktp_seniman" class="form-label">Foto Kartu Tanda Penduduk</label>
                                    <input type="file" class="form-file-input form-control" id="ktp_seniman">
                                </div>
                                <div class="col-12">
                                    <label for="pass_foto" class="form-label">Pass Foto 3x4</label>
                                    <input type="file" class="form-file-input form-control" id="pass_foto">
                                </div>
                                <div class="row mb-3 justify-content-end">
                                    <div class="col-sm-10 text-end">
                                        <a href="/tempat/status_peminjaman.php" class="btn btn-info"><i>kembali</i></a>
                                        <?php if($seniman['status'] == 'diajukan'){?>
                                            <a href="/tempat/edit_detail_tempat.php?id_tempat=<?= $id ?>" class="btn btn-info"><i>Proses</i></a>
                                        <?php }else if($seniman['status'] == 'proses'){?>
                                            <button type="button" class="btn btn-success">
                                                <i class="bi bi-check-circle">Setuju</i>
                                            </button>
                                            <button type="button" class="btn btn-danger">
                                                <i class="bi bi-x-circle">Tolak</i>
                                            </button>
                                        <?php }?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main><!-- End #main -->
    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <?php include('footer.php');
        ?>
    </footer>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/public/assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="/public/assets/vendor/tinymce/tinymce.min.js"></script>

    <!-- Template Main JS File -->
    <script src="/public/assets/js/main.js"></script>

</body>

</html>