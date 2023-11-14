<?php
require_once(__DIR__.'/../web/koneksi.php');
require_once(__DIR__.'/../web/authenticate.php');
require_once(__DIR__.'/../env.php');
loadEnv();
$database = koneksi::getInstance();
$conn = $database->getConnection();
$userAuth = authenticate($_POST,[
  'uri'=>$_SERVER['REQUEST_URI'],
  'method'=>$_SERVER['REQUEST_METHOD'
  ]
],$conn);
if($userAuth['status'] == 'error'){
	header('Location: /login.php');
}else{
	$userAuth = $userAuth['data'];
  if($userAuth['role'] != 'super admin'){
    echo "<script>alert('Anda bukan super admin !')</script>";
    echo "<script>window.location.href = '/dashboard.php';</script>";
    exit();
  }
  $tPath = ($_SERVER['APP_ENV'] == 'local') ? '' : $_SERVER['APP_FOLDER'];
  $csrf = $GLOBALS['csrf'];
  if (isset($_GET['id_user']) && !empty($_GET['id_user'])) {
    $id  = $_GET['id_user'];
    $sql  = mysqli_query($conn, "SELECT * FROM users WHERE id_user = '$id'");
    $users = mysqli_fetch_assoc($sql);
  }else{
    header('Location: /admin.php');
  }
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
  <link href="<?php echo $tPath; ?>/public/img/icon/utama/logo.png" rel="icon">

  <!-- Google Fonts -->
  <!-- <link href="https://fonts.gstatic.com" rel="preconnect"> -->
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?php echo $tPath; ?>/public/assets/css/style.css" rel="stylesheet">

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
    include(__DIR__.'/../header.php');
    ?>

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">
        <?php
        $nav = 'admin';
        include(__DIR__.'/../sidebar.php');
        ?>
    </ul>
  </aside><!-- End Sidebar-->

  <main id="main" class="main">

  <div class="pagetitle">
      <h1>Edit Admin</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
          <li class="breadcrumb-item"><a href="/admin.php">Kelola Admin</a></li>
          <li class="breadcrumb-item active">Edit Admin</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Edit Admin</h5>
            <form method="POST" action="/web/User.php" enctype="multipart/form-data">
              <input type="hidden" name="_method" value="PUT">
              <input type="hidden" name="id_admin" value="<?php echo $userAuth['id_user']; ?>">
              <input type="hidden" name="id_user" value="<?php echo $users['id_user']; ?>">
              <input type="hidden" name="csrf_token" value="<?php echo $csrf?>">
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Nama Lengkap</label>
                  <div class="col-sm-10">
                  <input type="text" class="form-control" name="nama" placeholder="Nama Lengkap" value="<?php echo $users['nama_lengkap']; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">No Handphone</label>
                  <div class="col-sm-10">
                  <input type="text" class="form-control" name="phone" placeholder="No Handphone" value="<?php echo $users['no_telpon']; ?>">
                  </div>
                </div>
                <fieldset class="row mb-3">
                  <legend class="col-form-label col-sm-2 pt-0">Jenis Kelamin</legend>
                  <div class="col-sm-10">
                    <div class="form-check">
                    <input class="form-check-input" type="radio" name="jenisK" value="laki-laki" <?php echo ($users['jenis_kelamin'] == 'laki-laki') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="gridRadios1">
                      Laki-Laki
                    </label>
                  </div>
                  <div class="form-check">
                      <input class="form-check-input" type="radio" name="jenisK" value="perempuan" <?php echo ($users['jenis_kelamin'] == 'perempuan') ? 'checked' : ''; ?>>
                      <label class="form-check-label" for="gridRadios2">
                        Perempuan
                      </label>
                    </div>
                  </div>
                </fieldset>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Tempat Lahir</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="tempatL" placeholder="Tempat Lahir" value="<?php echo $users['tempat_lahir']; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputDate" class="col-sm-2 col-form-label">Tanggal Lahir</label>
                  <div class="col-sm-10">
                    <input type="date" class="form-control" name="tanggalL" placeholder="Tanggal Tanggal" value="<?php echo $users['tanggal_lahir']; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Role</label>
                  <div class="col-sm-10">
                  <select class="form-select" name="role" aria-label="Default select example">
                      <option value="admin event" <?php echo ($users['role'] == 'admin event') ? 'selected' : ''; ?>>Admin Event</option>
                      <option value="admin tempat" <?php echo ($users['role'] == 'admin tempat') ? 'selected' : ''; ?>>Admin Tempat</option>
                      <option value="admin seniman" <?php echo ($users['role'] == 'admin seniman') ? 'selected' : ''; ?>>Admin Seniman</option>
                  </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                  <div class="col-sm-10">
                    <input type="email" class="form-control" name='email' placeholder="Email" value="<?php echo $users['email']; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
                  <div class="col-sm-10">
                    <input type="password" class="form-control" name='pass' placeholder="Password">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputPassword" class="col-sm-2 col-form-label">foto</label>
                  <div class="col-sm-10">
                    <input type="file" class="form-control" name='foto'>
                  </div>
                </div>
                <div class="row mb-3">
                <button type="submit" class="btn btn-success" name="editAdmin">Edit Data</button>
                </div>

              </form><!-- End General Form Elements -->

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
    <script src="<?php echo $tPath; ?>/public/assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Template Main JS File -->
    <script src="<?php echo $tPath; ?>/public/assets/js/main.js"></script>
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