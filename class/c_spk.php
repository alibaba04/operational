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

	function addproyek(&$params) 
	{
		global $dbLink;
		global $dbLink2;
		require_once './function/fungsi_formatdate.php';
		require_once( './config2.php' );
		require_once( './config.php' );
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
			$q = "INSERT INTO `aki_tabel_proyek`( `noproyek`, `noSpk`, `noKK`, `tanggal`, `rangka_in`, `rangka_out`, `hollow_in`, `hollow_out`, `hl_plafon`, `mal_in`, `mal_out`, `gambarp_in`, `gambarp_out`, `bahan_in`, `bahan_out`, `cat_in`, `cat_out`, `catmakara`, `makara_in`, `makara_out`, `rangka_ga`, `packing_in`, `packing_out`, `pk_makara`, `ekspedisi_in`, `ekspedisi_out`, `pemasangan_in`, `pemasangan_out`, `ketuatim`, `dp`, `ket_dp`, `t2`, `ket_t2`, `t3`, `ket_t3`, `t4`, `ket_t4`, `biaya_transportasi`, `biaya_kaligrafi`, `kodeUser`) VALUES ";
			$q.= "('".$noproject."','".$nospk."','".$nokk."','".$tgl."','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','0000-00-00','-',0,'-',0,'-',0,'-',0,'-',0,0,'".$pembuat."');";
			if (!mysql_query( $q, $dbLink2))
				throw new Exception($q.'Gagal Add Project2. ');

			$q3 = "UPDATE aki_spk SET `noproyek`='".$noproject."'  WHERE nokk='".$nokk."'";
			if (!mysql_query( $q3, $dbLink2))
				throw new Exception($q3.'Gagal Add Project1. ');
			
			$ket = "`nomer`=".$params["txtnoKk"]."  -has Add to Project, datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembuat."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink2))
				throw new Exception($q4.'Gagal Add Project2. ');

			
			/*if (!mysql_query( $q5, $dbLink2))
				throw new Exception($q5.'Gagal Add Project2. ');*/
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
			$this->strResults=$q1."Sukses Add";
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
		global $dbLink2;
		require_once( './config2.php' );
		require_once './function/fungsi_formatdate.php';
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
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
		$cat_in = secureParam($params["cat_in"],$dbLink2);
		$cat_out = secureParam($params["cat_out"],$dbLink2);
		$catmakara = secureParam($params["catmakara"],$dbLink2);
		$makara_in = secureParam($params["makara_in"],$dbLink2);
		$makara_out = secureParam($params["makara_out"],$dbLink2);
		$noproject = secureParam($params["txtnoproyek"],$dbLink2);
		$rangka_ga = secureParam($params["rangka_ga"],$dbLink2);
		$packing_in = secureParam($params["packing_in"],$dbLink2);
		$packing_out = secureParam($params["packing_out"],$dbLink2);
		$pk_makara = secureParam($params["pk_makara"],$dbLink2);
		$ekspedisi_in = secureParam($params["ekspedisi_in"],$dbLink2);
		$ekspedisi_out = secureParam($params["ekspedisi_out"],$dbLink2);
		$pemasangan_in = secureParam($params["pemasangan_in"],$dbLink2);
		$pemasangan_out = secureParam($params["pemasangan_out"],$dbLink2);
		$ketuatim = secureParam($params["txtketuatim"],$dbLink2);
		if ($params["chkdp"] == 'on') {
			$dp = 1;
		}else{
			$dp = 0;
		}
		$ket_dp = secureParam($params["ketdp"],$dbLink2);
		if ($params["chkt2"] == 'on') {
			$t2 = 1;
		}else{
			$t2 = 0;
		}
		$ket_t2 = secureParam($params["kett2"],$dbLink2);
		if ($params["chkt3"] == 'on') {
			$t3 = 1;
		}else{
			$t3 = 0;
		}
		$ket_t3 = secureParam($params["kett3"],$dbLink2);
		if ($params["chkt4"] == 'on') {
			$t4 = 1;
		}else{
			$t4 = 0;
		}
		$ket_t4 = secureParam($params["kett4"],$dbLink2);
		$bt = secureParam($params["biayatransport"],$dbLink2);
		$biaya_transportasi = preg_replace("/\D/", "", $bt);
		$bk = secureParam($params["biayakaligrafi"],$dbLink2);
		$biaya_kaligrafi = preg_replace("/\D/", "", $bk);

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

			$q3 = "UPDATE `aki_tabel_proyek` SET `tanggal`='".$tglTransaksi."',`rangka_in`='".$rangka_in."',`rangka_out`='".$rangka_out."',`hollow_in`='".$hollow_in."',`hollow_out`='".$hollow_out."',`hl_plafon`='".$hl_plafon."',`mal_in`='".$mal_in."',`mal_out`='".$mal_out."', `gambarp_in`='".$gambarp_in."',`gambarp_out`='".$gambarp_out."',`bahan_in`='".$bahan_in."',`bahan_out`='".$bahan_out."',`cat_in`='".$cat_in."',`cat_out`='".$cat_out."',`catmakara`='".$catmakara."',`makara_in`='".$makara_in."', `makara_out`='".$makara_out."',`rangka_ga`='".$rangka_ga."',`packing_in`='".$packing_in."',`packing_out`='".$packing_out."',`pk_makara`='".$pk_makara."',`ekspedisi_in`='".$ekspedisi_in."',`ekspedisi_out`='".$ekspedisi_out."',`pemasangan_in`='".$pemasangan_in."',`pemasangan_out`='".$pemasangan_out."',`ketuatim`='".$ketuatim."',`dp`='".$dp."',`ket_dp`='".$ket_dp."',`t2`='".$t2."',`ket_t2`='".$ket_t2."',`t3`='".$t3."',`ket_t3`='".$ket_t3."',`t4`='".$t4."',`ket_t4`='".$ket_t4."',`biaya_transportasi`='".$biaya_transportasi."',`biaya_kaligrafi`='".$biaya_kaligrafi."',`kodeUser`='".$pembuat."'";
			$q3.= " WHERE noproyek='".$noproject."'";
			if (!mysql_query( $q3, $dbLink2))
				throw new Exception($q3.'Gagal Update Proyek2. ');

			@mysql_query("COMMIT", $dbLink2);
			$this->strResults="Sukses Update Proyek ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Update Proyek - ".$e->getMessage().'<br/>';
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
