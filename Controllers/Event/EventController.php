<?php
namespace Controllers\Event;
use Database\Database;
use Database\Models\Event;
use Carbon\Carbon;
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
            //check role
            if(!isset($data['role']) || empty($data['role'])){
                return ['status'=>''];
            }
            if(!isset($data['id_user']) || empty($data['id_user'])){
                return ['status'=>'error','message'=>'ID User harus di isi','code'=>400];
            }
            if (!isset($data['nama_event']) || empty($data['nama_event'])) {
                return ['status'=>'error','message'=>'Nama event harus di isi','code'=>400];
            } elseif (strlen($data['nama']) < 8) {
                return ['status'=>'error','message'=>'Nama event minimal 8 karakter','code'=>400];
            } elseif (strlen($data['nama']) > 50) {
                return ['status'=>'error','message'=>'Nama event maksimal 50 karakter','code'=>400];
            }
            if (!isset($data['deskripsi']) || empty($data['deskripsi'])) {
                return ['status'=>'error','message'=>'Deskripsi event harus di isi','code'=>400];
            } elseif (strlen($data['deskripsi']) > 4000) {
                return ['status'=>'error','message'=>'deskripsi event maksimal 4000 karakter','code'=>400];
            }
            if (!isset($data['kategori']) || empty($data['kategori'])) {
                return ['status'=>'error','message'=>'Kategori event harus di isi','code'=>400];
            }else if(!in_array($data['kategori'],[''])){
                return ['status'=>'error','Kategori salah','code'=>400];
            }
            if (!isset($data['tanggal_awal']) || empty($data['tanggal_awal'])) {
                return ['status'=>'error','message'=>'Tanggal awal harus di isi','code'=>400];
            }else if (!isset($data['tanggal_akhir']) || empty($data['tanggal_akhir'])) {
                return ['status'=>'error','message'=>'Tanggal akhir harus di isi','code'=>400];
            }
            // Create DateTime objects for both tanggal_awal and tanggal_akhir
            $tanggal_awal = DateTime::createFromFormat('d-m-Y s:i:H', $data['tanggal_awal']);
            $tanggal_akhir = DateTime::createFromFormat('d-m-Y s:i:H', $data['tanggal_akhir']);
            // Check if the date formats are valid
            if (!$tanggal_awal) {
                return ['status' => 'error', 'message' => 'Format tanggal awal tidak valid', 'code' => 400];
            }else if (!$tanggal_akhir) {
                return ['status' => 'error', 'message' => 'Format tanggal akhir tidak valid', 'code' => 400];
            }
            // Compare the dates
            if ($tanggal_akhir < $tanggal_awal) {
                return ['status' => 'error', 'message' => 'Tanggal akhir tidak boleh lebih awal dari tanggal awal', 'code' => 400];
            }
            return ['status'=>'success','message'=>'data lengkap'];
            $query = "INSERT INTO event (email,password, nama, email_verified, level,created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $now = Carbon::now('Asia/Jakarta');
            $verified = false;
            $stmt = self::$con->prepare($query);
            $level = 'ADMIN';
            $stmt->bind_param("sssbsss", $data['email'], $data[''], $data['nama'],$verified, $level, $now, $now);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                    return ['status'=>'error','message'=>'event berhasil ditambahkan'];
            } else {
                $stmt->close();
                return ['status'=>'error','message'=>'event gagal ditambahkan'];
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
    //
    public function editEvent($data, $uri = null){
        //
    }
    public function hapusEvent($data, $uri = null){
        //
    }
    //khusus admin event dan super admin
    public function verifikasiEvent($data, $uri = null){
        //
    }
}
?>