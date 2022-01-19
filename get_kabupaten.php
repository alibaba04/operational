<?php
require_once( 'config.php' );
global $dbLink;
$id_prov = $_GET['id_prov'];
$sql = "SELECT * FROM kota WHERE `provinsi_id` = '$id_prov'";
$query = mysql_query($sql,$dbLink);
$data = array();
while($row =mysql_fetch_assoc($query)){
$data[] = array("id" => $row['id'], "name" => $row['name']);
}
echo json_encode($data);?>