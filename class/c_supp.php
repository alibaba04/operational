<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_supp
{
	var $strResults="";
	
	function validate(&$params) 
	{
		$temp=TRUE;

		if($params["txtksupp"]=='' )
		{
			$this->strResults.="id supp belum terisi!<br/>";
			$temp=FALSE;
		}       
		return $temp;
	}

	function addsupp(&$params) 
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';
		require_once( './config.php' );
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Add Supp - ".$this->strResults;
			return $this->strResults;
		}
		$tgl = date("Y-m-d");
		$kodesupp = secureParam($params["txtksupp"],$dbLink);
		$supp = secureParam($params["txtsupp"],$dbLink);
		$sjenis = secureParam($params["txtsjenis"],$dbLink);
		$alamat = secureParam($params["txtalamat"],$dbLink);
		$hp = secureParam($params["txtnohp"],$dbLink);
		$nbank = secureParam($params["cbobank"],$dbLink);
		$nameb = secureParam($params["txtnameb"],$dbLink);
		$norek = secureParam($params["txtnorek"],$dbLink);
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
			$qq = "INSERT INTO `aki_supplier`( `kodesupp`,`supplier`,`jenis`, `alamat`, `kontak`, `nomor`, `norek`, `nrek`, `kodeUser`) VALUES ";
			$qq.= "('".$kodesupp."','".$supp."','".$sjenis."','".$alamat."','".$nameb."','".$hp."','".$norek."','".$nbank."','".$pembuat."');";
			if (!mysql_query( $qq, $dbLink))
				throw new Exception('Gagal Add Supp.');
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
		global $dbLink;
		require_once './function/fungsi_formatdate.php';
		require_once( './config.php' );
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal edit Supp - ".$this->strResults;
			return $this->strResults;
		}
		$tgl = date("Y-m-d");
		$kodesupp = secureParam($params["txtksupp"],$dbLink);
		$supp = secureParam($params["txtsupp"],$dbLink);
		$sjenis = secureParam($params["txtsjenis"],$dbLink);
		$alamat = secureParam($params["txtalamat"],$dbLink);
		$hp = secureParam($params["txtnohp"],$dbLink);
		$nbank = secureParam($params["cbobank"],$dbLink);
		$nameb = secureParam($params["txtnameb"],$dbLink);
		$norek = secureParam($params["txtnorek"],$dbLink);
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
			$qq = "UPDATE `aki_supplier` SET `jenis`='".$sjenis."',`supplier`='".$supp."',`alamat`='".$alamat."',`kontak`='".$nameb."',`nomor`='".$hp."',`norek`='".$norek."',`nrek`='".$nbank."'";
			$qq.= " WHERE kodesupp='".$kodesupp."'";
			if (!mysql_query( $qq, $dbLink))
				throw new Exception('Gagal edit Supp.');
			$this->strResults="Sukses edit";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal edit Project - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink2);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink2);
			  return $this->strResults;
		}
		return $this->strResults;
	}
}
?>
