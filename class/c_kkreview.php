<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
require_once( 'config2.php' );
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_kkreview
{
	var $strResults="";
	
	function addnote(&$params){
		global $dbLink2;
		require_once './function/fungsi_formatdate.php';
		$nokk = secureParam($params["txtnoKk"],$dbLink2);
        $treport = secureParam($params["txtNote"],$dbLink2);
        $pembuat = $_SESSION["my"]->id;
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink2);
			$result = @mysql_query('BEGIN', $dbLink2);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
				date_default_timezone_set("Asia/Jakarta");
				$tgl = date("Y-m-d h:i:sa");
				$readby = '';
				if ($_SESSION["my"]->privilege == 'ADMIN') {
					$readby = 'kpenjualan';
				}elseif($_SESSION["my"]->privilege == 'kpenjualan'){
					$readby = 'ADMIN';
				}else{
					$readby = 'ADMIN';
				}
				$ket = "KK Note, nokk=".$nokk.", note=".$treport.", read by ".$readby."=1";
				$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
				$q4.= "('".$pembuat."','".$tgl."','".$ket."');";
				if (!mysql_query( $q4, $dbLink2))
							throw new Exception('Gagal note KK. ');

				@mysql_query("COMMIT", $dbLink2);
					$this->strResults="Sukses Note";
				/*$destination = 0; 
				$q = "SELECT phone FROM `aki_user` as auser left join aki_usergroup agroup on auser.kodeUser=agroup.kodeuser where agroup.kodeGroup='".$readby."'";
				$result=mysql_query($q, $dbLink2);

				if($dataMenu=mysql_fetch_row($result))
				{
					$destination = $dataMenu[0]; 
				}
				//API send WA
				$my_apikey = "ZDMMOCURFXUCNH8EEK36"; 
				
				$message = "SIKUBAH - Message from ".$_SESSION["my"]->privilege." Please Check 'Review Kontrak Kerja'. Number KK : '".$nokk."', Note : '".$treport."' https://bit.ly/2SpMdIo"; 
				$api_url = "http://panel.rapiwha.com/send_message.php"; 
				$api_url .= "?apikey=". urlencode ($my_apikey); 
				$api_url .= "&number=". urlencode ($destination); 
				$api_url .= "&text=". urlencode ($message); 
				$my_result_object = json_decode(file_get_contents($api_url, false)); 
				if ($my_result_object->success != 0) {
					$this->strResults="Sukses Note";
				}else{
					$this->strResults=$my_result_object->description;
				}*/
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Tambah Data - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink2);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink2);
			  return $this->strResults;
		}
		return $this->strResults;
	}

	function approve(&$params) {
		global $dbLink2;
		require_once './function/fungsi_formatdate.php';
		$nokk = secureParam($params["txtnoKk"],$dbLink2);
		$nokkEn = secureParam($params["txtnoKkEn"],$dbLink2);
		$nosph = secureParam($params["txtnoSph"],$dbLink2);
        $pembuat = $_SESSION["my"]->id;
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink2);
			$result = @mysql_query('BEGIN', $dbLink2);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
				date_default_timezone_set("Asia/Jakarta");
				$tgl = date("Y-m-d h:i:sa");
				$ket = "KK Approve k_operational, nokk=".$nokk;

				$q3 = "UPDATE aki_kk SET `approve_koperational`='".$pembuat."',`approve_tgl2`='".$tgl."'  WHERE noKk='".$nokk."'";
				if (!mysql_query( $q3, $dbLink2))
					throw new Exception($q3.'Gagal ubah data KK. ');

				$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
				$q4.= "('".$pembuat."','".$tgl."','".$ket."');";
				if (!mysql_query( $q4, $dbLink2))
						throw new Exception('Gagal approve KK. ');

				//API send firebase
				$privilegeU='ADMIN';
				$rsTemp=mysql_query("SELECT s.*,g.kodeGroup FROM `aki_user` s left join aki_usergroup g on s.kodeUser=g.kodeUser where g.kodeGroup='".$privilegeU."' limit 1", $dbLink2);
				$temp = mysql_fetch_array($rsTemp);
				$token=$temp['token'];
				$Message = "SIKUBAH - Pesan dari Kepala Operational 'Kontrak Kerja'. Nomor KK : '".$nokk."', Telah disetujui. Note : '".$treport."' ";
				$url ="https://fcm.googleapis.com/fcm/send";
				$fields=array(
					"to"=>$token,
					"notification"=>array(
						"body"=>$Message,
						"title"=>'Sikubah',
						"click_action"=>"https://sikubah.com/marketing/index.php?page=view/kkreview_detail&mode=addNote&noKK=".$nokkEn
					)
				);
				$headers=array(
					'Authorization: key=AAAA-drRgeY:APA91bGaAAaXRV5K9soSk_cFyKSkWkFSu1Nr3MO3OofWYjM_S0HEEX1IZtMLGZpcbx-N0RTFDMqk4hoOEkXA0PbqnSThk5qemRdkK7gPiuUQFHPWNzfeWbj-WRnFtpCVb17Fop4JRu6o',
					'Content-Type:application/json'
				);
				$ch=curl_init();
				curl_setopt($ch,CURLOPT_URL,$url);
				curl_setopt($ch,CURLOPT_POST,true);
				curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
				curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($fields));
				$result=curl_exec($ch);
				print_r($result);
				curl_close($ch);
				@mysql_query("COMMIT", $dbLink2);
				$this->strResults="Sukses";
			
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Tambah Data - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink2);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink2);
			  return $this->strResults;
		}
		return $this->strResults;
	}
}
?>
