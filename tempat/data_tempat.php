<?php
require_once(__DIR__.'/../web/koneksi.php');
require_once(__DIR__.'/../web/authenticate.php');
require_once(__DIR__.'/../env.php');
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
            <h1>Data Tempat</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="/tempat.php">Kelola Tempat</a></li>
                    <li class="breadcrumb-item active">Data Tempat</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">
                          <h4 class="card-title">Data Tempat</h4>
                          <a href="/tempat/tambah_tempat.php">
                            <button type="button" class="btn btn-success">
                                <i class="bi bi-person-plus-fill"></i> Tambah Tempat
                            </button>
                          </a>
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th class="col"><strong>No.</th>
                                        <th scope="col">Nama Tempat</th>
                                        <th scope="col">Alamat Tempat</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php
                                    $query = mysqli_query($conn, "SELECT id_tempat, nama_tempat, alamat_tempat, deskripsi_tempat FROM list_tempat ");
                                    $no = 1;
                                    while ($tempat = mysqli_fetch_array($query)) {
                                  ?>
                                      <tr>
                                        <td><?php echo $no?></td>
                                        <td><?php echo $tempat['nama_tempat'] ?></td>
                                        <td><?php echo $tempat['alamat_tempat'] ?></td>
                                        <td>
                                          <a href="/tempat/detail_tempat.php?id_tempat=<?= $tempat['id_tempat'] ?>" class="btn btn-lihat"><i class="bi bi-eye-fill">Lihat</i></a>
                                          <a href="/tempat/edit_detail_tempat.php?id_tempat=<?= $tempat['id_tempat'] ?>" class="btn btn-edit"><i class="bi bi-pencil-fill"></i> Edit</a>
                                          <button type="button" class="btn btn-danger" onclick="openDelete(<?php echo $tempat['id_tempat']?>)"> <i class="bi bi-trash-fill">Hapus</i></button>
                                        </td>
                                      </tr>
                                    <?php $no++;
                                  } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </section>

    </main><!-- End #main -->
    <!-- start modal delete -->
  <div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi hapus tempat</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Apakah Anda yakin ingin menghapus tempat ?  
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <form action="/web/tempat/tempat.php" id="deleteForm" method="POST">
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user'] ?>">
            <input type="hidden" name="id_tempat" id="inpTempat">
            <button type="submit" class="btn btn-success" name="hapusAdmin">Hapus</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- end modal delete -->
  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <?php include(__DIR__.'/../footer.php');
    ?>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script>
    var modal = document.getElementById('modalDelete');
    var deleteForm = document.getElementById('deleteForm');
    var inpTempat = document.getElementById('inpTempat');
    function openDelete(dataU){
      inpTempat.value = dataU;
      var myModal = new bootstrap.Modal(modal);
      myModal.show();
    }
  </script>
  <!-- Vendor JS Files -->
  <script src="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/tinymce/tinymce.min.js"></script>

  <!-- Template Main JS File -->
  <script src="<?php echo $tPath; ?>/public/assets/js/main.js"></script>

</body>

</html>