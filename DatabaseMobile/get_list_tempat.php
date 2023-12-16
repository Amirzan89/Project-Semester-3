<?php
require('Koneksi.php');

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT id_tempat, nama_tempat, alamat_tempat, foto_tempat FROM list_tempat"; 
    $result = $konek->query($sql);

    if ($result->num_rows >= 1) {
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $row['foto_tempat'] = 'uploads/tempat' . $row['foto_tempat'];
            $data[] = $row;
        }

        $response = array("status"=>"success", "message"=>"data berhasil didapatkan", "data"=>$data);
        echo json_encode($response);
        mysqli_close($konek);
        exit();
    } else {
        $response = array("status"=>"error", "message"=>"error get data");
        echo json_encode($response);
        mysqli_close($konek);
        exit();
    }
} else {
    $response = array("status"=>"error", "message"=>"the method is not get");
    echo json_encode($response);
    mysqli_close($konek);
    exit();
}
?>
