<?php
require('Koneksi.php');

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $sql = "SELECT  d.id_detail, d.nama_event, d.tanggal_awal, d.tempat_event, d.poster_event  FROM `events` AS e  JOIN detail_events AS d ON e.id_detail = d.id_detail WHERE status = 'diterima' AND MONTH(d.tanggal_awal) = MONTH(NOW()) ORDER BY ABS(TIMESTAMPDIFF(SECOND, NOW(), tanggal_awal)) ASC LIMIT 5";
    $result = $konek->query($sql);

    if($result){
        $hasil = $result->fetch_all(MYSQLI_ASSOC);
        foreach ($hasil as &$event) {
            $event['poster_event'] = "uploads/events" . $event['poster_event'];
        }
        $response = array("status" => "success", "message" => "data diambil", "data"=>$hasil);
    }else{
        $response = array("status" => "error", "message" => "fail get data");
    }
 
 
} else {
    $response = array("status" => "error", "message" => "Metode tidak valid");
}

echo json_encode($response);
mysqli_close($konek);
?>