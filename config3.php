<?php 
// require_once('./function/mysql.php');
require_once('function/mysql.php');

/*********** Database Settings ***********/
$dbHost = 'localhost';
$dbName = 'u8364183_hcm'; 


$dbUser = 'u8364183_marketing';
$dbPass = 'PVMMA0Akp4;(';

$passSalt = 'UFqPNrZENKSQc5yc';

//Default database link
error_reporting(E_ALL ^ E_DEPRECATED);
$dbLink3 = mysql_connect($dbHost,$dbUser,$dbPass, true)or die('Could not connect: ' . mysql_error());
mysql_query("SET NAMES 'UTF8'");

if(!mysql_select_db($dbName,$dbLink3))
{
	die('Database Connection Failed!');
}


/*********** Email Settings ***********/
$mailFrom = 'aki';

$mailSupport = 'albaihaqial@gmail.com';

/*********** Display Settings ***********/
$siteTitle = 'Operational AKI';
$recordPerPage = 10;

$wajibIsiKeterangan ='<font style="color:#FF0000; font-weight:bold">Field Bertanda * Wajib Diisi</font>';
$wajibIsiSimbol = '<font style="color:#FF0000; font-weight:bold">&nbsp;&nbsp;*</font>';
?>