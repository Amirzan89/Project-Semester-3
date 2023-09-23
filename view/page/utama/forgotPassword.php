<?php 
if(!defined('APP')){
    http_response_code(404);    
    include('view/page/PageNotFound.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo $title?></title>
    <link rel="stylesheet" href="/public/css/forgotPassword.css">
</head>
<body>
    <script>
        var email = '<?php echo $email?>'
    </script>
    <div class="card-body" id="sendEmail">
        <div class="pt-4 pb-2">
            <h5 class="card-title text-left pb-0 fs-4">Lupa Password</h5>
            <p class="text-left small">Pakai fitur ini apabila anda lupa dengan kata sandi.</p>
        </div>
        <form class="row g-3 needs-validation" method="post" novalidate id="ForgotPassword">
            <div class="col-12">
                <label for="email" class="form-label">Masukkan Email Terdaftar</label>
                <div class="input-group has-validation">
                    <input type="email" name="email" class="form-control" id="email" required>
                    <div class="invalid-feedback">Masukkan  Email</div>
                </div>
            </div>
            <div class="col-12">
                <button class="btn btn-success w-100" type="submit">Kirim Tautan Password</button>
            </div>
            <div class="col-12">
                <p class="small mb-0">Sudah punya akun? <a href="/login">Login Sekarang!</a></p>
            </div>
        </form>
    </div>
    <div id="otp" style="display: none;">
        <form id="verifyOTP">
            <h3>Lupa Password</h3>
            <p>Pakai fitur ini apabila anda lupa dengan kata sandi</p>
            <p>Verifikasi OTP</p>
            <div class="input">
                <input type="text" id="otp1">
                <input type="text" id="otp2">
                <input type="text" id="otp3">   
                <input type="text" id="otp4">
                <input type="text" id="otp5">
                <input type="text" id="otp6">
            </div>
            <input type="submit" value="Konfirmasi">
            <span>Tidak Menerima Kode OTP ? <a onclick="sendOtp()">kirim ulang</a></span>
        </form>
    </div>
    <div id="gantiPassword" style="display: none;">
        <form class="row g-3 needs-validation" novalidate id="verifyChange">
            <div class="col-12">
                <?php if(isset($description) && $description == 'createUser'){ ?>
                    <label for="newPassword" class="form-label">Password</label>
                    <div class="input-group has-validation">
                    <input type="password" name="pass" class="form-control" id="password" required>
                    <div class="invalid-feedback">Masukkan Password</div>
                </div>
                <?php }else{ ?> 
                <label for="newPassword" class="form-label">Password Baru</label>
                <div class="input-group has-validation">
                    <input type="password" name="pass" class="form-control" id="password" required>
                    <div class="invalid-feedback">Masukkan Password Baru</div>
                </div>
                <?php } ?>
            </div>
            <div class="col-12">
            <?php if(isset($description) && $description == 'createUser'){ ?>
                    <label for="confirmPassword" class="form-label">Konfirmasi Password</label>
                    <div class="input-group has-validation">
                        <input type="password" name="pass_new" class="form-control" id="password_new" required>
                        <div class="invalid-feedback">Masukkan Konfirmasi Password</div>
                    </div>
                    <?php }else{ ?>
                    <label for="confirmPassword" class="form-label">Konfirmasi Password Baru</label>
                    <div class="input-group has-validation">
                        <input type="password" name="pass_new" class="form-control" id="password_new" required>
                        <div class="invalid-feedback">Masukkan Konfirmasi Password Baru</div>
                    </div>
                <?php } ?>
            </div>
            <div class="col-12">
            <?php if(isset($description) && $description == 'createUser'){ ?>
                    <button class="btn btn-success w-100" type="submit">Buat Akun</button>
            <?php }else{ ?>
                    <button class="btn btn-success w-100" type="submit">Ganti Password</button>
            <?php } ?>
            </div>
        </form>
    </div>
    <div id="preloader" style="display: none;"></div>
    <div id="greenPopup" style="display:none"></div>
    <div id="redPopup" style="display:none"></div>
    <script>
  var waktu = '';
    <?php if(empty($div) || is_null($div) || !isset($div)){ ?>
        var email = "";
        var div = "";
        var description = "";
        var otp = "";
        var link = "";
    <?php }else{ ?>
        var email = "<?php echo $email ?>";
        var div = "<?php echo $div ?>";
        var description = "<?php echo $description ?>";
        // var otp = "{{$code}}";
        var link = "<?php echo $link ?>";
        <?php if(isset($nama)){ ?>
            var nama = "<?php echo $nama?>";
        <?php }?>
        console.log('divvv ');
        console.log("<?php echo $email ?>");
        console.log("<?php echo $div ?>");
        console.log("<?php echo $description ?>");
        console.log("<?php echo $code ?>");
        console.log("<?php echo $link ?>");
    <?php } ?>
    if(div == 'verifyDiv'){
        document.querySelector('div#sendEmail').style.display = 'none';
        document.querySelector('div#otp').style.display = 'none';
        document.querySelector('div#gantiPassword').style.display = 'block';
    }
</script>
<script src="/public/js/forgotPassword.js?"></script>
</body>
</html>