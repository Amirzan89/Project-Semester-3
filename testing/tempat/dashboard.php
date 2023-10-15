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
                        <th scope="row"><?php echo $no ?></th>
                        <td> <?php echo $dataEvent['nama_event'] ?></td>
                        <td> <?php echo $dataEvent['tanggal_awal_event'] ?></td>
                        <td> <?php echo $dataEvent['tanggal_akhir_event'] ?></td>
                        <td>
                            <button onclick="showForm('edit',<?php echo json_encode($dataEvent['id_event']) ?>,<?php echo $no ?>)">edit</button>
                            <button onclick="showForm('hapus',<?php echo json_encode($dataEvent['id_event']) ?>,<?php echo $no ?>)">hapus</button>
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
            <form id="" action="/mobile/tempat/tempat.php" method="POST" enctype="multipart/form-data">
                    <div class="header">
                        <h1>tambah tempat</h1>
                    </div>
                    <input type="hidden" name="id_user" value="32">
                    <div class="row">
                        <label>ID tempat</label>
                        <input type="text" name="id_tempat" id="nama_tempat">
                    </div>
                    <div class="row">
                        <label>Nama tempat</label>
                        <input type="text" name="nama_tempat" id="nama_tempat">
                    </div>
                    <div class="row">
                        <label>NIK penyewa</label>
                        <input type="text" name="nik_penyewa" id="nik_penyewa">
                    </div>  
                    <div class="row">
                        <label>Nama peminjam</label>
                        <input type="text" name="nama_peminjam" id="nama_peminjam">
                    </div>
                    <div class="row">
                        <label>Deskripsi sewa</label>
                        <textarea name="deskripsi" id="deskripsi"></textarea>
                    </div>
                    <div class="row">
                        <label>Nama kegiatan</label>
                        <input type="text" name="nama_kegiatan_sewa" id="nama_kegiatan_sewa">
                    </div>
                    <div class="row">
                        <label>jumlah peserta</label>
                        <input type="text" name="jumlah_peserta" id="jumlah_peserta">
                    </div>
                    <div class="row">
                        <label>nama instansi</label>
                        <input type="text" name="instansi" id="instansi">
                    </div>
                    <div class="row">
                        <label>Tanggal awal event</label>
                        <input type="datetime-local" name="tanggal_awal_sewa" id="tanggal_awal_sewa">
                    </div>
                    <div class="row">
                        <label>Tanggal akhir event</label>
                        <input type="datetime-local" name="tanggal_akhir_sewa" id="tanggal_akhir_sewa">
                    </div>
                    <div class="row">
                        <label>surat_keterangan</label>
                        <input type="file" name="surat_keterangan" id="inpPosterEvent">
                    </div>
                    <input type="submit" value="Kirim">
                </form>
            </div>
        </div>
        <div id="divTambahEvent" style="display:block">
            <div class="bg" onclick="closeForm('tambah')"></div>
            <div class="content">
            <form id="" action="/mobile/tempat/tempat.php" method="POST" enctype="multipart/form-data">
                    <div class="header">
                        <h1>edit tempat</h1>
                    </div>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="id_user" value="32">
                    <!-- <input type="hidden" name="id_tempat" value="2"> -->
                    <div class="row">
                        <label>ID sewa</label>
                        <input type="text" name="id_sewa" id="nama_tempat">
                    </div>
                    <div class="row">
                        <label>ID tempat</label>
                        <input type="text" name="id_tempat" id="nama_tempat">
                    </div>
                    <div class="row">
                        <label>Nama tempat</label>
                        <input type="text" name="nama_tempat" id="nama_tempat">
                    </div>
                    <div class="row">
                        <label>NIK penyewa</label>
                        <input type="text" name="nik_penyewa" id="nik_penyewa">
                    </div>
                    <div class="row">
                        <label>Nama peminjam</label>
                        <input type="text" name="nama_peminjam" id="nama_peminjam">
                    </div>
                    <div class="row">
                        <label>Deskripsi sewa</label>
                        <textarea name="deskripsi" id="deskripsi"></textarea>
                    </div>
                    <div class="row">
                        <label>Nama kegiatan</label>
                        <input type="text" name="nama_kegiatan_sewa" id="nama_kegiatan_sewa">
                    </div>
                    <div class="row">
                        <label>jumlah peserta</label>
                        <input type="text" name="jumlah_peserta" id="jumlah_peserta">
                    </div>
                    <div class="row">
                        <label>nama instansi</label>
                        <input type="text" name="instansi" id="instansi">
                    </div>
                    <div class="row">
                        <label>Tanggal awal event</label>
                        <input type="datetime-local" name="tanggal_awal_sewa" id="tanggal_awal_sewa">
                    </div>
                    <div class="row">
                        <label>Tanggal akhir event</label>
                        <input type="datetime-local" name="tanggal_akhir_sewa" id="tanggal_akhir_sewa">
                    </div>
                    <div class="row">
                        <label>surat_keterangan</label>
                        <input type="file" name="surat_keterangan" id="inpPosterEvent">
                    </div>
                    <input type="submit" value="Kirim">
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
        <button onclick="showForm('tambah')"> tambah event</button>
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