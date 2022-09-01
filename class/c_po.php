<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_po
{
	var $strResults="";
	
	function validate(&$params) 
	{
		$temp=TRUE;

		if($params["txtnopo"]=='' )
		{
			$this->strResults.="id belum terakumulasi!<br/>";
			$temp=FALSE;
		}       
		return $temp;
	}

	function addpo(&$params) 
	{
		global $dbLink;
		require_once './function/fungsi_formatdate.php';
		require_once( './config.php' );
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Add PO - ".$this->strResults;
			return $this->strResults;
		}
		$tgl = date("Y-m-d");
		$nopo = secureParam($params["txtnopo"],$dbLink);
		$totalh = secureParam($params["txttotalh"],$dbLink);
		$totalh = preg_replace("/\D/", "", $totalh);
		$supp = secureParam($params["idsupp"],$dbLink);
		$tgl = secureParam($params["txttglpo"],$dbLink);
		$cust = secureParam($params["txtcust"],$dbLink);
		$ket = secureParam($params["txtket"],$dbLink);
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
			$qq = "INSERT INTO `aki_po`(`nopo`, `totalharga`, `id_supplier`, `tgl_po`, `tgl_beli`, `ket`, `pengirim`, `cust`, `kodeUser`) VALUES ";
			$qq.= "('".$nopo."','".$totalh."','".$supp."','".$tgl."','','".$ket."','-','".$cust."','".$pembuat."');";
			if (!mysql_query( $qq, $dbLink))
				throw new Exception('Gagal Add Po.');
			$jumData = $params["jumAddPo"];
			for ($j = 0; $j <= $jumData ; $j++){
				if (!empty($params['chkAddJurnal_'.$j])){

                    $idb = secureParam($params["txtkodeb_" . $j], $dbLink);
                    $qty = secureParam($params["txtqty_" . $j], $dbLink);
                    $satuan = secureParam($params["txtSatuan_" . $j], $dbLink);
                    $harga = secureParam($params["txtHarga_" . $j], $dbLink);
					$jbarang = secureParam($params["txtjbrg_" . $j],$dbLink);
                    $total = secureParam($params["txtTotal_" . $j], $dbLink);
                    $h = preg_replace("/\D/", "", $harga);
                    $t = preg_replace("/\D/", "", $total);
                    $q2 = "INSERT INTO `aki_dpo`(`nopo`, `jbarang`,`kode_barang`, `qty`,`satuan`,  `harga`, `subtotal`) ";
					$q2.= "VALUES ('".$nopo."','".$jbarang."','".$idb."','".$qty."', '".$satuan."', '".$h."', '".$t."');";
					if (!mysql_query( $q2, $dbLink))
						throw new Exception('dpo.'.mysql_error());
					@mysql_query("COMMIT", $dbLink);
					$this->strResults="Sukses Add dpo";
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

	function editpo(&$params) 
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
		$nopo = secureParam($params["txtnopo"],$dbLink);
		$totalh = secureParam($params["txttotalh"],$dbLink);
		$totalh = preg_replace("/\D/", "", $totalh);
		$supp = secureParam($params["idsupp"],$dbLink);
		$tgl = secureParam($params["txttglpo"],$dbLink);
		$cust = secureParam($params["txtcust"],$dbLink);
		$ket = secureParam($params["txtket"],$dbLink);
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
			$q3 = "UPDATE `aki_po` SET `id_supplier`='".$supp."',`totalharga`='".$totalh."',`tgl_po`='".$tgl."',`ket`='".$ket."',`acc_op`='0',`acc_fa`='0',`cust`='".$cust;
			$q3.= "' WHERE nopo='".$nopo."'";
			if (!mysql_query( $q3, $dbLink))
				throw new Exception($q3.'Gagal Edit PO. ');
			$jumData = $params["jumAddPo"];
			for ($j = 0; $j <= $jumData ; $j++){
				$q3 = "DELETE FROM `aki_dpo` WHERE nopo='".$nopo."'";
				if (!mysql_query( $q3, $dbLink))
					throw new Exception('Gagal edit data po.');
			}
			for ($j = 0; $j <= $jumData ; $j++){
				if (!empty($params['chkAddJurnal_'.$j])){
                    $idb = secureParam($params["txtkodeb_" . $j], $dbLink);
                    $qty = secureParam($params["txtqty_" . $j], $dbLink);
                    $satuan = secureParam($params["txtSatuan_" . $j], $dbLink);
                    $jbarang = secureParam($params["txtjbrg_" . $j],$dbLink);
                    $harga = secureParam($params["txtHarga_" . $j], $dbLink);
                    $total = secureParam($params["txtTotal_" . $j], $dbLink);
                    $h = preg_replace("/\D/", "", $harga);
                    $t = preg_replace("/\D/", "", $total);
                    $q2 = "INSERT INTO `aki_dpo`(`nopo`,`jbarang`, `kode_barang`, `qty`,`satuan`, `harga`, `subtotal`) ";
					$q2.= "VALUES ('".$nopo."','".$jbarang."','".$idb."','".$qty."', '".$satuan."', '".$h."', '".$t."');";
					if (!mysql_query( $q2, $dbLink))
						throw new Exception('dpo.'.mysql_error());
				}
			}
			$ket =" -Update to PO no=".$nopo.", datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembuat."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
				throw new Exception($q4.'Gagal Edit Project2. ');
			
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
	function delete($nopo)
	{
		global $dbLink;

		$nopo  = secureParam($nopo,$dbLink);
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
			$ket = "`npo`=".$nopo." -has delete, datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembatal."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
						throw new Exception($q4.'Gagal hapus data nopo. ');

			$q = "UPDATE `aki_po` SET `aktif`='1' WHERE md5(nopo)='".$nopo."'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal hapus data nopo.');
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
	function accop($nopo)
	{
		global $dbLink;

		$nopo  = secureParam($nopo,$dbLink);
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
			$ket = "`npo`=".$nopo." -has acc by Head OP , datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembatal."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
						throw new Exception($q4.'Gagal Acc data nopo. ');

			$q = "UPDATE `aki_po` SET `acc_op`='1' WHERE md5(nopo)='".$nopo."'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal Acc data nopo.');
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Acc Data PO ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Acc Data KK - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
		
	}
	function cancelop($nopo)
	{
		global $dbLink;

		$nopo  = secureParam($nopo,$dbLink);
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
			$ket = "`npo`=".$nopo." -has cancel by Head OP , datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembatal."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
						throw new Exception($q4.'Gagal Acc data nopo. ');

			$q = "UPDATE `aki_po` SET `acc_op`='0' WHERE md5(nopo)='".$nopo."'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal Acc data nopo.');
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Acc Data PO ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Acc Data KK - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
		
	}
	function accfa($nopo)
	{
		global $dbLink;

		$nopo  = secureParam($nopo,$dbLink);
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
			$ket = "`npo`=".$nopo." -has acc by Head FA , datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembatal."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
						throw new Exception($q4.'Gagal Acc data nopo. ');

			$q = "UPDATE `aki_po` SET `acc_fa`='1' WHERE md5(nopo)='".$nopo."'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal Acc data nopo.');
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Acc Data PO ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Acc Data KK - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
		
	}
	function cancelfa($nopo)
	{
		global $dbLink;

		$nopo  = secureParam($nopo,$dbLink);
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
			$ket = "`npo`=".$nopo." -has cancel by Head FA , datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembatal."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
						throw new Exception($q4.'Gagal Acc data nopo. ');

			$q = "UPDATE `aki_po` SET `acc_fa`='0' WHERE md5(nopo)='".$nopo."'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal Acc data nopo.');
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Acc Data PO ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Acc Data KK - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
		
	}
}
?>
