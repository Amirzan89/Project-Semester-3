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
  if($userAuth['role'] != 'super admin'){
    echo "<script>alert('Anda bukan super admin !')</script>";
    echo "<script>window.location.href = '/dashboard.php';</script>";
    exit();
  }
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
  <link href="/public/assets/img/LandingPage/favicon.png" rel="icon">
    <link href="/public/assets/img/LandingPage/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <!-- <link href="https://fonts.gstatic.com" rel="preconnect"> -->
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="/public/assets/css/style.css" rel="stylesheet">

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
    include('header.php');
    ?>

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <?php
        $nav = 'admin';
        include('sidebar.php');
        ?>
    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

  <div class="pagetitle">
      <h1>Kelola Admin</h1>
      <nav>
        <ol class="breadcrumb">
          <!-- <li class="breadcrumb-item"><a href="/dashboard.php">Home</a></li> -->
          <li class="breadcrumb-item">Tabel</li>
          <li class="breadcrumb-item active">Admin</li>
        </ol>
      </nav>
  </div><!-- End Page Title -->
  
  <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Data Admin</h4>
              <a href="/halaman/users/form-tambah-user.php">
                <button type="button" class="btn btn-success">
                    <i class="bi bi-person-plus-fill"></i> Tambah User
                </button>
              </a>
              <!-- <button type="button" class="btn btn-outline-secondary"><a href="../users/form-tambah-user.php"> Tambah User</a></button> -->
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>NO</th>
                    <th>Nama Pengguna</th>
                    <th>No Telpon</th>
                    <th>Jenis Kelamin</th>
                    <th>Tanggal Lahir</th>
                    <th>Tempat Lahir</th>
                    <th>Role User</th>
                    <th>Email</th>
                    <th>keterangan</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                    $query = mysqli_query($conn, "SELECT id_user, nama_lengkap, no_telpon, jenis_kelamin, tanggal_lahir, tempat_lahir, role, email  FROM users WHERE role != 'masyarakat'"); 
                    $no = 1;
                    while ($users = mysqli_fetch_array($query)) {
                    ?>
                      <tr>
                        <td><?php echo $no?></td>
                        <td><?php echo $users['nama_lengkap'] ?></td>
                        <td><?php echo $users['no_telpon'] ?></td>
                        <td><?php echo $users['jenis_kelamin'] ?></td>
                        <td><?php echo $users['tanggal_lahir'] ?></td>
                        <td><?php echo $users['tempat_lahir'] ?></td>
                        <td><?php echo $users['role'] ?></td>
                        <td><?php echo $users['email'] ?></td>
                        <td>
                          <a href="/halaman/users/form-edit-user.php?id_user=<?= $users['id_user'] ?>" class="btn btn-info"><i class="bi bi-pencil-square"></i></i></a>
                          <a href="/halaman/users/proses-hapus-user.php?id_user=<?= $users['id_user'] ?>" onclick="return confirm('Anda yakin ingin menghapus data <?php echo $users['nama_lengkap']; ?>?');" class="btn btn-danger"><i class="bi bi-trash-fill"></i></a>
                        </td>
                      </tr>
                    <?php $no++;
                  } ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

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

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
      <i class="bi bi-arrow-up-short"></i>
    </a>
        <!-- Vendor JS Files -->
    <script src="../public/assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="../public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        
    <!-- Template Main JS File -->
        <script src="/public/assets/js/admin/main.js"></script>
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