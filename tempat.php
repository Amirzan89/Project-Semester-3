<?php
require_once(__DIR__.'web/koneksi.php');
require_once(__DIR__.'web/authenticate.php');
require_once(__DIR__.'env.php');
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
  // if($userAuth['role'] != 'super admin'){
  //   echo "<script>alert('Anda bukan super admin !')</script>";
  //   echo "<script>window.location.href = '/dashboard.php';</script>";
  //   exit();
  // }
  $tPath = ($_SERVER['APP_ENV'] == 'local') ? '' : '/public';
}
$csrf = $GLOBALS['csrf'];

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
  <link href="/public/img/icon/utama/logo.png" rel="icon">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet" />
  <!-- Vendor CSS Files -->
  <link href="/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
  <link href="/public/assets/vendor/simple-datatables/style.css" rel="stylesheet" />

  <!-- Template Main CSS File -->
  <link href="/public/assets/css/nomor-induk.css" rel="stylesheet" />
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
    <?php include(__DIR__.'header.php');
    ?>
  </header>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <?php
      $nav = 'tempat';
      include(__DIR__.'sidebar.php');
      ?>
    </ul>
  </aside>
  <!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Kelola Tempat</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
          <li class="breadcrumb-item active">Kelola Tempat</li>
        </ol>
      </nav>
    </div>
    <!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">
        <div class="col-lg-12">
          <div class="row">
            <div class="col-xxl-4 col-md-4">
              <div class="card success-card revenue-card"><a href="/tempat/formulir-sewa.php">
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
            <div class="card success-card revenue-card"><a href="/tempat/pengajuan.php">
                <div class="card-body">
                  <h5 class="card-title">Verifikasi Peminjaman</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-bell-fill"></i>
                    </div>
                    <div class="ps-3">
                      <?php
                      $sql = mysqli_query($conn, "SELECT COUNT(*) AS total FROM sewa_tempat WHERE status = 'diajukan' OR status = 'proses'");
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
          <div class="card success-card revenue-card"><a href="/tempat/riwayat.php">
              <div class="card-body">
                <h5 class="card-title">Riwayat</h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-clock-fill"></i>
                  </div>
                  <div class="ps-3">
                    <?php
                    $sql = mysqli_query($conn, "SELECT COUNT(*) AS total FROM sewa_tempat WHERE status = 'diterima' OR status = 'ditolak'");
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
          <div class="card success-card revenue-card"><a href="/tempat/data_tempat.php">
              <div class="card-body">
                <h5 class="card-title">Data Tempat</h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-folder-fill"></i>
                  </div>
                  <div class="ps-3">
                    <?php
                    $sql = mysqli_query($conn, "SELECT COUNT(*) AS total FROM list_tempat");
                    $data = mysqli_fetch_assoc($sql);
                    echo "<h4>" . $data['total'] . "</h4>";
                    ?>
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
    <?php include(__DIR__.'footer.php');
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