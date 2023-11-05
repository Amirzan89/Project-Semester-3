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
                <form id="" action="/mobile/seniman/seniman.php" method="POST" enctype="multipart/form-data">
                    <div class="header">
                        <h1>registrasi seniman</h1>
                    </div>
                    <div>
                        <input type="hidden" name="id_user" value="32">
                    </div>
                    <div class="row">
                        <label>NIK seniman</label>
                        <input type="text" name="nik_seniman" id="nama_seniman">
                    </div>
                    <div class="row">
                        <label>Nama seniman</label>
                        <input type="text" name="nama_seniman" id="nama_seniman">
                    </div>
                    <div class="row">
                        <label>no telpon seniman</label>
                        <input type="text" name="no_telpon" id="nama_seniman">
                    </div>
                    <div class="row">
                        <label>Alamat seniman</label>
                        <textarea name="alamat" id="alamat"></textarea>
                    </div>
                    <div class="row">
                        <label>Jenis kelamin</label>
                        <select name="jenis_kelamin_seniman" id="jenis_kelamin" multiple>
                            <option value="laki-laki">Laki-laki</option>  
                            <option value="perempuan">perempuan</option>
                        </select>
                    </div>
                    <div class="row">
                        <label>Kategori</label>
                        <select name="kategori" id="kategori" multiple>
                            <option value="campursari">campursari</option>  
                            <option value="dalang">dalang</option>  
                            <option value="jaranan">jaranan</option>  
                            <option value="karawitan">karawitan</option>  
                            <option value="mc">MC</option>  
                            <option value="ludruk">ludruk</option>  
                            <option value="organisasi kesenian musik">organisasi kesenian musik</option>  
                            <option value="organisasi">organisasi</option>  
                            <option value="pramugari tayup">pramugari tayup</option>  
                            <option value="sanggar">sanggar</option>  
                            <option value="sinden">sinden</option>  
                            <option value="vocalis">vocalis</option>  
                            <option value="waranggono">waranggono</option>  
                        </select>
                    </div>
                    <div class="row">
                        <label>kecamatan</label>
                        <select name="kecamatan" id="kecamatan" multiple>
                            <option $formattedTotal = str_pad($total, 3, '0', STR_PAD_LEFT);value="bagor">bagor</option>
                            <option value="baron">baron</option>
                            <option value="berbek">berbek</option>
                            <option value="gondang">gondang</option>
                            <option value="jatikalen">jatikalen</option>
                            <option value="kertosono">kertosono</option>
                            <option value="lengkong">lengkong</option>
                            <option value="loceret">loceret</option>
                            <option value="nganjuk">nganjuk</option>
                            <option value="ngetos">ngetos</option>
                            <option value="ngluyu">ngluyu</option>
                            <option value="ngronggot">ngronggot</option>
                            <option value="pace">pace</option>
                            <option value="patianrowo">patianrowo</option>
                            <option value="prambon">prambon</option>
                            <option value="rejoso">rejoso</option>
                            <option value="sawahan">sawahan</option>
                            <option value="sukomoro">sukomoro</option>
                            <option value="tanjunganom">tanjonganom</option>
                            <option value="wilangan">wilangan</option>
                        </select>
                    </div>
                    <div class="row">
                        <label>tempat lahir seniman</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir">
                    </div>
                    <div class="row">
                        <label>Tanggal lahir seniman</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir">
                    </div>
                    <div class="row">
                        <label>nama organisasi</label>
                        <input type="text" name="nama_organisasi" id="nama_organisasi">
                    </div>
                    <div class="row">
                        <label>jumlah anggota organisasi</label>
                        <input type="text" name="anggota_organisasi" id="anggota_organisasi">
                    </div>
                    <div class="row">
                        <label>foto ktp</label>
                        <input type="file" name="foto_ktp" id="foto_ktp">
                    </div>
                    <div class="row">
                        <label>pass foto</label>
                        <input type="file" name="pass_foto" id="pass_foto">
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
                <form id="" action="/mobile/seniman/seniman.php" method="POST" enctype="multipart/form-data">
                    <div class="header">
                        <h1>edit seniman</h1>
                    </div>
                    <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id_user" value="32">
                    <div class="row">
                        <label>id seniman</label>
                        <input type="text" name="id_seniman" id="id_seniman">
                    </div>
                    <div class="row">
                        <label>NIK seniman</label>
                        <input type="text" name="nik_seniman" id="nama_seniman">
                    </div>
                    <div class="row">
                        <label>Nama seniman</label>
                        <input type="text" name="nama_seniman" id="nama_seniman">
                    </div>
                    <div class="row">
                        <label>no telpon seniman</label>
                        <input type="text" name="no_telpon" id="nama_seniman">
                    </div>
                    <div class="row">
                        <label>Alamat seniman</label>
                        <textarea name="alamat" id="alamat"></textarea>
                    </div>
                    <div class="row">
                        <label>Jenis kelamin</label>
                        <select name="jenis_kelamin_seniman" id="jenis_kelamin" multiple>
                            <option value="laki-laki">Laki-laki</option>  
                            <option value="perempuan">perempuan</option>
                        </select>
                    </div>
                    <div class="row">
                        <label>Kategori</label>
                        <select name="kategori" id="kategori" multiple>
                            <option value="campursari">campursari</option>  
                            <option value="dalang">dalang</option>  
                            <option value="jaranan">jaranan</option>  
                            <option value="karawitan">karawitan</option>  
                            <option value="mc">MC</option>  
                            <option value="ludruk">ludruk</option>  
                            <option value="organisasi kesenian musik">organisasi kesenian musik</option>  
                            <option value="organisasi">organisasi</option>  
                            <option value="pramugari tayup">pramugari tayup</option>  
                            <option value="sanggar">sanggar</option>  
                            <option value="sinden">sinden</option>  
                            <option value="vocalis">vocalis</option>  
                            <option value="waranggono">waranggono</option>  
                        </select>
                    </div>
                    <div class="row">
                        <label>kecamatan</label>
                        <select name="kecamatan" id="kecamatan" multiple>
                            <option value="bagor">bagor</option>
                            <option value="baron">baron</option>
                            <option value="berbek">berbek</option>
                            <option value="gondang">gondang</option>
                            <option value="jatikalen">jatikalen</option>
                            <option value="kertosono">kertosono</option>
                            <option value="lengkong">lengkong</option>
                            <option value="loceret">loceret</option>
                            <option value="nganjuk">nganjuk</option>
                            <option value="ngetos">ngetos</option>
                            <option value="ngluyu">ngluyu</option>
                            <option value="ngronggot">ngronggot</option>
                            <option value="pace">pace</option>
                            <option value="patianrowo">patianrowo</option>
                            <option value="prambon">prambon</option>
                            <option value="rejoso">rejoso</option>
                            <option value="sawahan">sawahan</option>
                            <option value="sukomoro">sukomoro</option>
                            <option value="tanjunganom">tanjonganom</option>
                            <option value="wilangan">wilangan</option>
                        </select>
                    </div>
                    <div class="row">
                        <label>tempat lahir seniman</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir">
                    </div>
                    <div class="row">
                        <label>Tanggal lahir seniman</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir">
                    </div>
                    <div class="row">
                        <label>nama organisasi</label>
                        <input type="text" name="nama_organisasi" id="nama_organisasi">
                    </div>
                    <div class="row">
                        <label>jumlah anggota organisasi</label>
                        <input type="text" name="anggota_organisasi" id="anggota_organisasi">
                    </div>
                    <div class="row">
                        <label>foto ktp</label>
                        <input type="file" name="foto_ktp" id="foto_ktp">
                    </div>
                    <div class="row">
                        <label>pass foto</label>
                        <input type="file" name="pass_foto" id="pass_foto">
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
            <form id="" action="/mobile/seniman/seniman.php" method="POST" enctype="multipart/form-data">
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
        <div id="divTambahperpanjangan" style="display:block">
            <div class="bg"></div>
            <div class="content">
            <form id="" action="/mobile/seniman/seniman.php" method="POST" enctype="multipart/form-data">
                    <div class="header">
                        <h1>Perpanjangan seniman</h1>
                    </div>
                    <!-- <input type="hidden" name="_method" value="DELETE"> -->
                    <input type="hidden" name="id_user" value="32">
                    <input type="hidden" name="keterangan" value="perpanjang">
                    <div class="row">
                        <label>ID seniman</label>
                        <input type="text" name="id_seniman" id="id_seniman">
                    </div>
                    <div class="row">
                        <label>foto ktp</label>
                        <input type="file" name="foto_ktp" id="foto_ktp">
                    </div>
                    <div class="row">
                        <label>pass foto</label>
                        <input type="file" name="pass_foto" id="pass_foto">
                    </div>
                    <div class="row">
                        <label>surat keterangan</label>
                        <input type="file" name="surat_keterangan" id="surat_keterangan">
                    </div>
                    <input type="submit" value="hapus">
                </form>
            </div>
        </div>
        <div id="divTambahperpanjangan" style="display:block">
            <div class="bg"></div>
            <div class="content">
            <form id="" action="/mobile/seniman/seniman.php" method="POST" enctype="multipart/form-data">
                    <div class="header">
                        <h1>edit Perpanjangan seniman</h1>
                    </div>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="id_user" value="32">
                    <input type="hidden" name="keterangan" value="perpanjang">
                    <div class="row">
                        <label>ID seniman</label>
                        <input type="text" name="id_seniman" id="id_seniman">
                    </div>
                    <div class="row">
                        <label>foto ktp</label>
                        <input type="file" name="foto_ktp" id="foto_ktp">
                    </div>
                    <div class="row">
                        <label>pass foto</label>
                        <input type="file" name="pass_foto" id="pass_foto">
                    </div>
                    <div class="row">
                        <label>surat keterangan</label>
                        <input type="file" name="surat_keterangan" id="surat_keterangan">
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