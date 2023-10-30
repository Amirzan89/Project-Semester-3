<?php
require_once('web/koneksi.php');
require_once('web/authenticate.php');
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
  <link href="/public/assets/vendor/simple-datatables/style.css" rel="stylesheet">


  <!-- Template Main CSS File -->
  <link href="/public/assets/css/admin.css" rel="stylesheet">

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
    <?php include('header.php');
    ?>
  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <?php 
      $nav = 'dashboard';
      include('sidebar.php');
      ?>
    </ul>
  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Beranda</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item active">Beranda</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">
        <div class="col-lg-12">
          <div class="row">
            <div class="col-lg-6 col-md-4">
              <div class="card success-card revenue-card">
                <div class="card-body">
                  <div class="d-flex align-items-center mt-3 mb-3">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="ps-1">
                      <h5 class="card-title"><a href="/admin.php"><b>Daftar Admin</b></a></h5>
                      <?php 
                        $sql  = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role != 'super admin' AND role != 'masyarakat'");
                        $data = mysqli_fetch_assoc($sql);
                        echo "<h6 class= 'notif'>".$data['total']."</h6>";
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-4">
              <div class="card success-card revenue-card">
                <div class="card-body">
                  <div class="d-flex align-items-center mt-3 mb-3">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="ps-1">
                      <h5 class="card-title"><a href="/pengguna.php"><b>Daftar Pengguna</b></a></h5>
                      <?php 
                        $sql  = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'masyarakat'");
                        $data = mysqli_fetch_assoc($sql);
                        echo "<h6 class= 'notif'>".$data['total']."</h6>";
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

            <div class="col-lg-4 col-xl-8">
              <div class="card">
                <div class="card-body mt-2 mb-5">
                  <h5 class="card-title mt-3 "><strong>Kalender</strong></h5>
                  <?php include('kalender.php');?>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <!-- Recent Activity -->
              <div class="card">
                <div class="card-body mt-3 mb-3">
                  <h5 class="card-title mb-3 "><strong>Pengajuan Terbaru </strong><span>| Hari Ini</span></h5>

                  <div class="activity">

                    <div class="activity-item d-flex">
                      <div class="activite-label"><?php echo date('d M Y') ?></div>
                      <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                      <div class="activity-content">
                        <a href="/event.php" class="fw-bold text-dark"><h6><strong>Kelola Event</strong></h6>
                          <?php 
                            $sql  = mysqli_query($conn, "SELECT COUNT(*) AS total FROM events WHERE status = 'diajukan'");
                            $data = mysqli_fetch_assoc($sql);
                            echo $data['total']." notifikasi";
                          ?>
                        </a>
                      </div>
                    </div><!-- End activity item-->

                    <div class="activity-item d-flex">
                      <div class="activite-label"><?php echo date('d M Y') ?></div>
                      <i class='bi bi-circle-fill activity-badge text-danger align-self-start'></i>
                      <div class="activity-content">
                        <a href="/tempat.php" class="fw-bold text-dark"><h6><strong>Peminjaman Tempat</strong></h6>
                          <?php 
                            $sql  = mysqli_query($conn, "SELECT COUNT(*) AS total FROM sewa_tempat WHERE status = 'diajukan'");
                            $data = mysqli_fetch_assoc($sql);
                            echo $data['total']." notifikasi";
                          ?>
                        </a>
                      </div>
                    </div><!-- End activity item-->

                    <div class="activity-item d-flex">
                      <div class="activite-label"><?php echo date('d M Y') ?></div>
                      <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                      <div class="activity-content">
                        <a href="/seniman.php" class="fw-bold text-dark"><h6><strong>Nomor Induk Seniman</strong></h6>
                          <?php 
                            $sql  = mysqli_query($conn, "SELECT COUNT(*) AS total FROM seniman WHERE status = 'diajukan'");
                            $data = mysqli_fetch_assoc($sql);
                            echo $data['total']." notifikasi";
                          ?>
                        </a>
                      </div>
                    </div><!-- End activity item-->

                    <div class="activity-item d-flex">
                      <div class="activite-label"><?php echo date('d M Y') ?></div>
                      <i class='bi bi-circle-fill activity-badge text-warning align-self-start'></i>
                      <div class="activity-content">
                        <a href="/pentas.php" class="fw-bold text-dark"><h6><strong>Surat Advis</strong></h6>
                          <?php 
                            $sql  = mysqli_query($conn, "SELECT COUNT(*) AS total FROM surat_advis WHERE status = 'diajukan'");
                            $data = mysqli_fetch_assoc($sql);
                            echo $data['total']." notifikasi";
                          ?>
                        </a>
                      </div>
                    </div><!-- End activity item-->
                  </div>

                </div>
              </div>
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