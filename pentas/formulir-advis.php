<?php
require_once('../web/koneksi.php');
require_once('../web/authenticate.php');
$database = koneksi::getInstance();
$conn = $database->getConnection();
$userAuth = authenticate($_POST,[
  'uri'=>$_SERVER['REQUEST_URI'],
  'method'=>$_SERVER['REQUEST_METHOD']
],$conn);
if($userAuth['status'] == 'error'){
	header('Location: /login.php');
}else{
	$userAuth = $userAuth['data'];
  // if($userAuth['role'] != 'super admin'){
  //   echo "<script>alert('Anda bukan super admin !')</script>";
  //   echo "<script>window.location.href = '/dashboard.php';</script>";
  //   exit();
  // }
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

  <!-- Template Main CSS File -->
  <link href="/public/assets/css/style.css" rel="stylesheet">

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

    <?php
    include('../header.php');
    ?>

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">
        <?php
        $nav = "pentas";
        include('../sidebar.php');
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
              <h5 class="card-title">SURAT ADVIS PENYELENGGARAAN PERTUNJUKAN KESENIAN</h5>
                <p>
                Lampiran VI 2 Peraturan Bupati Nganjuk Nomor : 28 Tahun 2021 Tanggal, 21 September 2021, Tentang Rincian Tugas, Fungsi dan Tata Kerja Dinas Kepemudaan, Olahraga, Kebudayaan dan Pariwisata Daerah Kabupaten Nganjuk, Maka Kepala Dinas Kepemudaan, Olahraga, Kebudayaan dan Pariwisata Kabupaten Nganjuk setelah memperhatikan permohonan dari :
                </p>

              <form method="POST" action="../users/proses-tambah-user.php" >
                <!-- <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">ID USER</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" value="Read only / Disabled" disabled>
                  </div>
                </div> -->
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Nama Pemohon</label>
                  <div class="col-sm-10">
                  <input type="text" class="form-control" name="nama" placeholder="Nama Pemohon">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputtext" class="col-sm-2 col-form-label">Alamat</label>
                  <div class="col-sm-10">
                    <textarea class="form-control" style="height: 100px"></textarea>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Untuk Pentas</label>
                  <div class="col-sm-10">
                  <input type="text" class="form-control" name="phone" placeholder="Untuk Pentas">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputDate" class="col-sm-2 col-form-label">Tanggal Lahir</label>
                  <div class="col-sm-10">
                    <input type="date" class="form-control" name="tanggalL" placeholder="Tanggal">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Bertempat di</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="tempatL" placeholder="contoh : Balai Budaya">
                  </div>
                </div>

              </form><!-- End General Form Elements -->

              <p>
              Menyatakan tidak keberatan memberikan Surat Advis sebagai pelengkap Surat Induk nomor ... untuk mendapatkan ijin keramaian dari kepolisian. Surat Advis ini berlaku tgl ... (Satu kali pentas). Pementasan kesenian tanpa Surat Advis merupakan pelanggaran Peraturan Daerah.
              </p>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main>
  <!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Huffle Puff</span></strong>. All Rights Reserved
    </div>
  </footer>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="/public/assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Template Main JS File -->
    <script src="/public/assets/js/main.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var currentPageURL = window.location.href;
        var menuLinks = document.querySelectorAll('.nav-link');
        menuLinks.forEach(function (menuLink) {
          var menuLinkURL = menuLink.getAttribute('href');
          if (currentPageURL === menuLinkURL) {
            menuLink.parentElement.classList.add('active');
          }
        });
      });

    </script>
</body>

</html>