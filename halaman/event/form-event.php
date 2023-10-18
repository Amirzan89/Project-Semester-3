<!DOCTYPE html>
<html lang="en">


<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Disporabudpar - Nganjuk</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <!-- <link href="https://fonts.gstatic.com" rel="preconnect"> -->
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">
  <!-- Vendor CSS Files -->
  <!-- <link href="assets/vendor/bootstrap/css/bootstrap.css" rel="stylesheet"> -->

  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">


  <!-- Template Main CSS File -->
  <link href="assets/css/style1.css" rel="stylesheet">

</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <?php include('header.php');
    ?>
  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <?php include('sidebar.php');
    ?>
  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Formulir Pengajuan</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Beranda</a></li>
          <li class="breadcrumb-item"><a href="nis1.php">Kelola Event</a></li>
          <li class="breadcrumb-item active">Formulir Pengajuan</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">FORMULIR UPLOAD EVENT</h5>

              <form class="row g-3">
                <div class="col-md-12">
                  <label for="inputText" class="form-label">Nama Pengirim :</label>
                  <input type="text" class="form-control" id="inputText">
                </div>
                <div class="col-md-12">
                  <label for="inputText" class="form-label">Nama Event : </label>
                  <input type="text" class="form-control" id="inputText">
                </div>
                <div class="col-md-4">
                    <label for="inputDate" class="form-label">Tanggal :</label>
                    <input type="date" class="form-control" id="inputDate">
                  </div>
                <div class="col-md-8">
                  <label for="inputText" class="form-label">Tempat :</label>
                  <input type="text" class="form-control" id="inputText">
                </div>
                <div class="col-12">
                  <label for="inputText" class="form-label">Deskripsi Event :</label>
                  <textarea class="form-control" id="inputTextarea" style="height: 100px;"></textarea>
                </div>
                <div class="col-12">
                  <label for="inputLink" class="form-label">Link Pendaftaran :</label>
                  <input type="link" class="form-control" id="inputLink">
                </div>
                <div class="col-12">
                  <label for="inputFile" class="form-label">Poster Event :</label>
                  <input type="file" class="form-file-input form-control" id="inputFile">
                </div>
                <div class="text-center">
                  <button type="kirim" class="btn btn-warning">Kirim</button>
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
    <?php include('footer.php');
    ?>
  </footer>
  <!-- </footer> -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>