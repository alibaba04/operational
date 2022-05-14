<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_proyek
{
	var $strResults="";
	
	function validate(&$params) 
	{
		$temp=TRUE;

		if($params["txtnoproyek"]=='' )
		{
			$this->strResults.="Harga belum terakumulasi!<br/>";
			$temp=FALSE;
		}       
		return $temp;
	}

	function edit(&$params) 
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Add Project - ".$this->strResults;
			return $this->strResults;
		}
		$tglTransaksi = date("Y-m-d");
		$rangka_in = secureParam($params["rangka_in"],$dbLink);
		$rangka_out = secureParam($params["rangka_out"],$dbLink);
		$hollow_in = secureParam($params["hollow_in"],$dbLink);
		$hollow_out = secureParam($params["hollow_out"],$dbLink);
		$hl_plafon = secureParam($params["hollow_plafon"],$dbLink);
		$mal_in = secureParam($params["mal_in"],$dbLink);
		$mal_out = secureParam($params["mal_out"],$dbLink);
		$gambarp_in = secureParam($params["gambarp_in"],$dbLink);
		$gambarp_out = secureParam($params["gambarp_out"],$dbLink);
		$bahan_in = secureParam($params["bahan_in"],$dbLink);
		$bahan_out = secureParam($params["bahan_out"],$dbLink);
		$nospk = secureParam($params["txtnospk"],$dbLink);
		$noproject = secureParam($params["txtnoproyek"],$dbLink);
        $pembuat = $_SESSION["my"]->id;
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d H:i:s");
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			/*$q3 = "UPDATE `aki_proyek` SET `tanggal`='".$tglTransaksi."',`rangka_in`='".$rangka_in."',`rangka_out`='".$rangka_out."',`hollow_in`='".$hollow_in."',`hollow_out`='".$hollow_out."',`hl_plafon`='".$hl_plafon."',`mal_in`='".$mal_in."',`mal_out`='".$mal_out."',`gambarp_in`='".$gambarp_in."',`gambarp_out`='".$gambarp_out."',`bahan_in`='".$bahan_in."',`bahan_out`='".$bahan_out."'";
			$q3.= " WHERE noproyek='".$noproject."'";
			if (!mysql_query( $q3, $dbLink))
				throw new Exception($q3.'Gagal Add Project1. ');*/
			$ket =" -Update to Project noproyek=".$noproject.", datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembuat."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
				throw new Exception($q4.'Gagal Add Project2. ');
			$q4= "UPDATE `aki_proyek` SET `dp`='2' WHERE noproyek='S0002'";
			if (!mysql_query( $q4, $dbLink))
				throw new Exception($q4.'Gagal Add Project2. ');
			$q5 = "INSERT INTO `aki_proyek`(`noproyek`, `noSpk`, `noKK`, `tanggal`, `kodeUser`) VALUES";
			$q5.= "('".$noproject."','0003/SPK-MS/PTAKI/II/2022','0003/SPK-MS/PTAKI/II/2022','".$tgl."','".$pembuat."');";
			if (!mysql_query( $q5, $dbLink))
						throw new Exception($q5.'Gagal Add Project2. ');
			//API send firebase
			/*$privilegeU='';
			if ($_SESSION["my"]->privilege == 'ADMIN') {
				$privilegeU = 'kpenjualan';
			}else if($_SESSION["my"]->privilege == 'kpenjualan'){
				$privilegeU = 'ADMIN';
			}else{
				$privilegeU = 'GODMODE';
			}
			$rsTemp=mysql_query("SELECT s.*,g.kodeGroup FROM `aki_user` s left join aki_usergroup g on s.kodeUser=g.kodeUser where g.kodeGroup='".$privilegeU."' limit 1", $dbLink);
			$temp = mysql_fetch_array($rsTemp);
			$token=$temp['token'];
			$Message = "SIKUBAH - Message from ".$_SESSION["my"]->privilege." Please Check 'Kontrak Kerja'. Nomor KK : '".$nokk."', Note : '".$treport."' ";
			$url ="https://fcm.googleapis.com/fcm/send";
			$fields=array(
				"to"=>$token,
				"notification"=>array(
					"body"=>$Message,
					"title"=>'Sikubah',
					"click_action"=>"https://sikubah.com/marketing/index.php?page=view/kk_list"
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
			@mysql_query("COMMIT", $dbLink);*/
			$this->strResults=$q5."Sukses Edit";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Add Project - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
}
?>
