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
  if(!in_array($userAuth['role'],['super admin','admin event'])){
    echo "<script>alert('Anda bukan admin event !')</script>";
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
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">
  <!-- Vendor CSS Files -->
  <!-- <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/css/bootstrap.css" rel="stylesheet"> -->

  <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/assets/vendor/simple-datatables/style.css" rel="stylesheet">


  <!-- Template Main CSS File -->
  <link href="<?php echo $tPath; ?>/public/assets/css/nomor-induk.css" rel="stylesheet">
  <link href="<?php echo $tPath; ?>/public/css/popup.css" rel="stylesheet">
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
      $nav = 'event';
      include(__DIR__.'/../sidebar.php');
      ?>
    </ul>
  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Riwayat Pengajuan</h1>
      <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
            <li class="breadcrumb-item"><a href="/event.php">Kelola Event</a></li>
            <li class="breadcrumb-item active">Riwayat Pengajuan</li>
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
                        <input type="text" name="" id="inpTahun" placeholder="Tahun" class="inp" value="<?php echo date('Y') ?>" oninput="tampilkanTahun()">
                      </div>
                      <div class="col-lg-5">
                        <select id="inpBulan" onchange="tampilkanBulan()" class="inp">
                          <option value="semua">semua</option>
                          <option value="1" <?php echo (date('m') == 1) ? 'selected' : ''; ?> >Januari</option>
                          <option value="2" <?php echo (date('m') == 2) ? 'selected' : ''; ?> >Februari</option>
                          <option value="3" <?php echo (date('m') == 3) ? 'selected' : ''; ?> >Maret</option>
                          <option value="4" <?php echo (date('m') == 4) ? 'selected' : ''; ?> >April</option>
                          <option value="5" <?php echo (date('m') == 5) ? 'selected' : ''; ?> >Mei</option>
                          <option value="6" <?php echo (date('m') == 6) ? 'selected' : ''; ?> >Juni</option>
                          <option value="7" <?php echo (date('m') == 7) ? 'selected' : ''; ?> >Juli</option>
                          <option value="8" <?php echo (date('m') == 8) ? 'selected' : ''; ?> >Agustus</option>
                          <option value="9" <?php echo (date('m') == 9) ? 'selected' : ''; ?> >September</option>
                          <option value="10" <?php echo (date('m') == 10) ? 'selected' : ''; ?> >Oktober</option>
                          <option value="11" <?php echo (date('m') == 11) ? 'selected' : ''; ?> >November</option>
                          <option value="12" <?php echo (date('m') == 12) ? 'selected' : ''; ?> >Desember</option>
                        </select>
                      </div>
                    </div>
                  </div>
              </div>
              <table class="table datatable">
                <thead>
                  <tr>
                  <th scope="col">No</th>
                    <th scope="col">Nama Pengirim</th>
                    <th scope="col">Nama Event</th>
                    <th scope="col">Tanggal Pengajuan</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                  </tr>
                </thead>
                <tbody id="tableEvent">
                  <?php
                    $query = mysqli_query($conn, "SELECT id_event, nama_pengirim, nama_event, kategori, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, catatan FROM events INNER JOIN detail_events ON events.id_detail = detail_events.id_detail WHERE status = 'diterima' OR status = 'ditolak' ORDER BY id_event DESC");
                    $no = 1;
                    while ($event = mysqli_fetch_array($query)) {
                  ?>
                    <tr>
                      <td><?php echo $no?></td>
                      <td><?php echo $event['nama_pengirim']?></td>
                      <td><?php echo $event['nama_event']?></td>
                      <td><?php echo $event['tanggal']?></td>
                      <td>
                        <?php if($event['status'] == 'diterima'){ ?>
                          <span class="badge bg-terima">Diterima</span>
                        <?php }else if($event['status'] == 'ditolak'){ ?>
                          <span class="badge bg-tolak">Ditolak </span>
                        <?php } ?>
                      </td>
                      <td>
                        <a href="/event/detail_event.php?id_event=<?= $event['id_event'] ?>" class="btn btn-lihat"><i class="bi bi-eye-fill"></i>  Lihat</a>
                      </td>
                    </tr>
                  <?php $no++;
                  } ?>
                </tbody>
              </table>
              <div class="row mb-3 justify-content-end">
                <div class="col-sm-10 text-end">
                  <a href="../event.php" class="btn btn-secondary">Kembali</a>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->
  <div id="redPopup" style="display:none"></div>
  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <?php include(__DIR__.'/../footer.php');
    ?>
  </footer>
  <!-- </footer> -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="<?php echo $tPath; ?>/public/js/popup.js"></script>
  <script>
    var tableEvent = document.getElementById('tableEvent');
    var tahunInput = document.getElementById('inpTahun');
    var bulanInput = document.getElementById('inpBulan');
    var tahun;
    function updateTable(dataT = ''){
      while (tableEvent.firstChild) {
        tableEvent.removeChild(tableEvent.firstChild);
      }
      var num = 1;
      if(dataT != ''){
        dataT.forEach(function (item){
          var row = document.createElement('tr');
          var td = document.createElement('td');
          //data
          td.innerText = num;
          row.appendChild(td);
          var td = document.createElement('td');
          td.innerText = item['nama_pengirim'];
          row.appendChild(td);
          var td = document.createElement('td');
          td.innerText = item['nama_event'];
          row.appendChild(td);
          var td = document.createElement('td');
          td.innerText = item['tanggal'];
          row.appendChild(td);
          //status
          var span = document.createElement('span');
          var icon = document.createElement('i');
          if(item['status'] == 'ditolak'){
            icon.innerText = 'Ditolak';
            icon.classList.add('bi','bi-x-circle-fill');
            span.appendChild(icon);
            span.classList.add('badge','bg-tolak');
          }else if(item['status'] == 'diterima'){
            icon.innerText = 'Diterima';
            icon.classList.add('bi','bi-check-circle-fill');
            span.appendChild(icon);
            span.classList.add('badge','bg-terima');
          }
          var td = document.createElement('td');
          td.appendChild(span);
          row.appendChild(td);
          //btn
          var link = document.createElement('a');
          var icon = document.createElement('i');
          icon.classList.add('bi','bi-eye-fill');
          icon.innerText = 'Lihat';
          link.appendChild(icon);
          link.classList.add('btn','btn-lihat');
          link.setAttribute('href',`/event/detail_event.php?id_event=${item['id_event']}`);
          var td = document.createElement('td');
          td.appendChild(link);
          row.appendChild(td);
          tableEvent.appendChild(row);
          num++;
        });
      }
    }
    function getData(con = null){
      var xhr = new XMLHttpRequest();
      if(con == 'semua'){
        var requestBody = {
          email: email,
          tanggal:'semua',
          desc:'riwayat'
        };
      }else if(con == null){
        var tanggal = bulanInput.value +'-'+tahunInput.value;
        var requestBody = {
          email: email,
          tanggal:tanggal,
          desc:'riwayat'
        };
      }
      //open the request
      xhr.open('POST', domain + "/web/event/event.php")
      xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
      xhr.setRequestHeader('Content-Type', 'application/json');
      //send the form data
      xhr.send(JSON.stringify(requestBody));
      xhr.onreadystatechange = function () {
        if (xhr.readyState == XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            var response = xhr.responseText;
            updateTable(JSON.parse(response)['data']);
          } else {
            var response = xhr.responseText;
            console.log(response);
            updateTable();
            return;
          }
        }
      }
    }
    function tampilkanBulan(){
      if(bulanInput.value == 'semua'){
        tahun = tahunInput.value;
        tahunInput.disabled = true;
        tahunInput.value = '';
        setTimeout(() => {
          getData('semua');
        }, 250);
      }else{
        if(tahunInput.disabled == true){
          tahunInput.disabled = false;
          tahunInput.value = tahun;
        }
        setTimeout(() => {
          getData();
        }, 250);
      }
    }
    function tampilkanTahun(){
      setTimeout(() => {
        var tahun = tahunInput.value;
        tahun = tahun.replace(/\s/g, '');
        if (isNaN(tahun)) {
          showRedPopup('Tahun harus angka !');
          console.log("Tahun harus angka");
          return;
        }
        setTimeout(() => {
          getData();
        }, 250);
      }, 5);
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