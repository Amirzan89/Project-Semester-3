<?php 
namespace Controllers\Tempat;
use Database\Database;
use Database\Models\Tempat;
use Database\Models\ListTempat;
class TempatController{
    private static $database;
    private static $con;
    public function __construct(){
        self::$database = Database::getInstance();
        self::$con = self::$database->getConnection();
    }
    //hanya aadmin
    public function tambahTempat($data, $uri = null){
        try{
            $data = $data['request'];
            if (!isset($data['nama_tempat']) || empty($data['nama_tempat'])) {
                return ['status' => 'error', 'message' => 'Nama tempat harus di isi', 'code' => 400];
            }
            if (!isset($data['alamat_tempat']) || empty($data['alamat_tempat'])) {
                return ['status' => 'error', 'message' => 'Alaamt tempat harus di isi', 'code' => 400];
            }
            $namaTempat = $data['nama_tempat'];
            $alamatTempat = $data['alamat_tempat'];
            $deskripsiTempat = $data['deskripsi_tempat'];
            $query = "INSERT INTO list_tempat (nama_tempat,alamat_tempat, deskripsi_tempat) VALUES (?, ?, ?)";
            $stmt = self::$con->prepare($query);
            $stmt->bind_param("sss", $namaTempat, $alamatTempat,$deskripsiTempat);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                return['status'=>'success','message'=>'Berhasil menambahkan tempat'];
            }else{
                return ['status'=>'error','message'=>'Gagal menambahkan tempat','code'=>'400'];
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
    public function sewaTempat($data, $uri = null){
        
    }
    public function editTempat($data, $uri = null){
        
    }
    public function hapusTempat($data, $uri = null){
        //
    }
}
?>