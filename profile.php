<?php
require_once(__DIR__.'/web/koneksi.php');
require_once(__DIR__.'/web/authenticate.php');
require_once(__DIR__.'/env.php');
require_once(__DIR__.'/Date.php');
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
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
  <link href="<?php echo $tPath; ?>/public/css/popup.css" rel="stylesheet">
  <style>
    div.drag#divImg{
      border:4px solid black;
    }
    #divImg{
      position: relative;
      left:0;
      max-width: 300px;
      width:100%;
      max-height: 200px;
      height: 200px;
      cursor:pointer;
    }
    #divText{
      position: relative;
      left:50%;
      top:50%;
      translate: -50% -50%;
      font-size:22px;
      text-align: center;
      display:flex;
      flex-direction: column;
    }
    #divText i{
      font-size:65px;
    }
    #inpImg {
      display: block;
      margin: auto;
      max-width: 100%;
      max-height: 100%;
      width: auto;
      height: auto;
    }
    @media (max-width: 480px) {
    }
    @media (min-width: 481px) and (max-width: 767px) {
    }
    @media (min-width: 768px) {
    }
  </style>
</head>

<body>
  <script>
    var csrfToken = "<?php echo $csrf ?>";
    var email = "<?php echo $userAuth['email'] ?>";
    var idUser = "<?php echo $userAuth['id_user'] ?>";
    var number = "<?php echo $userAuth['number'] ?>";
    var role = "<?php echo $userAuth['role'] ?>";
    var users = <?php echo json_encode($userAuth) ?>;
    var tPath = "<?php echo $tPath ?>";
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
                  <?php
                  if(isset($userAuth['foto']) && !empty($userAuth['foto']) && !is_null($userAuth['foto'])){ 
                  ?>
                    <img src="/private/profile/admin<?php echo $userAuth['foto'] ?>" alt="Profile" class="">
                  <?php 
                  }else{
                    if(isset($userAuth['jenis_kelamin']) && $userAuth['jenis_kelamin'] === 'laki-laki'){
                  ?>
                      <img src="/private/profile/admin/default_boy.jpg" alt="Profile" class="">
                    <?php 
                    }else if(isset($userAuth['jenis_kelamin']) && $userAuth['jenis_kelamin'] === 'perempuan'){
                    ?>
                      <img src="/private/profile/admin/default_girl.png" alt="Profile" class="">
                  <?php } 
                  } ?>
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
                      echo changeMonth([['tanggal'=>$userAuth['tanggal_lahir']]])[0]['tanggal'];
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
                  </div>

                </div>

                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                  <!-- Profile Edit Form -->
                  <form method="POST" action="/web/User.php" enctype="multipart/form-data">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user'] ?>">
                    <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user'] ?>">
                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Foto Profil</label>
                      <div class="col-md-8 col-lg-9">
                        <div id="divImg" ondrop="dropHandler(event)" ondragover="dragHandler(event,'over')" ondragleave="dragHandler(event,'leave')">
                          <input class="form-control" type="file" multiple="false" id="inpFile" name="foto" style="display:none">
                          <?php
                          if(isset($userAuth['foto']) && !empty($userAuth['foto']) && !is_null($userAuth['foto'])){ 
                          ?>
                            <img src="/private/profile/admin<?php echo $userAuth['foto'] ?>" alt="Profile" id="inpImg" class="">
                          <?php 
                          }else{
                            if(isset($userAuth['jenis_kelamin']) && $userAuth['jenis_kelamin'] === 'laki-laki'){
                              ?>
                              <img src="/private/profile/admin/default_boy.jpg" alt="Profile" id="inpImg" class="">
                            <?php 
                            }else if(isset($userAuth['jenis_kelamin']) && $userAuth['jenis_kelamin'] === 'perempuan'){
                            ?>
                              <img src="/private/profile/admin/default_girl.png" alt="Profile" id="inpImg" class="">
                          <?php }
                          } ?>
                        </div>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="Nama Lengkap" class="col-md-4 col-lg-3 col-form-label">Nama Lengkap</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="nama" type="text" class="form-control" id="Nama Lengkap"
                          value="<?php echo $userAuth['nama_lengkap'] ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Nomor Telepon" class="col-md-4 col-lg-3 col-form-label">Nomor Telepon</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="phone" type="text" class="form-control" id="phone"
                          value="<?php echo $userAuth['no_telpon'] ?>">
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label class="col-form-label col-md-4 col-lg-3">Jenis Kelamin</label>
                      <div class="col-md-8 col-lg-9">
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="jenisK" value="laki-laki" <?php echo ($userAuth['jenis_kelamin'] == 'laki-laki') ? 'checked' : ''; ?>>
                          Laki-Laki
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="jenisK" value="perempuan" <?php echo ($userAuth['jenis_kelamin'] == 'perempuan') ? 'checked' : ''; ?>>
                          Perempuan
                        </div>
                      </div>
                    </div>
                    <!-- <fieldset class="row mb-3">
                      <legend class="col-form-label col-sm-2 pt-0">Jenis Kelamin</legend>
                      <div class="col-sm-10">
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="jenisK" value="laki-laki" checked>
                          <label class="form-check-label" for="gridRadios1">
                            Laki-Laki
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="jenisK" value="perempuan">
                          <label class="form-check-label" for="gridRadios2">
                            Perempuan
                          </label>
                        </div>
                      </div>
                    </fieldset> -->
                    <div class="row mb-3">
                      <label for="Tanggal Lahir" class="col-md-4 col-lg-3 col-form-label">Tanggal Lahir</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="tanggalL" type="date" class="form-control" id="Tanggal Lahir"
                          value="<?php echo $userAuth['tanggal_lahir'] ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Tempat Lahir" class="col-md-4 col-lg-3 col-form-label">Tempat Lahir</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="tempatL" type="text" class="form-control" id="Tempat Lahir"
                          value="<?php echo $userAuth['tempat_lahir'] ?>">
                      </div>
                    </div>

                    <?php if($userAuth['role'] != 'super admin'){?>
                    <div class="row mb-3">
                      <label for="Role" class="col-md-4 col-lg-3 col-form-label">Role</label>
                      <div class="col-md-8 col-lg-9">
                      <select class="form-select" name="role" aria-label="Default select example">
                        <option value="admin event" <?php echo ($userAuth['role'] == 'admin event') ? 'selected' : ''; ?>>Admin Event</option>
                        <option value="admin tempat" <?php echo ($userAuth['role'] == 'admin tempat') ? 'selected' : ''; ?>>Admin Tempat</option>
                        <option value="admin seniman" <?php echo ($userAuth['role'] == 'admin seniman') ? 'selected' : ''; ?>>Admin Seniman</option>
                      </select> 
                    </div>
                    </div>
                    <?php } ?>

                    <div class="row mb-3">
                      <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="email" type="email" class="form-control" id="Email"
                          value="<?php echo $userAuth['email'] ?>">
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="button" onclick="uploadEdit()" class="btn btn-primary">Edit</button>
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

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <div id="greenPopup" style="display:none"></div>
  <div id="redPopup" style="display:none"></div>
  <!-- Vendor JS Files -->
  <script src="<?php echo $tPath; ?>/public/assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="<?php  echo $tPath ?>/public/js/popup.js"></script>
  <script>
    const maxSizeInBytes = 4 * 1024 * 1024; //max file 4MB
    var divImg = document.getElementById('divImg');
    var inpFile = document.getElementById('inpFile');
    var imgText = document.getElementById('imgText');
    var fileImg = '';
    var uploadStat = false;
    divImg.addEventListener("click", function(){
      inpFile.click();
    });
    function uploadEdit(){
      if(uploadStat){
        return;
      }
      var inpNama = document.querySelector("input[name='nama']").value;
      var inpTLP = document.querySelector("input[name='phone']").value;
      var inpJenis = document.querySelector("input[name='jenisK']").value;
      var inpTempat = document.querySelector("input[name='tempatL']").value;
      var inpTanggal = document.querySelector("input[name='tanggalL']").value;
      var inpEmail = document.querySelector("input[name='email']").value;
      <?php if($userAuth['role'] == 'super admin'){ ?>
        //check data if edit or not
        if((fileImg === null || fileImg === '') && inpNama === users.nama_lengkap && inpTLP === users.no_telpon && inpJenis === users.jenis_kelamin && inpTempat === users.tempat_lahir && inpTanggal === users.tanggal_lahir && inpEmail === users.email){
          showRedPopup('Data belum diubah');
        }
        <?php }else{ ?>
          var inpRole = document.querySelector("select[name='role']").value;
        //check data if edit or not
          if((fileImg === null || fileImg === '') && inpNama === users.nama_lengkap && inpTLP === users.no_telpon && inpJenis === users.jenis_kelamin && inpTempat === users.tempat_lahir && inpTanggal === users.tanggal_lahir && inpRole === users.role && inpEmail === users.email){
            showRedPopup('Data belum diubah');
          }
      <?php } ?>
      uploadStat = true;
      const formData = new FormData();
      formData.append('editAdmin','');
      formData.append('desc','profile');
      formData.append('_method','PUT');
      formData.append('id_admin',idUser);
      formData.append('id_user',idUser);
      formData.append('nama', document.querySelector('input[name="nama"]').value);
      formData.append('phone', document.querySelector('input[name="phone"]').value);
      formData.append('jenisK', document.querySelector('input[name="jenisK"]:checked').value);
      formData.append('tempatL', document.querySelector('input[name="tempatL"]').value);
      formData.append('tanggalL', document.querySelector('input[name="tanggalL"]').value);
      <?php if($userAuth['role'] == 'super admin'){ ?>
        formData.append('role', 'super admin');
      <?php }else{ ?>
        formData.append('role', document.querySelector('select[name="role"]').value);
      <?php } ?>
      formData.append('email', document.querySelector('input[name="email"]').value);
      if(fileImg !== null && fileImg !== ''){
        formData.append('foto', fileImg, fileImg.name);
      }
      const xhr = new XMLHttpRequest();
      xhr.open('POST', '/web/User.php', true);
      xhr.onload = function () {
        if (xhr.status === 200) {
          showGreenPopup(JSON.parse(xhr.responseText));
          setTimeout(() => {
                window.location.href = '/profile.php';
            }, 1000);
          return;
        } else {
          uploadStat = false;
          showRedPopup(JSON.parse(xhr.responseText));
          return;
        }
      };
      xhr.onerror = function () {
        uploadStat = false;
        showRedPopup('Request gagal');
        return;
      };
      xhr.send(formData);
    }
    inpFile.addEventListener('change',function(e){
      if (e.target.files.length === 1) {
        const file = e.target.files[0];
        if (file.type.startsWith('image/')) {
          if (file.size <= maxSizeInBytes) {
            const reader = new FileReader();
            reader.onload = function (event) {
            document.getElementById('inpImg').src = event.target.result;
          };
          reader.readAsDataURL(file);
          fileImg = file;
          //delete inside box
          divImg.style.borderStyle = "none";
          divImg.style.borderWidth = "0px";
          divImg.style.borderColor = "transparent";
          } else {
            showRedPopup('Ukuran maksimal gambar 4MB !');
          }
        } else {
          showRedPopup('File harus Gambar !');
        }
      }
    });
    function dropHandler(event) {
      event.preventDefault();
      if (event.dataTransfer.items) {
        const file = event.dataTransfer.items[0].getAsFile();
        if (file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = function (event) {
            document.getElementById('inpImg').src = event.target.result;
          };
          reader.readAsDataURL(file);
          fileImg = file;
          //delete inside box
          divImg.style.borderStyle = "none";
          divImg.style.borderWidth = "0px";
          divImg.style.borderColor = "transparent";
        } else {
          showRedPopup('File harus Gambar !');
        }
      }
    }
    function dragHandler(event, con){
      event.preventDefault();
      if(con == 'over'){
        imgText.innerText = 'Jatuhkan file';
        divImg.classList.add('drag');
      }else if(con == 'leave'){
        imgText.innerText = 'Pilih atau jatuhkan file gambar tempat';
        divImg.classList.remove('drag');
      }
    }
    </script>
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