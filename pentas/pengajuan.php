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
  <link href="/public/assets/img/favicon.png" rel="icon">
  <link href="/public/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <!-- <link href="https://fonts.gstatic.com" rel="preconnect"> -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="/public/assets/vendor/simple-datatables/style.css" rel="stylesheet">


  <!-- Template Main CSS File -->
  <link href="/public/assets/css/tempat.css" rel="stylesheet">
  <link href="/public/css/popup.css" rel="stylesheet">
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
    <?php include('../header.php');
    ?>
  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <?php 
        $nav = 'pentas';
        include('../sidebar.php');
      ?>
    </ul>
  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Pengajuan Pentas</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
          <li class="breadcrumb-item"><a href="/pentas.php">Kelola Pentas</a></li>
          <li class="breadcrumb-item active">Pengajuan Pentas</li>
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
                        <select id="inpBulan" onchange="tampilkanBulan()" class="inp" value="<?php echo date('M')  ?>">
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
              <table class="table">
              <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nomor Induk Seniman</th>
                    <th scope="col">Nama Seniman</th>
                    <th scope="col">Tanggal Pengajuan</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                  </tr>
                  </thead>
                  <tbody id="tablePentas">
                    <?php
                      // $query = mysqli_query($conn, "SELECT id_advis, nomor_induk, nama_advis, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, catatan FROM surat_advis WHERE (status = 'diajukan' OR status = 'proses') AND MONTH(created_at) = ".date('m')." AND YEAR(created_at) = ".date('Y'). " ORDER BY id_advis DESC");
                      $query = mysqli_query($conn, "SELECT id_advis, nomor_induk, nama_advis, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, catatan FROM surat_advis WHERE status = 'diajukan' OR status = 'proses' ORDER BY id_advis DESC");
                      $no = 1;
                      while ($advis = mysqli_fetch_array($query)) {
                    ?>
                    <tr>
                      <td><?php echo $no?></td>
                      <td><?php echo $advis['nomor_induk']?></td>
                      <td><?php echo $advis['nama_advis']?></td>
                      <td><?php echo $advis['tanggal']?></td>
                      <td>
                        <?php if($advis['status'] == 'diajukan'){ ?>
                          <span class="badge bg-proses">Diajukan</span>
                        <?php }else if($advis['status'] == 'proses'){ ?>
                          <span class="badge bg-terima">Diproses</span>
                        <?php } ?>
                      </td>
                      <td>
                      <?php if($advis['status'] == 'diajukan'){ ?>
                          <button class="btn btn-lihat" onclick="proses(<?php echo $advis['id_advis'] ?>)"><i class="bi bi-eye-fill">Lihat</i></button>
                        <?php }else if($advis['status'] == 'proses'){ ?>
                          <a href="/pentas/detail_pentas.php?id_pentas=<?= $advis['id_advis'] ?>" class="btn btn-lihat"><i class="bi bi-eye-fill">Lihat</i></a>
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
  <div id="redPopup" style="display:none"></div>
  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <?php include('../footer.php');
    ?>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="/public/js/popup.js"></script>
  <script>
    var tablePentas = document.getElementById('tablePentas');
    var tahunInput = document.getElementById('inpTahun');
    var bulanInput = document.getElementById('inpBulan');
    var tahun;
    function updateTable(dataT = ''){
      while (tablePentas.firstChild) {
        tablePentas.removeChild(tablePentas.firstChild);
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
          td.innerText = item['nomor_induk'];
          row.appendChild(td);
          var td = document.createElement('td');
          td.innerText = item['nama_advis'];
          row.appendChild(td);
          var td = document.createElement('td');
          td.innerText = item['tanggal'];
          row.appendChild(td);
          //status
          var span = document.createElement('span');
          if(item['status'] == 'diajukan'){
            span.classList.add('badge','bg-proses');
            span.innerText = 'Diajukan';
          }else if(item['status'] == 'proses'){
            span.classList.add('badge','bg-terima');
            span.innerText = 'Diproses';
          }
          var td = document.createElement('td');
          td.appendChild(span);
          row.appendChild(td);
          //btn
          if(item['status'] == 'diajukan'){
            var btn = document.createElement('button');
            var icon = document.createElement('i');
            icon.classList.add('bi','bi-eye-fill');
            icon.innerText = 'Lihat';
            btn.appendChild(icon);
            btn.classList.add('btn','btn-lihat');
            btn.onclick = function (){
              proses(`${item['id_advis']}`);
            }
            var td = document.createElement('td');
            td.appendChild(btn);
            row.appendChild(td);
          }else if(item['status'] == 'proses'){
            var link = document.createElement('a');
            var icon = document.createElement('i');
            icon.classList.add('bi','bi-eye-fill');
            icon.innerText = 'Lihat';
            link.appendChild(icon);
            link.classList.add('btn','btn-lihat');
            link.setAttribute('href',`/pentas/detail_pentas.php?id_pentas=${item['id_advis']}`);
            var td = document.createElement('td');
            td.appendChild(link);
            row.appendChild(td);
          }
          tablePentas.appendChild(row);
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
          desc:'pengajuan'
        };
      }else if(con == null){
        var tanggal = bulanInput.value +'-'+tahunInput.value;
        var requestBody = {
          email: email,
          tanggal:tanggal,
          desc:'pengajuan'
        };
      }
      //open the request
      xhr.open('POST', domain + "/web/pentas/pentas.php")
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
          return;
        }
        setTimeout(() => {
          getData();
        }, 250);
      }, 50);
    }
    function proses(Id) {
      var xhr = new XMLHttpRequest();
      var requestBody = {
        _method: 'PUT',
        id_user: idUser,
        id_pentas: Id,
        keterangan: 'proses'
      };
      //open the request
      xhr.open('POST', domain + "/web/pentas/pentas.php")
      xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
      xhr.setRequestHeader('Content-Type', 'application/json');
      //send the form data
      xhr.send(JSON.stringify(requestBody));
      xhr.onreadystatechange = function () {
        if (xhr.readyState == XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            window.location.href = "/pentas/detail_pentas.php?id_pentas="+Id;
          } else {
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
  <script src="/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="/public/assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="/public/assets/vendor/tinymce/tinymce.min.js"></script>

  <!-- Template Main JS File -->
  <script src="/public/assets/js/main.js"></script>

</body>

</html>