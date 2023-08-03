<?php 
namespace Controllers;
use Database\Database;
use Controllers\Mail\MailController;
// use Exception;
class UserController{
    private static $database;
    private static $con;
    public function __construct(){
        self::$database = Database::getInstance();
        self::$con = self::$database->getConnection();
    }
    public function createUser($data, MailController $mailController){
        try{
            if (!isset($data['email']) || empty($data['email'])) {
                throw new \Exception(json_encode(['status'=>'error','message'=>'Email wajib di isi','code'=>400]));
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception(json_encode(['status'=>'error','message'=>'Email invalid','code'=>400]));
            }
            if (!isset($data['password']) || empty($data['password'])) {
                throw new \Exception(json_encode(['status'=>'error','message'=>'Password wajib di isi00','code'=>400]));
            } elseif (strlen($data['password']) < 8) {
                throw new \Exception(json_encode(['status'=>'error','message'=>'Password minimal 8 karakter','code'=>400]));
            } elseif (strlen($data['password']) > 25) {
                throw new \Exception(json_encode(['status'=>'error','message'=>'Password maksimal 8 karakter','code'=>400]));
            } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password'])) {
                throw new \Exception(json_encode(['status' => 'error', 'message' => 'Password harus mengandung setidaknya satu huruf kecil, satu huruf besar, dan satu angka', 'code' => 400]));
            }
            // Validate 'nama' field
            if (!isset($data['nama']) || empty($data['nama'])) {
                throw new \Exception(json_encode(['status' => 'error', 'message' => 'Nama Wajib di isi', 'code' => 400]));
            }
            // Check if there are any validation errors
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            $query = "INSERT INTO users (email,password, nama, email_verified) VALUES (?, ?, ?, ?)";
            $verified = false;
            $stmt = self::$con->prepare($query);
            $stmt->bind_param("sssb", $data['email'], $hashedPassword, $data['nama'],$verified);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $email = $mailController->createVerifyEmail($data);
                $stmt->close();
                if($email['status'] == 'error'){
                    return ['status'=>'error','message'=>$email['message']];
                }else{
                    return ['status'=>'success','message'=>$email['message'],'data'=>$email['data']];
                }
            } else {
                $stmt->close();
                return ['status'=>'error','message'=>'Akun Gagal Dibuat'];
            }
        }catch(\Exception $e){
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
//      public function getUser($email, $data, $where){
    //         try{
//             $database = Database::getInstance();
//             $con = $database->getConnection();
//             $check = "SELECT email FROM users WHERE BINARY email LIKE CONCAT('%', ?, '%') LIMIT 1";
//             $Stmt[0] = $con->prepare($check);
//             $email = '%' . $email . '%';
//             $Stmt[0]->bind_param('s', $email);
//             $Stmt[0]->execute();
//             // Bind the result
//             $Stmt[0]->bind_result($email);
//             if ($Stmt[0]->fetch()) {
    //                 $query = "SELECT $data FROM users WHERE BINARY email LIKE CONCAT('%', ?, '%') LIMIT 1";
    //                 $Stmt[1] = $con->prepare($query);
    //                 $email = '%' . $email . '%';
    //                 $Stmt[1]->bind_param('s', $email);
//                 $Stmt[1]->execute();
//                 // Bind the result
//                 $result = [];
//                 $Stmt[1]->bind_result($result);
//                 while ($Stmt[1]->fetch()) {
//                 }
//                 return $email;
//             } else {
    
    //                 // No row found with the email
    //                 echo "Email not found.";
//             }
//             foreach($Stmt as $stmt){
    //                 $stmt->close();
    //             }
    //         }catch(Exception $e){
        //             return $e->getMessage();
        //         }
        //     }
    public function getUser($email, $data){
        $database = Database::getInstance();
        $con = $database->getConnection();
        $columns = implode(', ', $data); // Convert the array of columns to a comma-separated string
        $query = "SELECT $columns FROM users WHERE BINARY email LIKE CONCAT('%', ?, '%') LIMIT 1";
        $stmt = $con->prepare($query);
        $email = '%' . $email . '%';
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $bindResultArray = [];
        foreach ($data as $column) {
            $bindResultArray[] = &$$column;
        }
        call_user_func_array([$stmt, 'bind_result'], $bindResultArray);
        $result = [];
        if ($stmt->fetch()) {
            // Fetch the data and store it in the $result array
            foreach ($data as $column) {
                $result[$column] = $$column;
            }
        }
        $stmt->close();
        return $result;
    }
}
?>