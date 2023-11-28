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
    if (isset($_GET['id_tempat']) && !empty($_GET['id_tempat'])) {
        $id  = $_GET['id_tempat'];
        $sql  = mysqli_query($conn, "SELECT * FROM list_tempat WHERE `id_tempat` = '" . $id . "'");
        $tempat = mysqli_fetch_assoc($sql);
    }else{
        header('Location: /tempat/data_tempat.php');
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
            <h1>Edit Data Tempat</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="/tempat.php">Kelola Tempat</a></li>
                    <li class="breadcrumb-item"><a href="/tempat/data_tempat.php?id_tempat=<?php $id;?>">Data tempat</a></li>
                    <li class="breadcrumb-item active">Edit Data Tempat</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <h5 class="card-title text-center">Edit Data Tempat</h5>
                            </div>
                            <form method="POST" action="/web/tempat/tempat.php" enctype="multipart/form-data">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user']; ?>">
                                <input type="hidden" name="id_tempat" value="<?php echo $id; ?>">
                                <div class="col mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">Nama Tempat</label>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="nama_tempat" value="<?php echo $tempat['nama_tempat']?>">
                                    </div>
                                </div>
                                <div class="col mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">Alamat Tempat</label>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="alamat" value="<?php echo $tempat['alamat_tempat']?>">
                                    </div>
                                </div>
                                <div class="col mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">Nama Pengelola</label>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="phone" value="<?php echo $tempat['pengelola']?>">
                                    </div>
                                </div>
                                <div class="col mb-3">
                                    <label for="inputText" class="col-md-12 col-form-label">No. Telpon Pengelola</label>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="phone" value="<?php echo $tempat['contact_person']?>">
                                    </div>
                                </div>
                                <div class="col mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">Deskripsi Kegiatan</label>
                                    <div class="col-md-12">
                                        <textarea class="form-control" name="deskripsi" style="height: 100px"><?php echo $tempat['deskripsi_tempat']?></textarea>
                                    </div>
                                </div>
                                <div class="col mb-3">
                                    <label for="inputNumber" class="col-sm-2 col-form-label">Gambar tempat</label>
                                    <div class="col-md-12">
                                        <input class="form-control" name="foto" type="file" id="formFile">
                                    </div>
                                </div>
                                <div class="row mb-3 justify-content-end">
                                    <div class="col-sm-10 text-end">
                                    <a href="/tempat/data_tempat.php" class="btn btn-secondary">Kembali</a>
                                    <button type="submit" class="btn btn-tambah" onclick="openEdit(<?php echo $tempat['id_tempat'] ?>)">Edit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
        <!-- start modal edit -->
        <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi edit data tempat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah anda yakin ingin mengedit data tempat?
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form action="/web/tempat/tempat.php" id="deleteForm" method="POST">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user'] ?>">
                            <input type="hidden" name="id_tempat" id="inpTempat">
                            <button type="submit" class="btn btn-tambah" name="hapusAdmin">Edit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- end modal edit -->

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