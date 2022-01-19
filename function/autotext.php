<?php
require_once('../config.php' );
require_once('../function/secureParam.php');
$searchTerm = $_GET['term'];
$result = mysql_query("SELECT * FROM aki_tabel_master WHERE kode_rekening LIKE '%".$searchTerm."%' or nama_rekening LIKE '%".$searchTerm."%' ", $dbLink);
			while ($row = mysql_fetch_assoc($result)) {
				// $data[] = $row['kode_rekening'] +" "+"("+$row['nama_rekening']+")"
			    // $data[] = $row['kode_rekening'];
			    $data[] =  array(
		         'kode' => $row['kode_rekening'],
		         'nama' => $row['nama_rekening'],
		        );

			}
			echo json_encode($data);

?>