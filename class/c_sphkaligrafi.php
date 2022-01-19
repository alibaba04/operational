<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_sphkaligrafi
{
	var $strResults="";
	
	function addsphk(&$params,$nameimg){
		global $dbLink;
		require_once './function/fungsi_formatdate.php';
        $tglTransaksi = date("Y-m-d");
        $namacust = secureParam($params["txtnamacust"],$dbLink);
        $sdr = secureParam($params["cbosdr"],$dbLink);
        $ket = secureParam($params["txtket"],$dbLink);
        $noSph = secureParam($params["txtnoSph"],$dbLink);
        $alamat = secureParam($params["provinsi"],$dbLink);
        $alamat = secureParam($params["provinsi"],$dbLink);
        $provinsi = substr($alamat,0, 2);
        $kota = substr($alamat,3, 6);
        $pembuat = $_SESSION["my"]->id;
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			
			$q = "INSERT INTO aki_sph(noSph, nama_cust, masjid, provinsi, kota, tanggal, keterangan_kk, kodeUser) ";
			$q.= "VALUES ('".$noSph."','".$sdr.$namacust."','".$ket."','".$provinsi."','".$kota."','".$tglTransaksi."','','".$pembuat."');";
			if (!mysql_query($q, $dbLink))
				throw new Exception('Gagal masukkan data dalam database.');
			$jumData = $params["jumAddJurnal"];
			$nomer=0;
			for ($j = 0; $j < $jumData ; $j++){
				if (!empty($params['chkAddJurnal_'.$j])){

                    $transport = secureParam($params["chkTransport_" . $j], $dbLink);
                    $ppn = secureParam($params["chkPPN_" . $j], $dbLink);
                    $harga = secureParam($params["txtBplafon_" . $j], $dbLink);
                    $h1 = preg_replace("/\D/", "", $harga);
                    $diameter = secureParam($params["txtD_". $j],$dbLink);
                    $tinggi = secureParam($params["txtT_". $j],$dbLink);
                    $motif = secureParam($params["txtMotif_". $j],$dbLink);
                    
                    $q2 = "INSERT INTO `aki_dkaligrafi`(`nomer`, `noSph`, `d`, `t`, `motif`,`harga`, `ppn`, `transport`, `kaligrafi`) VALUES";
					$q2.= " ('".$nomer."','".$noSph."', '".$diameter."', '".$tinggi."', '".$motif."', '".$h1."', '".$ppn."', '".$transport."', 'empty');";

					if (!mysql_query( $q2, $dbLink))
						throw new Exception($q2.'Gagal tambah data Kaligrafi.');
					@mysql_query("COMMIT", $dbLink);
					$this->strResults="Sukses Tambah Data Kaligrafi";
					$nomer++;
				}
			}
			
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Tambah Data - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}

	function validate(&$params) 
	{
		$temp=TRUE;

		if($params["txtnoSph"]=='' )
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
		$q='';
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Ubah Data SPH - ".$this->strResults;
			return $this->strResults;
		}
		$tglTransaksi = date("Y-m-d");
        $namacust = secureParam($params["txtnamacust"],$dbLink);
        $sdr = secureParam($params["cbosdr"],$dbLink);
        $nmasjid = secureParam($params["txtnmasjid"],$dbLink);
        $tmasjid = secureParam($params["cbomasjid"],$dbLink);
        $alamat = secureParam($params["provinsi"],$dbLink);
        $provinsi = substr($alamat,0, 2);
        $kota = substr($alamat,3, 6);
        $pembuat = $_SESSION["my"]->id;
		$q3='';
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			
			//report
			$rsTemp=mysql_query("SELECT s.`nama_cust`,s.`provinsi`,s.`kota`,ds.`model`,ds.`d`,ds.`dt`,ds.`t`,ds.`luas`,ds.`plafon`,ds.`harga`,ds.`harga2`,ds.`harga3`,ds.`jumlah`,ds.`ket`,ds.`transport`,ds.`biaya_plafon`,ds.`bahan` FROM aki_sph s LEFT JOIN aki_dsph ds ON s.`noSph`=ds.`noSph` WHERE s.`noSph` = '".$params["txtnoSph"]."'", $dbLink);
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
			$tempHarga2  = $temp['harga2'];
			$tempHarga3  = $temp['harga3'];
			$tempJumlah  = $temp['jumlah'];
			$tempKet  = $temp['ket'];
			$tempTrans  = $temp['transport'];
			$tempBiaya  = $temp['biaya_plafon'];
			$tempBahan  = $temp['bahan'];

			$q3 = "UPDATE aki_sph SET `masjid`='".$tmasjid.$nmasjid."',`nama_cust`='".$sdr.$namacust."',`provinsi`='".$provinsi."',`kota`='".$kota."' WHERE noSph='".$params["txtnoSph"]."'";
			if (!mysql_query( $q3, $dbLink))
						throw new Exception($q3.'Gagal ubah data SPH. ');
			$jumData = $params["jumAddJurnal"];
			$nomer =0;
			for ($j = 0; $j < $jumData ; $j++){
				if (!empty($params['chkEdit_'.$j])){

                    $idSph = secureParam($params["chkEdit_" . $j], $dbLink);
                    $harga1 = secureParam($params["txtHarga1_" . $j], $dbLink);
                    $h1 = preg_replace("/\D/", "", $harga1);
                    $diameter = secureParam($params["txtD_". $j],$dbLink);
                    $tinggi = secureParam($params["txtT_". $j],$dbLink);
                    $kaligrafi = secureParam($params["txtkaligrafi_". $j],$dbLink);
                    $chkppn = secureParam($params["txtppn_". $j],$dbLink);
                    $chktransport = secureParam($params["txttrans_". $j],$dbLink);
                    $motif = secureParam($params["txtMotif_". $j],$dbLink);
                    $q = "UPDATE aki_dkaligrafi SET `noSph`='".$params["txtnoSph"]."',`d`='".$diameter."',`t`='".$tinggi."',`motif`='".$motif."',`harga`='".$h1."',`ppn`='".$chkppn."',`transport`='".$chktransport."',`kaligrafi`='".$kaligrafi."'";
					$q.= " WHERE nosph='".$params["txtnoSph"]."' ;";

					if (!mysql_query( $q, $dbLink))
						throw new Exception('Gagal ubah data Kaligrafi.');
                    $nomer++;
					
				}
			}
			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d h:i:sa");
			$ket = "`nomer`=".$params["txtnoSph"]."  -has change, ket : ".$tempNamecust.", ".$tempP.", ".$tempK.", ".$tempModel.", ".$tempD.", ".$tempT.", ".$tempDt.", ".$tempTrans.", ".$tempKet.", ".$tempLuas.", ".$tempJumlah.", ".$tempBiaya.", ".$tempHarga.", ".$tempHarga2.", ".$tempPlafon.", ".$tempBahan.", datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembuat."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
						throw new Exception($q4.'Gagal ubah data Kaligrafi. ');
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Ubah Data Kaligrafi";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Ubah Data SPH - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
	
	function validateDelete($kode) 
	{
		global $dbLink;
		$temp=FALSE;
		if(empty($kode))
		{
			$this->strResults.="No SPH tidak ditemukan!<br/>";
			$temp=FALSE;
		}

		//cari ID inisiasi di tabel penyusunan
		$rsTemp=mysql_query("SELECT idSph, noSph FROM aki_sph WHERE (noSph) = '".$kode."'", $dbLink);
                $rows = mysql_num_rows($rsTemp);
                if($rows!=0)
		{
			$temp=TRUE;
		} 
		
		return $temp;
	}

	function delete($kode)
	{
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateDelete($kode))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data SPH - ".$this->strResults;
			return $this->strResults;
		}

		$noSph = secureParam($kode,$dbLink);
        $pembatal = $_SESSION["my"]->id;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			$rsTemp=mysql_query("SELECT s.`nama_cust`,s.`provinsi`,s.`kota`,ds.`model`,ds.`d`,ds.`dt`,ds.`t`,ds.`luas`,ds.`plafon`,ds.`harga`,ds.`harga2`,ds.`harga2`,ds.`jumlah`,ds.`ket`,ds.`transport`,ds.`biaya_plafon`,ds.`bahan` FROM aki_sph s LEFT JOIN aki_dsph ds ON s.`noSph`=ds.`noSph` WHERE s.`noSph` = '".$noSph."'", $dbLink);
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
			$tempHarga2  = $temp['harga2'];
			$tempHarga3  = $temp['harga3'];
			$tempJumlah  = $temp['jumlah'];
			$tempKet  = $temp['ket'];
			$tempTrans  = $temp['transport'];
			$tempBiaya  = $temp['biaya_plafon'];
			$tempBahan  = $temp['bahan'];

			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d h:i:sa");
			$ket = "`nomer`=".$noSph." -has delete, ket : ".$tempNamecust.", ".$tempP.", ".$tempK.", ".$tempModel.", ".$tempD.", ".$tempT.", ".$tempDt.", ".$tempTrans.", ".$tempKet.", ".$tempLuas.", ".$tempJumlah.", ".$tempBiaya.", ".$tempHarga.", ".$tempHarga2.", ".$tempPlafon.", ".$tempBahan.", datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembatal."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
						throw new Exception($q4.'Gagal ubah data SPH. ');

			$q = "DELETE FROM aki_sph ";
			$q.= "WHERE (noSph)='".$noSph."';";

			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal hapus data SPH.');

			$q2 = "DELETE FROM aki_dkaligrafi ";
			$q2.= "WHERE MD5(noSph)='".$noSph."';";

			if (!mysql_query( $q2, $dbLink))
				throw new Exception('Gagal hapus data SPH.');

			@mysql_query("COMMIT", $dbLink);
			$this->strResults=$q2."Sukses Hapus Data SPH ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Hapus Data SPH - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
		
	}

}
?>
