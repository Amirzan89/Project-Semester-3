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
    if (is_array($inpDate) && count($inpDate) > 0) {
        foreach ($inpDate as &$row) {
            foreach (['tanggal', 'tanggal_awal', 'tanggal_akhir'] as $dateField) {
                if (isset($row[$dateField]) && $row[$dateField] !== null) {
                    $monthNumber = date('m', strtotime($row[$dateField]));
                    $indonesianMonth = $monthTranslations[$monthNumber];
                    $row[$dateField] = preg_replace('/(\d{4})-(\d{2})-0?(\d{1,2})/', '$3 ' . $indonesianMonth . ' $1', $row[$dateField]);
                }
            }
        }
    }
    return $inpDate;
}
?>