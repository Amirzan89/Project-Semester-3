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
    <link rel="stylesheet" href="/public/css/evet/dashboard.css">
</head>
<body class="bg-red">
    <p>aloginvianvnavnaiv</p>
    <h1>whvbabvavau</h1>
    <form id="tambahEvent">
    </form>
    <a href="/event/dashboard"><h1>masuk event</h1></a>
    <script src="<?php echo $tPath.'/public/js/utama/dashboard.js?'?>"></script>
</body>
</html>