<?php
require_once('../../web/koneksi.php');
require_once('../../web/authenticate.php');
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
}
$csrf = $GLOBALS['csrf'];
if (isset($_GET['id_tempat']) && !empty($_GET['id_tempat'])) {
  $id  = $_GET['id_tempat'];
  $sql  = mysqli_query($conn, "SELECT * FROM list_tempat WHERE `id_tempat` = '" . $id . "'");
  $tempat = mysqli_fetch_assoc($sql);
}else{
    header('Location: /halaman/tempat/data_tempat.php');
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
  <link href="/public/assets/img/landing-page/favicon.png" rel="icon">
    <link href="/public/assets/img/landing-page/apple-touch-icon.png" rel="apple-touch-icon">

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
  <link href="/public/assets/css/tempat.css" rel="stylesheet">

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
    <?php include('../../header.php');
    ?>
    </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    <?php 
    $nav = 'tempat';
    include('../../sidebar.php');
    ?>
    </ul>
  </aside><!-- End Sidebar-->

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Detail Data Tempat</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="/tempat.php">Kelola Tempat</a></li>
                    <li class="breadcrumb-item"><a href="/halaman/tempat/data_tempat.php">Data tempat</a></li>
                    <li class="breadcrumb-item active">Detail Data Tempat</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <h5 class="card-title text-center">Data Detail Tempat</h5>
                            </div>
                            <!-- General Form Elements -->
                            <form>
                                <div class="row mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">Nama Tempat</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="<?php echo $tempat['nama_tempat']?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">Alamat Tempat</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="<?php echo $tempat['alamat_tempat']?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">Deskripsi Kegiatan</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" style="height: 100px" readonly><?php echo $tempat['deskripsi_tempat']?></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputNumber" class="col-sm-2 col-form-label">Gambar tempat</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="file" id="formFile" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3 justify-content-end">
                                    <div class="col-sm-10 text-end">
                                        <a href="/halaman/tempat/edit_detail_tempat.php?id_tempat=<?= $id ?>" class="btn btn-info"><i class="bi bi-pencil-square">edit</i></a>
                                            <!-- <button class="btn btn-primary">Edit</button> -->
                                        </a>
                                        <a href="/halaman/users/proses-hapus-user.php?id_user=<?= $users['id_user'] ?>" onclick="return confirm('Anda yakin ingin menghapus data <?php echo $users['nama_lengkap']; ?>?');" class="btn btn-danger"><i class="bi bi-trash-fill">Hapus</i></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <?php include('../../footer.php');
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