<?php
require_once('../web/koneksi.php');
require_once('../web/authenticate.php');
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
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="/public/assets/vendor/simple-datatables/style.css" rel="stylesheet">


  <!-- Template Main CSS File -->
  <link href="/public/assets/css/nomor-induk.css" rel="stylesheet">

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
    <?php include('../header.php');
    ?>
  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <?php
        $nav = 'seniman'; 
        include('../sidebar.php');
      ?>
    </ul>
  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Data Seniman</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
          <li class="breadcrumb-item"><a href="/seniman.php">Nomor Induk Seniman</a></li>
          <li class="breadcrumb-item active">Data Seniman</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title"></h5>
              <a href="tambah-seniman.php" class="btn btn-primary">
  <i class="bi bi-person-plus-fill"></i> Tambahkan data seniman
</a>
              <!-- <button type="button" class="btn btn-primary" href="formulir-baru.php"><i class="bi bi-person-plus-fill"></i> Tambahkan data seniman</button> -->

              <table class="table datatable">
              <thead>
                  <tr>
                    <th>No</th>
                    <th>Nomor Induk Seniman</th>
                    <th>Nama Seniman</th>
                    <th>Nomor Telepon</th>
                    <th>Aksi</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                      $query = mysqli_query($conn, "SELECT id_seniman, nomor_induk, nama_seniman, no_telpon FROM seniman WHERE status = 'diterima' OR status = 'ditolak' ORDER BY id_seniman ASC");
                      $no = 1;
                      while ($seniman = mysqli_fetch_array($query)) {
                  ?>
                  <tr>
                    <td><?php echo $no ?></td>
                    <td><?php echo $seniman['nomor_induk'] ?></td>
                    <td><?php echo $seniman['nama_seniman'] ?></td>
                    <td><?php echo $seniman['no_telpon'] ?></td>
                    <td>
                      <a href="/seniman/detail_seniman.php?id_seniman=<?= $seniman['id_seniman'] ?>" class="btn btn-warning"><i class="bi bi-eye-fill">Lihat</i></a>
                      <button type="button" class="btn btn-success"><i class="bi bi-pencil-fill"></i>  edit </button>
                      <button type="button" class="btn btn-danger"><i class="bi bi-trash-fill"></i>  hapus</button>
                    </td>
                  </tr>
                  <?php $no++;
                  } ?>
                  <!-- <tr>
                    <td>1</td>
                    <td>3576447103910003
                    </td>
                    <td>Puji Utami</td>
                    <td>0857362457957</td>
                    <td><button type="button" class="btn btn-warning"><i class="bi bi-eye-fill"></i>  lihat
                    </button>
                    <button type="button" class="btn btn-success"><i class="bi bi-pencil-fill"></i>  edit
                    </button>
                    <button type="button" class="btn btn-danger"><i class="bi bi-trash-fill"></i>  hapus
                    </button>
                    </td>
                  </tr> -->
                  <!-- <tr>
                    <td>2</td>
                    <td>3576441606910003
                    </td>
                    <td>Muhammad Lutfi Hakim</td>
                    <td>0881334394400</td>
                    <td><button type="button" class="btn btn-warning"><i class="bi bi-eye-fill"></i>  lihat
                    </button>
                    <button type="button" class="btn btn-success"><i class="bi bi-pencil-fill"></i>  edit
                    </button>
                    <button type="button" class="btn btn-danger"><i class="bi bi-trash-fill"></i>  hapus
                    </button>
                    </td>
                  </tr> -->
               </tbody>
              </table>
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

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="/public/assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="/public/assets/vendor/tinymce/tinymce.min.js"></script>

  <!-- Template Main JS File -->
  <script src="/public/assets/js/main.js"></script>

</body>

</html>