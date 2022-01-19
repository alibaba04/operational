<?php
require_once( 'config.php' );
global $dbLink;
$id_kota = $_GET['id_kota'];
$sql = "SELECT * FROM provinsi WHERE id=(SELECT provinsi_id FROM `kota`where id='".$id_kota."')ORDER BY name ASC";
$query = mysql_query($sql,$dbLink);
$data = array();
while($row =mysql_fetch_assoc($query)){
$data[] = array("id" => $row['id'], "name" => $row['name']);
}
echo json_encode($data);?>