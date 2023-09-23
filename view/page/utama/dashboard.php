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
    <title>Dashboard</title>
    <link rel="stylesheet" href="/event/public/css/dashboard.css">
</head> 
<body class="bg-red">
    <p>halaman dasboard utama </p>
    <br>
    <a href="/event/dashboard"><h1>halaman event</h1></a>
    <br>
    <a href="/tempat/dashboard"><h1>halaman tempat</h1></a>
    <br>
    <a href="/pentas/dashboard"><h1>halaman izin pentas seni</h1></a>
    <br>
    <a href="/seniman/dashboard"><h1>halaman seniman</h1></a>
</body>
</html>