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
        if (mysqli_num_rows($sql) > 0) {
            $tempat = mysqli_fetch_assoc($sql);
        } else {
            header("Location: /tempat.php");
            exit();
        }
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
  <link href="<?php echo $tPath; ?>/public/css/popup.css" rel="stylesheet">
  <style>
        div.drag#divImg {
            border: 4px solid black;
        }

        #divImg {
            position: relative;
            left: 50%;
            transform: translateX(-50%);
            max-width: 800px;
            width: 100%;
            max-height: 450px;
            height: 450px;
            cursor: pointer;
        }

        #divText {
            position: absolute;
            left: 50%;
            top: 50%;
            translate: -50% -50%;
            font-size: 25px;
            text-align: center;
            display: flex;
            flex-direction: column;
        }

        #divText i {
            font-size: 65px;
        }

        #inpImg {
            display: block;
            margin: auto;
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
        }

        @media (max-width: 480px) {}

        @media (min-width: 481px) and (max-width: 767px) {}

        @media (min-width: 768px) {}
    </style>
</head>

<body>
    <script>
        var tPath = "<?php echo $tPath ?>";
        var csrfToken = "<?php echo $csrf ?>";
        var email = "<?php echo $userAuth['email'] ?>";
        var idUser = "<?php echo $userAuth['id_user'] ?>";
        var idTempat = <?php echo $id ?>;
        var number = "<?php echo $userAuth['number'] ?>";
        var role = "<?php echo $userAuth['role'] ?>";
        var tempats = <?php echo json_encode($tempat) ?>;
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
            <h1>Edit Data Tempat</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="/tempat.php">Kelola Tempat</a></li>
                    <li class="breadcrumb-item"><a href="/tempat/data_tempat.php?id_tempat=<?php $id;?>">Data tempat</a></li>
                    <li class="breadcrumb-item active">Edit Data Tempat</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <h5 class="card-title text-center">Edit Data Tempat</h5>
                            </div>
                            <form method="POST" action="/web/tempat/tempat.php" enctype="multipart/form-data">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="id_user" value="<?php echo $userAuth['id_user']; ?>">
                                <input type="hidden" name="id_tempat" value="<?php echo $id; ?>">
                                <div class="col mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">Nama Tempat</label>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="nama_tempat" value="<?php echo $tempat['nama_tempat']?>">
                                    </div>
                                </div>
                                <div class="col mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">Alamat Tempat</label>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="alamat" value="<?php echo $tempat['alamat_tempat']?>">
                                    </div>
                                </div>
                                <div class="col mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">Nama Pengelola</label>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="nama_pengelola" value="<?php echo $tempat['pengelola']?>">
                                    </div>
                                </div>
                                <div class="col mb-3">
                                    <label for="inputText" class="col-md-12 col-form-label">No. Telpon Pengelola</label>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="phone" value="<?php echo $tempat['contact_person']?>">
                                    </div>
                                </div>
                                <div class="col mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label">Deskripsi Kegiatan</label>
                                    <div class="col-md-12">
                                        <textarea class="form-control" name="deskripsi" style="height: 100px"><?php echo $tempat['deskripsi_tempat']?></textarea>
                                    </div>
                                </div>
                                <div class="col mb-3">
                                    <label for="inputNumber" class="col-sm-2 col-form-label">Gambar tempat</label>
                                    <div class="col-md-12" id="divImg" ondrop="dropHandler(event)" ondragover="dragHandler(event,'over')" ondragleave="dragHandler(event,'leave')">
                                        <input class="form-control" type="file" multiple="false" id="inpFile" name="foto" style="display:none">
                                        <img src="<?php echo $tPath ?>/DatabaseMobile/uploads/tempat<?php echo $tempat['foto_tempat'] ?>" id="inpImg" class="d-block" alt="">
                                        <!-- <img src="<?php echo $tPath ?>/public/img/tempat<?php echo $tempat['foto_tempat'] ?>" id="inpImg" class="d-block" alt=""> -->
                                        <!-- <input class="form-control" name="foto" type="file" id="formFile"> -->
                                    </div>
                                </div>
                                <div class="row mb-3 justify-content-end">
                                    <div class="col-sm-10 text-end">
                                    <a href="/tempat/data_tempat.php" class="btn btn-secondary">Kembali</a>
                                    <button type="button" class="btn btn-tambah" onclick="upload()">Edit</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->
        <!-- start modal edit -->
        <!-- end modal edit -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <?php include(__DIR__.'/../footer.php');
    ?>
  </footer>
  <div id="greenPopup" style="display:none"></div>
  <div id="redPopup" style="display:none"></div>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <!-- Vendor JS Files -->
  <script src="<?php echo $tPath; ?>/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="<?php echo $tPath; ?>/public/assets/vendor/tinymce/tinymce.min.js"></script> 

  <!-- Template Main JS File -->
  <script src="<?php echo $tPath; ?>/public/assets/js/main.js"></script>
  <script src="<?php echo $tPath ?>/public/js/popup.js"></script>
    <script>
        const maxSizeInBytes = 4 * 1024 * 1024; //max file 4MB
        var divText = document.getElementById('divText');
        var divImg = document.getElementById('divImg');
        var inpFile = document.getElementById('inpFile');
        var imgText = document.getElementById('imgText');
        var fileImg = '';
        var uploadStat = false;
        divImg.addEventListener("click", function () {
            inpFile.click();
        });
        function upload() {
            if(uploadStat){
                return;
            }
            var inpNamaTempat = document.querySelector("input[name='nama_tempat']").value;
            var inpAlamatTempat = document.querySelector("input[name='alamat']").value;
            var inpDeskripsiTempat = document.querySelector("textarea[name='deskripsi']").value;
            var inpNamaPengelola = document.querySelector("input[name='nama_pengelola']").value;
            var inpTLP = document.querySelector("input[name='phone']").value;
            //check data if edit or not
            if (!fileImg && inpNamaTempat === tempats.nama_tempat && inpAlamatTempat === tempats.alamat_tempat && inpDeskripsiTempat === tempats.deskripsi_tempat && inpNamaPengelola === tempats.pengelola && inpTLP === tempats.contact_person) {
                showRedPopup('Data belum diubah');
            }
            if((fileImg === null || fileImg === '') && (inpNamaTempat === '' || inpNamaTempat === null) && (inpAlamatTempat === '' || inpAlamatTempat === null) && (inpDeskripsiTempat === "" || inpDeskripsiTempat === null) && (inpNamaPengelola === '' || inpNamaPengelola === null) && ( inpTLP === '' || inpTLP === null)){
                showRedPopup('Data tidak boleh kosong');
            }
            uploadStat = true;
            const formData = new FormData();
            formData.append('_method','PUT');
            formData.append('id_user',idUser);
            formData.append('id_tempat',idTempat);
            formData.append('nama_tempat', document.querySelector('input[name="nama_tempat"]').value);
            formData.append('alamat', document.querySelector('input[name="alamat"]').value);
            formData.append('deskripsi', document.querySelector('textarea[name="deskripsi"]').value);
            formData.append('nama_pengelola', document.querySelector('input[name="nama_pengelola"]').value);
            formData.append('phone', document.querySelector('input[name="phone"]').value);
            if(fileImg !== null && fileImg !== ''){
                formData.append('foto', fileImg, fileImg.name);
            }
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/web/tempat/tempat.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    showGreenPopup(JSON.parse(xhr.responseText));
                    setTimeout(() => {
                        window.location.href = '/tempat/data_tempat.php';
                    }, 1000);
                    return;
                } else {
                    uploadStat = false;
                    showRedPopup(JSON.parse(xhr.responseText));
                    return;
                }
            };
            xhr.onerror = function () {
                uploadStat = false;
                showRedPopup('Request gagal');
                return;
            };
            xhr.send(formData);
        }
        inpFile.addEventListener('change', function (e) {
            if (e.target.files.length === 1) {
                const file = e.target.files[0];
                if (file.type.startsWith('image/')) {
                    if (file.size <= maxSizeInBytes) {
                        const reader = new FileReader();
                        reader.onload = function (event) {
                            document.getElementById('inpImg').src = event.target.result;
                        };
                        reader.readAsDataURL(file);
                        fileImg = file;
                        //delete inside box
                        divImg.style.borderStyle = "none";
                        divImg.style.borderWidth = "0px";
                        divImg.style.borderColor = "transparent";
                    } else {
                        showRedPopup('Ukuran maksimal gambar 4MB !');
                    }
                } else {
                    showRedPopup('File harus Gambar !');    
                }
            }
        });
        function dropHandler(event) {
            event.preventDefault();
            if (event.dataTransfer.items) {
                const file = event.dataTransfer.items[0].getAsFile();
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        document.getElementById('inpImg').src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                    fileImg = file;
                    //delete inside box
                    divImg.style.borderStyle = "none";
                    divImg.style.borderWidth = "0px";
                    divImg.style.borderColor = "transparent";
                } else {
                    showRedPopup('File harus Gambar !');
                }
            }
        }
        function dragHandler(event, con) {
            event.preventDefault();
            if (con == 'over') {
                imgText.innerText = 'Jatuhkan file';
                divImg.classList.add('drag');
            } else if (con == 'leave') {
                imgText.innerText = 'Pilih atau jatuhkan file gambar tempat';
                divImg.classList.remove('drag');
            }
        }
    </script>

</body>

</html>