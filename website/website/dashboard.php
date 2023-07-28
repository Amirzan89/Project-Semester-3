<?php
    require "../manage/website.php";
    session_start();
    require "../manage/User.php";
    require "../data/encrypt.php";
    require "../manage/Session.php";
    $user = new User();
    try{
        $id_karyawan; $username; $level; $email;
        //mengecek apakah ada cookie
        if(isset($_COOKIE["id"]) && isset($_COOKIE["key"])){
            $id = $_COOKIE["id"];
            $key = $_COOKIE["key"];
            //mengecek session 
            //mengecek apakah id karyawan ada di database
            if(!empty($user -> getDataKolomP("id_karyawan","users","id_karyawan",$id))){
                //mengecek apakah email nya sama
                $input = $user -> getDataKolomP("email","users","id_karyawan",$id);
                if(verifyHash($key, createSha512($input))){
                    //mengecek session
                    if(isset($_SESSION['user'])){
                        $se1 = $_SESSION['user'];
                        $Session = unserialize($se1);
                        $id_karyawan = $id;
                        $email = $input;
                        $username = $Session -> username;
                        $level = $Session -> level;
                        echo "username : $username <br>";
                        echo "email : $email <br>";
                        echo "level : $level <br>";
                    //jika session tidak ada maka buat session baru
                    }else{
                        $id_karyawan = $id;
                        $email = $input;
                        $username = $user -> getUsername($email);
                        $level = $user ->getLevel($email);
                        echo "username : $username <br>";
                        echo "email : $email <br>";
                        echo "level : $level <br>";
                        $_SESSION['user'] = serialize(new Session($username, $email, $level, "dashboard"));
                    }
                //jika cookie key diubah maka hapus cookie lalu login kembali
                }else{
                    //menghapus cookie
                    setcookie("id", "", time() - 3600, "/", $_SERVER["SERVER_NAME"], false, true);
                    setcookie("key", "", time() - 3600, "/", $_SERVER["SERVER_NAME"], false, true);
                    //menghapus session
                    // unset($_SESSION['username']);
                    // unset($_SESSION["email"]);
                    // unset($_SESSION["level"]);
                    unset($_SESSION['user']);
                    header("Location: loginPage.php");
                    exit();
                }
            //jika cookie id diubah dan tidak ada di database maka harus login lagi
            }else{
                //menghapus cookie
                setcookie("id", "", time() - 3600, "/", $_SERVER["SERVER_NAME"], false, true);
                setcookie("key", "", time() - 3600, "/", $_SERVER["SERVER_NAME"], false, true);
                //menghapus session
                unset($_SESSION['user']);
                header("Location: loginPage.php");
                exit();
            }
        }else{
            if(isset($_SESSION['user'])){
                $se1 = $_SESSION['user'];
                $Session = unserialize($se1);
                $username = $Session -> username;
                $email = $Session -> email;
                $level = $Session -> level;
                $id_karyawan = $user -> getIdKaryawan($GLOBALS['email']);
                echo "username : $username <br>";
                echo "email : $email <br>";
                echo "level : $level <br>";
            }else{
                header("Location: loginPage.php");
                exit();
            }
        }
        //logout
        if(isset($_POST["logout"])){
            //menghapus cookie
            setcookie("id", "", time() - 3600, "/", $_SERVER["SERVER_NAME"], false, true);
            setcookie("key", "", time() - 3600, "/", $_SERVER["SERVER_NAME"], false, true);
            //menghapus session
            // unset($_SESSION['username']);
            // unset($_SESSION["email"]);
            // unset($_SESSION["level"]);
            unset($_SESSION['user']);
            header("Location: loginPage.php");
            exit();
        }
        //redirect
        if(isset($_POST["dashboard"])){
            $_SESSION['user'] = serialize(new Session($username, $email, $level, "dashboard"));
            header("Location: ". navBar("dashboard"));
        }
        if(isset($_POST["dataBarang"])){
            $_SESSION['user'] = serialize(new Session($username, $email, $level, "dataBarang"));
            header("Location: ". navBar("dataBarang"));
        }
        if(isset($_POST["dataSupplier"])){
            $_SESSION['user'] = serialize(new Session($username, $email, $level, "dataSupplier"));
            header("Location: ". navBar("dataSupplier"));
        }
        if(isset($_POST["dataKaryawan"])){
            $_SESSION['user'] = serialize(new Session($username, $email, $level, "dataKaryawan"));
            header("Location: ". navBar("dataKaryawan"));
        }
        if(isset($_POST["transaksi_beli"])){
            $_SESSION['user'] = serialize(new Session($username, $email, $level, "transaksi_beli"));
            header("Location: ". navBar("transaksiBeli"));
        }
        if(isset($_POST["transaksi_jual"])){
            $_SESSION['user'] = serialize(new Session($username, $email, $level, "transaksi_jual"));
            header("Location: ". navBar("transaksiJual"));
        }
        if(isset($_POST["laporan_pemasukan"])){
            $_SESSION['user'] = serialize(new Session($username, $email, $level, "laporan_pemasukan"));
            header("Location: ". navBar("laporanPemasukan"));
        }
        if(isset($_POST["laporan_pengeluaran"])){
            $_SESSION['user'] = serialize(new Session($username, $email, $level, "laporan_pengeluaran"));
            header("Location: ". navBar("laporanPengeluaran"));
        }
    }catch(Exception $e){
        $pError = $e->getMessage();
        $e->getTrace();
        echo "<script type='text/javascript'>alert('$pError');</script>";
    }
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <b>ppppp</b>
    <form action="" method="post" id="logout">
        <input type="submit" name="logout" value="logout" id="btnLogout">
    </form>
    <form action="" method="post" id="navBar">
        <input type="submit" name="dashboard" value="Dashboard" id="btnDashboard">
        <input type="submit" name="dataBarang" value="Barang" id="btnDataBarang">
        <input type="submit" name="dataKaryawan" value="Karyawan" id="btnDataKaryawan">
        <input type="submit" name="dataSupplier" value="Supplier" id="btnDataSupplier">
        <input type="submit" name="transaksi_beli" value="Transaksi Beli" id="btnTransaksiBeli">
        <input type="submit" name="transaksi_jual" value="Transaksi Jual" id="btnTransaksiJual">
        <input type="submit" name="laporan_pemasukan" value="Laporan Pemasukan" id="btnLaporanPemasukan">
        <input type="submit" name="laporan_pengeluaran" value="Laporan Pengeluaran" id="btnLaporanPengeluaran">
    </form>
</body>
</html>