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
			$this->strResults.="id brg belum terakumulasi!<br/>";
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
		$jenis = secureParam($params["txtbjenis"],$dbLink);
		$rack = secureParam($params["txtrack"],$dbLink);
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
			$qq = "INSERT INTO `aki_barang`(`kode`, `nama`, `satuan`,  `jenis`, `lokasi`, `rack`, `astok`, `kodeUser`) VALUES ";
			$qq.= "('".$nokodeb."','".$name."','".$sat."','".$jenis."','".$lok."','".$rack."','".$stok."','".$pembuat."');";
			if (!mysql_query( $qq, $dbLink))
				throw new Exception($qq.'Gagal Add Barang.');
			$this->strResults="Sukses Add";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Add brg - ".$e->getMessage().'<br/>';
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
		$jenis = secureParam($params["txtbjenis"],$dbLink);
		$rack = secureParam($params["txtrack"],$dbLink);
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
			$qq = "UPDATE `aki_barang` SET `kode`='".$nokodeb."',`nama`='".$name."',`satuan`='".$sat."',`jenis`='".$jenis."',`lokasi`='".$lok."',`rack`='".$rack."',`astok`='".$stok."'";
			$qq.= " WHERE kode='".$nokodeb."'";
			if (!mysql_query( $qq, $dbLink))
				throw new Exception($qq.'Gagal edit Barang.');
			$this->strResults="Sukses edit";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Edit Project - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink2);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink2);
			  return $this->strResults;
		}
		return $this->strResults;
	}
}
?>
