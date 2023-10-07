<?php 
if(!defined('APP')){
    $rootDir = dirname(dirname(__DIR__));
    http_response_code(404);
    include($rootDir.'/view/page/PageNotFound.php');
    exit();
}
$tPath = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/public/css/event/dashboard.css">
    <!-- <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
</head>
<body class="">
    <script>
        var csrfToken = "<?php echo $csrf ?>";
        var email = "<?php echo $user['email'] ?>";
        var idUser = "<?php echo $user['id_user'] ?>";
        console.log('id user '+idUser);
        var number = "<?php echo $number ?>";
        var showForm, closeForm;
    </script>
    <h1>aoog</h1>
    <?php  if(in_array($role, $comRole)){ ?>
        <script>
            <?php if(isset($dataUsers) && !empty($dataUsers && !is_null($dataUsers))){?>
                var  dataUsers = <?php echo json_encode($dataUsers) ?>;
                var id_user = dataUsers[dataUsers.length-1].id_user;
                <?php }else{ ?>
                    var dataUsers = [], id_user = 1; 
                <?php }?>
        </script>
        <table class="tableEvent" id="tableEvent">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama lengkap</th>
                    <th scope="col">email</th>
                    <th scope="col">role</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $no = 1;
                    foreach($dataUsers as $dataUser){
                ?>
                    <tr>
                        <th scope="row"><?php echo $no ?></th>
                        <td> <?php echo $dataUser['nama_lengkap'] ?></td>
                        <td> <?php echo $dataUser['email'] ?></td>
                        <td> <?php echo $dataUser['role'] ?></td>
                        <td>
                            <button onclick="showForm('edit',<?php echo json_encode($dataUser['id_user']) ?>,<?php echo $no ?>)">edit</button>
                            <button onclick="showForm('hapus',<?php echo json_encode($dataUser['id_user']) ?>,<?php echo $no ?>)">hapus</button>
                        </td>
                    </tr>
            <?php
                $no++;
            }
            ?>
            </tbody>
        </table>
        <div id="divTambahEvent" style="display:none">
            <div class="bg" onclick="closeForm('tambah')"></div>
            <div class="content">
                <form id="tambahEventForm">
                    <div class="header">
                        <h1>tambah event</h1>
                    </div>
                    <div class="row">
                        <label>Nama event</label>
                        <input type="text" name="inpNamaEvent" id="inpNamaEvent">
                    </div>
                    <div class="row">
                        <label>Deskripsi event</label>
                        <textarea name="inpDeskripsiEvent" id="inpDeskripsiEvent"></textarea>
                    </div>
                    <div class="row">
                        <label>Daftar kategori</label>
                        <select name="inpKategoriEvent" id="inpKategoriEvent" multiple>
                            <option value="olahraga">Olahraga</option>  
                            <option value="seni">Seni</option>
                            <option value="budaya">Budaya</option>
                            <option value="lain-lain">Lain-lain</option>
                        </select>
                    </div>
                    <div class="row">
                        <label>Tanggal awal event</label>
                        <input type="datetime-local" name="inpTAwalEvent" id="inpTAwalEvent">
                    </div>
                    <div class="row">
                        <label>Tanggal akhir event</label>
                        <input type="datetime-local" name="inpTAkhirEvent" id="inpTAkhirEvent">
                    </div>
                    <div class="row">
                        <label>link pendaftaran event</label>
                        <input type="text" name="inpPendaftaranEvent" id="inpPendaftaranEvent">
                    </div>
                    <div class="row">
                        <label>Poster event</label>
                        <input type="file" name="inpPosterEvent" id="inpPosterEvent">
                    </div>
                    <input type="submit" value="Kirim">
                </form>
            </div>
        </div>
        <div id="divEditEvent" style="display:none">
            <div class="bg" onclick="closeForm('hapus')"></div>
            <div class="content">
                <form id="editEventForm">
                    <input type="hidden" id="IDEvent">
                    <div class="header">
                        <h1>edit event</h1>
                    </div>
                    <div class="row">
                        <label>Nama event</label>
                        <input type="text" name="inpNamaEvent" id="inpENamaEvent">
                    </div>
                    <div class="row">
                        <label>Deskripsi event</label>
                        <textarea name="inpDeskripsiEvent" id="inpEDeskripsiEvent"></textarea>
                    </div>
                    <div class="row">
                        <label>Daftar kategori</label>
                        <select name="inpKategoriEvent" id="inpEKategoriEvent" multiple>
                            <option value="olahraga">Olahraga</option>  
                            <option value="seni">Seni</option>
                            <option value="budaya">Budaya</option>
                            <option value="lain-lain">Lain-lain</option>
                        </select>
                    </div>
                    <div class="row">
                        <label>Tanggal awal event</label>
                        <input type="datetime-local" name="inpTAwalEvent" id="inpETAwalEvent">
                    </div>
                    <div class="row">
                        <label>Tanggal akhir event</label>
                        <input type="datetime-local" name="inpTAkhirEvent" id="inpETAkhirEvent">
                    </div>
                    <div class="row">
                        <label>link pendaftaran event</label>
                        <input type="text" name="inpPendaftaranEvent" id="inpEPendaftaranEvent">
                    </div>
                    <div class="row">
                        <label>Poster event</label>
                        <input type="file" name="inpPosterEvent" id="inpEPosterEvent">
                    </div>
                    <input type="submit" value="Kirim">
                    <button type="button" onclick="closeForm('edit')"">kembali</button>
                </form>
            </div>
        </div>
        <div id="divHapusEvent" style="display:none">
            <div class="bg"></div>
            <div class="content">
                <span>apakah anda mau menghapus</span>
                <button id="btnHapusEvent" onclick="hapusEvent()">hapus</button>
                <button onclick="closeForm('hapus')">batal</button>
            </div>
        </div>
        <button onclick="showForm('tambah')"> tambah user</button>
        <?php } ?>
        <a href="/dashboard"><h1>kembali</h1></a>
        <br>
        <button onclick="logout()"> metu</button>
    <div id="preloader" style="display: none;"></div>
    <div id="greenPopup" style="display:none"></div>    
    <div id="redPopup" style="display: none"></div>
    <?php if(in_array($role, $comRole)){ ?>
        <script src="<?php echo $tPath.'/public/js/event/dashboardAdmin.js?'?>"></script>
    <?php } ?>
</body>
</html>