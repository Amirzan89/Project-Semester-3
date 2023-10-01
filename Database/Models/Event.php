<?php 
namespace Database\Models;
class Event{
    public static $eventColumns = ['id_event','nama_event','deskripsi_event','kategori_event','tanggal_awal_event','tanggal_akhir_event','link_pendaftaran','poster_event','status'];
    //status = ['terkirim','disetujui','ditolak'];
}
?>