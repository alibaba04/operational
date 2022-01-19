<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_profil
{
	var $strResults="";
	
	function validate(&$params) 
	{
		$temp=TRUE;
        
        if($params["txtNamaYayasan"]=='' )
		{
			$this->strResults.="Nama Yayasan harus diisi!<br/>";
			$temp=FALSE;
		}
		if($params["txtGedung"]=='' )
		{
			$this->strResults.="Alamat 1 harus diisi!<br/>";
			$temp=FALSE;
		}
        if($params["txtJalan"]=='' )
		{
			$this->strResults.="Alamat 2 harus diisi!<br/>";
			$temp=FALSE;
		}
		if($params["txtKelurahan"]=='' )
		{
			$this->strResults.="Kelurahan harus diisi!<br/>";
			$temp=FALSE;
		}
		if($params["txtPropinsi"]=='' )
		{
			$this->strResults.="Propinsi harus diisi!<br/>";
			$temp=FALSE;
		}
		if($params["txtNegara"]=='' )
		{
			$this->strResults.="Negara harus diisi!<br/>";
			$temp=FALSE;
		}
		if($params["txtTelepon"]=='' )
		{
			$this->strResults.="Nomor Telepon harus diisi!<br/>";
			$temp=FALSE;
		}
		if($params["txtFax"]=='' )
		{
			$this->strResults.="Nomor Fax harus diisi!<br/>";
			$temp=FALSE;
		}
		if($params["txtEmail"]=='' )
		{
			$this->strResults.="Email harus diisi!<br/>";
			$temp=FALSE;
		}
		if($params["txtWebsite"]=='' )
		{
			$this->strResults.="Website harus diisi!<br/>";
			$temp=FALSE;
		}

		return $temp;
	}
        
    /*function validateUpload(&$params) 
	{
		$temp=TRUE;
                
                if($params["thnAkademik"]=='' )
		{
			$this->strResults.="Tahun Akademik harus diisi!<br/>";
			$temp=FALSE;
		}
		if(empty($_FILES['dataSiswa']['tmp_name']))
		{
			$this->strResults.="File Data Siswa harus diisi!<br/>";
			$temp=FALSE;
		}
                
		return $temp;
	}*/
	
	function validateDelete($kode) 
	{
		global $dbLink;
		$temp=FALSE;
		if(empty($kode))
		{
			$this->strResults.="Kode tidak ditemukan!<br/>";
			$temp=FALSE;
		}

		//cari kode rekening di tabel master
		$rsTemp=mysql_query("SELECT kode_rekening FROM aki_tabel_master WHERE md5(kode_rekening) = '".$kode."'", $dbLink);
        $rows = mysql_num_rows($rsTemp);
        if($rows==0)
		{
			$temp=TRUE;
		} 
		else
        {
        	$this->strResults.="Data Siswa masih terpakai dalam Salah satu tabel SMS Gateway ini!<br />";
            $temp=FALSE;
        }
		
		return $temp;
	}
	
	// function add(&$params) 
	// {
	// 	global $dbLink;
		
	// 	//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
	// 	if(!$this->validate($params))
	// 	{	//Pesan error harus diawali kata "Gagal"
	// 		$this->strResults="Gagal Tambah Data Perkiraan - ".$this->strResults;
	// 		return $this->strResults;
	// 	}

	// 	$namaYayasan = secureParam($params["txtNamaYayasan"],$dbLink);
 //        $gedung = secureParam($params["txtGedung"],$dbLink);
 //        $jalan = secureParam($params["txtJalan"],$dbLink);
 //        $kelurahan = secureParam($params["txtKelurahan"],$dbLink);
 //        $propinsi = secureParam($params["txtPropinsi"],$dbLink);
 //        $negara = secureParam($params["txtNegara"],$dbLink);
 //        $telepon = secureParam($params["txtTelepon"],$dbLink);
 //        $fax = secureParam($params["txtFax"],$dbLink);
 //        $email = secureParam($params["txtEmail"],$dbLink);
 //        $website = secureParam($params["txtWebsite"],$dbLink);
        
 //        $pembuat = $_SESSION["my"]->id;

	// 	try
	// 	{
	// 		$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
	// 		$result = @mysql_query('BEGIN', $dbLink);
	// 		if (!$result) {
	// 			throw new Exception('Could not begin transaction');
	// 		}
				
	// 		$result = mysql_query("SELECT kode_rekening FROM tabel_master WHERE kode_rekening='".$kodeRekening."' ");
	// 		if(mysql_num_rows($result))
	// 			throw new Exception('Data Kode Perkiraan yang akan ditambahkan sudah pernah terdaftar dalam database.');
			
	// 		$q = "INSERT INTO tabel_master(kode_rekening, nama_rekening, awal_debet, awal_kredit, normal, posisi) ";
	// 		$q.= "VALUES('".$kodeRekening."',  '".$namaRekening."',  '".$awalDebet."', '".$awalKredit."', '".$normal."',  '".$posisi."');";
			
	// 		if (!mysql_query( $q, $dbLink))
	// 			throw new Exception('Gagal masukkan data dalam database.');
				
	// 		@mysql_query("COMMIT", $dbLink);
	// 		$this->strResults="Sukses Tambah Data Perkiraan ";
	// 	}
	// 	catch(Exception $e) 
	// 	{
	// 		  $this->strResults="Gagal Tambah Data Perkiraan - ".$e->getMessage().'<br/>';
	// 		  $result = @mysql_query('ROLLBACK', $dbLink);
	// 		  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
	// 		  return $this->strResults;
	// 	}
	// 	return $this->strResults;
	// }
	
	function edit(&$params) 
	{
		global $dbLink;
		
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Ubah Data Profil - ".$this->strResults;
			return $this->strResults;
		}
		
		$namaYayasan = secureParam($params["txtNamaYayasan"],$dbLink);
        $gedung = secureParam($params["txtGedung"],$dbLink);
        $jalan = secureParam($params["txtJalan"],$dbLink);
        $kelurahan = secureParam($params["txtKelurahan"],$dbLink);
        $kecamatan = secureParam($params["txtKecamatan"],$dbLink);
        $kota = secureParam($params["txtKota"],$dbLink);
        $propinsi = secureParam($params["txtPropinsi"],$dbLink);
        $negara = secureParam($params["txtNegara"],$dbLink);
        $telepon = secureParam($params["txtTelepon"],$dbLink);
        $fax = secureParam($params["txtFax"],$dbLink);
        $email = secureParam($params["txtEmail"],$dbLink);
        $website = secureParam($params["txtWebsite"],$dbLink);

        $id = secureParam($params["id"],$dbLink);
        
        $pembuat = $_SESSION["my"]->id;
		
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
            $q = "UPDATE aki_tabel_profil SET nama_perusahaan='".$namaYayasan."', gedung='".$gedung."', jalan='".$jalan."', kelurahan='".$kelurahan."', kecamatan='".$kecamatan."', kota='".$kota."', propinsi='".$propinsi."', negara='".$negara."', telepon='".$telepon."', fax='".$fax."', email='".$email."', website='".$website."' ";
                        
			$q.= "WHERE id='".$id."' ";
			
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal mengubah database.');
				
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Ubah Data Profil ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Ubah Data Profil - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}
	
	// function delete($kodePerkiraan)
	// {
	// 	global $dbLink;

	// 	//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
	// 	if(!$this->validateDelete($kodeSiswa))
	// 	{	//Pesan error harus diawali kata "Gagal"
	// 		$this->strResults="Gagal Hapus Data Perkiraan - ".$this->strResults;
	// 		return $this->strResults;
	// 	}
		
 //                $kodeRekening = secureParam($kodePerkiraan,$dbLink);
 //                $pembatal = $_SESSION["my"]->id;
                		
	// 	$q = "DELETE FROM tabel_master ";
	// 	$q.= "WHERE md5(kode_rekening)='".$kodeRekening."';";
                
	// 	if (mysql_query( $q, $dbLink))
	// 	{	
	// 		$this->strResults="Sukses Hapus Data Perkiraan";
	// 	}
	// 	else
	// 	{	//Pesan error harus diawali kata "Gagal"
	// 		$this->strResults="Gagal Hapus Data Perkiraan - ".mysql_error();
	// 	}
	// 	return $this->strResults;
	// }
}
?>
