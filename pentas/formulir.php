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
  if(!in_array($userAuth['role'],['super admin','admin seniman','admin pentas'])){
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
  <link href="<?php echo $tPath; ?>/public/assets/img/LandingPage/favicon.png" rel="icon">
  <link href="<?php echo $tPath; ?>/public/assets/img/LandingPage/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <!-- <link href="https://fonts.gstatic.com" rel="preconnect"> -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?php echo $tPath; ?>/public/assets/css/tempat.css" rel="stylesheet">

</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <?php
    include(__DIR__.'/../header.php');
    ?>

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">
      <?php
      $nav = "pentas";
      include(__DIR__.'/../sidebar.php');
      ?>
    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Formulir Pentas</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
          <li class="breadcrumb-item"><a href="/pentas.php">Kelola Pentas</a></li>
          <li class="breadcrumb-item active">Formulir Pentas</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title  mt-3 mb-4"><strong>
                  SURAT ADVIS
                  <br>
                  <u>PENYELENGGARAAN PERTUNJUKAN KESENIAN</u>
                </strong>
              </h5>

              <form method="POST" action="../users/proses-tambah-user.php">
                <form method="POST" action="">
                  <div class="col-md-12">
                    <label for="nik" class="form-label">Nomor Induk Seniman</label>
                    <input type="text" class="form-control" id="nis" placeholder="Masukkan Nomor Induk Seniman" readonly>
                  </div>
                  <br>
                  <div class="col-md-12">
                    <label for="nama_seniman" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama" placeholder="Masukkan Nama Lengkap" readonly>
                  </div>
                  <br>
                  <div class="col-md-12 ">
                    <label for="alamat_seniman" class="form-label">Alamat</label>
<<<<<<< HEAD
                    <textarea class="form-control" id="alamat_seniman" placeholder="Masukkan Alamat" style="height: 100px;" readonly></textarea>
=======
                    <textarea class="form-control" id="alamat_seniman" placeholder="Masukkan Alamat" readonly style="height: 100px;"></textarea>
>>>>>>> b5cf5289b030ab7a03d0ba8a356b1da0c7fe0519
                  </div>
                  <br>
                  <div class="col-md-12">
                    <label for="no_telpon" class="form-label">Untuk Pentas</label>
                    <input type="text" class="form-control" name="phone" placeholder="Contoh : Pentas Tari Tradisional" readonly>
                  </div>
                  <br>
                  <div class="col-md-12">
                    <label for="tanggal" class="form-label">Tanggal </label>
<<<<<<< HEAD
                    <input type="date" class="form-control" name="tanggalL" placeholder="Tanggal" disabled>
=======
                    <input type="date" class="form-control" name="tanggalL" placeholder="Tanggal"readonly>
>>>>>>> b5cf5289b030ab7a03d0ba8a356b1da0c7fe0519
                  </div>
                  <br>
                  <div class="col-md-12">
                    <label for="nama_organisasi" class="form-label">Bertempat Di</label>
                    <input type="text" class="form-control" name="tempatL" placeholder="contoh : Balai Budaya" readonly>
                  </div>
                </form> <br><br>

                <div class="col-lg-12 col-md-4">
                  <div class="card success-card revenue-card">
                    <div class="card-body">
                      <h6><strong>DENGAN PENGAJUAN FORMULIR INI, ANDA MENYETUJUI HAL- HAL BERIKUT :</strong></h6>
                      <br>
                      <h6>
                        <ol start="1">
                          <li>Tidak keberatan memberikan Surat Advis untuk mendapatkan ijin keramaian dari kepolisian. </li>
                          <li>Surat Advis ini berlaku satu kali pentas. </li>
                          <li>Pementasan kesenian tanpa Surat Advis merupakan pelanggaran Peraturan Daerah.</li>
                          <li>Dilarang mengadakan pementasan kesenian yang bertentangan dengan Kepribadian Bangsa Indonesia.</li>
                          <li>Tidak melanggar Tata Tertib dan bertentangan dengan norma-norma Agama.</li>
                          <li>Advis ini adalah bukti Legalitas dari Organisasi Seni / Seniman bukan sebagai Ijin Pentas</li>
                        </ol>
                      </h6>
                    </div>
                  </div>
                </div>
                <br>
                <div class="row mb-3 justify-content-end">
                  <div class="col-sm-10 text-end">
                        <a href="../pentas.php" class="btn btn-secondary">Kembali</a>
                  </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </section>


  </main>
  <!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
        <?php include(__DIR__.'/../footer.php');
        ?>
    </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="<?php echo $tPath; ?>/public/assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Template Main JS File -->
  <script src="<?php echo $tPath; ?>/public/assets/js/main.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var currentPageURL = window.location.href;
      var menuLinks = document.querySelectorAll('.nav-link');
      menuLinks.forEach(function(menuLink) {
        var menuLinkURL = menuLink.getAttribute('href');
        if (currentPageURL === menuLinkURL) {
          menuLink.parentElement.classList.add('active');
        }
      });
    });
  </script>
</body>

</html>