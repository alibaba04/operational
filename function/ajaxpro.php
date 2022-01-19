<?php

require_once('../config.php' );
require_once('../function/secureParam.php');

   // $result = mysql_query("SELECT kode_rekening, nama_rekening FROM tabel_master WHERE kode_rekening LIKE '%".$_GET['query']."%' OR nama_rekening LIKE '%".$_GET['query']."%' LIMIT 10", $dbLink); 

   //  $json = [];

   //  while($row = mysql_fetch_array($result)){

   //       $json[] = $row['kode_rekening'];

   //  }


   //  echo json_encode($json);

if (isset($_REQUEST['query'])) {
    $query = $_REQUEST['query'];
    $sql = mysql_query ("SELECT kode_rekening, nama_rekening FROM tabel_master WHERE kode_rekening LIKE '%{$query}%' OR nama_rekening LIKE '%{$query}%'", $dbLink);
    $array = array();
    while ($row = mysql_fetch_array($sql)) {
        $array[] = array (
            'label' => $row['kode_rekening'].', '.$row['nama_rekening'],
            'value' => $row['kode_rekening'],
        );
    }
    //RETURN JSON ARRAY
    echo json_encode ($array);
}
?>
