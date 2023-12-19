<?php
function changeMonth($inpDate){
    $monthTranslations = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ];
    if (is_array($inpDate) && count($inpDate) > 0 && isset($inpDate[0]['tanggal'])) {
        foreach ($inpDate as &$row) {
            if(isset($row['tanggal'])){
                $monthNumber = date('m', strtotime($row['tanggal']));
                $indonesianMonth = $monthTranslations[$monthNumber];
                $row['tanggal'] = preg_replace('/(\d{4})-(\d{2})-0?(\d{1,2})/', '$3 ' . $indonesianMonth . ' $1', $row['tanggal']);
            }
            if(isset($row['tanggal_awal'])){
                $monthNumber = date('m', strtotime($row['tanggal_awal']));
                $indonesianMonth = $monthTranslations[$monthNumber];
                $row['tanggal_awal'] = preg_replace('/(\d{4})-(\d{2})-0?(\d{1,2})/', '$3 ' . $indonesianMonth . ' $1', $row['tanggal_awal']);
            }
            if(isset($row['tanggal_akhir'])){
                $monthNumber = date('m', strtotime($row['tanggal_akhir']));
                $indonesianMonth = $monthTranslations[$monthNumber];
                $row['tanggal_akhir'] = preg_replace('/(\d{4})-(\d{2})-0?(\d{1,2})/', '$3 ' . $indonesianMonth . ' $1', $row['tanggal_akhir']);
            }
        }
    }
    return $inpDate;
}
?>