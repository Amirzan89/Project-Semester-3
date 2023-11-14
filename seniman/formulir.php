<?php
require_once(__DIR__.'/../web/koneksi.php');
require_once(__DIR__.'/../web/authenticate.php');
require_once(__DIR__.'/../env.php');
loadEnv();
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
  if(!in_array($userAuth['role'],['super admin','admin seniman'])){
    echo "<script>alert('Anda bukan admin seniman !')</script>";
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
  <link href="<?php echo $tPath; ?>/public/assets/img/favicon.png" rel="icon">
  <link href="<?php echo $tPath; ?>/public/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <!-- <link href="https://fonts.gstatic.com" rel="preconnect"> -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/assets/vendor/simple-datatables/style.css" rel="stylesheet">


  <!-- Template Main CSS File -->
  <link href="<?php echo $tPath; ?>/public/assets/css/nomor-induk.css" rel="stylesheet">

</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <?php include(__DIR__.'/../header.php');
    ?>
  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <?php
      $nav = 'seniman';
      include(__DIR__.'/../sidebar.php');
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
          <li class="breadcrumb-item active">Formulir Pendaftaran</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title mb-3 mt-3"> Formulir Registrasi Nomor Induk Seniman</h5>

              <!-- Multi Columns Form -->
              <form class="row g-3">
                <div class="col-md-12">
                  <label for="nik" class="form-label">Nomor Induk Kependudukan</label>
                  <input type="text" class="form-control" id="nik" readonly>
                </div>
                <div class="col-md-12">
                  <label for="nama_seniman" class="form-label">Nama Lengkap</label>
                  <input type="text" class="form-control" id="nama_seniman" readonly>
                </div>
                <div class="col-mb-3 mt-0">
                  <label for="jenis_kelamin" class="col-md-12 pt-3 col-form-label">Jenis Kelamin</label>
                  <div class="col-md-6">
                    <select class="form-select" aria-label="Default select example">
                      <option selected>Pilih Jenis Kelamin</option>
                      <option value="laki-laki">Laki-laki</option>
                      <option value="perempuan">Perempuan</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-8">
                  <label for="tempat_lahir" class="form-label">Tempat lahir</label>
                  <input type="text" class="form-control" id="tempat_lahir" readonly>
                </div>
                <div class="col-md-4">
                  <label for="tanggal_lahir" class="form-label">Tanggal lahir</label>
                  <input type="date" class="form-control" id="tanggal_lahir" readonly>
                </div>
                <div class="col-md-6 mt-0">
                <label for="jenis_kelamin" class="col-md-12 pt-3 col-form-label">Kecamatan</label>
                <select class="form-select" aria-label="Default select example">
                      <option selected>Pilih Kecamatan</option>
                      <option value="laki-laki">Laki-laki</option>
                      <option value="perempuan">Perempuan</option>
                    </select>
                </div>
                <div class="col-md-12 ">
                  <label for="alamat_seniman" class="form-label">Alamat</label>
                  <textarea class="form-control" id="alamat_seniman" style="height: 100px;" readonly></textarea>
                </div>
                <div class="col-md-12">
                  <label for="no_telpon" class="form-label">Nomor Telepon</label>
                  <input type="text" class="form-control" id="no_telpon" readonly>
                </div>
                <div class="col-mb-3 mt-0">
                  <label for="jenis_kelamin" class="col-md-12 pt-3 col-form-label">Kategori Seni</label>
                  <div class="col-md-6">
                    <select class="form-select" aria-label="Default select example">
                      <option selected>Pilih Kategori Seni</option>
                      <option value="laki-laki">Laki-laki</option>
                      <option value="perempuan">Perempuan</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-8">
                  <label for="nama_organisasi" class="form-label">Nama Organisasi</label>
                  <input type="text" class="form-control" id="nama_organisasi" readonly>
                </div>
                <div class="col-md-4">
                  <label for="jumlah_anggota" class="form-label">Jumlah Anggota</label>
                  <input type="number" class="form-control" id="jumlah_anggota" readonly>
                </div>
                <div class="col-md-12">
                  <label for="surat_keterangan" class="form-label">Surat Keterangan</label>
                  <input type="file" class="form-file-input form-control" id="surat_keterangan" disabled>
                </div>
                <div class="col-md-12">
                  <label for="ktp_seniman" class="form-label">Foto Kartu Tanda Penduduk</label>
                  <input type="file" class="form-file-input form-control" id="ktp_seniman" disabled>
                </div>
                <div class="col-md-12">
                  <label for="pass_foto" class="form-label">Pas Foto 3x4</label>
                  <input type="file" class="form-file-input form-control" id="pass_foto" disabled>
                </div>
            </form>
            <br><br>

            <div class="col-lg-12 col-md-4">
              <div class="card success-card revenue-card">
                <div class="card-body">
                  <h6><strong>DENGAN PENGAJUAN FORMULIR INI, ANDA MENYETUJUI HAL- HAL BERIKUT :</strong></h6>
                  <br>
                  <h6>
                    <ol start="1">
                      <li>Dokumen yang disertakan sudah sesuai dengan persyaratan yang ada. </li>
                      <li>Nomor Induk Seniman hanya berlaku per 31 Desember tiap tahunnya, silahkan lakukan perpanjangan setelahnya.</li>
                      <li>Apabila data tidak setujui silahkan lakukan pengajuan ulang.</li>
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

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/tinymce/tinymce.min.js"></script>

  <!-- Template Main JS File -->
  <script src="<?php echo $tPath; ?>/public/assets/js/main.js"></script>

</body>

</html>