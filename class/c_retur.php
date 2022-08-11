<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_retur
{
	var $strResults="";
	
	function validate(&$params) 
	{
		$temp=TRUE;

		if($params["txtnoretur"]=='' )
		{
			$this->strResults.="id belum terakumulasi!<br/>";
			$temp=FALSE;
		}       
		return $temp;
	}

	function add(&$params) 
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';
		require_once( './config.php' );
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Add retur - ".$this->strResults;
			return $this->strResults;
		}
		$tgl = date("Y-m-d");
		$noretur = secureParam($params["txtnoretur"],$dbLink);
		$tgl = secureParam($params["txttglpo"],$dbLink);
		$cust = secureParam($params["txtcust"],$dbLink);
		$ket = secureParam($params["txtket"],$dbLink);
		$kodeproyek = secureParam($params["txtproyek"],$dbLink);
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
			$qq = "INSERT INTO `aki_bretur`( `nobretur`, `tgl_bretur`, `ket`, `kodeproyek`, `cust`, `kodeUser`, `aktif`) VALUES ";
			$qq.= "('".$noretur."','".$tgl."','".$ket."','".$kodeproyek."','".$cust."','".$pembuat."','0');";
			if (!mysql_query( $qq, $dbLink))
				throw new Exception($qq.'Gagal Add retur.');
			$jumData = $params["jumAddPo"];
			for ($j = 0; $j <= $jumData ; $j++){
				if (!empty($params['chkAddJurnal_'.$j])){
                    $idb = secureParam($params["txtkodeb_" . $j], $dbLink);
                    $qty = secureParam($params["txtqty_" . $j], $dbLink);
                    $satuan = secureParam($params["txtSatuan_" . $j], $dbLink);
                    $q2 = "INSERT INTO `aki_dbretur`(`nobretur`, `kode_barang`, `qty`, `satuan`)";
					$q2.= "VALUES ('".$noretur."','".$idb."','".$qty."', '".$satuan."');";
					if (!mysql_query( $q2, $dbLink))
						throw new Exception('dretur.'.mysql_error());
					@mysql_query("COMMIT", $dbLink);
					$this->strResults="Sukses Add dretur";
				}
			}
			$this->strResults="Sukses Add";
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

	function edit(&$params) 
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Edit PO - ".$this->strResults;
			return $this->strResults;
		}
		$tgl = date("Y-m-d");
		$noretur = secureParam($params["txtnoretur"],$dbLink);
		$tgl = secureParam($params["txttglpo"],$dbLink);
		$cust = secureParam($params["txtcust"],$dbLink);
		$ket = secureParam($params["txtket"],$dbLink);
		$kodeproyek = secureParam($params["txtkodeproyek"],$dbLink);
        $pembuat = $_SESSION["my"]->id;
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d H:i:s");
			$idb = secureParam($params["txtkodeb_" . $j], $dbLink);
			$qty = secureParam($params["txtqty_" . $j], $dbLink);
			$satuan = secureParam($params["txtSatuan_" . $j], $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			$q3 = "UPDATE `aki_bretur` SET `nobretur`='".$noretur."',`tgl_bretur`='".$tgl."',`kodeproyek`='".$kodeproyek."',`cust`='".$cust."',`ket`='".$ket."'";
			$q3.= " WHERE nobretur='".$noretur."'";
			if (!mysql_query( $q3, $dbLink))
				throw new Exception($q3.'Gagal Edit retur. ');
			$jumData = $params["jumretur"];
			for ($j = 0; $j <= $jumData ; $j++){
				$q3 = "DELETE FROM `aki_dbretur` WHERE nobretur='".$noretur."'";
				if (!mysql_query( $q3, $dbLink))
					throw new Exception('Gagal edit data retur.');
			}
			for ($j = 0; $j <= $jumData ; $j++){
				if (!empty($params['chkAddJurnal_'.$j])){
                    $idb = secureParam($params["txtkodeb_" . $j], $dbLink);
                    $qty = secureParam($params["txtqty_" . $j], $dbLink);
                    $satuan = secureParam($params["txtSatuan_" . $j], $dbLink);
                    $q2 = "INSERT INTO `aki_dbretur`(`nobretur`, `kode_barang`, `qty`, `satuan`)";
					$q2.= "VALUES ('".$noretur."','".$idb."','".$qty."', '".$satuan."');";
					if (!mysql_query( $q2, $dbLink))
						throw new Exception('dretur.'.mysql_error());
					@mysql_query("COMMIT", $dbLink);
					$this->strResults="Sukses Add dretur";
				}
			}
			$ket =" -Update to retur nobretur=".$noretur.", datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembuat."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
				throw new Exception($q4.'Gagal Edit retur. ');
			$this->strResults="Sukses Edit";
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
	function delete($noretur)
	{
		global $dbLink;

		$noretur  = secureParam($noretur,$dbLink);
        $pembatal = $_SESSION["my"]->id;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			
			date_default_timezone_set("Asia/Jakarta");
			$tgl = date("Y-m-d h:i:sa");
			$ket = "`npo`=".$noretur." -has delete, datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembatal."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
						throw new Exception($q4.'Gagal hapus data noretur. ');

			$q = "UPDATE `aki_po` SET `aktif`='1' WHERE md5(noretur)='".$noretur."'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal hapus data noretur.');
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Hapus Data PO ";
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
}
?>
