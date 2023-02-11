<?php
    session_start();
    require "../manage/User.php";
    require "../data/encrypt.php";
    require "../manage/Session.php";
    // require "../website/dashboard.php";
    $user = new User();
    try{
        $user -> createConn();
        if(isset($_POST["login"])){
            if(empty($_POST["email"])){
                throw new Exception("Email tidak boleh kosong");
                // echo "username kosong";
            }
            if(empty($_POST["password"])){
                // echo "password kosong";
                throw new Exception("Password tidak boleh kosong");
            }
            $email = $_POST["email"];
            $password = $_POST["password"];
            echo "email : ".$email. "<br>";
            echo "password : ".$password. "<br>";
            //mengecek username dan password
            if ($user -> checkLogin($email, $password)){
                //jika benar maka alihkan ke halaman dashboard
                if(isset($_POST["remember"])){
                    $id_karyawan = $user->getIdKaryawan($email);
                    // $namaUser = $user -> getNamaUser($username);
                    $_SESSION['user'] = serialize(new Session($user -> getUsername($email), $email, $user -> getLevel($email), "dashboard"));
                    setcookie("id", $id_karyawan, time() + (60 * 60 * 24 * 1), "/", $_SERVER["SERVER_NAME"], false, true);
                    setcookie("key", createSha512($email), time() + (60 * 60 * 24 * 1), "/", $_SERVER["SERVER_NAME"], false, true);
                    header("Location: dashboard.php");
                    exit();
                }else{
                    $id_karyawan = $user -> getIdKaryawan($email);
                    // $_SESSION['username'] = $user -> getUsername($email);
                    // $_SESSION['email'] = $email;
                    // $_SESSION['level'] = $user -> getLevel($email);
                    $_SESSION['user'] = serialize(new Session($user -> getUsername($email), $email, $user -> getLevel($email), "dashboard"));
                    header("Location: dashboard.php");
                    exit();
                }
            }
        }
        //digunakan untuk redirect
        if(isset($_COOKIE["id"]) && isset($_COOKIE["key"])){
            if(isset($_SESSION['user'])){
                header("Location: dashboard.php");
                exit();
            }else{
                header("Location: dashboard.php");
                exit();
            }
        }else{
            if(isset($_SESSION['user'])){
                header("Location: dashboard.php");
                exit();
            }
        }
        //terserah
        if(isset($_POST['getdata'])){
            echo "outtpuutt ".$user -> getDataKolomP("username","users","username","Admin");
        }
    }catch(Exception $e){
        $pError = $e->getMessage();
        $e->getTrace();
        echo "<script type='text/javascript'>alert('$pError');</script>";
    }
?>
<!DOCTYPE html>
<head>
    <title>login</title>
</head>
<body>
    <form method="post" action=" <?php $_SERVER["PHP_SELF"]; ?>">
        Masukkan email :<input type="text" name="email"><br>
        Masukkan Password :<input type="password" name="password"><br>
        Remember Me <input type="checkbox" name="remember"><br>
        <input type="submit" name="login" value="Login"><br>
    </form>
    <form action="" method="post">
        <input type="submit" name="getdata" value="getdata"><br>
    </form>
    <form action="" method="post">
        <input type="submit" name="terserah" value="cookie"><br>
    </form>
    <form action="" method="post">
        <input type="submit" name="tambahKoneksi" value="tambah koneksi"><br>
    </form>
    <form action="" method="post">
        <input type="submit" name="getData" value="getData"><br>
    </form>
    <form action="" method="post">
        <input type="submit" name="putus" value="putus koneksi"><br>
    </form>
    <b1><?php echo $_SERVER["SERVER_NAME"];?></b1><br>
</body>
</html>