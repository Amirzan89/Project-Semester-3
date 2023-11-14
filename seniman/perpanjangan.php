<?php
require_once(__DIR__.'/../web/koneksi.php');
require_once(__DIR__.'/../web/authenticate.php');
require_once(__DIR__.'/../env.php');
loadEnv();
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
  if(!in_array($userAuth['role'],['super admin','admin seniman'])){
    echo "<script>alert('Anda bukan admin seniman !')</script>";
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
  <link href="<?php echo $tPath; ?>/public/assets/img/favicon.png" rel="icon">
  <link href="<?php echo $tPath; ?>/public/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <!-- <link href="https://fonts.gstatic.com" rel="preconnect"> -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/assets/vendor/simple-datatables/style.css" rel="stylesheet">


  <!-- Template Main CSS File -->
  <link href="<?php echo $tPath; ?>/public/assets/css/nomor-induk.css" rel="stylesheet">
  <style>
    .ui-datepicker-calendar {
      display: none;
    }
    
    .srcDate {
      float: right;
      padding: 10px;
    }

    .inp {
      padding: 6px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
      width: 100%;
    }

  </style>
</head>

<body>
  <script>
    const domain = window.location.protocol + '//' + window.location.hostname + ":" + window.location.port;
	  var csrfToken = "<?php echo $csrf ?>";
    var email = "<?php echo $userAuth['email'] ?>";
    var idUser = "<?php echo $userAuth['id_user'] ?>";
    var number = "<?php echo $userAuth['number'] ?>";
    var role = "<?php echo $userAuth['role'] ?>";
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
      <h1>Verifikasi Pengajuan</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
          <li class="breadcrumb-item"><a href="/seniman.php">Kelola Seniman</a></li>
          <li class="breadcrumb-item active">Verifikasi Pengajuan</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title"></h5>
              <div class="srcDate">
                  <div class="col-lg-12">
                    <div class="row">
                      <div class="col-lg-3">
                        <input type="text" name="" id="" placeholder="Tahun" class="inp">
                      </div>
                      <div class="col-lg-5">
                        <select id="bulanDropdown" onchange="tampilkanBulan()" class="inp">
                          <option value="01">Januari</option>
                          <option value="02">Februari</option>
                          <option value="03">Maret</option>
                          <option value="04">April</option>
                          <option value="05">Mei</option>
                          <option value="06">Juni</option>
                          <option value="07">Juli</option>
                          <option value="08">Agustus</option>
                          <option value="09">September</option>
                          <option value="10">Oktober</option>
                          <option value="11">November</option>
                          <option value="12">Desember</option>
                        </select>
                      </div>
                    </div>
                  </div>
              </div>
              <table class="table datatable">
              <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama Seniman</th>
                    <th scope="col">Tanggal Pengajuan</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                      // $query = mysqli_query($conn, "SELECT id_seniman, nama_seniman, DATE_FORMAT(tgl_pembuatan, '%d %M %Y') AS tanggal, status FROM seniman WHERE status = 'diajukan' OR status = 'proses' AND MONTH(tgl_pembuatan) = ".date('m')." AND YEAR(tgl_pembuatan) = ".date('Y')." ORDER BY id_seniman DESC");
                      $query = mysqli_query($conn, "SELECT id_seniman, nama_seniman, DATE_FORMAT(tgl_pembuatan, '%d %M %Y') AS tanggal, status FROM seniman WHERE status = 'diajukan' OR status = 'proses' ORDER BY id_seniman DESC");
                      $no = 1;
                      while ($seniman = mysqli_fetch_array($query)) {
                  ?>
                    <tr>
                      <td><?php echo $no?></td>
                      <td><?php echo $seniman['nama_seniman']?></td>
                      <td><?php echo $seniman['tanggal']?></td>
                      <td>
                        <?php if($seniman['status'] == 'diajukan'){ ?>
                          <span class="badge bg-proses">Diajukan</span>
                        <?php }else if($seniman['status'] == 'proses'){ ?>
                          <span class="badge bg-terima">Diproses</span>
                        <?php } ?>
                      </td>
                      <td>
                      <?php if($seniman['status'] == 'diajukan'){ ?>
                          <button class="btn btn-lihat" onclick="proses(<?php echo $seniman['id_seniman'] ?>)"><i class="bi bi-eye-fill">Lihat</i></button>
                        <?php }else if($seniman['status'] == 'proses'){ ?>
                          <a href="/seniman/detail_seniman.php?id_seniman=<?= $seniman['id_seniman'] ?>" class="btn btn-lihat"><i class="bi bi-eye-fill">Lihat</i></a>
                        <?php } ?>
                      </td>
                    </tr>
                  <?php $no++;
                  } ?>
               </tbody>
              </table>
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
  
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script>
    function proses(Id) {
      var xhr = new XMLHttpRequest();
      var requestBody = {
        _method: 'PUT',
        id_user: idUser,
        id_seniman: Id,
        keterangan: 'proses'
      };
      //open the request
      xhr.open('POST', domain + "/web/seniman/seniman.php")
      xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
      xhr.setRequestHeader('Content-Type', 'application/json');
      //send the form data
      xhr.send(JSON.stringify(requestBody));
      xhr.onreadystatechange = function () {
        if (xhr.readyState == XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            window.location.href = "/seniman/detail_seniman.php?id_seniman="+Id;
          } else {
            console.log(xhr.responseText);
            try {
                eval(xhr.responseText);
            } catch (error) {
                console.error('Error evaluating JavaScript:', error);
            }
          }
        }
      }
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