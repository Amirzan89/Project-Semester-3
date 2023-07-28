<?php
    include 'db/koneksi.php';
    session_start();
    if(isset($_SESSION['username'])){
        header("dashboard.php");
    }else if(isset($_SESSION['SUBMIT'])){
        $email = $_POST['email'];
        $password = $_POST['password'];
        $sql = "SELECT * FROM users where email = $email AND password = $password";
        $result = mysqli_query($conn, $sql);
        if($result -> num_rows >0){
            $baris = mysqli_fetch_assoc($result);
            $_SESSION['username'] = $baris['username'];
            $_SESSION['email'] = $baris['email'];
            $_SESSION['password'] = $baris['password'];
            $_SESSION['level'] = $baris['level'];
            header('dashboard.php');
        }else{
            echo "<script>alert('Email atau Password Anda salah.'</script>";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="login.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class='login'>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method='POST'>
            Masukkan email : <input type="email" name='email' required><br>
            Masukkan password : <input type="password" name='password' required>
            <input type="submit" name='submit' value='Login'>
        </form>
    </div>
</body>
</html>