<?php
require_once(__DIR__ . '/../../mobile/seniman/seniman.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = SenimanMobile::handle();
    $data['desc'] = 'diajukan';
    $getPerpanjangan($data);
}
//protection
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    include(__DIR__.'/../../notfound.php');
}
require('../Koneksi.php');

$id_user = $_POST['id_user'];

$response = array(); 

$sql = "SELECT * FROM perpanjangan WHERE status = 'diajukan' AND id_user = '$id_user' ORDER BY tgl_pembuatan desc;";
$result = $konek->query($sql);

if ($result->num_rows > 0) {
    $seniman = array();

    while ($row = $result->fetch_assoc()) {
        $seniman[] = $row; 
    }

    $response["kode"] = 1;
    $response["pesan"] = "Data Tersedia";
    $response["data"] = $seniman;
} else {
    $response["kode"] = 0;
    $response["pesan"] = "Data Tidak Tersedia";
}

echo json_encode($response);

mysqli_close($konek);
?>
