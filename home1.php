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
                <li><a class="nav-link scrollto active" href="/home.php#hero">Beranda</a></li>
                <li><a class="nav-link scrollto" href="/home.php#event">Event</a></li>
                <li><a class="nav-link scrollto" href="/home.php#about">Informasi</a></li>
                <li><a class="nav-link scrollto" href="/home.php#layanan">Layanan</a></li>
                <li><a class="nav-link   scrollto" href="/home.php#profil">Profil</a></li>
                <li><a class="getstarted scrollto" href="/login.php">Masuk</a></li>
            </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
        </div>
    </header>
    <!-- End Header -->

    <main id="main">

        <section id="event" class="about">
            <div class="container" data-aos="fade-up">

                <div class="section-title">
                    <h2>EVENT</h2>
                </div>

                <div class="row content">
                    <div class="row row-cols-1 row-cols-md-3 g-3">
                        <?php 
                            $query = mysqli_query($con, "SELECT events.id_detail, nama_event, deskripsi, tempat_event, DATE_FORMAT(tanggal_awal, '%d %M %Y')AS tanggal_awal, DATE_FORMAT(tanggal_akhir, '%d %M %Y') AS tanggal_akhir, poster_event FROM events INNER JOIN detail_events ON events.id_detail = detail_events.id_detail WHERE status = 'diterima' ORDER BY ABS(TIMESTAMPDIFF(SECOND, NOW(), tanggal_awal)) ASC");
                            while ($events = mysqli_fetch_array($query)) {
                        ?>
                        <div class="col">
                            <div class="card">
                                <img src="<?php echo $tPath; ?>/DatabaseMobile/uploads/events<?php echo $events['poster_event']?>" class="card-img-top" alt="Hollywood Sign on The Hill" />
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $events['nama_event']?></h5>
                                    <p class="card-text">
                                        Tanggal Pelaksanaan : <?php echo $events['tanggal_awal'] ?> 
                                        <br><br>
                                        Tempat : <?php echo $events['tempat_event']?>
                                        <br><br>
                                        <?php echo $events['deskripsi']?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
        </section>

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
    </footer><!-- End Footer -->

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