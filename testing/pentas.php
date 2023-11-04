<?php 
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
        <script>
            <?php if(isset($dataEvents) && !empty($dataEvents && !is_null($dataEvents))){?>
                var  dataEvents = <?php echo json_encode($dataEvents) ?>;
                var id_event = dataEvents[dataEvents.length-1].id_event;
                <?php }else{ ?>
                    var dataEvents = [], id_event = 1; 
                <?php }?>
        </script>
        <!-- <table class="tableEvent" id="tableEvent">
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
            </tbody>
        </table> -->
        <div id="divTambahSeniman" style="display:block">
            <div class="bg" onclick="closeForm('tambah')"></div>
            <div class="content">
                <form id="qdqd" action="/mobile/pentas/pentas.php" method="POST" enctype="multipart/form-data">
                    <div class="header">
                        <h1>pengajuan pentas</h1>
                    </div>
                    <input type="hidden" name="id_user" value="32">
                    <div class="row">
                        <label>id seniman</label>
                        <input type="text" name="id_seniman" id="id_seniman">
                    </div>
                    <div class="row">
                        <label>Nama advis</label>
                        <input type="text" name="nama" id="nama_seniman">
                    </div>
                    <div class="row">
                        <label>Alamat advis</label>
                        <textarea name="alamat" id="alamat"></textarea>
                    </div>
                    <div class="row">
                        <label>Deskripsi advis</label>
                        <textarea name="deskripsi" id="deskripsi"></textarea>
                    </div>
                    <div class="row">
                        <label>Nama pentas</label>
                        <input type="text" name="nama_pentas" id="tempat_lahir">
                    </div>
                    <div class="row">
                        <label>Tanggal awal pentas</label>
                        <input type="date" name="tanggal_awal" id="tanggal_awal">
                    </div>
                    <div class="row">
                        <label>Tanggal akhir pentas</label>
                        <input type="date" name="tanggal_akhir" id="tanggal_akhir">
                    </div>
                    <div class="row">
                        <label>Nama tempat</label>
                        <input type="text" name="tempat_pentas" id="tempat_pentas">
                    </div>
                    <div class="row">
                        <label>surat keterangan</label>
                        <input type="file" name="surat_keterangan" id="surat_keterangan">
                    </div>
                    <input type="submit" name="tambah" value="Kirim">
                </form>
            </div>
        </div>
        <div id="divEditSeniman" style="display:block">
            <div class="bg" onclick="closeForm('tambah')"></div>
            <div class="content">
                <form id="" action="/mobile/pentas/pentas.php" method="POST" enctype="multipart/form-data">
                    <div class="header">
                        <h1>edit pentas</h1>
                    </div>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="id_user" value="32">
                    <div class="row">
                        <label>id pentas</label>
                        <input type="text" name="id_advis" id="id_pentas">
                    </div>
                    <div class="row">
                        <label>id seniman</label>
                        <input type="text" name="id_seniman" id="id_seniman">
                    </div>
                    <div class="row">
                        <label>Nama advis</label>
                        <input type="text" name="nama" id="nama_seniman">
                    </div>
                    <div class="row">
                        <label>Alamat advis</label>
                        <textarea name="alamat" id="alamat"></textarea>
                    </div>
                    <div class="row">
                        <label>Deskripsi advis</label>
                        <textarea name="deskripsi" id="deskripsi"></textarea>
                    </div>
                    <div class="row">
                        <label>Nama pentas</label>
                        <input type="text" name="nama_pentas" id="tempat_lahir">
                    </div>
                    <div class="row">
                        <label>Tanggal awal pentas</label>
                        <input type="date" name="tanggal_awal" id="tanggal_awal">
                    </div>
                    <div class="row">
                        <label>Tanggal akhir pentas</label>
                        <input type="date" name="tanggal_akhir" id="tanggal_akhir">
                    </div>
                    <div class="row">
                        <label>Nama tempat</label>
                        <input type="text" name="tempat_pentas" id="tempat_pentas">
                    </div>
                    <div class="row">
                        <label>surat keterangan</label>
                        <input type="file" name="surat_keterangan" id="surat_keterangan">
                    </div>
                    <input type="submit" name="tambah" value="Kirim">
                </form>
            </div>
        </div>
        <div id="divHapusEvent" style="display:block">
            <div class="bg"></div>
            <div class="content">
            <form id="" action="/mobile/pentas/pentas.php" method="POST" enctype="multipart/form-data">
                    <div class="header">
                        <h1>hapus seniman</h1>
                    </div>
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="id_user" value="32">
                    <div class="row">
                        <label>ID seniman</label>
                        <input type="text" name="id_seniman" id="id_seniman">
                    </div>
                    <input type="submit" value="hapus">
                </form>
            </div>
        </div>
        <button onclick="showForm('tambah')"> tambah event</button>
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
                <?php
                    $no = 1;
                    foreach($dataEvents as $dataEvent){
                ?>
                <tr>
                    <th scope="row"><?php echo $no?></th>
                    <td> <?php echo $dataEvent['nama_event'] ?></td>
                    <td> <?php echo $dataEvent['tanggal_awal_event'] ?></td>
                    <td> <?php echo $dataEvent['tanggal_akhir_event'] ?></td>
                    <td>
                        <button onclick="showForm('proses',<?php echo json_encode($dataEvent['id_event']) ?>,<?php echo $no ?>)">proses</button>
                    </td>
                </tr>
                <?php
                    $no++;
                }
                ?>
            </tbody>
        </table>
        <script>
            var dataEvent = <?php echo json_encode($dataEvent) ?>
        </script>
        <a href="/dashboard"><h1>kembali</h1></a>
        <br>
        <button onclick="logout()"> metu</button>
    <div id="preloader" style="display: none;"></div>
    <div id="greenPopup" style="display:none"></div>    
    <div id="redPopup" style="display: none"></div>
    <?php if($role == 'masyarakat'){ ?>
    <script src="<?php echo $tPath.'/public/js/event/dashboardMasyarakat.js?'?>"></script>
    <?php }else if($role == 'super admin' || $role == 'admin event'){ ?>
    <script src="<?php echo $tPath.'/public/js/event/dashboardAdmin.js?'?>"></script>
    <?php } ?>
</body>
</html>