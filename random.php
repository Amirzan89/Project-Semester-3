<?php 
require_once(__DIR__.'/web/koneksi.php');
require_once(__DIR__.'/web/authenticate.php'); 
require_once(__DIR__.'/env.php');
loadEnv();
$db = koneksi::getInstance();
$con = $db->getConnection();
$query = mysqli_query($con, "SELECT id_tempat, nama_tempat, alamat_tempat, foto_tempat FROM list_tempat");
echo json_encode(mysqli_fetch_array($query));
exit();
$userAuth = authenticate($_POST,[
      'uri'=>$_SERVER['REQUEST_URI'],
      'method'=>$_SERVER['REQUEST_METHOD'
    ]
],$con);
if($userAuth['status'] == 'success'){
  $userAuth = $userAuth['data'];
  if(!in_array($userAuth['role'],['super admin','admin seniman','admin tempat','admin sewa','admin pentas'])){
    header('Location: /dashboard.php');
  }
  $tPath = ($_SERVER['APP_ENV'] == 'local') ? '' : $_SERVER['APP_FOLDER'];
}
?>