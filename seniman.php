<?php
require_once(__DIR__.'/web/koneksi.php');
require_once(__DIR__.'/web/authenticate.php');
require_once(__DIR__.'/env.php');
loadEnv();
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
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />

  <title>Disporabudpar - Nganjuk</title>
  <meta content="" name="description" />
  <meta content="" name="keywords" />

  <!-- Favicons -->
  <link href="<?php echo $tPath; ?>/public/img/icon/utama/logo.png" rel="icon">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet" />
  <!-- Vendor CSS Files -->
  <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
  <link href="<?php echo $tPath; ?>/public/assets/vendor/simple-datatables/style.css" rel="stylesheet" />

  <!-- Template Main CSS File -->
  <link href="<?php echo $tPath; ?>/public/assets/css/nomor-induk.css" rel="stylesheet" />
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
    <?php include(__DIR__.'/header.php');
        ?>
  </header>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <?php
        $nav = 'seniman'; 
        include(__DIR__.'/sidebar.php');
      ?>
    </ul>
  </aside>
  <!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Kelola Seniman</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
          <li class="breadcrumb-item active">Kelola Seniman</li>
        </ol>
      </nav>
    </div>
    <!-- End Page Title -->

    <section class="section dashboard">
    <div class="row">
        <div class="col-lg-12">
          <div class="row">
            <div class="col-xxl-4 col-md-4">
              <div class="card success-card revenue-card"><a href="/seniman/formulir.php">
                  <div class="card-body">
                    <h5 class="card-title">Formulir</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-file-earmark-text-fill"></i>
                      </div>
                    </div>
                </a>
              </div>
            </div>
          </div>
          <div class="col-xxl-4 col-md-4">
            <div class="card success-card revenue-card"><a href="/seniman/pengajuan.php">
                <div class="card-body">
                  <h5 class="card-title">Verifikasi registrasi</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-bell-fill"></i>
                    </div>
                    <div class="ps-3">
                      <?php
                      $sql = mysqli_query($conn, "SELECT COUNT(*) AS total FROM seniman WHERE status = 'diajukan' OR status = 'proses'");
                      $data = mysqli_fetch_assoc($sql);
                      echo "<h4>" . $data['total'] . "</h4>";
                      ?>
                    </div>
                  </div>
              </a>
            </div>
          </div>
        </div>
        <div class="col-xxl-4 col-md-4">
          <div class="card success-card revenue-card"><a href="/seniman/perpanjangan.php">
              <div class="card-body">
                <h5 class="card-title">Verifikasi perpanjangan</h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-clock-fill"></i>
                  </div>
                  <div class="ps-3">
                    <?php
                    $sql = mysqli_query($conn, "SELECT COUNT(*) AS total FROM perpanjangan WHERE status = 'diajukan' OR status = 'proses'");
                    $data = mysqli_fetch_assoc($sql);
                    echo "<h4>" . $data['total'] . "</h4>";
                    ?>
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xxl-4 col-md-4">
          <div class="card success-card revenue-card"><a href="/seniman/riwayat.php">
              <div class="card-body">
                <h5 class="card-title">Riwayat</h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-clock-fill"></i>
                  </div>
                  <div class="ps-3">
                    <?php
                    $sql = mysqli_query($conn, "SELECT COUNT(*) AS total FROM seniman WHERE status = 'diterima' OR status = 'ditolak'");
                    $data = mysqli_fetch_assoc($sql);
                    echo "<h4>" . $data['total'] . "</h4>";
                    ?>
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>
      <div class="col-xxl-4 col-md-4">
        <div class="card success-card revenue-card"><a href="/seniman/data_seniman.php">
            <div class="card-body">
              <h5 class="card-title">Data Seniman</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-folder-fill"></i>
                </div>
              </div>
          </a>
        </div>
      </div>
      </div>
    </section>
  </main>
  <!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <?php include(__DIR__.'/footer.php');
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