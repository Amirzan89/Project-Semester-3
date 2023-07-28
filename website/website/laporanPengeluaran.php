<?php 
    require "../manage/website.php";
    require "../manage/Session.php";
    session_start();
    //cek cookie
    if(isset($_COOKIE["id"]) && isset($_COOKIE["key"])){
        //cek session
        if(isset($_SESSION['user'])){
            $se1 = $_SESSION['user'];
            $Session = unserialize($se1);
            if($Session -> page == 'dashboard'){
                header("Location: dashboard.php");
                exit();
            }
        //jika session tidak ada maka masuk ke dashboard
        }else{
            header("Location: dashboard.php");
            exit();
        }
    //jika cookie tidak ada maka cek session 
    }else{
        if(isset($_SESSION['user'])){
            $se1 = $_SESSION['user'];
            $Session = unserialize($se1);
            if($Session -> page == 'dashboard'){
                header("Location: dashboard.php");
                exit();
            }
        //jika session dan cookie tidak ada maka masuk ke login
        }else{
            header("Location: loginPage.php");
            exit();
        }
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