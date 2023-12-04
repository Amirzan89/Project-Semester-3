<?php 
require_once(__DIR__.'/web/koneksi.php');
require_once(__DIR__.'/web/authenticate.php'); 
require_once(__DIR__.'/env.php');
loadEnv();
$db = koneksi::getInstance();
$con = $db->getConnection();
$userAuth = authenticate($_POST,[
      'uri'=>$_SERVER['REQUEST_URI'],
      'method'=>$_SERVER['REQUEST_METHOD'
    ]
],$con);
if($userAuth['status'] == 'success'){
  $userAuth = $userAuth['data'];
  if(!in_array($userAuth['role'],['super admin','admin seniman','admin tempat','admin sewa','admin pentas'])){
    header('Location: /dashboard.php');
  }
  $tPath = ($_SERVER['APP_ENV'] == 'local') ? '' : $_SERVER['APP_FOLDER'];
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
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?php echo $tPath; ?>/public/assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?php echo $tPath; ?>/public/assets/css/LandingPage.css" rel="stylesheet">
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top ">
    <div class="container d-flex align-items-center">
      <h1 class="logo me-auto"><a href="/home.php">DISPORABUDPAR</a></h1>
      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto active" href="#hero">Beranda</a></li>
          <li><a class="nav-link scrollto" href="#event">Event</a></li>
          <li><a class="nav-link scrollto" href="#about">Informasi</a></li>
          <li><a class="nav-link scrollto" href="#layanan">Layanan</a></li>
          <li><a class="nav-link   scrollto" href="#profil">Profil</a></li>
          <li><a class="getstarted scrollto" href="login.php">Masuk</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>
    </div>
  </header>
  <!-- End Header -->

  <!-- ======= Hero Section ======= -->
  <section id="hero" class="d-flex align-items-center">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 d-flex flex-column justify-content-center pt-4 pt-lg-0 order-2 order-lg-1"
          data-aos="fade-up" data-aos-delay="200">
          <h1><b>DINAS KEPEMUDAAN, <br> OLAHRAGA, KEBUDAYAAN, <br> DAN PARIWISATA</b></h1>
          <br>
          <h2>Selamat datang di situs web DISPORABUDPAR Kabupaten Nganjuk!
            Di sini, Anda dapat mengakses informasi agenda kegiatan dan berbagai layanan yang kami sediakan.
            Kami telah mengembangkan Aplikasi Nganjuk Elok untuk meningkatkan efisiensi dan kecepatan pelayanan kepada
            masyarakat,
            sehingga masyarakat dapat dengan lebih mudah menikmati layanan kami.</h2>
          <div class="d-flex justify-content-center justify-content-lg-start">
            <a href="" class="btn-get-started scrollto">Unduh Aplikasi</a>
          </div>
        </div>
        <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-in" data-aos-delay="200">
          <img src="<?php echo $tPath; ?>/public/assets/img/LandingPage/hero.png" class="img-fluid animated" alt="">
        </div>
      </div>
    </div>

  </section>
  <!-- End Hero -->

  <main id="main">

  <!-- ======= Hero 2 Section ======= -->
  <section id="" class="skills">
    <div class="container" data-aos="fade-up">

      <div class="row">
        <div class="col-lg-6 d-flex align-items-center" data-aos="fade-right" data-aos-delay="100">
          <img src="<?php echo $tPath; ?>/public/assets/img/LandingPage/hero2.png" class="img-fluid" alt="">
        </div>
        <div class="col-lg-6 pt-4 pt-lg-0 content" data-aos="fade-left" data-aos-delay="100">
          <br>
          <br>
          <h3><strong>APA ITU Nganjuk Elok?</strong></h3>
          <br>
          <p class="fst-italic">
            Nganjuk Elok merupakan sebuah platform aplikasi berbasis mobile yang terintegrasi dengan website, berfungsi
            sebagai
            platform untuk mengakses dan memanfaatkan layanan yang dikelola oleh Dinas Kepemudaan, Olahraga,
            Kebudayaan, dan Pariwisata Kabupaten Nganjuk dengan lebih efisien.
            Melalui aplikasi ini, masyarakat dapat mengajukan pelayanan secara online, menggantikan proses manual yang
            sebelumnya dilakukan di kantor dinas.
            Tujuan utama aplikasi ini adalah untuk memfasilitasi kolaborasi antara petugas dinas dan masyarakat,
            memungkinkan pelayanan yang lebih mudah,
            dan mengurangi potensi kesalahan serta pemalsuan dalam proses pelayanan.
          </p>
        </div>
      </div>
    </div>
  </section>
  <!-- End Hero 2 Section -->

    <!-- ======= Event ======= -->
    <section id="event" class="about">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>EVENT</h2>
        </div>
        <div class="row content">
          <div class="row row-cols-1 row-cols-md-3 g-3">
          <?php 
            $query = mysqli_query($con, "SELECT events.id_detail, nama_event, deskripsi, tempat_event, DATE_FORMAT(tanggal_awal, '%d %M %Y')AS tanggal_awal, DATE_FORMAT(tanggal_akhir, '%d %M %Y') AS tanggal_akhir, poster_event FROM events INNER JOIN detail_events ON events.id_detail = detail_events.id_detail WHERE status = 'diterima' ORDER BY ABS(TIMESTAMPDIFF(SECOND, NOW(), tanggal_awal)) ASC LIMIT 3");
            while ($events = mysqli_fetch_array($query)) {
          ?>
            <div class="col">
              <div class="card">
              <img src="<?php echo $tPath; ?>/DatabaseMobile/uploads/event<?php echo $events['poster_event']?>" class="card-img-top" alt="Hollywood Sign on The Hill" />
                <div class="card-body">
                  <h5 class="card-title"><?php echo $events['nama_event']?></h5>
                  <p class="card-text">
                    Tanggal Pelaksanaan : <?php echo $events['tanggal_awal']?>
                    <br>
                    Tempat : <?php echo $events['tempat_event'] ?>
                  </p>
                </div>
              </div>
            </div>
          <?php } ?>
          </div>
          <a href="/home1.php" class="btn-learn-more">Lainnya</a>
        </div>

      </div>
    </section>
    <!-- End Event Section -->

    <!-- ======= Tempat Section ======= -->
    <section id="about" class="portfolio">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>Gedung / Tempat Wisata</h2>
          <p>Gedung dan Tempat Wisata Yang dikelola oleh Dinas Kepemudaan, Olahraga, Kebudayaan dan Pariwisata Kab.
            Nganjuk</p>
        </div>

        <div class="row portfolio-container" data-aos="fade-up" data-aos-delay="200">
          <?php
          $query = mysqli_query($con, "SELECT id_tempat, nama_tempat, alamat_tempat, foto_tempat FROM list_tempat");
          while ($tempat = mysqli_fetch_array($query)) {
          ?>
            <div class="col-lg-4 col-md-6 portfolio-item filter-web">
              <div class="portfolio-img"><img src="<?php echo $tPath; ?>/public/img/tempat<?php echo $tempat['foto_tempat']?>" class="img-fluid" alt=""></div>
              <div class="portfolio-info">
                <h4><?php echo $tempat['nama_tempat']?></h4>
                <!-- <p>Gedung</p> -->
                <a href="<?php echo $tPath; ?>/public/img/tempat<?php echo $tempat['foto_tempat']?>" data-gallery="portfolioGallery"
                  class="portfolio-lightbox preview-link" title="Balai Budaya"><i class="bx bx-plus"></i></a>
                <a href="/home2.php?id_tempat=<?= $tempat['id_tempat'] ?>" class="details-link" title="Selengkapnya"><i class="bx bx-link"></i></a>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </section>
    <!-- End Tempat Section -->

    <!-- ======= Layanan Section ======= -->
    <section id="layanan" class="team section-bg">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>LAYANAN</h2>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="member d-flex align-items-start" data-aos="zoom-in" data-aos-delay="100">
              <div class="pic"><img src="<?php echo $tPath; ?>/public/assets/img/LandingPage/layanan1.png" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>Upload Event</h4>
                <p>Masyrakar dapat mengupload event atau kegiatan yang akan dilaksanakan.</p>
                <br>
                <div>
                  <a href="/home3.php" class="btn-baca">Baca Selengkapnya</a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6 mt-4 mt-lg-0">
            <div class="member d-flex align-items-start" data-aos="zoom-in" data-aos-delay="200">
              <div class="pic"><img src="<?php echo $tPath; ?>/public/assets/img/LandingPage/layanan2.png" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>Peminjaman Gedung/Tempat</h4>
                <p>Masyarakat dapat mengajukan peminjaman gedung dan tempat wisata yang dikelola oleh DISPARPORABUD.
                </p>
                <br>
                <div>
                  <a href="/home5.php" class="btn-baca">Baca Selengkapnya</a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6 mt-4">
            <div class="member d-flex align-items-start" data-aos="zoom-in" data-aos-delay="300">
              <div class="pic"><img src="<?php echo $tPath; ?>/public/assets/img/LandingPage/layanan3.png" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>Nomer Induk Seniman</h4>
                <p>Masyarakat dapat mendaftarkan nomer induk seniman dan memperpanjang masa berlaku kartu.</p>
                <br>
                <div>
                  <a href="/home4.php" class="btn-baca">Baca Selengkapnya</a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6 mt-4">
            <div class="member d-flex align-items-start" data-aos="zoom-in" data-aos-delay="400">
              <div class="pic"><img src="<?php echo $tPath; ?>/public/assets/img/LandingPage/layanan4.png" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>Surat Advis / Izin Pentas</h4>
                <p>Masyarakat dapat mengajukan surat advis / surat perizinan pentas kesenian.</p>
                <br>
                <div>
                  <a href="/home6.php" class="btn-baca">Baca Selengkapnya</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- End Layanan Section -->
    <!-- ======= Profil Section ======= -->
    <section id="profil" class="contact">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>LOKASI KANTOR DINAS</h2>
        </div>
        <div class="row">
          <div class="col-lg-12 d-flex align-items-stretch">
            <div class="info">
              <div class="address">
                <i class="bi bi-geo-alt"></i>
                <h4>Alamat</h4>
                <p>Mangundikaran, Mangun Dikaran, Kec. Nganjuk, Kabupaten Nganjuk, Jawa Timur 64419</p>
              </div>
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3950.270853382954!2d111.9027164!3d-7.601066!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e784ba9d9447a99:0x1e4f0169e2940678!2sGedung+Balai+Budaya+Mpu+Sendok!5e0!3m2!1sen!2sid!4v1666513915249" frameborder="0" style="border:0; width: 100%; height: 290px;" allowfullscreen></iframe>
            </div>
          </div>
        </div>
      </div>
    </section><!-- End Profil Section -->
  </main><!-- End #main -->
  <!-- ======= Footer ======= -->
  <footer id="footer">
    <div class="footer-top">
      <div class="container">
        <div class="row">

          <div class="col-lg-8 col-md-6 footer-contact">
            <h3>DISPORABUDPAR</h3>
            <p>
              Kabupaten Nganjuk <br>
              Jawa Timur<br>
              64419 <br><br>
            </p>
          </div>

          <div class="col-lg-4 col-md-6 footer-links">
            <h4>Kontak</h4>
            <div class="social-links mt-3">
              <a href="https://mail.google.com/mail/?view=cm&fs=1&to=disporabudpar@nganjukkab.co.id" class="twitter"><i class="bx bi-envelope"></i></a>
              <strong>disporabudpar@nganjukkab.co.id</strong> <br><br>
              <a href="https://wa.me/628113319289" class="facebook"><i class="bx bi-phone"></i></a>
              <strong>+62 8113319289</strong> <br><br>
              <a href="http://disporabudpar.nganjukkab.co.id" class="instagram"><i class="bx bxl-instagram"></i></a>
              <strong>@disporabudpar.nganjuk </strong>
            </div>
          </div>

        </div>
      </div>
    </div>

    <div class="container footer-bottom clearfix">
      <div class="copyright">
        &copy; Copyright <strong><span>HufflePuff</span></strong>. All Rights Reserved
      </div>
      <div class="credits">
        Designed by <a href="https://www.instagram.com/hufflepuff.ie">HufflePuff</a>
      </div>
    </div>
  </footer>
  <!-- End Footer -->

  <div id="preloader"></div>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="<?php echo $tPath; ?>/public/assets/vendor/aos/aos.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="<?php echo $tPath; ?>/public/assets/js/LandingPage.js"></script>
  
</body>

</html>