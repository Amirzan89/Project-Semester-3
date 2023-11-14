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
  if(!in_array($userAuth['role'],['super admin','admin tempat'])){
    echo "<script>alert('Anda bukan admin tempat !')</script>";
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
  <link href="<?php echo $tPath; ?>/public/img/icon/utama/logo.png" rel="icon">
  <!-- Google Fonts -->
  <!-- <link href="https://fonts.gstatic.com" rel="preconnect"> -->
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/assets/vendor/simple-datatables/style.css" rel="stylesheet">


  <!-- Template Main CSS File -->
  <link href="<?php echo $tPath; ?>/public/assets/css/tempat.css" rel="stylesheet">

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
    <?php include(__DIR__.'/../header.php');
    ?>
  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <?php 
        $nav = 'tempat';
        include(__DIR__.'/../sidebar.php');
      ?>
    </ul>
  </aside><!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
        <h1>Tambah Tempat</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
            <li class="breadcrumb-item"><a href="/tempat.php">Kelola Tempat</a></li>
            <li class="breadcrumb-item"><a href="/tempat/data_tempat.php">Data tempat</a></li>
            <li class="breadcrumb-item active">Tambah Data Tempat</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->
    <section class="section dashboard">
      <div class="row">
        <div class="row align-items-top">
            <div class="col-lg-12">
              <!-- Default Card -->
              <div class="card">
                <div class="card-body"> <br>
                  <form action="/web/tempat/tempat.php" method="POST" class="row" enctype="multipart/form-data">
                  <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user']; ?>">
                    <div class="col-md-6">
                      <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                          <div class="carousel-item active">
                            <img src="assets/img/slides-1.jpg" class="d-block w-100" alt="...">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label for="inputText"><strong>Nama Tempat</strong></label>
                      <div class="row mb-3">
                        <div class="col-sm-12">
                          <input type="text" name="nama_tempat" class="form-control" placeholder="Masukkan Nama Tempat">
                        </div>
                      </div>
                      <label for="inputText"><strong>Alamat Tempat</strong></label>
                      <div class="row mb-3">
                        <div class="col-sm-12">
                          <input type="text" name="alamat" class="form-control" placeholder="Masukkan Alamat Tempat">
                        </div>
                      </div>
                      <label for="inputText"><strong>Contact Person</strong></label>
                      <div class="row mb-3">
                        <div class="col-sm-12">
                          <input type="text" name="phone" class="form-control" placeholder="Masukkan Contact Person">
                        </div>
                      </div>
                      <label for="inputText"><strong>Deskripsi Tempat</strong></label>
                      <div class="col-sm-12">
                        <textarea class="form-control" name="deskripsi" style="height: 80px" placeholder="Masukkan Deskripsi Tempat"></textarea>
                      </div>
                      <div class="row mb-3">
                          <label for="inputNumber" class="col-sm-2 col-form-label">Gambar tempat</label>
                          <div class="col-sm-10">
                              <input class="form-control" type="file" id="formFile" name="foto">
                          </div>
                      </div> <br>
                      <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                  </form>
                </div>
              </div><!-- End Default Card -->
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