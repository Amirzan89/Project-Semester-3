<?php
if(!defined('APP')){
    $rootDir = dirname(dirname(__DIR__));
    http_response_code(404);
    include($rootDir.'/view/page/PageNotFound.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="view/css/PageNotFound.css">
</head>
<body>
    <div>
        <div class="bg"></div>
        <div class="content">
            <h1>404</h1>
            <p>Page not found</p>
        </div>
    </div>
</body>
</html>