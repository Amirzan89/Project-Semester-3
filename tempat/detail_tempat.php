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
    <style>
        #divImg {
            position: relative;
            left: 50%;
            transform: translateX(-50%);
            max-width: 800px;
            width: 100%;
            max-height: 450px;
            height: 450px;
        }

        #divText {
            position: absolute;
            left: 50%;
            top: 50%;
            translate: -50% -50%;
            font-size: 25px;
            text-align: center;
            display: flex;
            flex-direction: column;
        }

        #divText i {
            font-size: 65px;
        }

        #inpImg {
            display: block;
            margin: auto;
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
        }

        @media (max-width: 480px) {}

        @media (min-width: 481px) and (max-width: 767px) {}

        @media (min-width: 768px) {}
    </style>
</head>

<body>
    <script>
        const domain = window.location.protocol + '//' + window.location.hostname +":"+window.location.port;
		var csrfToken = "<?php echo $csrf ?>";
        var email = "<?php echo $userAuth['email'] ?>";
        var idUser = "<?php echo $userAuth['id_user'] ?>";
        var number = "<?php echo $userAuth['number'] ?>";
        var role = "<?php echo $userAuth['role'] ?>";
        var idTempat = "<?php echo $id ?>";
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
            <h1>Detail Data Tempat</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="/tempat.php">Kelola Tempat</a></li>
                    <li class="breadcrumb-item"><a href="/tempat/data_tempat.php">Data tempat</a></li>
                    <li class="breadcrumb-item active">Detail Data Tempat</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Default Card -->
                    <div class="card">
                        <div class="card-body"> <br>
                            <form action="/web/tempat/tempat.php" method="POST" class="row"
                                enctype="multipart/form-data">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user']; ?>">
                                <input type="hidden" name="id_tempat" value="<?php echo $id; ?>">
                                <div class="col-md-6">
                                    <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner" id="divImg" ondrop="dropHandler(event)" ondragover="dragHandler(event,'over')" ondragleave="dragHandler(event,'leave')">
                                            <div class="carousel-item active">
                                                <!-- <input class="form-control" type="file" multiple="false" id="inpFile" name="foto" style="display:none"> -->
                                                <img src="<?php echo $tPath ?>/public/img/tempat<?php echo $tempat['foto_tempat'] ?>"
                                                    id="inpImg" class="d-block" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <br>
                                    <label for="inputText"><strong>Nama Tempat</strong></label>
                                    <div class="row mb-3">
                                        <div class="col-sm-12">
                                            <input type="text" name="nama_tempat" class="form-control"
                                                placeholder="Masukkan Nama Tempat"
                                                value="<?php echo $tempat['nama_tempat'] ?>">
                                        </div>
                                    </div>
                                    <label for="inputText"><strong>Alamat Tempat</strong></label>
                                    <div class="row mb-3">
                                        <div class="col-sm-12">
                                            <input type="text" name="alamat" class="form-control"
                                                placeholder="Masukkan Alamat Tempat"
                                                value="<?php echo $tempat['alamat_tempat'] ?>">
                                        </div>
                                    </div>
                                    <label for="inputText"><strong>Nama Pengelola</strong></label>
                                    <div class="row mb-3">
                                        <div class="col-sm-12">
                                            <input type="text" name="nama_pengelola" class="form-control"
                                                placeholder="Masukkan nama pengelola"
                                                value="<?php echo $tempat['pengelola'] ?>">
                                        </div>
                                    </div>
                                    <label for="inputText"><strong>Nomor pengelola</strong></label>
                                    <div class="row mb-3">
                                        <div class="col-sm-12">
                                            <input type="text" name="phone" class="form-control"
                                                placeholder="Masukkan Nomor pengelola"
                                                value="<?php echo $tempat['contact_person'] ?>">
                                        </div>
                                    </div>
                                    <label for="inputText"><strong>Deskripsi Tempat</strong></label>
                                    <div class="col-sm-12">
                                        <textarea class="form-control" name="deskripsi" style="height: 80px"
                                            placeholder="Masukkan Deskripsi Tempat"><?php echo $tempat['deskripsi_tempat'] ?></textarea>
                                    </div>
                                    <br>
                                    <div class="row mb-3">
                                    <label for="inputNumber" class="col-sm-2 col-form-label">Gambar tempat</label>
                                    <div class="col-sm-10">
                                        <button class="btn btn-info" type="button" onclick="preview('foto')"> Lihat Foto Tempat </button>
                                        <button class="btn btn-info" type="button" onclick="download('foto')"> Download Foto Tempat </button>
                                    </div>
                                </div>
                                <div class="row mb-3 justify-content-end">
                                    <div class="col-sm-10 text-end">
                                        <a href="/tempat/data_tempat.php" class="btn btn-info"><i>kembali</i></a>
                                        <a href="/tempat/edit_detail_tempat.php?id_tempat=<?= $id ?>" class="btn btn-info"><i class="bi bi-pencil-square">edit</i></a>
                                        </a>
                                        <a href="/users/proses-hapus-user.php?id_user=<?= $tempat['id_user'] ?>" onclick="return confirm('Anda yakin ingin menghapus data <?php echo $tempat['nama_lengkap']; ?>?');" class="btn btn-danger"><i class="bi bi-trash-fill">Hapus</i></a>
                                    </div>
                                </div>
                                </div>
                            </form>
                        </div>
                    </div><!-- End Default Card -->
                </div>
            </div>
        </section>

    </main><!-- End #main -->
  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <?php include(__DIR__.'/../footer.php');
    ?>
  </footer>
  <script>
        //preview data
        function preview(desc){
            if (desc != 'foto'){
                console.log('invalid description');
                return;
            }
            var xhr = new XMLHttpRequest();
            var requestBody = {
                email: email,
                id_tempat:idTempat,
                item:'tempat',
                deskripsi:desc
            };
            //open the request
            xhr.open('POST',domain+"/preview.php")
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.setRequestHeader('Content-Type', 'application/json');
            //send the form data
            xhr.send(JSON.stringify(requestBody));
            xhr.onreadystatechange = function() {
                if (xhr.readyState == XMLHttpRequest.DONE) {
                    if (xhr.status === 200 || xhr.status === 300 || xhr.status === 302) {
                        var response = JSON.parse(xhr.responseText);
                        window.location.href = response.data;
                    } else {
                        var response = xhr.responseText;
                        console.log('errorrr '+response);
                    }
                }
            }
        }
        //preview data
        function download(desc){
            if (desc != 'foto'){
                console.log('invalid description');
                return;
            }
            var xhr = new XMLHttpRequest();
            var requestBody = {
                email: email,
                id_tempat:idTempat,
                item:'tempat',
                deskripsi:desc
            };
            //open the request
            xhr.open('POST',domain+"/download.php")
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.responseType = 'blob';
            // send the form data
            xhr.send(JSON.stringify(requestBody));
            xhr.onreadystatechange = function () {
                if (xhr.readyState == XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var blob = xhr.response;
                        var contentDisposition = xhr.getResponseHeader('Content-Disposition');
                        var match = contentDisposition.match(/filename="(.+\..+?)"/);
                        if (match) {
                            var filename = match[1];
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = filename;
                            link.click();
                        } else {
                            console.log('Invalid content-disposition header');
                        }
                    } else {
                        var response = xhr.responseText;
                        console.log('errorrr ' + response);
                    }
                }
            };
        }
    </script>
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