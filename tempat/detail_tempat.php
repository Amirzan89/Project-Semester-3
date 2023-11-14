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
                                    <label for="inputText" class="col-sm-2 col-form-label">Contact Person</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="<?php echo $tempat['contact_person']?>" readonly>
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
                                        <button class="btn btn-info" type="button" onclick="preview('foto')"> Lihat Foto Tempat </button>
                                        <button class="btn btn-info" type="button" onclick="download('foto')"> Download Foto Tempat </button>
                                    </div>
                                </div>
                                <div class="row mb-3 justify-content-end">
                                    <div class="col-sm-10 text-end">
                                        <a href="/tempat/edit_detail_tempat.php?id_tempat=<?= $id ?>" class="btn btn-info"><i class="bi bi-pencil-square">edit</i></a>
                                            <!-- <button class="btn btn-primary">Edit</button> -->
                                        </a>
                                        <a href="/users/proses-hapus-user.php?id_user=<?= $tempat['id_user'] ?>" onclick="return confirm('Anda yakin ingin menghapus data <?php echo $tempat['nama_lengkap']; ?>?');" class="btn btn-danger"><i class="bi bi-trash-fill">Hapus</i></a>
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