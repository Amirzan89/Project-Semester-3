<?php
require_once(__DIR__.'/web/koneksi.php');
require_once(__DIR__.'/web/authenticate.php');
require_once(__DIR__.'/env.php');
loadEnv();
$database = koneksi::getInstance();
$conn = $database->getConnection();
$userAuth = authenticate($_POST, [
  'uri' => $_SERVER['REQUEST_URI'],
  'method' => $_SERVER['REQUEST_METHOD'
  ]
], $conn);
if ($userAuth['status'] == 'error') {
  header('Location: /login.php');
} else {
  $userAuth = $userAuth['data'];
  if($userAuth['role'] == 'masyarakat'){
    echo "<script>alert('Anda bukan admin !')</script>";
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
    include(__DIR__.'/header.php');
    ?>

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">
      <?php
      $nav = 'admin';
      include(__DIR__.'/sidebar.php');
      ?>
    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Profil</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
          <li class="breadcrumb-item active">Profil</li>
        </ol>
      </nav>
    </div>
    <!-- End Page Title -->

    <section class="section profile">
      <div class="row">

        <div class="col-xl-12">

          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab"
                    data-bs-target="#profile-overview">Profil</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profil</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Ubah
                    Password</button>
                </li>

              </ul>
              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">

                  <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                  <?php if(isset($userAuth['foto']) && !empty($userAuth['foto']) && !is_null($userAuth['foto'])){?>
                    <img src="/private/profile/admin<?php echo $userAuth['foto'] ?>" alt="Profile" class="rounded-circle">
                    <?php }else{?>
                    <img src="/private/profile/admin/default.jpg" alt="Profile" class="rounded-circle">
                  <?php }?>
                    <!-- <img src="/private/profile/admin<?php echo $userAuth['foto'] ?>" alt="Profile" class="rounded-circle"> -->
                    <h2>
                      <center>
                        <?php echo $userAuth['nama_lengkap'] ?>
                      </center>
                    </h2>
                    <h3>
                      <?php echo $userAuth['role'] ?>
                    </h3>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Nama Lengkap</div>
                    <div class="col-lg-9 col-md-8">
                      <?php echo $userAuth['nama_lengkap'] ?>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Nomor Telepon</div>
                    <div class="col-lg-9 col-md-8">
                      <?php echo $userAuth['no_telpon'] ?>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Jenis Kelamin</div>
                    <div class="col-lg-9 col-md-8">
                      <?php echo $userAuth['jenis_kelamin'] ?>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Tanggal Lahir</div>
                    <div class="col-lg-9 col-md-8">
                      <?php
                      $tanggal = strtotime($userAuth['tanggal_lahir']);
                      echo date('d F Y', $tanggal);
                      ?>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Role</div>
                    <div class="col-lg-9 col-md-8">
                      <?php echo $userAuth['role'] ?>
                    </div>
                    <!-- <div class="col-lg-9 col-md-8">Admin</div> -->
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email</div>
                    <div class="col-lg-9 col-md-8">
                      <?php echo $userAuth['email'] ?>
                    </div>
                    <!-- <div class="col-lg-9 col-md-8">k.anderson@example.com</div> -->
                  </div>

                </div>

                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                  <!-- Profile Edit Form -->
                  <!-- <form > -->
                  <form method="POST" action="/web/User.php" enctype="multipart/form-data">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user'] ?>">
                    <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user'] ?>">
                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Foto Profil</label>
                      <div class="col-md-8 col-lg-9">
                      <?php if(isset($userAuth['foto']) && !empty($userAuth['foto']) && !is_null($userAuth['foto'])){?>
                        <img src="/private/profile/admin<?php echo $userAuth['foto'] ?>" alt="Profile" class="rounded-circle">
                        <?php }else{?>
                        <img src="/private/profile/admin/default.jpg" alt="Profile" class="rounded-circle">
                      <?php }?>
                        <!-- <img src="/private/profile/admin<?php echo $userAuth['foto'] ?>" alt="Profile"> -->
                        <div class="pt-2">
                          <a href="#" class="btn btn-primary btn-sm" title="Upload new profile image"><i
                              class="bi bi-upload"></i></a>
                          <a href="#" class="btn btn-danger btn-sm" title="Remove my profile image"><i
                              class="bi bi-trash"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="Nama Lengkap" class="col-md-4 col-lg-3 col-form-label">Nama Lengkap</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="Nama Lengkap" type="text" class="form-control" id="Nama Lengkap"
                          value="<?php echo $userAuth['nama_lengkap'] ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Nomor Telepon" class="col-md-4 col-lg-3 col-form-label">Nomor Telepon</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="Nomor Telepon" type="text" class="form-control" id="Nomor Telepon"
                          value="<?php echo $userAuth['no_telpon'] ?>">
                      </div>
                    </div>
                    <div class="row mb-3">
                      <legend class="col-form-label col-sm-2 pt-0">Jenis Kelamin</legend>
                      <div class="col-sm-10">
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="jenisK" value="laki-laki" <?php echo ($userAuth['jenis_kelamin'] == 'laki-laki') ? 'checked' : ''; ?>>
                          <label class="form-check-label" for="gridRadios1">
                            Laki-Laki
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="jenisK" value="perempuan" <?php echo ($userAuth['jenis_kelamin'] == 'perempuan') ? 'checked' : ''; ?>>
                          <label class="form-check-label" for="gridRadios2">
                            Perempuan
                          </label>
                        </div>
                      </div>
                    </div>
                    <!-- <div class="row mb-3">
                      <label for="Jenis Kelamin" class="col-md-4 col-lg-3 col-form-label">Jenis Kelamin</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="Jenis Kelamin" type="text" class="form-control" id="Jenis Kelamin" value="<?php // echo $userAuth['jenis_kelamin'] ?>">
                      </div>
                    </div> -->

                    <div class="row mb-3">
                      <label for="Tanggal Lahir" class="col-md-4 col-lg-3 col-form-label">Tanggal Lahir</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="Tanggal Lahir" type="date" class="form-control" id="Tanggal Lahir"
                          value="<?php echo $userAuth['tanggal_lahir'] ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Tempat Lahir" class="col-md-4 col-lg-3 col-form-label">Tempat Lahir</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="Tempat Lahir" type="text" class="form-control" id="Tempat Lahir"
                          value="<?php echo $userAuth['tempat_lahir'] ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Role" class="col-md-4 col-lg-3 col-form-label">Role</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="Role" type="text" class="form-control" id="Role"
                          value="<?php echo $userAuth['role'] ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="email" type="email" class="form-control" id="Email"
                          value="<?php echo $userAuth['email'] ?>">
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">Edit</button>
                    </div>
                  </form><!-- End Profile Edit Form -->

                </div>

                <div class="tab-pane fade pt-3" id="profile-change-password">
                  <!-- Change Password Form -->
                  <!-- <form> -->
                  <form method="POST" action="/web/User.php" enctype="multipart/form-data">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user'] ?>">
                    <div class="row mb-3">
                      <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Password Lama</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="pass_old" type="password" class="form-control" id="currentPassword">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">Password Baru</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="pass_new" type="password" class="form-control" id="newPassword">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Masukkan Kembali Password
                        Baru</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="password_new" type="password" class="form-control" id="renewPassword">
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary" name="changePass">Ubah Password</button>
                    </div>
                  </form><!-- End Change Password Form -->

                </div>

              </div><!-- End Bordered Tabs -->

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

      <?php
      include(__DIR__.'/footer.php');
      ?>

    </div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="<?php echo $tPath; ?>/public/assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Template Main JS File -->
  <script src="<?php echo $tPath; ?>/public/assets/js/admin/main.js"></script>
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