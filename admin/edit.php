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
    var idAdminData = "<?php echo $id ?>";
    var number = "<?php echo $userAuth['number'] ?>";
    var role = "<?php echo $userAuth['role'] ?>";
    var users = <?php echo json_encode($users) ?>;
    var tPath = "<?php echo $tPath ?>";

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
                    <div id="divImg" ondrop="dropHandler(event)" ondragover="dragHandler(event,'over')" ondragleave="dragHandler(event,'leave')">
                      <input class="form-control" type="file" multiple="false" id="inpFile" name="foto" style="display:none">
                      <img src="/private/profile/admin<?php echo $users['foto'] ?>" id="inpImg" class="d-block" alt="">
                    </div>
                  </div>
                </div>
                <div class="row mb-3">
                <button type="button" class="btn btn-success" name="editAdmin" onclick="upload()">Edit Data</button>
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

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <div id="greenPopup" style="display:none"></div>
    <div id="redPopup" style="display:none"></div>
    <!-- Vendor JS Files -->
    <script src="<?php echo $tPath; ?>/public/assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $tPath; ?>/public/assets/vendor/tinymce/tinymce.min.js"></script>

    <!-- Template Main JS File -->
    <script src="<?php echo $tPath; ?>/public/assets/js/main.js"></script>
  <script src="<?php  echo $tPath ?>/public/js/popup.js"></script>
  <script>
    const maxSizeInBytes = 4 * 1024 * 1024; //max file 4MB
    var divImg = document.getElementById('divImg');
    var inpFile = document.getElementById('inpFile');
    var imgText = document.getElementById('imgText');
    var fileImg = '';
    var uplaodStat = false;
    divImg.addEventListener("click", function(){
      inpFile.click();
    });
    function upload(){
      if(uplaodStat){
        return;
      }
      var inpNama = document.querySelector("input[name='nama']").value;
      var inpTLP = document.querySelector("input[name='phone']").value;
      var inpJenis = document.querySelector("input[name='jenisK']").value;
      var inpTempat = document.querySelector("input[name='tempatL']").value;
      var inpTanggal = document.querySelector("input[name='tanggalL']").value;
      var inpRole = document.querySelector("select[name='role']").value;
      var inpEmail = document.querySelector("input[name='email']").value;
      var inpPass = document.querySelector("input[name='pass']").value;
      //check data if edit or not
      if((fileImg === null || fileImg === '') && inpNama === users.nama_lengkap && inpTLP === users.no_telpon && inpJenis === users.jenis_kelamin && inpTempat === users.tempat_lahir && inpTanggal === users.tanggal_lahir && inpRole === users.role && inpEmail === users.email){
          showRedPopup('Data belum diubah');
      }
      uplaodStat = true;
      const formData = new FormData();
      formData.append('editAdmin','');
      formData.append('_method','PUT');
      formData.append('id_admin',idUser);
      formData.append('id_user',idAdminData);
      formData.append('nama', document.querySelector('input[name="nama"]').value);
      formData.append('phone', document.querySelector('input[name="phone"]').value);
      formData.append('jenisK', document.querySelector('input[name="jenisK"]:checked').value);
      formData.append('tempatL', document.querySelector('input[name="tempatL"]').value);
      formData.append('tanggalL', document.querySelector('input[name="tanggalL"]').value);
      formData.append('role', document.querySelector('select[name="role"]').value);
      formData.append('email', document.querySelector('input[name="email"]').value);
      formData.append('pass', document.querySelector('input[name="pass"]').value);
      if(fileImg !== null && fileImg !== ''){
        formData.append('foto', fileImg, fileImg.name);
      }
      const xhr = new XMLHttpRequest();
      xhr.open('POST', '/web/User.php', true);
      xhr.onload = function () {
        if (xhr.status === 200) {
          showGreenPopup(JSON.parse(xhr.responseText));
          setTimeout(() => {
                window.location.href = '/admin.php';
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
</body>

</html>