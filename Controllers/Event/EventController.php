<?php
namespace Controllers\Event;
if(!defined('APP')){
    http_response_code(404);
    include('view/page/PageNotFound.php');
    exit();
}
use Database\Database;
use Database\Models\Event;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Exception;
class EventController{
    private static $database;
    private static $con;
    private static $kategori;
    public function __construct(){
        self::$database = Database::getInstance();
        self::$con = self::$database->getConnection();
        self::$kategori = ['olahraga','makanan'];
    }
    //untuk masyarakat
    public function tambahEventMasyarakat($data, $uri = null){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                return ['status'=>'error','message'=>'ID User harus di isi','code'=>400];
            }
            if (!isset($data['nama_event']) || empty($data['nama_event'])) {
                return ['status'=>'error','message'=>'Nama event harus di isi','code'=>400];
            } elseif (strlen($data['nama_event']) < 5) {
                return ['status'=>'error','message'=>'Nama event minimal 5 karakter','code'=>400];
            } elseif (strlen($data['nama_event']) > 50) {
                return ['status'=>'error','message'=>'Nama event maksimal 50 karakter','code'=>400];
            }
            if (strlen($data['deskripsi']) > 4000) {
                return ['status'=>'error','message'=>'deskripsi event maksimal 4000 karakter','code'=>400];
            }
            if (!isset($data['kategori']) || empty($data['kategori'])) {
                return ['status'=>'error','message'=>'Kategori event harus di isi','code'=>400];
            }else if(!in_array($data['kategori'],['olahraga','seni','budaya'])){
                return ['status'=>'error','message'=>'Kategori salah','code'=>400];
            }
            if (!isset($data['tanggal_awal']) || empty($data['tanggal_awal'])) {
                return ['status'=>'error','message'=>'Tanggal awal harus di isi','code'=>400];
            }else if (!isset($data['tanggal_akhir']) || empty($data['tanggal_akhir'])) {
                return ['status'=>'error','message'=>'Tanggal akhir harus di isi','code'=>400];
            }
            $tanggal_awal = date('Y-m-d H:i:s',strtotime($data['tanggal_awal']));
            $tanggal_akhir = date('Y-m-d H:i:s',strtotime($data['tanggal_akhir']));
            // Check if the date formats are valid
            if (!$tanggal_awal) {
                return ['status' => 'error', 'message' => 'Format tanggal awal tidak valid', 'code' => 400];
            }else if (!$tanggal_akhir) {
                return ['status' => 'error', 'message' => 'Format tanggal akhir tidak valid', 'code' => 400];
            }
            // Compare the dates
            if ($tanggal_awal > $tanggal_akhir) {
                return ['status' => 'error', 'message' => 'Tanggal akhir tidak boleh lebih awal dari tanggal awal', 'code' => 400];
            }
            $query = "INSERT INTO event (nama_event,deskripsi_event, kategori_event, tanggal_awal_event, tanggal_akhir_event, link_pendaftaran, poster_event,status,id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = self::$con->prepare($query);
            $status = 'terkirim';
            $data['kategori'] = strtoupper($data['kategori']);
            $stmt->bind_param("sssssssss", $data['nama_event'], $data[  'deskripsi'], $data['kategori'],$tanggal_awal, $tanggal_akhir, $data['link'], $data['poster'],$status,$data['id_user']);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                    return ['status'=>'success','message'=>'event berhasil ditambahkan'];
            } else {
                $stmt->close();
                return ['status'=>'error','message'=>'event gagal ditambahkan','code'=>400];
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $erorr = json_decode($error, true);
            if ($erorr === null) {
                $responseData = array(
                    'status' => 'error',
                    'message' => $error,
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'message' => $erorr->message,
                );
            }
            return $responseData;
        }
    }
    public function editEvent($data, $uri = null){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                return ['status'=>'error','message'=>'ID User harus di isi','code'=>400];
            }
            if(!isset($data['id_event']) || empty($data['id_event'])){
                return ['status'=>'error','message'=>'ID event harus di isi','code'=>400];
            }
            if (!isset($data['nama_event']) || empty($data['nama_event'])) {
                return ['status'=>'error','message'=>'Nama event harus di isi','code'=>400];
            } elseif (strlen($data['nama_event']) < 5) {
                return ['status'=>'error','message'=>'Nama event minimal 5 karakter','code'=>400];
            } elseif (strlen($data['nama_event']) > 50) {
                return ['status'=>'error','message'=>'Nama event maksimal 50 karakter','code'=>400];
            }
            if (strlen($data['deskripsi_event']) > 4000) {
                return ['status'=>'error','message'=>'deskripsi event maksimal 4000 karakter','code'=>400];
            }
            if (!isset($data['kategori_event']) || empty($data['kategori_event'])) {
                return ['status'=>'error','message'=>'Kategori event harus di isi','code'=>400];
            }else if(!in_array($data['kategori_event'],['olahraga','seni','budaya'])){
                return ['status'=>'error','message'=>'Kategori salah','code'=>400];
            }
            if (!isset($data['tanggal_awal_event']) || empty($data['tanggal_awal_event'])) {
                return ['status'=>'error','message'=>'Tanggal awal harus di isi','code'=>400];
            }else if (!isset($data['tanggal_akhir_event']) || empty($data['tanggal_akhir_event'])) {
                return ['status'=>'error','message'=>'Tanggal akhir harus di isi','code'=>400];
            }
            $tanggal_awal = date('Y-m-d H:i:s',strtotime($data['tanggal_awal_event']));
            $tanggal_akhir = date('Y-m-d H:i:s',strtotime($data['tanggal_akhir_event']));
            // Check if the date formats are valid
            if (!$tanggal_awal) {
                return ['status' => 'error', 'message' => 'Format tanggal awal tidak valid', 'code' => 400];
            }else if (!$tanggal_akhir) {
                return ['status' => 'error', 'message' => 'Format tanggal akhir tidak valid', 'code' => 400];
            }
            // Compare the dates
            if ($tanggal_awal > $tanggal_akhir) {
                return ['status' => 'error', 'message' => 'Tanggal akhir tidak boleh lebih awal dari tanggal awal', 'code' => 400];
            }
            $query = "UPDATE event SET nama_event = ?, deskripsi_event = ?, kategori_event = ?, tanggal_awal_event = ?, tanggal_akhir_event = ?, link_pendaftaran = ?, poster_event = ?, status = ? WHERE id_user = ? AND id_event = ?";
            // $query = "INSERT INTO event (nama_event,deskripsi_event, kategori_event, tanggal_awal_event, tanggal_akhir_event, link_pendaftaran, poster_event,status,id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = self::$con->prepare($query);
            $status = 'terkirim';
            $data['kategori'] = strtoupper($data['kategori']);
            $stmt->bind_param("ssssssssii", $data['nama_event'], $data['deskripsi_event'], $data['kategori_event'], $tanggal_awal, $tanggal_akhir, $data['link_pendaftaran'], $data['poster_event'], $status, $data['id_user'], $data['id_event']);
            // $stmt->bind_param("sssssssss", $data['nama_event'], $data[  'deskripsi_event'], $data['kategori'],$tanggal_awal, $tanggal_akhir, $data['link'], $data['poster'],$status,$data['id_user']);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                    return ['status'=>'success','message'=>'event berhasil diupdate'];
            } else {
                $stmt->close();
                return ['status'=>'error','message'=>'event gagal diupdate','code'=>400];
            }
        }catch(Exception $e){
            echo $e->getTraceAsString();
            $error = $e->getMessage();
            $erorr = json_decode($error, true);
            if ($erorr === null) {
                $responseData = array(
                    'status' => 'error',
                    'message' => $error,
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'message' => $erorr->message,
                );
            }
            return $responseData;
        }
    }
    public function hapusEvent($data, $uri = null){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                return ['status'=>'error','message'=>'ID User harus di isi','code'=>400];
            }
            if(!isset($data['id_event']) || empty($data['id_event'])){
                return ['status'=>'error','message'=>'ID event harus di isi','code'=>400];
            }
            $query = "DELETE FROM event WHERE id_event = ? AND id_user = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('ss', $data['id_event'],$data['id_user']);
            if ($stmt[2]->execute()) {
                $stmt[2]->close();
                return ['status'=>'success','message'=>'event berhasil dihapus'];
            } else {
                $stmt[2]->close();
                return ['status'=>'error','message'=>'event gagal dihapus','terserah'];
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $erorr = json_decode($error, true);
            if ($erorr === null) {
                $responseData = array(
                    'status' => 'error',
                    'message' => $error,
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'message' => $erorr->message,
                );
            }
            return $responseData;
        }
    }
    //khusus admin event dan super admin
    public function prosesEvent($data, $uri = null){
        if(!isset($data['id_user']) || empty($data['id_user'])){
            return ['status'=>'error','message'=>'ID User harus di isi','code'=>400];
        }
        if (!isset($data['nama_event']) || empty($data['nama_event'])) {
            return ['status'=>'error','message'=>'Nama event harus di isi','code'=>400];
        } elseif (strlen($data['nama_event']) < 5) {
            return ['status'=>'error','message'=>'Nama event minimal 5 karakter','code'=>400];
        } elseif (strlen($data['nama_event']) > 50) {
            return ['status'=>'error','message'=>'Nama event maksimal 50 karakter','code'=>400];
        }
        if (!isset($data['deskripsi']) || empty($data['deskripsi'])) {
            return ['status'=>'error','message'=>'Deskripsi event harus di isi','code'=>400];
        } elseif (strlen($data['deskripsi']) > 4000) {
            return ['status'=>'error','message'=>'deskripsi event maksimal 4000 karakter','code'=>400];
        }
        if (!isset($data['kategori']) || empty($data['kategori'])) {
            return ['status'=>'error','message'=>'Kategori event harus di isi','code'=>400];
        }else if(!in_array($data['kategori'],['olahraga','seni'])){
            return ['status'=>'error','message'=>'Kategori salah','code'=>400];
        }
        if (!isset($data['tanggal_awal']) || empty($data['tanggal_awal'])) {
            return ['status'=>'error','message'=>'Tanggal awal harus di isi','code'=>400];
        }else if (!isset($data['tanggal_akhir']) || empty($data['tanggal_akhir'])) {
            return ['status'=>'error','message'=>'Tanggal akhir harus di isi','code'=>400];
        }
    }
    public function verfikasiEvent($data, $uri = null){
        if(!isset($data['id_user']) || empty($data['id_user'])){
            return ['status'=>'error','message'=>'ID User harus di isi','code'=>400];
        }
        if (!isset($data['nama_event']) || empty($data['nama_event'])) {
            return ['status'=>'error','message'=>'Nama event harus di isi','code'=>400];
        } elseif (strlen($data['nama_event']) < 5) {
            return ['status'=>'error','message'=>'Nama event minimal 5 karakter','code'=>400];
        } elseif (strlen($data['nama_event']) > 50) {
            return ['status'=>'error','message'=>'Nama event maksimal 50 karakter','code'=>400];
        }
        if (!isset($data['deskripsi']) || empty($data['deskripsi'])) {
            return ['status'=>'error','message'=>'Deskripsi event harus di isi','code'=>400];
        } elseif (strlen($data['deskripsi']) > 4000) {
            return ['status'=>'error','message'=>'deskripsi event maksimal 4000 karakter','code'=>400];
        }
        if (!isset($data['kategori']) || empty($data['kategori'])) {
            return ['status'=>'error','message'=>'Kategori event harus di isi','code'=>400];
        }else if(!in_array($data['kategori'],['olahraga','seni'])){
            return ['status'=>'error','message'=>'Kategori salah','code'=>400];
        }
        if (!isset($data['tanggal_awal']) || empty($data['tanggal_awal'])) {
            return ['status'=>'error','message'=>'Tanggal awal harus di isi','code'=>400];
        }else if (!isset($data['tanggal_akhir']) || empty($data['tanggal_akhir'])) {
            return ['status'=>'error','message'=>'Tanggal akhir harus di isi','code'=>400];
        }
    }
}
?>