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
    <link rel="stylesheet" href="<?php echo '/public/css/utama/dashboard.css' ?>">
    <script></script>
</head> 
<body class="bg-red">
    <script>
        var csrfToken = "<?php echo($GLOBALS['csrf']) ?>";
        var email = "<?php echo($data['email'])?>";
        var number = "<?php echo($number) ?>";
    </script>
    <p>halaman dasboard utama </p>
    <br>
    <a href="/event/dashboard"><h1>halaman event</h1></a>
    <br>
    <a href="/tempat/dashboard"><h1>halaman tempat</h1></a>
    <br>
    <a href="/pentas/dashboard"><h1>halaman izin pentas seni</h1></a>
    <br>
    <a href="/seniman/dashboard"><h1>halaman seniman</h1></a>
    <form method="POST" id="logoutForm">
        <input type="submit" value="metu">
            <span>Keluar</span>
    </form>
    <script src="<?php echo $tPath.'/public/js/utama/dashboard.js?'?>"></script>
</body>
</html>