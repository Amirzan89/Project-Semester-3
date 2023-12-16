<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = ['status' => 'error', 'message' => 'method invalid'];
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode($response);
        exit();
}
require_once(__DIR__.'/../../web/koneksi.php');
$listTempat = json_decode(
'[
    {
        "id_tempat": 1,
        "nama_tempat": "Museum Anjuk Ladang",
        "alamat_tempat": "Jl. Gatot Subroto Kec. Nganjuk Kab. Nganjuk",
        "deskripsi_tempat": "Museum Anjuk Ladang Terletak di kota Nganjuk, tepatnya sebelah timur Terminal Bus Kota Nganjuk, di dalamnya tersimpan benda dan cagar budaya pada zaman Hindu, Doho dan Majapahit yang terdapat di daerah Kabupaten Nganjuk. Disamping itu di simpan Prasasti Anjuk Ladang yang merupakan cikal bakal berdirinya Kabupaten Nganjuk.",
        "pengelola": "Arip",
        "contact_person": "08414141",
        "foto_tempat": "/1.png"
    },
    {
        "id_tempat": 2,
        "nama_tempat": "Balai Budaya",
        "alamat_tempat": "Mangundikaran, Kec. Nganjuk, Kab. Nganjuk",
        "deskripsi_tempat": "Gedung Balai Budaya Nganjuk adalah salah satu legenda bangunan bersejarah di Kabupaten Nganjuk. Gedung ini bisa digunakan untuk berbagai acara.",
        "pengelola": "Asep",
        "contact_person": "0855151515",
        "foto_tempat": "/2.png"
    },
    {
        "id_tempat": 3,
        "nama_tempat": "Monumen Dr. Soetomo",
        "alamat_tempat": "Sono, Ngepeh, Kec. Loceret Kab. Nganjuk",
        "deskripsi_tempat": "Monumen Dr. Soetomo Nganjuk yang menempati tanah seluas 3,5 ha ini merupakan tempat kelahiran Dr. Soetomo Secara keseluruhan kompleks bangunan ini terdiri dari patung Dr. Soetomo, Pendopo induk, yang terletak di belakang patung, dan bangunan pringgitan jumlahnya 2 buah masing-masing 6 x 12 m.",
        "pengelola": "Johan",
        "contact_person": "081121313132",
        "foto_tempat": "/3.png"
    },
    {
        "id_tempat": 4,
        "nama_tempat": "Air Terjun Sedudo",
        "alamat_tempat": "Jl. Sedudo Kec. Sawahan Kab. Nganjuk",
        "deskripsi_tempat": "Air Terjun Sedudo adalah sebuah air terjun dan objek wisata yang terletak di Desa Ngliman Kecamatan Sawahan, Kabupaten Nganjuk, Jawa Timur. Jaraknya sekitar 30 km arah selatan ibu kota kabupaten Nganjuk. Berada pada ketinggian 1.438 meter dpl, ketinggian air terjun ini sekitar 105 meter. Tempat wisata ini memiliki fasilitas yang cukup baik, dan jalur transportasi yang mudah diakses.",
        "pengelola": "Joko",
        "contact_person": "08741415355",
        "foto_tempat": "/4.png"
    },
    {
        "id_tempat": 5,
        "nama_tempat": "Goa Margo Tresno",
        "alamat_tempat": "Ngluyu, Kec. Ngluyu Kab. Nganjuk",
        "deskripsi_tempat": "Goa Margo Tresno adalah salah satu obyek wisata di Jawa Timur yang terletak di Dusun Cabean, Desa Sugih Waras, Kecamatan Ngluyu, Kabupaten Nganjuk. Wisata Goa Margo Tresno Nganjuk adalah destinasi wisata yang ramai dengan wisatawan baik dari dalam maupun luar kota pada hari biasa maupun hari liburan dan sudah terkenal di Nganjuk dan sekitarnya.",
        "pengelola": "Bagas",
        "contact_person": "089987741124",
        "foto_tempat": "/5.png"
    },
    {
        "id_tempat": 6,
        "nama_tempat": "Air Terjun Roro Kuning",
        "alamat_tempat": "Nglarangan, Bajulan, Kec. Loceret Kab. Nganjuk",
        "deskripsi_tempat": "Air Terjun Roro Kuning adalah sebuah air terjun yang berada sekitar 27-30 km selatan kota Nganjuk, di ketinggian 600 m dpl dan memiliki tinggi antara 10-15 m. Air terjun ini mengalir dari tiga sumber di sekitar Gunung Wilis yang mengalir merambat di sela-sela bebatuan padas di bawah pepohonan hutan pinus.",
        "pengelola": "Wahyu",
        "contact_person": "08414142144",
        "foto_tempat": "/6.png"
    }
]',true);

$kategoriDATA = json_decode(
'[
    {
        "nama_kategori": "campursari",
        "singkatan_kategori": "CAMP"
    },
    {
        "nama_kategori": "dalang",
        "singkatan_kategori": "DLG"
    },
    {
        "nama_kategori": "jaranan",
        "singkatan_kategori": "JKP"
    },
    {
        "nama_kategori": "karawitan",
        "singkatan_kategori": "KRW"
    },
    {
        "nama_kategori": "mc",
        "singkatan_kategori": "MC"
    },
    {
        "nama_kategori": "ludruk",
        "singkatan_kategori": "LDR"  
    },
    {
        "nama_kategori": "organisasi kesenian musik",
        "singkatan_kategori": "OKM"
    },
    {
        "nama_kategori": "organisasi",
        "singkatan_kategori": "ORG"
    },
    {
        "nama_kategori": "pramugari tayup",
        "singkatan_kategori": "PRAM"
    },
    {
        "nama_kategori": "sanggar",
        "singkatan_kategori": "SGR"
    },
    {
        "nama_kategori": "sinden",
        "singkatan_kategori": "SIND"
    },
    {
        "nama_kategori": "vocalis",
        "singkatan_kategori": "VOC"
    },
    {
        "nama_kategori": "waranggono",
        "singkatan_kategori": "WAR"
    },
    {
        "nama_kategori": "barongsai",
        "singkatan_kategori": "BAR"
    },
    {
        "nama_kategori": "ketoprak",
        "singkatan_kategori": "KTP"
    },
    {
        "nama_kategori": "pataji",
        "singkatan_kategori": "PTJ"
    },
    {
        "nama_kategori": "reog",
        "singkatan_kategori": "REOG"
    },
    {
        "nama_kategori": "taman hiburan rakyat",
        "singkatan_kategori": "THR"
    },
    {
        "nama_kategori": "pelawak",
        "singkatan_kategori": "PLWK"
    }
]',true);
$database = koneksi::getInstance();
$conn = $database->getConnection();
$contentType = $_SERVER["CONTENT_TYPE"];
if ($contentType === "application/json") {
    $rawData = file_get_contents("php://input");
    $requestData = json_decode($rawData, true);
    if ($requestData === null && json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
        exit();
    }
    $data = $requestData;
}else{
    $data = $_POST;
}
if(!isset($data['email']) || empty($data['email'])){
    $response = ['status' => 'error', 'message' => 'Email empty'];
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode($response);
    exit();
}
if(!isset($data['password']) || empty($data['password'])){
    $response = ['status' => 'error', 'message' => 'Password empty'];
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode($response);
    exit();
}
$email = $data['email'];
$pass = $data['password'];
$query = "SELECT role, password FROM users WHERE BINARY email = ? LIMIT 1";
$stmt[3] = $conn->prepare($query);
$stmt[3]->bind_param('s', $email);
$stmt[3]->execute();
$roleDB = '';
$passDb = '';
$stmt[3]->bind_result($roleDB, $passDb);
if (!$stmt[3]->fetch()) {
    $response = ['status' => 'error', 'message' => 'User not found'];
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode($response);
    exit();
}
$stmt[3]->close();
//check role
if($roleDB !== 'super admin'){
    $response = ['status' => 'error', 'message' => 'Role must be super admin'];
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode($response);
    exit();
}
//check password
if(!password_verify($pass,$passDb)){
    $response = ['status' => 'error', 'message' => 'Wrong password'];
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode($response);
    exit();
}
////////////////////////////   RESET DATA     ////////////////////////////
$response = array();
//reset sewa tempat
$stmt[0] = $conn->prepare("DELETE FROM sewa_tempat");
if (!$stmt[0]->execute()) {
    $response = ['status' => 'error'];
    $response['message']['sewa_tempat'][] = ['Gagal reset sewa tempat'];
} else {
    $response['message']['sewa_tempat'][] = ['Berhasil reset sewa tempat'];
    $conn->query("ALTER TABLE sewa_tempat AUTO_INCREMENT = 1");
}
//delete all file sewa tempat
$pinjamPath = __DIR__.'/../../DatabaseMobile/uploads/pinjam';
$files = glob($pinjamPath . '/*');
foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}
rmdir($pinjamPath);
mkdir($pinjamPath, 0777, true);

//reset detail_events
$stmt[0] = $conn->prepare("DELETE FROM detail_events");
if (!$stmt[0]->execute()) {
    $response = ['status' => 'error'];
    $response['message']['detail_events'][] = ['Gagal reset detail event'];
} else {
    $response['message']['detail_events'][] = ['Berhasil reset detail event'];
    $conn->query("ALTER TABLE detail_events AUTO_INCREMENT = 1");
}

//reset events
$stmt[0] = $conn->prepare("DELETE FROM events");
if (!$stmt[0]->execute()) {
    $response = ['status' => 'error'];
    $response['message']['events'][] = ['Gagal reset event'];
} else {
    $response['message']['events'][] = ['Berhasil reset event'];
    $conn->query("ALTER TABLE events AUTO_INCREMENT = 1");
}
//delete all file events
$eventPath = __DIR__.'/../../DatabaseMobile/uploads/events';
$files = glob($eventPath . '/*');
foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}
rmdir($eventPath);
mkdir($eventPath, 0777, true);

//reset surat_advis
$stmt[0] = $conn->prepare("DELETE FROM surat_advis");
if (!$stmt[0]->execute()) {
    $response = ['status' => 'error'];
    $response['message']['surat_advis'][] = ['Gagal reset surat_advis'];
} else {
    $response['message']['surat_advis'][] = ['Berhasil reset surat_advis'];
    $conn->query("ALTER TABLE surat_advis AUTO_INCREMENT = 1");
}

//reset list tempat
$stmt[0] = $conn->prepare("DELETE FROM list_tempat");
if (!$stmt[0]->execute()) {
    $response = ['status' => 'error'];
    $response['message']['list_tempat'][] = ['Gagal reset list tempat'];
} else {
    $response['message']['list_tempat'][] = ['Berhasil reset list tempat'];
    $conn->query("ALTER TABLE list_tempat AUTO_INCREMENT = 1");
}
//delete all file list tempat
$tempatPath = __DIR__.'/../../DatabaseMobile/uploads/tempat';
$files = glob($tempatPath . '/*');
foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}
rmdir($tempatPath);
mkdir($tempatPath, 0777, true);

//reset kategori seniman
$stmt[0] = $conn->prepare("DELETE FROM kategori_seniman");
if (!$stmt[0]->execute()) {
    $response = ['status' => 'error'];
    $response['message']['kategori_seniman'][] = ['Gagal reset kategori seniman'];
} else {
    $response['message']['kategori_seniman'][] = ['Berhasil reset kategori seniman'];
    $conn->query("ALTER TABLE kategori_seniman AUTO_INCREMENT = 1");
}

//reset histori nis
$stmt[0] = $conn->prepare("DELETE FROM histori_nis");
if (!$stmt[0]->execute()) {
    $response = ['status' => 'error'];
    $response['message']['histori_nis'][] = ['Gagal reset histori nis'];
} else {
    $response['message']['histori_nis'][] = ['Berhasil reset histori nis'];
    $conn->query("ALTER TABLE histori_nis AUTO_INCREMENT = 1");
}

//reset perpanjangan
$stmt[0] = $conn->prepare("DELETE FROM perpanjangan");
if (!$stmt[0]->execute()) {
    $response = ['status' => 'error'];
    $response['message']['perpanjangan'][] = ['Gagal reset perpanjangan'];
} else {
    $response['message']['perpanjangan'][] = ['Berhasil reset perpanjangan'];
    $conn->query("ALTER TABLE perpanjangan AUTO_INCREMENT = 1");
}
//delete all file perpanjangan
$perpanjanganPath = __DIR__.'/../../DatabaseMobile/data_seniman_mobile/uploads/perpanjangan';
$files = glob($perpanjanganPath . '/*');
foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}
rmdir($perpanjanganPath);
mkdir($perpanjanganPath, 0777, true);

//reset seniman
$stmt[0] = $conn->prepare("DELETE FROM seniman");
if (!$stmt[0]->execute()) {
    $response = ['status' => 'error'];
    $response['message']['seniman'][] = ['Gagal reset seniman'];
} else {
    $response['message']['seniman'][] = ['Berhasil reset seniman'];
    $conn->query("ALTER TABLE seniman AUTO_INCREMENT = 1");
}
//delete all file seniman
$senimanPath = __DIR__.'/../../DatabaseMobile/data_seniman_mobile/uploads/seniman';
$files = glob($senimanPath . '/*');
foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}
rmdir($senimanPath);
mkdir($senimanPath, 0777, true);

//reset refresh_token
$stmt[0] = $conn->prepare("DELETE FROM refresh_token");
if (!$stmt[0]->execute()) {
    $response = ['status' => 'error'];
    $response['message']['refresh_token'][] = ['Gagal reset refresh token'];
} else {
    $response['message']['refresh_token'][] = ['Berhasil reset refresh token'];
    $conn->query("ALTER TABLE refresh_token AUTO_INCREMENT = 1");
}

//reset verifikasi
$stmt[0] = $conn->prepare("DELETE FROM verifikasi");
if (!$stmt[0]->execute()) {
    $response = ['status' => 'error'];
    $response['message']['verifikasi'][] = ['Gagal reset verifikasi'];
} else {
    $response['message']['verifikasi'][] = ['Berhasil reset verifikasi'];
    $conn->query("ALTER TABLE verifikasi AUTO_INCREMENT = 1");
}

//reset users
$stmt[0] = $conn->prepare("DELETE FROM users");
if (!$stmt[0]->execute()) {
    $response = ['status' => 'error'];
    $response['message']['users'][] = ['Gagal reset users'];
} else {
    $response['message']['users'][] = ['Berhasil reset users'];
    $conn->query("ALTER TABLE users AUTO_INCREMENT = 1");
}
$stmt[0]->close();

////////////////      INSERT DATA      ////////////////
$adminData = [
    [
        'email'=>'SuperAdmin@gmail.com',
        'password'=>password_hash('Admin@1234567890', PASSWORD_DEFAULT),
        'nama_lengkap'=>'Super Admin',
        'no_telpon'=>'08885152551251',
        'jenis_kelamin'=>'laki-laki',
        'tempat_lahir'=>'Jakarta Indonesia',
        'tanggal_lahir'=>'2000-12-11',
        'role'=>'super admin',
        'foto'=>'',
        'verifikasi'=>1
    ],
    [
        'email'=>'AdminTester@gmail.com',
        'password'=>password_hash('Admin@1234567890', PASSWORD_DEFAULT),
        'nama_lengkap'=>'Admin Tester',
        'no_telpon'=>'0881515135152',
        'jenis_kelamin'=>'perempuan',
        'tempat_lahir'=>'Jakarta Indonesia',
        'tanggal_lahir'=>'2000-11-12',
        'role'=>'super admin',
        'foto'=>'',
        'verifikasi'=>1
    ]
];
$query = "INSERT INTO users (email,password, nama_lengkap, no_telpon, jenis_kelamin, tempat_lahir, tanggal_lahir, role, foto, verifikasi) VALUES (?, ?, ?, ?, ? , ?, ?, ?, ?, ?)";
$stmt[1] = $conn->prepare($query);
foreach ($adminData as $admins) {
    $stmt[1]->bind_param("sssssssssi", $admins['email'], $admins['password'], $admins['nama_lengkap'], $admins['no_telpon'], $admins['jenis_kelamin'], $admins['tempat_lahir'], $admins['tanggal_lahir'], $admins['role'], $admins['foto'], $admins['verifikasi']);
    $stmt[1]->execute();
}
if (!$stmt[1]->affected_rows > 0) {
    $response = ['status' => 'error'];
    $response['message']['users'][] = ['Akun admin gagal dibuat'];
} else {
    $response['message']['users'][] = ['Akun admin berhasil dibuat'];
}

//insert kategori seniman to database
$query = "INSERT INTO kategori_seniman (nama_kategori, singkatan_kategori) VALUES (?, ?)";
$stmt[1] = $conn->prepare($query);
foreach ($kategoriDATA as $kategoriS) {
    $stmt[1]->bind_param("ss", $kategoriS['nama_kategori'], $kategoriS['singkatan_kategori']);
    $stmt[1]->execute();
}
if (!$stmt[1]->affected_rows > 0) {
    $response = ['status' => 'error'];
    $response['message']['kategori_seniman'][] = ['Data kategori seniman gagal dibuat'];
} else {
    $response['message']['kategori_seniman'][] = ['Data kategori seniman berhasil dibuat'];
}

//insert kategori seniman to file json
$jsonPath = __DIR__."/../../kategori_seniman.json";
$fileExist = file_exists($jsonPath);
if ($fileExist) {
    unlink($jsonPath);
}
//if file is delete will make new json file
$query = "SELECT * FROM kategori_seniman";
$stmt[1] = $conn->prepare($query);
if(!$stmt[1]->execute()){
    $response = ['status' => 'error'];
    $response['message']['kategori_seniman'][] = ['Data kategori seniman kosong'];
}
$result = $stmt[1]->get_result();
$kategoridb = [];
while ($row = $result->fetch_assoc()) {
    $kategoridb[] = $row;
}
if ($kategoridb === null) {
    $response = ['status' => 'error'];
    $response['message']['kategori_seniman'][] = ['Data kategori seniman kosong'];
}
$jsonData = json_encode($kategoridb, JSON_PRETTY_PRINT);
if (!file_put_contents($jsonPath, $jsonData)) {
    $response = ['status' => 'error'];
    $response['message']['kategori_seniman'][] = ['Gagal menyimpan file sistem'];
    throw new Exception('Gagal menyimpan file sistem');
}

//insert list tempat
$query = "INSERT INTO list_tempat (nama_tempat, alamat_tempat, deskripsi_tempat, pengelola, contact_person, foto_tempat) VALUES (?, ?, ?, ?, ?, ?)";
$stmt[1] = $conn->prepare($query);
foreach ($listTempat as $tempats) {
    $stmt[1]->bind_param("ssssss", $tempats['nama_tempat'],$tempats['alamat_tempat'],$tempats['deskripsi_tempat'], $tempats['pengelola'], $tempats['contact_person'], $tempats['foto_tempat']);
    $stmt[1]->execute();
}
if (!$stmt[1]->affected_rows > 0) {
    $response = ['status' => 'error'];
    $response['message']['list_tempat'][] = ['Data list tempat gagal dibuat'];
} else {
    $response['message']['list_tempat'][] = ['Data list tempat berhasil dibuat'];
}
//copy list tempat
$tempatSPath = __DIR__.'/tempat';
for ($i = 0; $i < count($listTempat); $i++) {
    if(copy($tempatSPath.$listTempat[$i]['foto_tempat'],$tempatPath.$listTempat[$i]['foto_tempat'])){
        $response['message']['list_tempat'][] = ["Foto list tempat ke ". ($i+1) ." berhasil dibuat"];
    }
}
if(!isset($response['status']) || $response['status'] != 'error'){
    $response['status'] = 'success';
}else{
    http_response_code(400);
}
$stmt[1]->close();
header('Content-Type: application/json');   
echo json_encode($response);
?>