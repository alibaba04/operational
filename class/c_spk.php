<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_spk
{
	var $strResults="";
	
	function validate(&$params) 
	{
		$temp=TRUE;

		if($params["txtnoKk"]=='' )
		{
			$this->strResults.="Harga belum terakumulasi!<br/>";
			$temp=FALSE;
		}       
		return $temp;
	}

	function edit(&$params) 
	{
		global $dbLink2;
		global $dbLink;
		require_once './function/fungsi_formatdate.php';
		require_once( './config2.php' );
		$q='';
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Add Project - ".$this->strResults;
			return $this->strResults;
		}
		$tglTransaksi = date("Y-m-d");
		$nokk = secureParam($params["txtnoKk"],$dbLink2);
		$nospk = secureParam($params["txtnoSpk"],$dbLink2);
		$noproject = secureParam($params["txtnoproject"],$dbLink2);
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
			$q3 = "UPDATE aki_spk SET `noproyek`='".$noproject."'  WHERE nokk='".$nokk."'";
			if (!mysql_query( $q3, $dbLink2))
				throw new Exception($q3.'Gagal Add Project1. ');
			
			$q5 = "INSERT INTO `aki_proyek`(`noproyek`, `noSpk`, `noKK`, `tanggal`, `kodeUser`) VALUES";
			$q5.= "('".$noproject."','".$nospk."','".$nokk."','".$tgl."','".$pembuat."');";
			if (!mysql_query( $q5, $dbLink))
						throw new Exception($q5.'Gagal Add Project2. ');

			
			$ket = "`nomer`=".$params["txtnoKk"]."  -has Add to Project, datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembuat."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink2))
						throw new Exception($q4.'Gagal Add Project2. ');

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
			$this->strResults="Sukses Edit";
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
	
	function validateDelete($kode) 
	{
		global $dbLink2;
		$temp=FALSE;
		if(empty($kode))
		{
			$this->strResults.="No KK tidak ditemukan!<br/>";
			$temp=FALSE;
		}

		//cari ID inisiasi di tabel penyusunan
		$rsTemp=mysql_query("SELECT * FROM aki_kk WHERE (noKk) = '".$kode."'", $dbLink2);
                $rows = mysql_num_rows($rsTemp);
                if($rows!=0)
		{
			$temp=TRUE;
		} 
		
		return $temp;
	}

	function delete($kode)
	{
		global $dbLink2;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateDelete($kode))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data KK - ".$this->strResults;
			return $this->strResults;
		}

		$noKk  = secureParam($kode,$dbLink2);
        $pembatal = $_SESSION["my"]->id;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink2);
			$result = @mysql_query('BEGIN', $dbLink2);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			$rsTemp=mysql_query("SELECT s.`nama_cust`,s.`provinsi`,s.`kota`,ds.`model`,ds.`d`,ds.`dt`,ds.`t`,ds.`luas`,ds.`plafon`,ds.`harga`,ds.`jumlah`,ds.`ket`,ds.`bahan` FROM aki_KK s LEFT JOIN aki_dkk ds ON s.`noKk`=ds.`noKk` WHERE s.`noKk` = '".$noKk."'", $dbLink2);
			$temp = mysql_fetch_array($rsTemp);
			$tempNamecust  = $temp['nama_cust'];
			$tempP  = $temp['provinsi'];
			$tempK  = $temp['kota'];
			$tempModel  = $temp['model'];
			$tempD  = $temp['d'];
			$tempDt  = $temp['dt'];
			$tempT  = $temp['t'];
			$tempLuas  = $temp['luas'];
			$tempPlafon  = $temp['plafon'];
			$tempHarga  = $temp['harga'];
			$tempJumlah  = $temp['jumlah'];
			$tempKet  = $temp['ket'];
			$tempBahan  = $temp['bahan'];

			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d h:i:sa");
			$ket = "`nomer`=".$noKk." -has delete, ket : ".$tempNamecust.", ".$tempP.", ".$tempK.", ".$tempModel.", ".$tempD.", ".$tempT.", ".$tempDt.", ".$tempKet.", ".$tempLuas.", ".$tempJumlah.", ".$tempHarga.", ".$tempPlafon.", ".$tempBahan.", datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembatal."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink2))
						throw new Exception($q4.'Gagal ubah data KK. ');

			$q = "DELETE FROM  `aki_kk`";
			$q.= "WHERE (noKk)='".$noKk."';";

			if (!mysql_query( $q, $dbLink2))
				throw new Exception('Gagal hapus data KK.');

			$q2 = "DELETE FROM aki_dpembayaran ";
			$q2.= "WHERE (noKk)='".$noKk."';";
			if (!mysql_query( $q2, $dbLink2))
				throw new Exception('Gagal hapus data KK.');

			$q3 = "DELETE FROM aki_dkk ";
			$q3.= "WHERE (noKk)='".$noKk."';";

			if (!mysql_query( $q3, $dbLink2))
				throw new Exception('Gagal hapus data KK.');

			$q4 = "DELETE FROM aki_kkcolor ";
			$q4.= "WHERE (noKk)='".$noKk."';";

			if (!mysql_query( $q4, $dbLink2))
				throw new Exception('Gagal hapus data KK.');

			$q5 = "DELETE FROM aki_report ";
			$q5.= "WHERE ket like'%".$noKk."%';";

			if (!mysql_query( $q5, $dbLink2))
				throw new Exception('Gagal hapus data KK.');

			@mysql_query("COMMIT", $dbLink2);
			$this->strResults="Sukses Hapus Data KK ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Hapus Data KK - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink2);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink2);
			  return $this->strResults;
		}
		return $this->strResults;
		
	}

	function update(&$params)
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
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
		$cat_in = secureParam($params["cat_in"],$dbLink);
		$cat_out = secureParam($params["cat_out"],$dbLink);
		$catmakara = secureParam($params["catmakara"],$dbLink);
		$makara_in = secureParam($params["makara_in"],$dbLink);
		$makara_out = secureParam($params["makara_out"],$dbLink);
		$noproject = secureParam($params["txtnoproyek"],$dbLink);
		$rangka_ga = secureParam($params["rangka_ga"],$dbLink);
		$packing_in = secureParam($params["packing_in"],$dbLink);
		$packing_out = secureParam($params["packing_out"],$dbLink);
		$pk_makara = secureParam($params["pk_makara"],$dbLink);
		$ekspedisi_in = secureParam($params["ekspedisi_in"],$dbLink);
		$ekspedisi_out = secureParam($params["ekspedisi_out"],$dbLink);
		$pemasangan_in = secureParam($params["pemasangan_in"],$dbLink);
		$pemasangan_out = secureParam($params["pemasangan_out"],$dbLink);

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

			$q3 = "UPDATE `aki_proyek` SET `tanggal`='".$tglTransaksi."',`rangka_in`='".$rangka_in."',`rangka_out`='".$rangka_out."',`hollow_in`='".$hollow_in."',`hollow_out`='".$hollow_out."',`hl_plafon`='".$hl_plafon."',`mal_in`='".$mal_in."',`mal_out`='".$mal_out."',`gambarp_in`='".$gambarp_in."',`gambarp_out`='".$gambarp_out."',`bahan_in`='".$bahan_in."',`bahan_out`='".$bahan_out."'`cat_in`='".$bahan_out."',`cat_out`='".$bahan_out."',`catmakara`='".$bahan_out."',`makara_in`='".$bahan_out."',`makara_out`='".$bahan_out."',`rangka_ga`=[value-22],`packing_in`=[value-23],`packing_out`=[value-24],`pk_makara`=[value-25],`ekspedisi_in`=[value-26],`ekspedisi_out`=[value-27],`pemasangan_in`=[value-28],`pemasangan_out`=[value-29],";
			$q3.= " WHERE noproyek='".$noproject."'";
			if (!mysql_query( $q3, $dbLink))
				throw new Exception($q4.'Gagal Add Project2. ');

			@mysql_query("COMMIT", $dbLink2);
			$this->strResults="Sukses Hapus Data KK ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Hapus Data KK - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink2);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink2);
			  return $this->strResults;
		}
		return $this->strResults;
		
	}
	function uploadimg(){
		echo "<pre>";
		print_r($_FILES);
		echo "</pre>";
	}

}
?>
