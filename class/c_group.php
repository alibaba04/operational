<?php
/* ==================================================
  //=======  : Alibaba
  ==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_group
{

    public function add(&$params)
    {
        global $dbLink;
        $kodeGroup = secureParam($params["txtKodeGroup"],$dbLink);
        $jumRow = $params["txtRow"];
        for ($j = 0; $j < $jumRow ; $j++){
            if (isset($params['chk_'.$j])){
                $kodeMenu = secureParam($params["chk_". $j],$dbLink);
                $level = secureParam($params["selLevel_". $j],$dbLink);
                $q2 = "INSERT INTO `aki_groupprivilege`( `kodeGroup`, `kodeMenu`, `level`) ";
                $q2.= "VALUES ('".$kodeGroup."','".$kodeMenu."','".$level."');";
                if (!mysql_query( $q2, $dbLink))
                    throw new Exception($q.'Gagal tambah data SPH.');
                @mysql_query("COMMIT", $dbLink);
                $this->strResults=$q."Sukses Tambah Data SPH";
            }
        }
    }
}
?>
