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
<body class="bg-red">
    <script>
        var csrfToken = "<?php echo($csrf) ?>";
        var email = "<?php echo($user['email'])?>";
        var idUser = "<?php echo($user['id_user'])?>";
        var number = "<?php echo($number) ?>";
        console.log('email  '+email )
        console.log('number  '+number )
        </script>
    <!-- <?php if($role == 'masyarakat'){?>
        <table class="tableEvent" id="tableEvent">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama event</th>
                    <th scope="col">Tanggal awal</th>
                    <th scope="col">Tanggal akhir</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
            <?php
                $no = 1;
                foreach($dataEvents as $dataEvent){
            ?>
                    <th scope="row"><?php echo $no?></th>
                    <td> <?php echo $dataEvent['nama_event'] ?></td>
                    <td> <?php echo $dataEvent['tanggal_awal_event'] ?></td>
                    <td> <?php echo $dataEvent['tanggal_akhir_event'] ?></td>
                    <td>
                        <button onclick="showForm('edit',<?php echo json_encode($dataEvent['id_event']) ?>)">hapus</button>
                        <button onclick="showForm('hapus',<?php echo json_encode($dataEvent['id_event']) ?>)">hapus</button>
                    </td>
            <?php 
                $no++;
            }
            ?>
                </tr>
            </tbody>
        </table>
        <div id="divTambahEvent" style="display:none">
            <div class="bg" onclick="closeForm(tambah)"></div>
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
                    <div class="header">
                        <h1>edit event</h1>
                    </div>
                    <input type="hidden" class="inpIdEvent" id="inpIdEvent">
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
        <div id="divHapusEvent" style="display:none">
            <div class="bg"></div>
            <div class="content">
                <span>apakah anda mau menghapus</span>
                <button class="hapusEvent" onclick="hapusEvent()">hapus</button>
                <button onclick="closeForm('hapus')">batal</button>
            </div>
        </div>
        <button onclick="showForm('tambah')"> tambah event</button>
        <script>
            // 
            <?php if(isset($dataEvent) || !is_null($dataEvent)){?>
                var dataEvent = <?php echo json_encode($dataEvent) ?>
            <?php }?>
        </script>
        <script src="<?php echo $tPath.'/public/js/event/dashboardMasyarakat.js?'?>"></script>
    <?php }else if($role == 'super admin' || $role == 'admin event'){ ?>
        <form id="editEventForm">
            <input type="text">
        </form>
        <table class="tableEvent" id="tableEvent">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama event</th>
                    <th scope="col">Tanggal awal</th>
                    <th scope="col">Tanggal akhir</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
            <?php
                $no = 1;
                foreach($dataEvents as $dataEvent){
            ?>
                    <th scope="row"><?php echo $no?></th>
                    <td> <?php echo $dataEvent['nama_event'] ?></td>
                    <td> <?php echo $dataEvent['tanggal_awal_event'] ?></td>
                    <td> <?php echo $dataEvent['tanggal_akhir_event'] ?></td>
                    <td>
                        <button onclick="showEditForm(<?php echo json_encode($dataEvent['id_event']) ?>)">hapus</button>
                        <button onclick="showDeleteForm(<?php echo json_encode($dataEvent['id_event']) ?>)">hapus</button>
                    </td>
            <?php 
                $no++;
            }
            ?>
                </tr>
            </tbody>
        </table>
        <script>
            var dataEvent = <?php echo json_encode($dataEvent) ?>
        </script>
        <script src="<?php echo $tPath.'/public/js/event/dashboardAdmin.js?'?>"></script>
    <?php } ?> -->
    <a href="/dashboard"><h1>kembali</h1></a>
    <br>
    <form method="POST" id="logoutForm">
        <input type="submit" value="metu">
    </form>
    <div id="preloader" style="display: none;"></div>
    <div id="greenPopup" style="display:none"></div>
    <div id="redPopup" style="display: none"></div>
</body>
</html>