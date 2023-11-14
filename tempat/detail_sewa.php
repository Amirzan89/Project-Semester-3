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
    $csrf = $GLOBALS['csrf'];
    if (isset($_GET['id_sewa']) && !empty($_GET['id_sewa'])) {
        $id  = $_GET['id_sewa'];
        $sql = mysqli_query($conn, "SELECT id_sewa, nik_sewa, nama_peminjam, nama_tempat, deskripsi_sewa_tempat, nama_kegiatan_sewa, jumlah_peserta, instansi, DATE_FORMAT(tgl_awal_peminjaman, '%d %M %Y') AS tanggal_awal, DATE_FORMAT(tgl_akhir_peminjaman, '%d %M %Y') AS tanggal_akhir, status, catatan FROM sewa_tempat WHERE id_sewa = '$id'");
        $sewa = mysqli_fetch_assoc($sql);
    }else{
        header('Location: /tempat.php');
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
        var idSewa = "<?php echo $id ?>";
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
                    <?php if($sewa['status'] == 'diajukan' || $sewa['status'] == 'proses'){ ?>
                        <li class="breadcrumb-item"><a href="/tempat/pengajuan.php">Pengajuan sewa tempat</a></li>
                    <?php }else if($sewa['status'] == 'diterima' || $sewa['status'] == 'ditolak'){ ?>
                        <li class="breadcrumb-item"><a href="/tempat/riwayat.php">Riwayat sewa tempat</a></li>
                    <?php } ?>
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
                                <h5 class="card-title text-center">Data Detail Sewa Tempat</h5>
                            </div>
                            <!-- General Form Elements -->
                            <form>
                            <form>
                                <div class="row mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">Nama Lengkap</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" readonly value="<?php echo $sewa['nama_peminjam']?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">NIK</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" readonly value="<?php echo $sewa['nik_sewa']?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">Instansi</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" readonly value="<?php echo $sewa['instansi']?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">Nama Kegiatan</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" readonly value="<?php echo $sewa['nama_kegiatan_sewa']; ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputNumber" class="col-sm-2 col-form-label">Jumlah Peserta</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" readonly value="<?php echo $sewa['jumlah_peserta']?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">Tempat</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" readonly value="<?php echo $sewa['nama_tempat']?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputDate" class="col-sm-2 col-form-label">Tanggal awal sewa</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" readonly value="<?php echo $sewa['tanggal_awal']?>">
                                    </div>
                                    <label for="inputDate" class="col-sm-2 col-form-label">Tanggal akhir sewa</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" readonly value="<?php echo $sewa['tanggal_akhir']?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputNumber" class="col-sm-2 col-form-label">Surat Keterangan</label>
                                    <div class="col-sm-10">
                                    <button class="btn btn-info" type="button" onclick="preview('surat')"> Lihat surat keterangan </button>
                                    <button class="btn btn-info" type="button" onclick="download('surat')"> Download surat keterangan </button>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">Deskripsi Kegiatan</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" style="height: 100px" readonly><?php echo $sewa['deskripsi_sewa_tempat'] ?></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3 justify-content-end">
                                    <div class="col-sm-10 text-end">
                                    <?php if ($sewa['status'] == 'diajukan' || $sewa['status'] == 'proses') { ?>
                                            <a href="/tempat/pengajuan.php" class="btn btn-info"><i>kembali</i></a>
                                        <?php } else if ($sewa['status'] == 'diterima' || $sewa['status'] == 'ditolak') { ?>
                                                <a href="/tempat/riwayat.php" class="btn btn-info"><i>kembali</i></a>
                                        <?php } ?>
                                        <?php if ($sewa['status'] == 'diajukan') { ?>
                                            <button type="button" class="btn btn-success"
                                                onclick="openProses(<?php echo $sewa['id_sewa'] ?>)">
                                                <i class="bi bi-edit-fill">Proses</i>
                                            </button>
                                        <?php } else if ($sewa['status'] == 'proses') { ?>
                                            <button type="button" class="btn btn-success"
                                                onclick="openSetuju(<?php echo $sewa['id_sewa'] ?>)">
                                                <i class="bi bi-check-circle">Setuju</i>
                                            </button>
                                            <button type="button" class="btn btn-danger"
                                                onclick="openTolak(<?php echo $sewa['id_sewa'] ?>)">
                                                <i class="bi bi-x-circle">Tolak</i>
                                            </button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
    <!-- start modal proses -->
    <div class="modal fade" id="modalProses" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi proses sewa tempat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin memproses data sewa tempat ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="/web/tempat/tempat.php" id="prosesForm" method="POST">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user'] ?>">
                        <input type="hidden" name="id_sewa" id="inpSewaP">
                        <input type="hidden" name="keterangan" value="proses">
                        <button type="submit" class="btn btn-success">Proses</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end modal proses -->

    <!-- start modal setuju -->
    <div class="modal fade" id="modalSetuju" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi setuju sewa tempat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menyetujui sewa tempat ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="/web/tempat/tempat.php" id="prosesForm" method="POST">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user'] ?>">
                        <input type="hidden" name="id_sewa" id="inpSewaS">
                        <input type="hidden" name="keterangan" value="diterima">
                        <button type="submit" class="btn btn-success">Setuju</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end modal setuju -->

    <!-- start modal tolak -->
    <div class="modal fade" id="modalTolak" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi tolak sewa tempat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menolak sewa tempat ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="/web/tempat/tempat.php" id="prosesForm" method="POST">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user'] ?>">
                        <input type="hidden" name="id_sewa" id="inpSewaT">
                        <input type="hidden" name="catatan" value="terserah">
                        <input type="hidden" name="keterangan" value="ditolak">
                        <button type="submit" class="btn btn-success">Tolak</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end modal tolak -->
  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <?php include(__DIR__.'/../footer.php');
    ?>
  </footer>
    <script>
        var modalProses = document.getElementById('modalProses');
        var modalSetuju = document.getElementById('modalSetuju');
        var modalTolak = document.getElementById('modalTolak');
        var inpSewaP = document.getElementById('inpSewaP');
        var inpSewaS = document.getElementById('inpSewaS');
        var inpSewaT = document.getElementById('inpSewaT');
        function openProses(dataU,) {
            inpSewaP.value = dataU;
            var myModal = new bootstrap.Modal(modalProses);
            myModal.show();
        }
        function openSetuju(dataU) {
            inpSewaS.value = dataU;
            var myModal = new bootstrap.Modal(modalSetuju);
            myModal.show();
        }
        function openTolak(dataU) {
            inpSewaT.value = dataU;
            var myModal = new bootstrap.Modal(modalTolak);
            myModal.show();
        }
        //preview data
        function preview(desc){
            if (desc != 'ktp' && desc != 'foto' && desc != 'surat'){
                console.log('invalid description');
                return;
            }
            var xhr = new XMLHttpRequest();
            var requestBody = {
                email: email,
                id_sewa:idSewa,
                item:'sewa',
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
            if (desc != 'ktp' && desc != 'foto' && desc != 'surat'){
                console.log('invalid description');
                return;
            }
            var xhr = new XMLHttpRequest();
            var requestBody = {
                email: email,
                id_sewa:idSewa,
                item:'sewa',
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