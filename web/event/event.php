<?php
//untuk masyarakat
function tambahEventMasyarakat($data, $uri = null){
    try{
        if(!isset($data['id_user']) || empty($data['id_user'])){
            echo "<script>alert('ID User harus di isi')</script>";
            exit();
        }
        if (!isset($data['nama_event']) || empty($data['nama_event'])) {
            echo "<script>alert('Nama Event harus di isi')</script>";
            exit();
        } elseif (strlen($data['nama_event']) < 5) {
            echo "<script>alert('Nama Event minimal 5 karakter')</script>";
            exit();
        } elseif (strlen($data['nama_event']) > 50) {
            echo "<script>alert('Nama Event maksimal 50 karakter')</script>";
            exit();
        }
        if (strlen($data['deskripsi']) > 4000) {
            echo "<script>alert('deskripsi Event maksimal 4000 karakter')</script>";
            exit();
        }
        if (!isset($data['kategori']) || empty($data['kategori'])) {
            echo "<script>alert('kategori event harus di isi')</script>";
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
        $query = "INSERT INTO event (nama_event,deskripsi_event, kategori_event, tanggal_awal_event, tanggal_akhir_event, link_pendaftaran, poster_event,status,id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = self::$con->prepare($query);
        $status = 'terkirim';
        $data['kategori'] = strtoupper($data['kategori']);
        $stmt->bind_param("sssssssss", $data['nama_event'], $data[  'deskripsi'], $data['kategori'],$tanggal_awal, $tanggal_akhir, $data['link'], $data['poster'],$status,$data['id_user']);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('event berhasil ditambahkan')</script>";
            exit();
        } else {
            $stmt->close();
            echo "<script>alert('event gagal ditambahkan')</script>";
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
function editEvent($data){
    try{
        if(!isset($data['id_user']) || empty($data['id_user'])){
            echo "<script>alert('ID User harus di isi')</script>";
            exit();
        }
        if(!isset($data['id_event']) || empty($data['id_event'])){
            echo "<script>alert('ID event harus di isi')</script>";
            exit();
        }
        if (!isset($data['nama_event']) || empty($data['nama_event'])) {
            echo "<script>alert('Nama event harus di isi')</script>";
            exit();
        } elseif (strlen($data['nama_event']) < 5) {
            echo "<script>alert('Nama event minimal 5 karakter')</script>";
            exit();
        } elseif (strlen($data['nama_event']) > 50) {
            echo "<script>alert('Nama event maksimal 50 karakter')</script>";
            exit();
        }
        if (strlen($data['deskripsi_event']) > 4000) {
            echo "<script>alert('Deskripsi event maksimal 4000 karakter')</script>";
            exit();
        }
        if (!isset($data['kategori_event']) || empty($data['kategori_event'])) {
            echo "<script>alert('Kategori event harus di isi')</script>";
            exit();
        }else if(!in_array($data['kategori_event'],['olahraga','seni','budaya'])){
            echo "<script>alert('Kategori salah')</script>";
            exit();
        }
        if (!isset($data['tanggal_awal_event']) || empty($data['tanggal_awal_event'])) {
            echo "<script>alert('Tanggal awal harus di isi')</script>";
            exit();
        }else if (!isset($data['tanggal_akhir_event']) || empty($data['tanggal_akhir_event'])) {
            echo "<script>alert('Tanggal akhir harus di isi')</script>";
            exit();
        }
        $tanggal_awal = date('Y-m-d H:i:s',strtotime($data['tanggal_awal_event']));
        $tanggal_akhir = date('Y-m-d H:i:s',strtotime($data['tanggal_akhir_event']));
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
        $query = "UPDATE event SET nama_event = ?, deskripsi_event = ?, kategori_event = ?, tanggal_awal_event = ?, tanggal_akhir_event = ?, link_pendaftaran = ?, poster_event = ?, status = ? WHERE id_user = ? AND id_event = ?";
        $stmt = self::$con->prepare($query);
        $status = 'terkirim';
        $data['kategori'] = strtoupper($data['kategori']);
        $stmt->bind_param("ssssssssii", $data['nama_event'], $data['deskripsi_event'], $data['kategori_event'], $tanggal_awal, $tanggal_akhir, $data['link_pendaftaran'], $data['poster_event'], $status, $data['id_user'], $data['id_event']);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $stmt->close();
            echo "<script>alert('event berhasil diupdate')</script>";
            exit();
        } else {
            $stmt->close();
            echo "<script>alert('event gagal diupdate')</script>";
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
function hapusEvent($data, $uri = null){
    try{
        if(!isset($data['id_user']) || empty($data['id_user'])){
            echo "<script>alert('ID User harus di isi')</script>";
            exit();
        }
        if(!isset($data['id_event']) || empty($data['id_event'])){
            echo "<script>alert('ID event harus di isi')</script>";
            exit();
        }
        $query = "DELETE FROM event WHERE id_event = ? AND id_user = ?";
        $stmt[2] = self::$con->prepare($query);
        $stmt[2]->bind_param('ss', $data['id_event'],$data['id_user']);
        if ($stmt[2]->execute()) {
            $stmt[2]->close();
            echo "<script>alert('event berhasil dihapus')</script>";
            exit();
        } else {
            $stmt[2]->close();
            echo "<script>alert('event gagal dihapus')</script>";
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
//khusus admin event dan super admin
function prosesEvent($data, $uri = null){
    if(!isset($data['id_user']) || empty($data['id_user'])){
        echo "<script>alert('ID User harus di isi')</script>";
        exit();
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
function verfikasiEvent($data, $uri = null){
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
if(isset($_POST['tambah'])){
    tambahEventMasyarakat($_POST);
}
if(isset($_POST['edit'])){
    editEvent($_POST);
}
if(isset($_POST['hapus'])){
    hapusEvent($_POST);
}
if(isset($_POST['proses'])){
    prosesEvent($_POST);
}
?>