<?php 
if(!defined('APP')){
    $rootDir = dirname(dirname(__DIR__));
    http_response_code(404);
    include($rootDir.'/view/page/PageNotFound.php');
    exit();
}
$tPath = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/public/css/seniman/seniman.css">
    <!-- <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
</head>
<body>
    <script>
        var csrfToken = "<?php echo($csrf) ?>";
        var email = "<?php echo($user['email'])?>";
        var idUser = "<?php echo($user['id_user'])?>";
        var number = "<?php echo($number) ?>";
    </script>
    <!-- <form id="tambahEventForm" method="POST">
        <div class="header">
            <h1>daftar event</h1>
        </div>
        <div class="row">
            <label>Nama event</label>
            <input type="text" name="inpNamaEvent" id="inpNamaEvent">
        </div>
        <div class="row">
            <label>Deskripsi event</label>
            <textarea name="inpDeskripsiEvent" id="inpDeskripsiEvent"></textarea>
        </div>
        <div class="row">
            <label>Daftar kategori</label>
            <select name="inpKategoriEvent" id="inpKategoriEvent" multiple>
                <option value="olahraga">Olahraga</option>  
                <option value="seni">Seni</option>
                <option value="budaya">Budaya</option>
                <option value="lain-lain">Lain-lain</option>
            </select>
        </div>
        <div class="row">
            <label>Tanggal awal event</label>
            <input type="datetime-local" name="inpTAwalEvent" id="inpTAwalEvent">
        </div>
        <div class="row">
            <label>Tanggal akhir event</label>
            <input type="datetime-local" name="inpTAkhirEvent" id="inpTAkhirEvent">
        </div>
        <div class="row">
            <label>link pendaftaran event</label>
            <input type="text" name="inpPendaftaranEvent" id="inpPendaftaranEvent">
        </div>
        <div class="row">
            <label>Poster event</label>
            <input type="file" name="inpPosterEvent" id="inpPosterEvent">
        </div>
        <input type="submit" value="Kirim">
    </form> -->
    <a href="/dashboard"><h1>kembali</h1></a>
    <br>
    <form method="POST" id="logoutForm">
        <input type="submit" value="metu">
    </form>
    <div id="preloader" style="display: none;"></div>
    <div id="greenPopup" style="display:none"></div>
    <div id="redPopup" style="display:none"></div>
    <script src="<?php echo $tPath.'/public/js/seniman/seniman.js?'?>"></script>
</body>
</html>