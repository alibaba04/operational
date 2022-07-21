<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_brg
{
	var $strResults="";
	
	function validate(&$params) 
	{
		$temp=TRUE;

		if($params["nokodeb"]=='' )
		{
			$this->strResults.="id belum terakumulasi!<br/>";
			$temp=FALSE;
		}       
		return $temp;
	}

	function addbrg(&$params) 
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';
		require_once( './config.php' );
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Add Barang1 - ".$this->strResults;
			return $this->strResults;
		}
		$tgl = date("Y-m-d");
		$nokodeb = secureParam($params["nokodeb"],$dbLink);
		$name = secureParam($params["txtnamab"],$dbLink);
		$sat = secureParam($params["txtsatuan"],$dbLink);
		$gol = secureParam($params["txtgol"],$dbLink);
		$lok = secureParam($params["txtlok"],$dbLink);
		$stok = secureParam($params["txtastok"],$dbLink);
		$jenis = secureParam($params["txtjenis"],$dbLink);
        $pembuat = $_SESSION["my"]->id;
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink2);
			$result = @mysql_query('BEGIN', $dbLink2);
			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d H:i:s");
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			$qq = "INSERT INTO `aki_barang`(`kode`, `nama`, `satuan`, `golongan`, `jenis`, `lokasi`, `astok`, `user`) VALUES ";
			$qq.= "('".$nokodeb."','".$name."','".$sat."','".$gol."','".$jenis."','".$lok."','".$stok."','".$pembuat."');";
			if (!mysql_query( $qq, $dbLink))
				throw new Exception($qq.'Gagal Add Barang.');
			$this->strResults="Sukses Add";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Add Project - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink2);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink2);
			  return $this->strResults;
		}
		return $this->strResults;
	}

	function edit(&$params) 
	{
		global $dbLink2;
		require_once './function/fungsi_formatdate.php';
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Add Project - ".$this->strResults;
			return $this->strResults;
		}
		$tglTransaksi = date("Y-m-d");
		$rangka_in = secureParam($params["rangka_in"],$dbLink2);
		$rangka_out = secureParam($params["rangka_out"],$dbLink2);
		$hollow_in = secureParam($params["hollow_in"],$dbLink2);
		$hollow_out = secureParam($params["hollow_out"],$dbLink2);
		$hl_plafon = secureParam($params["hollow_plafon"],$dbLink2);
		$mal_in = secureParam($params["mal_in"],$dbLink2);
		$mal_out = secureParam($params["mal_out"],$dbLink2);
		$gambarp_in = secureParam($params["gambarp_in"],$dbLink2);
		$gambarp_out = secureParam($params["gambarp_out"],$dbLink2);
		$bahan_in = secureParam($params["bahan_in"],$dbLink2);
		$bahan_out = secureParam($params["bahan_out"],$dbLink2);
		$nospk = secureParam($params["txtnospk"],$dbLink2);
		$noproject = secureParam($params["txtnoproyek"],$dbLink2);
        $pembuat = $_SESSION["my"]->id;
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink2);
			$result = @mysql_query('BEGIN', $dbLink2);
			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d H:i:s");
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			$q3 = "UPDATE `aki_proyek` SET `tanggal`='".$tglTransaksi."',`rangka_in`='".$rangka_in."',`rangka_out`='".$rangka_out."',`hollow_in`='".$hollow_in."',`hollow_out`='".$hollow_out."',`hl_plafon`='".$hl_plafon."',`mal_in`='".$mal_in."',`mal_out`='".$mal_out."',`gambarp_in`='".$gambarp_in."',`gambarp_out`='".$gambarp_out."',`bahan_in`='".$bahan_in."',`bahan_out`='".$bahan_out."'";
			$q3.= " WHERE noproyek='".$noproject."'";
			if (!mysql_query( $q3, $dbLink2))
				throw new Exception($q3.'Gagal Add Project1. ');
			$ket =" -Update to Project noproyek=".$noproject.", datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembuat."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink2))
				throw new Exception($q4.'Gagal Add Project2. ');
			$q4= "UPDATE `aki_proyek` SET `dp`='2' WHERE noproyek='S0002'";
			if (!mysql_query( $q4, $dbLink2))
				throw new Exception($q4.'Gagal Add Project2. ');
			$q5 = "INSERT INTO `aki_proyek`(`noproyek`, `noSpk`, `noKK`, `tanggal`, `kodeUser`) VALUES";
			$q5.= "('".$noproject."','0003/SPK-MS/PTAKI/II/2022','0003/SPK-MS/PTAKI/II/2022','".$tgl."','".$pembuat."');";
			if (!mysql_query( $q5, $dbLink2))
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
			$rsTemp=mysql_query("SELECT s.*,g.kodeGroup FROM `aki_user` s left join aki_usergroup g on s.kodeUser=g.kodeUser where g.kodeGroup='".$privilegeU."' limit 1", $dbLink2);
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
			@mysql_query("COMMIT", $dbLink2);*/
			$this->strResults=$q5."Sukses Edit";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Add Project - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink2);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink2);
			  return $this->strResults;
		}
		return $this->strResults;
	}
}
?>
