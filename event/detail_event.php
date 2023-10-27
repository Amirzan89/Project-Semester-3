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
  if (isset($_GET['id_event']) && !empty($_GET['id_event'])) {
    $id  = $_GET['id_event'];
    $sql = mysqli_query($conn, "SELECT id_event, nama_pengirim, status, catatan, events.id_detail, id_sewa, nama_event, deskripsi, kategori, tempat_event, DATE_FORMAT(tanggal_awal, '%d %M %Y') AS tanggal_awal, DATE_FORMAT(tanggal_akhir, '%d %M %Y') AS tanggal_akhir, link_pendaftaran FROM events INNER JOIN detail_events ON events.id_detail = detail_events.id_detail WHERE id_event = '$id'");
    $events = mysqli_fetch_assoc($sql);
  }else{
    header('Location: /event.php');
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
  <link href="/public/img/icon/utama/logo.png" rel="icon">

  <!-- Google Fonts -->
  <!-- <link href="https://fonts.gstatic.com" rel="preconnect"> -->
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="/public/assets/vendor/simple-datatables/style.css" rel="stylesheet">


  <!-- Template Main CSS File -->
  <link href="/public/assets/css/event.css" rel="stylesheet">

</head>

<body>
  <script>
    const domain = window.location.protocol + '//' + window.location.hostname +":"+window.location.port;
		var csrfToken = "<?php echo $csrf ?>";
    var email = "<?php echo $userAuth['email'] ?>";
    var idUser = "<?php echo $userAuth['id_user'] ?>";
    var number = "<?php echo $userAuth['number'] ?>";
    var role = "<?php echo $userAuth['role'] ?>";
    var idEvent = "<?php echo $id ?>";
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
      $nav = 'event'; 
      include('../sidebar.php');
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
          <?php if($events['status'] == 'diajukan' || $events['status'] == 'proses'){ ?>
            <li class="breadcrumb-item"><a href="/event/pengajuan.php">Pengajuan event</a></li>
          <?php }else if($events['status'] == 'diterima' || $events['status'] == 'ditolak'){ ?>
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
              <?php if($events['status'] == 'diajukan' || $events['status'] == 'proses'){ ?>
                <h5 class="card-title"> Pengajuan event</h5>
                <?php }else if($events['status'] == 'diterima' || $events['status'] == 'ditolak'){ ?>
                  <h5 class="card-title"> Riwayat event</h5>
              <?php } ?>
              <form class="row g-3">
                <div class="col-md-12">
                  <label for="inputText" class="form-label">Nama Pengirim :</label>
                  <input type="text" class="form-control" id="inputText" readonly value="<?php echo $events['nama_pengirim']?>">
                </div>
                <div class="col-md-12">
                  <label for="inputText" class="form-label">Nama Event : </label>
                  <input type="text" class="form-control" id="inputText" readonly value="<?php echo $events['nama_event']?>">
                </div>
                <div class="col-md-4">
                    <label for="inputDate" class="form-label">Tanggal awal :</label>
                    <input type="text" class="form-control" id="inputDate" readonly value="<?php echo $events['tanggal_awal']?>">
                </div>
                <div class="col-md-4">
                    <label for="inputDate" class="form-label">Tanggal akhir :</label>
                    <input type="text" class="form-control" id="inputDate" readonly value="<?php echo $events['tanggal_akhir']?>">
                </div>
                <div class="col-md-8">
                  <label for="inputText" class="form-label">Tempat :</label>
                  <input type="text" class="form-control" id="inputText" readonly value="<?php echo $events['tempat_event']?>">
                </div>
                <div class="col-12">
                  <label for="inputText" class="form-label">Deskripsi Event :</label>
                  <textarea class="form-control" id="inputTextarea" style="height: 100px;" readonly><?php echo $events['deskripsi']?></textarea>
                </div>
                <div class="col-12">
                  <label for="inputLink" class="form-label">Link Pendaftaran :</label>
                  <input type="link" class="form-control" id="inputLink" readonly value="<?php echo $events['link_pendaftaran']?>">
                </div>
                <div class="col-12">
                  <label for="inputFile" class="form-label">Poster Event :</label>
                  <button class="btn btn-info" type="button" onclick="preview('foto')"> Lihat poster </button>
                  <button class="btn btn-info" type="button" onclick="download('foto')"> Download poster </button>
                </div>
                <?php if(isset($events['catatan']) && !is_null($events['catatan']) && !empty($events['catatan'])){?>
                  <div class="col-12">
                    <label for="inputText" class="form-label">Catatan :</label>
                    <textarea class="form-control" id="inputTextarea" style="height: 100px;" readonly><?php echo $events['catatan']?></textarea>
                  </div>
                  <?php } ?>
                  <div class="text-center">
                    <button type="kirim" class="btn btn-warning">proses</button>
                  </div>
              </form>
              <!-- End General Form Elements -->
            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <?php include('../footer.php');
    ?>
  </footer>

  <script>
        //preview data
        function preview(desc){
            if (desc != 'ktp' && desc != 'foto' && desc != 'surat'){
                console.log('invalid description');
                return;
            }
            var xhr = new XMLHttpRequest();
            var requestBody = {
                email: email,
                id_event:idEvent,
                item:'event',
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
        //download data
        function download(desc){
            if (desc != 'ktp' && desc != 'foto' && desc != 'surat'){
                console.log('invalid description');
                return;
            }
            var xhr = new XMLHttpRequest();
            var requestBody = {
                email: email,
                id_event:idEvent,
                item:'event',
                deskripsi:desc
            };
            //open the request
            xhr.open('POST',domain+"/download.php")
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
    </script>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="/public/assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="/public/assets/vendor/tinymce/tinymce.min.js"></script>

  <!-- Template Main JS File -->
  <script src="/public/assets/js/main.js"></script>

</body>

</html>