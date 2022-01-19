<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_kk
{
	var $strResults="";
	
	function addkk(&$params,$nameimg){
		global $dbLink;
		require_once './function/fungsi_formatdate.php';
        $tglTransaksi = date("Y-m-d");
        $nokk = secureParam($params["txtnoKk"],$dbLink);
        $noSph = secureParam($params["txtnomersph"],$dbLink);
        $namacust = secureParam($params["txtnamacust"],$dbLink);
        $jenis_id = secureParam($params["cboJenisid"],$dbLink);
        $no_id = secureParam($params["txtNoid"],$dbLink);
        $no_phone = secureParam($params["txtPhone"],$dbLink);
        $jabatan = secureParam($params["txtPosition"],$dbLink);
        $nmasjid = secureParam($params["txtnmasjid"],$dbLink);
        $nproyek = secureParam($params["txtnproyek"],$dbLink);
        $project_pemerintah = secureParam($params["txtppemerintah"],$dbLink);
        $alamat_proyek = secureParam($params["txtalamatp"],$dbLink);
        $mproduksi = secureParam($params["txtproduksi"],$dbLink);
        $mpemasangan = secureParam($params["txtPemasangan"],$dbLink);
        $alamat = secureParam($params["txtalamat"],$dbLink);
        $alamat2 = secureParam($params["provinsi"],$dbLink);
        $treport = secureParam($params["treport"],$dbLink);
        $provinsi = substr($alamat2,0, 2);
        $kota = substr($alamat2,3, 6);
        $pembuat = $_SESSION["my"]->id;
        
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			
			$q = "INSERT INTO aki_kk(`noKk`, `noSph`, `nama_cust`, `jenis_id`, `no_id`, `no_phone`, `jabatan`,`nmasjid`, `nproyek`, `project_pemerintah`, `alamat_proyek`, `mproduksi`, `mpemasangan`, `alamat`, `provinsi`, `kota`, `tanggal`, `kodeUser`, `aktif`) ";
			$q.= "VALUES ('".$nokk."','".$noSph."','".$namacust."','".$jenis_id."','".$no_id."','".$no_phone."','".$jabatan."','".$nmasjid."','".$nproyek."','".$project_pemerintah."','".$alamat_proyek."','".$mproduksi."','".$mpemasangan."','".$alamat."','".$provinsi."','".$kota."','".$tglTransaksi."','".$pembuat."','1');";
			if (!mysql_query($q, $dbLink))
				throw new Exception('Gagal masukkan data dalam database.');
			$w1 = secureParam($params["txtW1"],$dbLink);
			$w2 = secureParam($params["txtW2"], $dbLink);
			$w3 = secureParam($params["txtW3"], $dbLink);
			$w4 = secureParam($params["txtW4"], $dbLink);
			$p1 = secureParam($params["txtP1"], $dbLink);
			$p2 = secureParam($params["txtP2"],$dbLink);
			$p3 = secureParam($params["txtP3"], $dbLink);
			$p4 = secureParam($params["txtP4"], $dbLink);

			$q3 = "INSERT INTO `aki_dpembayaran`(	`noKk`, `wpembayaran1`, `wpembayaran2`, `wpembayaran3`, `wpembayaran4`, `persen1`, `persen2`, `persen3`, `persen4`) VALUES ";
			$q3.= " ('".$nokk."','".$w1."','".$w2."','".$w3."','".$w4."','".$p1."','".$p2."','".$p3."','".$p4."');";
			if (!mysql_query( $q3, $dbLink))
				throw new Exception('Gagal tambah data KK.');

			$jumData = $params["jumAddJurnal"];
			$nomer=0;
			$files = $_FILES;
			for ($j = 0; $j < $jumData ; $j++){
				if (($params['chkAddJurnal_'.$j])!=0){
					$color1 = secureParam($params["color1_". $j],$dbLink);
					$color2 = secureParam($params["color2_". $j], $dbLink);
					$color3 = secureParam($params["color3_". $j ], $dbLink);
					$color4 = secureParam($params["color4_". $j ], $dbLink);
					$color5 = secureParam($params["color5_". $j ], $dbLink);
					$kcolor1 = secureParam($params["kcolor1_". $j],$dbLink);
					$kcolor2 = secureParam($params["kcolor2_". $j], $dbLink);
					$kcolor3 = secureParam($params["kcolor3_". $j ], $dbLink);
					$kcolor4 = secureParam($params["kcolor4_". $j ], $dbLink);
					$kcolor5 = secureParam($params["kcolor5_". $j ], $dbLink);

					$q3 = "INSERT INTO `aki_kkcolor`(`noKk`, `nomer`, `color1`, `color2`, `color3`, `color4`, `color5`, `kcolor1`, `kcolor2`, `kcolor3`, `kcolor4`, `kcolor5`) VALUES ";
					$q3.= " ('".$nokk."','".$nomer."','".$color1."','".$color2."','".$color3."','".$color4."','".$color5."','".$kcolor1."','".$kcolor2."','".$kcolor3."','".$color4."','".$kcolor5."');";
					if (!mysql_query( $q3, $dbLink))
						throw new Exception('Gagal tambah data KK.');

					$model = secureParam($params["txtModel_". $j],$dbLink);
					$jkubah = secureParam($params["txtKubah_". $j],$dbLink);
					$diameter = secureParam($params["txtD_". $j],$dbLink);
                    $tinggi = secureParam($params["txtT_". $j],$dbLink);
                    $dtengah = secureParam($params["txtDt_". $j],$dbLink);
                    $luas = '';
                    if ($dtengah == 0) {
                    	$luas = ($diameter * $tinggi * 3.14);
                    }else{
                    	$luas = ($dtengah * $tinggi * 3.14);
                    }
                    $plafon = secureParam($params["txtPlafon_". $j],$dbLink);
                    $harga1 = secureParam($params["txtHarga_" . $j], $dbLink);
                    $h = preg_replace("/\D/", "", $harga1);
                    $qty = secureParam($params["txtQty_" . $j], $dbLink);
                    $bahan = secureParam($params["txtBahan_" . $j], $dbLink);
                    $transport = secureParam($params["txttransport"], $dbLink);
                    $kaligrafi = secureParam($params["txtKaligrafi_" . $j], $dbLink);
                    $hppn = secureParam($params["txtHargappn_" . $j], $dbLink);
                    
                    $q2 = "INSERT INTO aki_dkk(`nomer`, `noKK`, `model`, `kubah`, `d`, `t`, `dt`, `luas`, `plafon`, `kaligrafi`, `harga`, `jumlah`, `bahan`,`ppn`,`hppn`, `transport`,`filekubah`, `filekaligrafi`) ";
					$q2.= "VALUES ('".$nomer."','".$nokk."','".$model."', '".$jkubah."', '".$diameter."', '".$tinggi."', '".$dtengah."','".$luas."', '".$plafon."', '".$kaligrafi."', '".$h."', '".$qty."', '".$bahan."', '".$project_pemerintah."', '".$hppn."', '".$transport."', '".$nameimg[0]."', '".$nameimg[1]."');";

					if (!mysql_query( $q2, $dbLink))
						throw new Exception($q2.'Gagal tambah data KK.');

					$nomer++;
				}
			}
			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d H:i:s");
			$ket = "KK Note, nokk=".$nokk.", note=".$treport.", read by kpenjualan=1";
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembuat."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
						throw new Exception($q4.'Gagal ubah data KK. ');
			$privilegeU='';
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
			$Message = "SIKUBAH - Pesan dari ".$_SESSION["my"]->privilege." Please Check 'New Kontrak Kerja'. Nomor KK : '".$nokk."', Note : '".$treport."' ";
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
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses";
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

		if($params["txtnoKk"]=='' )
		{
			$this->strResults.="Harga belum terakumulasi!<br/>";
			$temp=FALSE;
		}       
		return $temp;
	}

	function edit(&$params,$nameimg) 
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';
		$q='';
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Ubah Data KK - ".$this->strResults;
			return $this->strResults;
		}
		$tglTransaksi = date("Y-m-d");
		$nokk = secureParam($params["txtnoKk"],$dbLink);
        $namacust = secureParam($params["txtnamacust"],$dbLink);
        $jenis_id = secureParam($params["cboJenisid"],$dbLink);
        $no_id = secureParam($params["txtNoid"],$dbLink);
        $no_phone = secureParam($params["txtPhone"],$dbLink);
        $jabatan = secureParam($params["txtPosition"],$dbLink);
        $nmasjid = secureParam($params["txtnmasjid"],$dbLink);
        $nproyek = secureParam($params["txtnproyek"],$dbLink);
        $project_pemerintah = secureParam($params["txtppemerintah"],$dbLink);
        $alamat_proyek = secureParam($params["txtalamatp"],$dbLink);
        $mproduksi = secureParam($params["txtproduksi"],$dbLink);
        $mpemasangan = secureParam($params["txtPemasangan"],$dbLink);
        $alamat = secureParam($params["txtalamat"],$dbLink);
        $alamat2 = secureParam($params["provinsi"],$dbLink);
        $provinsi = substr($alamat2,0, 2);
        $kota = substr($alamat2,3, 6);
        $pembuat = $_SESSION["my"]->id;
		$treport = secureParam($params["treport"],$dbLink);

		$q3='';
		$qimg='';
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			
			//report
			$rsTemp=mysql_query("SELECT s.`nama_cust`,s.`provinsi`,s.`kota`,ds.`model`,ds.`d`,ds.`dt`,ds.`t`,ds.`luas`,ds.`plafon`,ds.`harga`,ds.`harga2`,ds.`harga3`,ds.`jumlah`,ds.`ket`,ds.`transport`,ds.`biaya_plafon`,ds.`bahan` FROM aki_KK s LEFT JOIN aki_dkk ds ON s.`noKk`=ds.`noKk` WHERE s.`noKk` = '".$params["txtnoKk"]."'", $dbLink);
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
			$q3 = "UPDATE aki_kk SET `approve`='1',`approve_by`='".$pembuat."',`approve_tgl`='".$tgl."'  WHERE noKk='".$nokk."'";
				if (!mysql_query( $q3, $dbLink))
					throw new Exception('Gagal ubah data KK. ');
			$q3 = "UPDATE aki_kk SET `nama_cust`='".$namacust."',`jenis_id`='".$jenis_id."',`no_id`='".$no_id."',`no_phone`='".$no_phone."',`jabatan`='".$jabatan."',`nmasjid`='".$nmasjid."',`nproyek`='".$nproyek."',`project_pemerintah`='".$project_pemerintah."',`alamat_proyek`='".$alamat_proyek."',`mproduksi`='".$mproduksi."',`mpemasangan`='".$mpemasangan."',`alamat`='".$alamat."',`provinsi`='".$provinsi."',`kota`='".$kota."',`approve`='0',`approve_by`='-',`approve_tgl`='0000-00-00' WHERE noKk='".$nokk."'";
			if (!mysql_query( $q3, $dbLink))
						throw new Exception('Gagal ubah data KK. ');

			$w1 = secureParam($params["txtW1"],$dbLink);
			$w2 = secureParam($params["txtW2"], $dbLink);
			$w3 = secureParam($params["txtW3"], $dbLink);
			$w4 = secureParam($params["txtW4"], $dbLink);
			$p1 = secureParam($params["txtP1"], $dbLink);
			$p2 = secureParam($params["txtP2"],$dbLink);
			$p3 = secureParam($params["txtP3"], $dbLink);
			$p4 = secureParam($params["txtP4"], $dbLink);

			$q3 = "UPDATE `aki_dpembayaran` SET `noKk`='".$nokk."',`wpembayaran1`='".$w1."',`wpembayaran2`='".$w2."',`wpembayaran3`='".$w3."',`wpembayaran4`='".$w4."',`persen1`='".$p1."',`persen2`='".$p2."',`persen3`='".$p3."',`persen4`='".$p4."'";
			$q3.= " WHERE noKk='".$nokk."'";
			if (!mysql_query( $q3, $dbLink))
				throw new Exception('Gagal update data KK.');

			$jumData = $params["jumAddJurnal"];
			$nomer =0;
			for ($j = 0; $j < $jumData ; $j++){
				if (!empty($params['chkAddJurnal_'.$j])){
					$color1 = secureParam($params["color1_". $j],$dbLink);
					$color2 = secureParam($params["color2_". $j], $dbLink);
					$color3 = secureParam($params["color3_". $j ], $dbLink);
					$color4 = secureParam($params["color4_". $j ], $dbLink);
					$color5 = secureParam($params["color5_". $j ], $dbLink);
					$kcolor1 = secureParam($params["kcolor1_". $j],$dbLink);
					$kcolor2 = secureParam($params["kcolor2_". $j], $dbLink);
					$kcolor3 = secureParam($params["kcolor3_". $j ], $dbLink);
					$kcolor4 = secureParam($params["kcolor4_". $j ], $dbLink);
					$kcolor5 = secureParam($params["kcolor5_". $j ], $dbLink);

					$q3 = "UPDATE `aki_kkcolor` SET `color1`='".$color1."',`color2`='".$color2."',`color3`='".$color3."',`color4`='".$color4."',`color5`='".$color5."',`kcolor1`='".$kcolor1."',`kcolor2`='".$kcolor2."',`kcolor3`='".$kcolor3."',`kcolor4`='".$kcolor4."',`kcolor5`='".$kcolor5."' WHERE noKk='".$nokk."'";
					if (!mysql_query( $q3, $dbLink))
						throw new Exception('Gagal Edit data KK.');

					$idKk = secureParam($params["chkAddJurnal_" . $j], $dbLink);
                    $model = secureParam($params["txtModel_". $j],$dbLink);
					$jkubah = secureParam($params["txtKubah_". $j],$dbLink);
					$diameter = secureParam($params["txtD_". $j],$dbLink);
                    $tinggi = secureParam($params["txtT_". $j],$dbLink);
                    $dtengah = secureParam($params["txtDt_". $j],$dbLink);
                    $luas = 0;
                    $plafon = secureParam($params["txtPlafon_". $j],$dbLink);
                    $harga1 = secureParam($params["txtHarga_" . $j], $dbLink);
                    $h = preg_replace("/\D/", "", $harga1);
                    $qty = secureParam($params["txtQty_" . $j], $dbLink);
                    $bahan = secureParam($params["txtBahan_" . $j], $dbLink);
                    $filekubah = secureParam($params["filekubah_" . $j], $dbLink);
					$kaligrafi = secureParam($params["txtKaligrafi_" . $j], $dbLink);
					$transport = secureParam($params["txttransport"], $dbLink);
					$hppn = secureParam($params["txtHargappn_" . $j], $dbLink);
					if ($dtengah == 0) {
                    	$luas = ($diameter * $tinggi * 3.14);
                    }else{
                    	$luas = ($dtengah * $tinggi * 3.14);
                    }
                    
                    if ($nameimg[0]!=''){
                    	$qimg=",`filekubah`='".$nameimg[0]."',`filekaligrafi`='".$nameimg[1]."'";
                    }

                    $filekaligrafi = secureParam($params["filekaligrafi_" . $j], $dbLink);
                    $q = "UPDATE aki_dkk SET `luas`='".$luas."',`nomer`='".$nomer."',`bahan`='".$bahan."',`kubah`='".$jkubah."',`model`='".$model."',`d`='".$diameter."',`t`='".$tinggi."',`dt`='".$dtengah."',`kaligrafi`='".$kaligrafi."',`plafon`='".$plafon."',`jumlah`='".$qty."',`transport`='".$transport."',`ppn`='".$project_pemerintah."',`hppn`='".$hppn."',`harga`='".$h."'".$qimg;
					$q.= " WHERE idKk='".$idKk."' ;";

					if (!mysql_query( $q, $dbLink))
						throw new Exception($q.'Gagal ubah data KK.');
                    $nomer++;
				}
			}
			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d H:i:s");
			$ket = "`nomer`=".$params["txtnoKk"]."  -has change, ket : ".$tempNamecust.", ".$tempP.", ".$tempK.", ".$tempModel.", ".$tempD.", ".$tempT.", ".$tempDt.", ".$tempTrans.", ".$tempKet.", ".$tempLuas.", ".$tempJumlah.", ".$tempBiaya.", ".$tempHarga.", ".$tempHarga2.", ".$tempPlafon.", ".$tempBahan.", datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembuat."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
						throw new Exception($q4.'Gagal ubah data KK. ');

			$ket = "KK Note, nokk=".$nokk.", note=".$treport.", read by kpenjualan=1";
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembuat."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
				throw new Exception('Gagal ubah data KK. ');
			@mysql_query("COMMIT", $dbLink);
			//API send firebase
			$privilegeU='';
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
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Edit";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Ubah Data KK - ".$e->getMessage().'<br/>';
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
			$this->strResults.="No KK tidak ditemukan!<br/>";
			$temp=FALSE;
		}

		//cari ID inisiasi di tabel penyusunan
		$rsTemp=mysql_query("SELECT * FROM aki_kk WHERE (noKk) = '".$kode."'", $dbLink);
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
			$this->strResults="Gagal Hapus Data KK - ".$this->strResults;
			return $this->strResults;
		}

		$noKk  = secureParam($kode,$dbLink);
        $pembatal = $_SESSION["my"]->id;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			$rsTemp=mysql_query("SELECT s.`nama_cust`,s.`provinsi`,s.`kota`,ds.`model`,ds.`d`,ds.`dt`,ds.`t`,ds.`luas`,ds.`plafon`,ds.`harga`,ds.`jumlah`,ds.`ket`,ds.`bahan` FROM aki_KK s LEFT JOIN aki_dkk ds ON s.`noKk`=ds.`noKk` WHERE s.`noKk` = '".$noKk."'", $dbLink);
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
			if (!mysql_query( $q4, $dbLink))
						throw new Exception($q4.'Gagal ubah data KK. ');

			$q = "DELETE FROM  `aki_kk`";
			$q.= "WHERE (noKk)='".$noKk."';";

			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal hapus data KK.');

			$q2 = "DELETE FROM aki_dpembayaran ";
			$q2.= "WHERE (noKk)='".$noKk."';";
			if (!mysql_query( $q2, $dbLink))
				throw new Exception('Gagal hapus data KK.');

			$q3 = "DELETE FROM aki_dkk ";
			$q3.= "WHERE (noKk)='".$noKk."';";

			if (!mysql_query( $q3, $dbLink))
				throw new Exception('Gagal hapus data KK.');

			$q4 = "DELETE FROM aki_kkcolor ";
			$q4.= "WHERE (noKk)='".$noKk."';";

			if (!mysql_query( $q4, $dbLink))
				throw new Exception('Gagal hapus data KK.');

			$q5 = "DELETE FROM aki_report ";
			$q5.= "WHERE ket like'%".$noKk."%';";

			if (!mysql_query( $q5, $dbLink))
				throw new Exception('Gagal hapus data KK.');

			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Hapus Data KK ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Hapus Data KK - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
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
