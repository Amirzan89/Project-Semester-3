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
  if(!in_array($userAuth['role'],['super admin','admin event'])){
    echo "<script>alert('Anda bukan admin event !')</script>";
    echo "<script>window.location.href = '/dashboard.php';</script>";
    exit();
  }
  $tPath = ($_SERVER['APP_ENV'] == 'local') ? '' : $_SERVER['APP_FOLDER'];
  $csrf = $GLOBALS['csrf'];
  if (isset($_GET['id_event']) && !empty($_GET['id_event'])) {
    $id = $_GET['id_event'];
    $sql = mysqli_query($conn, "SELECT id_event, nama_pengirim, status, catatan, events.id_detail, id_event, nama_event, deskripsi, kategori, tempat_event, DATE_FORMAT(tanggal_awal, '%d %M %Y') AS tanggal_awal, DATE_FORMAT(tanggal_akhir, '%d %M %Y') AS tanggal_akhir, link_pendaftaran FROM events INNER JOIN detail_events ON events.id_detail = detail_events.id_detail WHERE id_event = '$id'");
    $events = mysqli_fetch_assoc($sql);
  } else {
    header('Location: /event.php');
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
  <link href="<?php echo $tPath; ?>/public/assets/css/event.css" rel="stylesheet">

</head>

<body>
  <script>
    const domain = window.location.protocol + '//' + window.location.hostname + ":" + window.location.port;
    var csrfToken = "<?php echo $csrf ?>";
    var email = "<?php echo $userAuth['email'] ?>";
    var idUser = "<?php echo $userAuth['id_user'] ?>";
    var number = "<?php echo $userAuth['number'] ?>";
    var role = "<?php echo $userAuth['role'] ?>";
    var idEvent = "<?php echo $id ?>";
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
      $nav = 'event';
      include(__DIR__.'/../sidebar.php');
      ?>
    </ul>
  </aside><!-- End Sidebar-->


  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Formulir Pengajuan</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
          <li class="breadcrumb-item"><a href="/event.php">Kelola Event</a></li>
          <?php if ($events['status'] == 'diajukan' || $events['status'] == 'proses') { ?>
            <li class="breadcrumb-item"><a href="/event/pengajuan.php">Pengajuan event</a></li>
          <?php } else if ($events['status'] == 'diterima' || $events['status'] == 'ditolak') { ?>
              <li class="breadcrumb-item"><a href="/event/riwayat.php">Riwayat Pengajuan event</a></li>
          <?php } ?>
          <li class="breadcrumb-item active">Detail event</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <?php if ($events['status'] == 'diajukan' || $events['status'] == 'proses') { ?>
                <h5 class="card-title"> Pengajuan event</h5>
              <?php } else if ($events['status'] == 'diterima' || $events['status'] == 'ditolak') { ?>
                  <h5 class="card-title"> Riwayat event</h5>
              <?php } ?>
              <form class="row g-3">
                <div class="col-md-12">
                  <label for="inputText" class="form-label">Nama Pengirim :</label>
                  <input type="text" class="form-control" id="inputText" readonly
                    value="<?php echo $events['nama_pengirim'] ?>">
                </div>
                <div class="col-md-12">
                  <label for="inputText" class="form-label">Nama Event : </label>
                  <input type="text" class="form-control" id="inputText" readonly
                    value="<?php echo $events['nama_event'] ?>">
                </div>
                <div class="col-md-4">
                  <label for="inputDate" class="form-label">Tanggal awal :</label>
                  <input type="text" class="form-control" id="inputDate" readonly
                    value="<?php echo $events['tanggal_awal'] ?>">
                </div>
                <div class="col-md-4">
                  <label for="inputDate" class="form-label">Tanggal akhir :</label>
                  <input type="text" class="form-control" id="inputDate" readonly
                    value="<?php echo $events['tanggal_akhir'] ?>">
                </div>
                <div class="col-md-8">
                  <label for="inputText" class="form-label">Tempat :</label>
                  <input type="text" class="form-control" id="inputText" readonly
                    value="<?php echo $events['tempat_event'] ?>">
                </div>
                <div class="col-12">
                  <label for="inputText" class="form-label">Deskripsi Event :</label>
                  <textarea class="form-control" id="inputTextarea" style="height: 100px;"
                    readonly><?php echo $events['deskripsi'] ?></textarea>
                </div>
                <div class="col-12">
                  <label for="inputLink" class="form-label">Link Pendaftaran :</label>
                  <input type="link" class="form-control" id="inputLink" readonly
                    value="<?php echo $events['link_pendaftaran'] ?>">
                </div>
                <div class="col-12">
                  <label for="inputFile" class="form-label">Poster Event :</label>
                  <button class="btn btn-info" type="button" onclick="preview('foto')"> Lihat poster </button>
                  <button class="btn btn-info" type="button" onclick="download('foto')"> Download poster </button>
                </div>
                <?php if (isset($events['catatan']) && !is_null($events['catatan']) && !empty($events['catatan'])) { ?>
                  <div class="col-12">
                    <label for="inputText" class="form-label">Catatan :</label>
                    <textarea class="form-control" id="inputTextarea" style="height: 100px;"
                      readonly><?php echo $events['catatan'] ?></textarea>
                  </div>
                <?php } ?>
                <div class="row mb-3 justify-content-end">
                  <div class="col-sm-10 text-end">
                    <?php if ($events['status'] == 'diajukan' || $events['status'] == 'proses') { ?>
                        <a href="/event/pengajuan.php" class="btn btn-info"><i>kembali</i></a>
                    <?php } else if ($events['status'] == 'diterima' || $events['status'] == 'ditolak') { ?>
                            <a href="/event/riwayat.php" class="btn btn-info"><i>kembali</i></a>
                    <?php } ?>
                    <?php if ($events['status'] == 'diajukan') { ?>
                        <button type="button" class="btn btn-success"
                            onclick="openProses(<?php echo $events['id_event'] ?>)">
                            <i class="bi bi-edit-fill">Proses</i>
                        </button>
                    <?php } else if ($events['status'] == 'proses') { ?>
                        <button type="button" class="btn btn-success"
                            onclick="openSetuju(<?php echo $events['id_event'] ?>)">
                            <i class="bi bi-check-circle">Setuju</i>
                        </button>
                        <button type="button" class="btn btn-danger"
                            onclick="openTolak(<?php echo $events['id_event'] ?>)">
                            <i class="bi bi-x-circle">Tolak</i>
                        </button>
                    <?php } ?>
                  </div>
                </div>
              </form>
              <!-- End General Form Elements -->
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
                    <h5 class="modal-title">Konfirmasi proses event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin memproses data event ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="/web/event/event.php" id="prosesForm" method="POST">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user'] ?>">
                        <input type="hidden" name="id_event" id="inpEventP">
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
                    <h5 class="modal-title">Konfirmasi setuju event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menyetujui event ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="/web/event/event.php" id="prosesForm" method="POST">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user'] ?>">
                        <input type="hidden" name="id_event" id="inpEventS">
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
                    <h5 class="modal-title">Konfirmasi tolak event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menolak event ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="/web/event/event.php" id="prosesForm" method="POST">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user'] ?>">
                        <input type="hidden" name="id_event" id="inpEventT">
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
    var inpEventP = document.getElementById('inpEventP');
    var inpEventS = document.getElementById('inpEventS');
    var inpEventT = document.getElementById('inpEventT');
    function openProses(dataU,) {
        inpEventP.value = dataU;
        var myModal = new bootstrap.Modal(modalProses);
        myModal.show();
    }
    function openSetuju(dataU) {
        inpEventS.value = dataU;
        var myModal = new bootstrap.Modal(modalSetuju);
        myModal.show();
    }
    function openTolak(dataU) {
        inpEventT.value = dataU;
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
        id_event: idEvent,
        item: 'event',
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
        id_event: idEvent,
        item: 'event',
        deskripsi: desc
      };
      // open the request
      xhr.open('POST', domain + "/download.php", true);
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
        };
      }
    }
  </script>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/tinymce/tinymce.min.js"></script>

  <!-- Template Main JS File -->
  <script src="<?php echo $tPath; ?>/public/assets/js/main.js"></script>

</body>

</html>