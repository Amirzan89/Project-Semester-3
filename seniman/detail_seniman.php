<?php
require_once(__DIR__.'/../web/koneksi.php');
require_once(__DIR__.'/../web/authenticate.php');
require_once(__DIR__.'/../env.php');
loadEnv();
$database = koneksi::getInstance();
$conn = $database->getConnection();
$userAuth = authenticate($_POST, [
    'uri' => $_SERVER['REQUEST_URI'],
    'method' => $_SERVER['REQUEST_METHOD']
], $conn);
if ($userAuth['status'] == 'error') {
    header('Location: /login.php');
} else {
    $userAuth = $userAuth['data'];
    if(!in_array($userAuth['role'],['super admin','admin seniman'])){
        echo "<script>alert('Anda bukan admin seniman !')</script>";
        echo "<script>window.location.href = '/dashboard.php';</script>";
        exit();
    }
    $tPath = ($_SERVER['APP_ENV'] == 'local') ? '' : $_SERVER['APP_FOLDER'];
    $csrf = $GLOBALS['csrf'];
    if (isset($_GET['id_seniman']) && !empty($_GET['id_seniman'])) {
        $id = $_GET['id_seniman'];
        $sql = mysqli_query($conn, "SELECT id_seniman, nik, nomor_induk, nama_seniman, jenis_kelamin, tempat_lahir, DATE_FORMAT(tanggal_lahir, '%d %M %Y') AS tanggal_lahir, alamat_seniman, no_telpon, nama_organisasi, jumlah_anggota, status, catatan FROM seniman WHERE id_seniman = '$id'");
        $seniman = mysqli_fetch_assoc($sql);
    } else {
        header('Location: /seniman.php');
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
    <link href="<?php echo $tPath; ?>/public/assets/css/nomor-induk.css" rel="stylesheet">

</head>

<body>
    <script>
        const domain = window.location.protocol + '//' + window.location.hostname + ":" + window.location.port;
        var csrfToken = "<?php echo $csrf ?>";
        var email = "<?php echo $userAuth['email'] ?>";
        var idUser = "<?php echo $userAuth['id_user'] ?>";
        var number = "<?php echo $userAuth['number'] ?>";
        var role = "<?php echo $userAuth['role'] ?>";
        var idSeniman = "<?php echo $id ?>";
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
            $nav = 'seniman';
            include(__DIR__.'/../sidebar.php');
            ?>
        </ul>
    </aside><!-- End Sidebar-->

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Detail data seniman</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="/seniman.php">Kelola Seniman</a></li>
                    <?php if ($seniman['status'] == 'diajukan' || $seniman['status'] == 'proses') { ?>
                        <li class="breadcrumb-item"><a href="/seniman/pengajuan.php">Pengajuan Nomer Induk
                                Seniman</a></li>
                    <?php } else if ($seniman['status'] == 'diterima' || $seniman['status'] == 'ditolak') { ?>
                            <li class="breadcrumb-item"><a href="/seniman/riwayat.php">Riwayat Nomer Induk Seniman</a>
                            </li>
                    <?php } ?>
                    <li class="breadcrumb-item active">Detail Data Seniman</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"></h5>
                            <!-- Multi Columns Form -->
                            <form class="row g-3">
                                <div class="col-md-12">
                                    <label for="nik" class="form-label">Nomor Induk Seniman</label>
                                    <input type="text" class="form-control" id="nik" readonly
                                        value="<?php echo $seniman['nomor_induk'] ?>">
                                </div>
                                <div class="col-md-12">
                                    <label for="nik" class="form-label">Nomor Induk Kependudukan</label>
                                    <input type="text" class="form-control" id="nik" readonly
                                        value="<?php echo $seniman['nik'] ?>">
                                </div>
                                <div class="col-md-12">
                                    <label for="nama_seniman" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama_seniman" readonly
                                        value="<?php echo $seniman['nama_seniman'] ?>">
                                </div>
                                <div class="col-mb-3 mt-0">
                                    <label for="jenis_kelamin" class="col-md-12 pt-3 col-form-label">Jenis
                                        Kelamin</label>
                                    <div class="col-md-6">
                                        <select class="form-select" aria-label="Default select example" disabled>
                                            <?php if ($seniman['jenis_kelamin'] == 'laki-laki') { ?>
                                                <option value="laki-laki" selected="selected">Laki-laki</option>
                                            <?php } else if ($seniman['jenis_kelamin'] === 'perempuan') { ?>
                                                    <option value="perempuan" selected="selected">Perempuan</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label for="tempat_lahir" class="form-label">Tempat lahir</label>
                                    <input type="text" class="form-control" id="tempat_lahir" readonly
                                        value="<?php echo $seniman['tempat_lahir'] ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="tanggal_lahir" class="form-label">Tanggal lahir</label>
                                    <input type="text" class="form-control" id="tanggal_lahir" readonly
                                        value="<?php echo $seniman['tanggal_lahir'] ?>">
                                </div>
                                <div class="col-md-12 ">
                                    <label for="alamat_seniman" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="alamat_seniman" placeholder="Masukkan Alamat"
                                        style="height: 100px;"
                                        readonly><?php echo $seniman['alamat_seniman'] ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label for="no_telpon" class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="no_telpon" readonly
                                        value="<?php echo $seniman['no_telpon'] ?>">
                                </div>
                                <div class="col-md-8">
                                    <label for="nama_organisasi" class="form-label">Nama Organisasi</label>
                                    <input type="text" class="form-control" id="nama_organisasi" readonly
                                        value="<?php echo $seniman['nama_organisasi'] ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="jumlah_anggota" class="form-label">Jumlah Anggota</label>
                                    <input type="number" class="form-control" id="jumlah_anggota" readonly
                                        value="<?php echo $seniman['jumlah_anggota'] ?>">
                                </div>
                                <div class="col-12">
                                    <label for="surat_keterangan" class="form-label">Surat Keterangan Desa</label>
                                    <button class="btn btn-info" type="button" onclick="preview('surat') "> Lihat surat
                                        keterangan </button>
                                    <button class="btn btn-info" type="button" onclick="download('surat') "> Download
                                        surat keterangan </button>
                                </div>
                                <div class=" col-12">
                                    <label for="ktp_seniman" class="form-label">Foto Kartu Tanda Penduduk</label>
                                    <button class="btn btn-info" type="button" onclick="preview('ktp')"> Lihat Foto
                                        KTP</button>
                                    <button class="btn btn-info" type="button" onclick="download('ktp')"> Download Foto
                                        KTP</button>
                                </div>
                                <div class="col-12">
                                    <label for="pass_foto" class="form-label">Pass Foto 3x4</label>
                                    <button class="btn btn-info" type="button" onclick="preview('foto')"> Lihat pass
                                        foto </button>
                                    <button class="btn btn-info" type="button" onclick="download('foto')"> Download pass
                                        foto </button>
                                </div>
                                <div class="row mb-3 justify-content-end">
                                    <div class="col-sm-10 text-end">
                                        <?php if ($seniman['status'] == 'diajukan' || $seniman['status'] == 'proses') { ?>
                                            <a href="/seniman/pengajuan.php" class="btn btn-info"><i>kembali</i></a>
                                        <?php } else if ($seniman['status'] == 'diterima' || $seniman['status'] == 'ditolak') { ?>
                                                <a href="/seniman/riwayat.php" class="btn btn-info"><i>kembali</i></a>
                                        <?php } ?>
                                        <?php if ($seniman['status'] == 'diajukan') { ?>
                                            <button type="button" class="btn btn-success"
                                                onclick="openProses(<?php echo $seniman['id_seniman'] ?>)">
                                                <i class="bi bi-edit-fill">Proses</i>
                                            </button>
                                        <?php } else if ($seniman['status'] == 'proses') { ?>
                                                <button type="button" class="btn btn-success"
                                                    onclick="openSetuju(<?php echo $seniman['id_seniman'] ?>)">
                                                    <i class="bi bi-check-circle">Setuju</i>
                                                </button>
                                                <button type="button" class="btn btn-danger"
                                                    onclick="openTolak(<?php echo $seniman['id_seniman'] ?>)">
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
                    <h5 class="modal-title">Konfirmasi proses seniman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin memproses data seniman ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="/web/seniman/seniman.php" id="prosesForm" method="POST">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user'] ?>">
                        <input type="hidden" name="id_seniman" id="inpSenimanP">
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
                    <h5 class="modal-title">Konfirmasi setuju seniman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menyetujui seniman ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="/web/seniman/seniman.php" id="prosesForm" method="POST">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user'] ?>">
                        <input type="hidden" name="id_seniman" id="inpSenimanS">
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
                    <h5 class="modal-title">Konfirmasi tolak seniman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menolak seniman ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="/web/seniman/seniman.php" id="prosesForm" method="POST">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user'] ?>">
                        <input type="hidden" name="id_seniman" id="inpSenimanT">
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
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>
    <script>
        var modalProses = document.getElementById('modalProses');
        var modalSetuju = document.getElementById('modalSetuju');
        var modalTolak = document.getElementById('modalTolak');
        var inpSenimanP = document.getElementById('inpSenimanP');
        var inpSenimanS = document.getElementById('inpSenimanS');
        var inpSenimanT = document.getElementById('inpSenimanT');
        function openProses(dataU,) {
            inpSenimanP.value = dataU;
            var myModal = new bootstrap.Modal(modalProses);
            myModal.show();
        }
        function openSetuju(dataU) {
            inpSenimanS.value = dataU;
            var myModal = new bootstrap.Modal(modalSetuju);
            myModal.show();
        }
        function openTolak(dataU) {
            inpSenimanT.value = dataU;
            var myModal = new bootstrap.Modal(modalTolak);
            myModal.show();
        }
        //preview data
        function preview(desc) {
            if (desc != 'ktp' && desc != 'foto' && desc != 'surat') {
                console.log('invalid description');
                return;
            }
            var xhr = new XMLHttpRequest();
            var requestBody = {
                email: email,
                id_seniman: idSeniman,
                item: 'seniman',
                deskripsi: desc
            };
            //open the request
            xhr.open('POST', domain + "/preview.php")
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.setRequestHeader('Content-Type', 'application/json');
            //send the form data
            xhr.send(JSON.stringify(requestBody));
            xhr.onreadystatechange = function () {
                if (xhr.readyState == XMLHttpRequest.DONE) {
                    if (xhr.status === 200 || xhr.status === 300 || xhr.status === 302) {
                        var response = JSON.parse(xhr.responseText);
                        window.location.href = response.data;
                    } else {
                        var response = xhr.responseText;
                        console.log('errorrr ' + response);
                    }
                }
            }
        }
        //download data
        function download(desc) {
            if (desc != 'ktp' && desc != 'foto' && desc != 'surat') {
                console.log('invalid description');
                return;
            }
            var xhr = new XMLHttpRequest();
            var requestBody = {
                email: email,
                id_seniman: idSeniman,
                item: 'seniman',
                deskripsi: desc
            };
            //open the request
            xhr.open('POST', domain + "/download.php")
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

    <!-- Vendor JS Files -->
    <script src="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $tPath; ?>/public/assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="<?php echo $tPath; ?>/public/assets/vendor/tinymce/tinymce.min.js"></script>

    <!-- Template Main JS File -->
    <script src="<?php echo $tPath; ?>/public/assets/js/main.js"></script>

</body>

</html>