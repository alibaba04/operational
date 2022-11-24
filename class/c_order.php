<?php
/*==================================================
//=======  : Alibaba
====================================================*/
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_order
{
	var $strResults="";
	
	function validate(&$params) 
	{
		$temp=TRUE;

		if($params["txtnobeli"]=='' )
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
			$this->strResults="Gagal Add PO - ".$this->strResults;
			return $this->strResults;
		}
		$tgl = date("Y-m-d");
		$nobeli = secureParam($params["txtnobeli"],$dbLink);
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
			$qq = "INSERT INTO `aki_beli`( `nobeli`,`nopo`, `tgl_beli`, `id_supplier`, `ket`, `totalharga`,`cust`, `kodeUser`) VALUES ";
			$qq.= "('".$nobeli."','".$nopo."','".$tgl."','".$supp."','".$ket."','".$totalh."','".$cust."','".$pembuat."');";
			if (!mysql_query( $qq, $dbLink))
				throw new Exception($qq.'Gagal Add Order.');
			$qu= "UPDATE `aki_po` SET `r_order`=`r_order`+1 WHERE nopo='".$nopo."'";
			if (!mysql_query( $qu, $dbLink))
				throw new Exception($qu.'Gagal Add Order.');
			$jumData = $params["jumaddOrder"];
			for ($j = 0; $j <= $jumData ; $j++){
				if (!empty($params['chkAddJurnal_'.$j])){
                    $idb = secureParam($params["txtkodeb_" . $j], $dbLink);
                    $qty = secureParam($params["txtqty_" . $j], $dbLink);
                    $satuan = secureParam($params["txtSatuan_" . $j], $dbLink);
                    $harga = secureParam($params["txtHarga_" . $j], $dbLink);
                    $total = secureParam($params["txtTotal_" . $j], $dbLink);
					$jbarang = secureParam($params["txtjbrg_" . $j],$dbLink);
					$qtym = secureParam($params["txtqtym_" . $j],$dbLink);
                    $h = preg_replace("/\D/", "", $harga);
                    $t = preg_replace("/\D/", "", $total);
                    $q2 = "INSERT INTO `aki_dbeli`(`nobeli`, `jbarang`,`kode_barang`, `qty`, `satuan`, `harga`, `subtotal`) ";
					$q2.= "VALUES ('".$nobeli."','".$jbarang."','".$idb."','".$qtym."', '".$satuan."', '".$h."', '".$t."');";
					if (!mysql_query( $q2, $dbLink))
						throw new Exception('dbeli.'.mysql_error());

					$q3 = "UPDATE `aki_dpo` SET `qtymasuk`=`qtymasuk`+".$qtym." WHERE nopo='".$nopo."'";
					if (!mysql_query( $q3, $dbLink))
						throw new Exception('dpo.'.mysql_error());
					@mysql_query("COMMIT", $dbLink);
					$this->strResults="Sukses Add dbeli";
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
		require_once( './config.php' );
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Edit Oder - ".$this->strResults;
			return $this->strResults;
		}
		$tgl = date("Y-m-d");
		$nobeli = secureParam($params["txtnobeli"],$dbLink);
		$nobeli = secureParam($params["txtnobeli"],$dbLink);
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
			$qq = "UPDATE `aki_beli` SET `nobeli`='".$nobeli."',`tgl_beli`='".$tgl."',`id_supplier`='".$supp."',`ket`='".$ket."',`totalharga`='".$totalh."',`cust`='".$cust;
			$qq.= "' WHERE nobeli='".$nobeli."'";
			if (!mysql_query( $qq, $dbLink))
				throw new Exception($qq.'Gagal Edit Order.');
			$jumData = $params["jumaddOrder"];
			for ($j = 0; $j <= $jumData ; $j++){
				$q3 = "DELETE FROM `aki_dbeli` WHERE nobeli='".$nobeli."'";
				if (!mysql_query( $q3, $dbLink))
					throw new Exception('Gagal edit data order.');
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
                    $qtym = secureParam($params["txtqtym_" . $j],$dbLink);
                    $qtypakai = secureParam($params["txtqtypakai_" . $j],$dbLink);
                    $q3 = "UPDATE `aki_dpo` SET `qtymasuk`='0' WHERE nopo='".$nopo."'";
					if (!mysql_query( $q3, $dbLink))
						throw new Exception('dpo.'.mysql_error());
                    $q2 = "INSERT INTO `aki_dbeli`(`nobeli`, `jbarang`,`kode_barang`, `qty`, `satuan`, `harga`, `subtotal`) ";
					$q2.= "VALUES ('".$nobeli."','".$jbarang."','".$idb."','".$qtym."', '".$satuan."', '".$h."', '".$t."');";
					if (!mysql_query( $q2, $dbLink))
						throw new Exception('dbeli.'.mysql_error());
					$q3 = "UPDATE `aki_dpo` SET `qtymasuk`=".($qtypakai+$qtym)." WHERE nopo='".$nopo."'";
					if (!mysql_query( $q3, $dbLink))
						throw new Exception('dpo.'.mysql_error());
					@mysql_query("COMMIT", $dbLink);
					$this->strResults="Sukses Edit dbeli";
				}
			}
			$this->strResults="Sukses Edit";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Edit Project - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}
		return $this->strResults;
	}

	function delete($nobeli)
	{
		global $dbLink;

		$nobeli  = secureParam($nobeli,$dbLink);
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
			$ket = "`nbeli`=".$nobeli." -has delete, datetime: ".$tgl;
			$q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
			$q4.= "('".$pembatal."','".$tgl."','".$ket."');";
			if (!mysql_query( $q4, $dbLink))
						throw new Exception($q4.'Gagal ubah data KK. ');

			$q = "UPDATE `aki_beli` SET `aktif`='1' WHERE md5(nobeli)='".$nobeli."'";
			if (!mysql_query( $q, $dbLink))
				throw new Exception('Gagal hapus data nobeli.');
			@mysql_query("COMMIT", $dbLink);
			$this->strResults=$q."Sukses Hapus Data Order ";
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
