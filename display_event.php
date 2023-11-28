<?php                
require 'web/koneksi.php'; 
$display_query = "select id_sewa, nama_peminjam, deskripsi_sewa_tempat, nama_kegiatan_sewa, tgl_awal_peminjaman, tgl_akhir_peminjaman from sewa_tempat";             
$results = mysqli_query($con,$display_query);   
$count = mysqli_num_rows($results);  
if($count>0) 
{
	$data_arr=array();
    $i=1;
	while($data_row = mysqli_fetch_array($results, MYSQLI_ASSOC))
	{	
	$data_arr[$i]['event_id'] = $data_row['id_sewa'];
	$data_arr[$i]['title'] = $data_row['nama_kegiatan_sewa'];
	$data_arr[$i]['name'] = $data_row['nama_peminjam'];
	$data_arr[$i]['desc'] = $data_row['deskripsi_sewa_tempat'];
	$data_arr[$i]['start'] = date("Y-m-d", strtotime($data_row['tgl_awal_peminjaman']));
	$data_arr[$i]['end'] = date("Y-m-d", strtotime($data_row['tgl_akhir_peminjaman']));
	$data_arr[$i]['color'] = '#'.substr(uniqid(),-6); // 'green'; pass colour name
	$i++;
	}
	
	$data = array(
                'status' => true,
                'msg' => 'successfully!',
				'data' => $data_arr
            );
}
else
{
	$data = array(
                'status' => false,
                'msg' => 'Error!'				
            );
}
echo json_encode($data);
?>