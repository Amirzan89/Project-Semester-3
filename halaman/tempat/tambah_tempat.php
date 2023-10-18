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
    <?php include('./sidebar/header.php');
    ?>
  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <?php include('./sidebar/sidebar.php');
    ?>
  </aside><!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Tambah Tempat</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Beranda</a></li>
          <li class="breadcrumb-item"><a href="menu_utama_tempat.php">Peminjaman Tempat</a></li>
          <li class="breadcrumb-item"><a href="daftar_tempat.php">Daftar Tempat</a></li>
          <li class="breadcrumb-item active">Tambah Tempat</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->


    <section class="section dashboard">
      <div class="row">
        <div class="row align-items-top">
          <div class="col-lg-12">
            <!-- Default Card -->
            <div class="card">
              <div class="card-body"> <br>
                <div class="row">
                  <div class="col-md-6">
                    <!-- Slides only carousel -->
                    <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
                      <div class="carousel-inner">
                        <div class="carousel-item active">
                          <img src="assets/img/slides-1.jpg" class="d-block w-100" alt="...">
                        </div>
                      </div>
                    </div><!-- End Slides only carousel-->
                  </div>
                  <div class="col-md-6">
                    <label for="inputText"><strong>Nama Tempat</strong></label>
                    <div class="row mb-3">
                      <div class="col-sm-12">
                        <input type="text" class="form-control" placeholder="Masukkan Nama Tempat">
                      </div>
                    </div>
                    <label for="inputText"><strong>Alamat Tempat</strong></label>
                    <div class="row mb-3">
                      <div class="col-sm-12">
                        <input type="text" class="form-control" placeholder="Masukkan Alamat Tempat">
                      </div>
                    </div>
                    <label for="inputText"><strong>Deskripsi Tempat</strong></label>
                    <div class="col-sm-12">
                      <textarea class="form-control" style="height: 80px"
                        placeholder="Masukkan Deskripsi Tempat"></textarea>
                    </div> <br>
                    <button type="button" class="btn btn-success">Tambah</button>
                  </div>
                </div>
              </div>
            </div><!-- End Default Card -->
          </div>
        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <?php include('./sidebar/footer.php');
    ?>
  </footer>

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