<?php
class seniman{
//untuk masyarakat
    public static function tambahSenimanMasyarakat($data, $uri = null){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                echo "<script>alert('ID User harus di isi')</script>";
                exit();
            }
            if (!isset($data['nama_seniman']) || empty($data['nama_seniman'])) {
                echo "<script>alert('Nama Seniman harus di isi')</script>";
                exit();
            } elseif (strlen($data['nama_seniman']) < 5) {
                echo "<script>alert('Nama Seniman minimal 5 karakter')</script>";
                exit();
            } elseif (strlen($data['nama_seniman']) > 50) {
                echo "<script>alert('Nama Seniman maksimal 50 karakter')</script>";
                exit();
            }
            if (strlen($data['deskripsi']) > 4000) {
                echo "<script>alert('deskripsi Seniman maksimal 4000 karakter')</script>";
                exit();
            }
            if (!isset($data['kategori']) || empty($data['kategori'])) {
                echo "<script>alert('kategori seniman harus di isi')</script>";
                exit();
            }else if(!in_array($data['kategori'],['olahraga','seni','budaya'])){
                echo "<script>alert('kategori salah')</script>";
                exit();
            }
            if (!isset($data['tanggal_awal']) || empty($data['tanggal_awal'])) {
                echo "<script>alert('Tanggal awal harus di isi')</script>";
                exit();
            }else if (!isset($data['tanggal_akhir']) || empty($data['tanggal_akhir'])) {
                echo "<script>alert('Tanggal akhir harus di isi')</script>";
                exit();
            }
            $tanggal_awal = date('Y-m-d H:i:s',strtotime($data['tanggal_awal']));
            $tanggal_akhir = date('Y-m-d H:i:s',strtotime($data['tanggal_akhir']));
            // Check if the date formats are valid
            if (!$tanggal_awal) {
                echo "<script>alert('Format tanggal awal tidak valid')</script>";
                exit();
                // return ['status' => 'error', 'message' => 'Format tanggal awal tidak valid', 'code' => 400];
            }else if (!$tanggal_akhir) {
                echo "<script>alert('Format tanggal akhir tidak valid')</script>";
                exit();
            }
            // Compare the dates
            if ($tanggal_awal > $tanggal_akhir) {
                echo "<script>alert('Tanggal akhir tidak boleh lebih awal dari tanggal awal')</script>";
                exit();
            }
            $query = "INSERT INTO seniman (nama_seniman,deskripsi_seniman, kategori_seniman, tanggal_awal_seniman, tanggal_akhir_seniman, link_pendaftaran, poster_seniman,status,id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = self::$con->prepare($query);
            $status = 'terkirim';
            $data['kategori'] = strtoupper($data['kategori']);
            $stmt->bind_param("sssssssss", $data['nama_seniman'], $data[  'deskripsi'], $data['kategori'],$tanggal_awal, $tanggal_akhir, $data['link'], $data['poster'],$status,$data['id_user']);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                echo "<script>alert('seniman berhasil ditambahkan')</script>";
                exit();
            } else {
                $stmt->close();
                echo "<script>alert('seniman gagal ditambahkan')</script>";
                exit();
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
            echo "<script>alert('$responseData')</script>";
            exit();
        }
    }
    public static function editSeniman($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                echo "<script>alert('ID User harus di isi')</script>";
                exit();
            }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                echo "<script>alert('ID seniman harus di isi')</script>";
                exit();
            }
            if (!isset($data['nama_seniman']) || empty($data['nama_seniman'])) {
                echo "<script>alert('Nama seniman harus di isi')</script>";
                exit();
            } elseif (strlen($data['nama_seniman']) < 5) {
                echo "<script>alert('Nama seniman minimal 5 karakter')</script>";
                exit();
            } elseif (strlen($data['nama_seniman']) > 50) {
                echo "<script>alert('Nama seniman maksimal 50 karakter')</script>";
                exit();
            }
            if (strlen($data['deskripsi_seniman']) > 4000) {
                echo "<script>alert('Deskripsi seniman maksimal 4000 karakter')</script>";
                exit();
            }
            if (!isset($data['kategori_seniman']) || empty($data['kategori_seniman'])) {
                echo "<script>alert('Kategori seniman harus di isi')</script>";
                exit();
            }else if(!in_array($data['kategori_seniman'],['olahraga','seni','budaya'])){
                echo "<script>alert('Kategori salah')</script>";
                exit();
            }
            if (!isset($data['tanggal_awal_seniman']) || empty($data['tanggal_awal_seniman'])) {
                echo "<script>alert('Tanggal awal harus di isi')</script>";
                exit();
            }else if (!isset($data['tanggal_akhir_seniman']) || empty($data['tanggal_akhir_seniman'])) {
                echo "<script>alert('Tanggal akhir harus di isi')</script>";
                exit();
            }
            $tanggal_awal = date('Y-m-d H:i:s',strtotime($data['tanggal_awal_seniman']));
            $tanggal_akhir = date('Y-m-d H:i:s',strtotime($data['tanggal_akhir_seniman']));
            // Check if the date formats are valid
            if (!$tanggal_awal) {
                echo "<script>alert('Format tanggal awal tidak valid')</script>";
                exit();
            }else if (!$tanggal_akhir) {
                echo "<script>alert('Format tanggal akhir tidak valid')</script>";
                exit();
            }
            // Compare the dates
            if ($tanggal_awal > $tanggal_akhir) {
                echo "<script>alert('Tanggal akhir tidak boleh lebih awal dari tanggal awal')</script>";
                exit();
            }
            $query = "UPDATE seniman SET nama_seniman = ?, deskripsi_seniman = ?, kategori_seniman = ?, tanggal_awal_seniman = ?, tanggal_akhir_seniman = ?, link_pendaftaran = ?, poster_seniman = ?, status = ? WHERE id_user = ? AND id_seniman = ?";
            $stmt = self::$con->prepare($query);
            $status = 'terkirim';
            $data['kategori'] = strtoupper($data['kategori']);
            $stmt->bind_param("ssssssssii", $data['nama_seniman'], $data['deskripsi_seniman'], $data['kategori_seniman'], $tanggal_awal, $tanggal_akhir, $data['link_pendaftaran'], $data['poster_seniman'], $status, $data['id_user'], $data['id_seniman']);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                echo "<script>alert('seniman berhasil diupdate')</script>";
                exit();
            } else {
                $stmt->close();
                echo "<script>alert('seniman gagal diupdate')</script>";
                exit();
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
            echo "<script>alert('$responseData')</script>";
            exit();
        }
    }
    public static function hapusSeniman($data, $uri = null){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                echo "<script>alert('ID User harus di isi')</script>";
                exit();
            }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                echo "<script>alert('ID seniman harus di isi')</script>";
                exit();
            }
            $query = "DELETE FROM seniman WHERE id_seniman = ? AND id_user = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('ss', $data['id_seniman'],$data['id_user']);
            if ($stmt[2]->execute()) {
                $stmt[2]->close();
                echo "<script>alert('seniman berhasil dihapus')</script>";
                exit();
            } else {
                $stmt[2]->close();
                echo "<script>alert('seniman gagal dihapus')</script>";
                exit();
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
            echo "<script>alert('$responseData')</script>";
            exit();
        }
    }
    //khusus admin seniman dan super admin
    public static function prosesSeniman($data, $uri = null){
        if(!isset($data['id_user']) || empty($data['id_user'])){
            echo "<script>alert('ID User harus di isi')</script>";
            exit();
        }
        if (!isset($data['nama_seniman']) || empty($data['nama_seniman'])) {
            return ['status'=>'error','message'=>'Nama seniman harus di isi','code'=>400];
        } elseif (strlen($data['nama_seniman']) < 5) {
            return ['status'=>'error','message'=>'Nama seniman minimal 5 karakter','code'=>400];
        } elseif (strlen($data['nama_seniman']) > 50) {
            return ['status'=>'error','message'=>'Nama seniman maksimal 50 karakter','code'=>400];
        }
        if (!isset($data['deskripsi']) || empty($data['deskripsi'])) {
            return ['status'=>'error','message'=>'Deskripsi seniman harus di isi','code'=>400];
        } elseif (strlen($data['deskripsi']) > 4000) {
            return ['status'=>'error','message'=>'deskripsi seniman maksimal 4000 karakter','code'=>400];
        }
        if (!isset($data['kategori']) || empty($data['kategori'])) {
            return ['status'=>'error','message'=>'Kategori seniman harus di isi','code'=>400];
        }else if(!in_array($data['kategori'],['olahraga','seni'])){
            return ['status'=>'error','message'=>'Kategori salah','code'=>400];
        }
        if (!isset($data['tanggal_awal']) || empty($data['tanggal_awal'])) {
            return ['status'=>'error','message'=>'Tanggal awal harus di isi','code'=>400];
        }else if (!isset($data['tanggal_akhir']) || empty($data['tanggal_akhir'])) {
            return ['status'=>'error','message'=>'Tanggal akhir harus di isi','code'=>400];
        }
    }
    public static function verfikasiSeniman($data, $uri = null){
        if(!isset($data['id_user']) || empty($data['id_user'])){
            return ['status'=>'error','message'=>'ID User harus di isi','code'=>400];
        }
        if (!isset($data['nama_seniman']) || empty($data['nama_seniman'])) {
            return ['status'=>'error','message'=>'Nama seniman harus di isi','code'=>400];
        } elseif (strlen($data['nama_seniman']) < 5) {
            return ['status'=>'error','message'=>'Nama seniman minimal 5 karakter','code'=>400];
        } elseif (strlen($data['nama_seniman']) > 50) {
            return ['status'=>'error','message'=>'Nama seniman maksimal 50 karakter','code'=>400];
        }
        if (!isset($data['deskripsi']) || empty($data['deskripsi'])) {
            return ['status'=>'error','message'=>'Deskripsi seniman harus di isi','code'=>400];
        } elseif (strlen($data['deskripsi']) > 4000) {
            return ['status'=>'error','message'=>'deskripsi seniman maksimal 4000 karakter','code'=>400];
        }
        if (!isset($data['kategori']) || empty($data['kategori'])) {
            return ['status'=>'error','message'=>'Kategori seniman harus di isi','code'=>400];
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
if(isset($_POST['tambah'])){
    tambahSeniman($_POST);
}
if(isset($_POST['edit'])){
    editSeniman($_POST);
}
if(isset($_POST['hapus'])){
    hapusSeniman($_POST);
}
if(isset($_POST['proses'])){
    prosesSeniman($_POST);
}
?>