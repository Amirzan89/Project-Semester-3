<?php 
if(!defined('APP')){
    http_response_code(404);
    include('view/page/PageNotFound.php');
    exit();
}
$tPath = "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="<?php echo '/public/css/utama/Admin.css' ?>">
    <script></script>
</head> 
<body class="bg-red">
    <script>
        var csrfToken = "<?php echo $csrf ?>";
        var email = "<?php echo $user['email'] ?>";
        var idUser = "<?php echo $user['id_user'] ?>";
        var number = "<?php echo $number ?>";
        var role = "<?php echo $role ?>";
        var showForm, closeForm;
    </script>
    <p>halaman super admin</p>
    <br>
    <a href="/dashboard"><h1>kembali</h1></a>
    <br>
    <button onclick="logout()">metu</button>
    <?php if($role == 'super admin'){ ?>
        <button onclick="showForm('tambah')">tambah admin</button><br>
        <form id=""></form>
    <?php } ?>
    <div id="preloader" style="display: none;"></div>
    <div id="greenPopup" style="display:none"></div>
    <div id="redPopup" style="display:none"></div>
    <script src="<?php echo $tPath.'/public/js/utama/Admin.js?'?>"></script>
</body>
</html>