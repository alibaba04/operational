<?php
global $passSalt;
require_once('../config.php' );
require_once('../config2.php' );
require_once('../function/secureParam.php');

switch ($_POST['fungsi']) {
case "checkKodeMenu":

    $result = mysql_query("select kodeMenu FROM aki_menu WHERE kodeMenu ='" . secureParamAjax($_POST['kodeMenu'], $dbLink) . "'", $dbLink);

    if (mysql_num_rows($result)) {
       echo "yes";
    } else {
       echo "no";
    }
    break;
    case "checkKodeGroup":
    $result = mysql_query("select KodeGroup FROM aki_groups WHERE KodeGroup ='" . secureParamAjax($_POST['kodeGroup'], $dbLink) . "'", $dbLink);
    if (mysql_num_rows($result)) {
        echo "yes";
    } else {
        echo "no";
    }
break;
case "sendnotif":
    $url ="https://fcm.googleapis.com/fcm/send";
    $fields=array(
        "to"=>$_POST['token'],
        "notification"=>array(
            "body"=>$_POST['message'],
            "title"=>'Sikubah',
            "click_action"=>$_POST['url']
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
break;
case "gettoken":
    $result = mysql_query("SELECT s.*,g.kodeGroup FROM `aki_user` s left join aki_usergroup g on s.kodeUser=g.kodeUser where s.aktif='Y' and g.kodeGroup='".$_POST['user']."' limit 1", $dbLink);
    if (mysql_num_rows($result)>0) {
        while ( $data = mysql_fetch_assoc($result)) {
            echo json_encode(array("token"=>$data['token'],"nama"=>$data['nama']));
        } 
        break;
    } 
case "getkodesupp":
    $result = mysql_query("SELECT max(kodesupp) as kodesupp FROM `aki_supplier`", $dbLink);
    if (mysql_num_rows($result)>0) {
        while ( $data = mysql_fetch_assoc($result)) {
            $id = explode("supp",$data["kodesupp"]);
            $id = (int)$id[1]+1;
            $id = str_pad($id, 4, '0', STR_PAD_LEFT);
        } 
        echo json_encode($id);
        break;
    } 
case "checknkode":
    $kode = $_POST['nkode'];
    $result = mysql_query("SELECT * FROM `aki_barang` WHERE kode='AWD'", $dbLink);
    if (mysql_num_rows($result)>0) {
        echo "yes";
    } else {
        echo "no";
    }
break;
case "ambilkodeb":
    $result = mysql_query("SELECT * FROM `aki_barang`", $dbLink);
    if (mysql_num_rows($result)>0) {
        $idx = 0;
        while ( $data = mysql_fetch_assoc($result)) {
            $output[$idx] = array("val"=>$data['kode'],"text"=>$data['kode'].' - '.$data['nama']);
            $idx++;
        } 
        echo json_encode($output);
        break;
    }
break;
case "getsatuan":
    $kode = $_POST['kode'];
    $result = mysql_query("SELECT b.*,masuk,keluar,retur,so FROM `aki_barang` b left join (SELECT kode_barang,sum(db.qty) as masuk FROM aki_dbeli as db group by db.kode_barang) as db on b.kode=db.kode_barang left join (SELECT kode_barang,sum(dk.qty) as keluar FROM aki_dbkeluar as dk group by dk.kode_barang) as dk on b.kode=dk.kode_barang left join (SELECT kode_barang,sum(dr.qty) as retur FROM aki_dbretur as dr group by dr.kode_barang) as dr on b.kode=dr.kode_barang left join (SELECT kode_barang,sum(dso.qty) as so FROM aki_dbso as dso group by dso.kode_barang) as dso on b.kode=dso.kode_barang WHERE kode='".$kode."' group by b.kode ORDER BY `dk`.`keluar` DESC", $dbLink);
    if (mysql_num_rows($result)>0) {
        $idx = 0;
        while ( $data = mysql_fetch_assoc($result)) {
            $stok = $data['astok']+$data['masuk']-$data['keluar']+$data['retur']+($data['so']);
            echo json_encode(array("satuan"=>$data['satuan'],"stok"=>$stok));
        } 
        break;
    }
break;
case "checkKodeUser":
    $result = mysql_query("select kodeUser FROM aki_user WHERE kodeUser ='" . secureParamAjax($_POST['kodeUser'], $dbLink) . "'", $dbLink);
    if (mysql_num_rows($result)) {
        echo "yes";
    } else {
        echo "no";
    }
break;
case "editbrg":
    $kode = $_POST['kode'];
    $result = mysql_query("SELECT * FROM `aki_barang` WHERE kode='".$kode."'", $dbLink);
    if (mysql_num_rows($result)>0) {
        while ( $data = mysql_fetch_assoc($result)) {
            echo json_encode(array("kode"=>$data['kode'],"nama"=>$data['nama'],"satuan"=>$data['satuan'],"lokasi"=>$data['lokasi'],"rack"=>$data['rack'],"jenis"=>$data['jenis']));
        } 
        break;
    } 
break;
case "editsupp":
    $kode = $_POST['kode'];
    $result = mysql_query("SELECT * FROM `aki_supplier` WHERE kodesupp='".$kode."'", $dbLink);
    if (mysql_num_rows($result)>0) {
        while ( $data = mysql_fetch_assoc($result)) {
            echo json_encode(array("kodesupp"=>$data['kodesupp'],"supplier"=>$data['supplier'],"alamat"=>$data['alamat'],"kontak"=>$data['kontak'],"jenis"=>$data['jenis'],"nomor"=>$data['nomor'],"norek"=>$data['norek'],"nrek"=>$data['nrek']));
        } 
        break;
    } 
break;
case "hitungtotal":
    $kaligrafi = $_POST['kaligrafi'];
    $hkubah = $_POST['hkubah'];
    $total = $kaligrafi+$hkubah;
    echo json_encode(array("total"=>number_format($total)));
break;
case "cekpass":
    $kodeUser = secureParamAjax($_POST['kodeUser'], $dbLink);
    $pass = HASH('SHA512',$passSalt.secureParamAjax($_POST['pass'], $dbLink));
    $result = mysql_query("SELECT kodeUser, nama FROM aki_user WHERE kodeUser='".$kodeUser."' AND  password='".$pass."' AND aktif='Y'", $dbLink);
    if (mysql_num_rows($result)) {
        echo "yes";
    } else {
        echo "no";
    }
break;
case "checkNamaSetting":
    $result = mysql_query("select namaSetting FROM aki_setting WHERE namaSetting ='" . secureParamAjax($_POST['namaSetting'], $dbLink) . "'", $dbLink);

    if (mysql_num_rows($result)) {
       echo "yes";
    } else {
       echo "no";
    }
    break;
case "ambilnoproyek":
    $result = mysql_query("SELECT * FROM `aki_proyek` WHERE 1", $dbLink);
    if (mysql_num_rows($result)>0) {
        $idx = 0;
        while ( $data = mysql_fetch_assoc($result)) {

            $output[$idx] = array("no"=>$data['noproyek']);
            $idx++;
        } 
        echo json_encode($output);
        break;
    }
break;
case "ambilKota":
    $result = mysql_query("SELECT * FROM `kota` WHERE 1", $dbLink);
    if (mysql_num_rows($result)>0) {
        $idx = 0;
        while ( $data = mysql_fetch_assoc($result)) {

            $output[$idx] = array($data['name']);
            $idx++;
        } 
        echo json_encode($output);
        break;
    }
break;
case "ambilProv":
    $result = mysql_query("SELECT * FROM `provinsi` WHERE 1", $dbLink);
    if (mysql_num_rows($result)>0) {
        $idx = 0;
        while ( $data = mysql_fetch_assoc($result)) {

            $output[$idx] = array($data['name']);
            $idx++;
        } 
        echo json_encode($output);
        break;
    } 
break;
case "cek":
    $d = $_POST['d'];
    $t = $_POST['t'];
    $dt = $_POST['dt'];
    $kel = $_POST['kel'];
    $transport = $_POST['ongkir'];
    $luas = 0;
    if ($dt == 0) {
        $luas = ($d * $t * 3.14);
    }else{
        $luas = ($dt * $t * 3.14);
    }
    echo json_encode($luas);
break;
case "chart":
    $result = mysql_query("SELECT count(idSph) as id,YEAR(tanggal) as tahun, MONTH(tanggal) as bulan FROM `aki_sph`GROUP BY YEAR(tanggal), MONTH(tanggal)", $dbLink);
    if (mysql_num_rows($result)>0) {
        $idx = 0;
        while ( $data = mysql_fetch_assoc($result)) {

            $output[$idx] = array($data['id']);
            $idx++;
        } 
        echo json_encode($output);
        break;

    } 
break;
case "idList":
    $id = $_POST['id'];
    $no = $_POST['nosph'];
    $q = "SELECT nomer,s.*,ds.luas,ds.bahan,ds.biaya_plafon,ds.model,ds.d,ds.t,ds.dt,ds.plafon,ds.harga,ds.harga2,ds.harga3,ds.jumlah,ds.ket,ds.transport,u.nama,p.name as pn,p.id as idP,k.name as kn,k.id as idK FROM aki_sph s right join aki_dsph ds on s.noSph=ds.noSph left join aki_user u on s.kodeUser=u.kodeUser left join provinsi p on s.provinsi=p.id LEFT join kota k on s.kota=k.id WHERE 1=1 and nomer ='".$id."' and (s.noSph)='".$no."' ORDER BY s.noSph desc";
    $result = mysql_query($q, $dbLink);
    if (mysql_num_rows($result)>0) {
        $idx = 0;
        while ( $data = mysql_fetch_assoc($result)) {
            echo json_encode(array("biaya_plafon"=>number_format($data['biaya_plafon']).'',"model"=>$data['model'].'',"d"=>$data['d'].'',"t"=>$data['t'].'',"harga3"=>number_format($data['harga3']).'',"dt"=>$data['dt'].'',"plafon"=>$data['plafon'].'',"harga"=>number_format($data['harga']).'',"harga2"=>number_format($data['harga2']).'',"jumlah"=>$data['jumlah'].'',"ket"=>$data['ket'].'',"transport"=>number_format($data['transport']).'',"bahan"=>$data['bahan'].'',"luas"=>$data['luas']));
            $idx++;
        } 
        //echo json_encode($output);
        break;
    } 
break;
case "idListkk":
    $id = $_POST['id'];
    $no = $_POST['noKk'];
    $q = "SELECT kk.*,dp.* FROM aki_dkk kk left join aki_dpembayaran dp on kk.noKK=dp.noKk WHERE 1=1 and nomer ='".$id."' and (kk.noKK)='".$no."' ORDER BY kk.noKK desc";
    $result = mysql_query($q, $dbLink);
    if (mysql_num_rows($result)>0) {
        $idx = 0;
        while ( $data = mysql_fetch_assoc($result)) {
            echo json_encode(array("model"=>$data['model'].'',"d"=>$data['d'].'',"t"=>$data['t'].'',"dt"=>$data['dt'].'',"plafon"=>$data['plafon'].'',"harga"=>number_format($data['harga']).'',"jumlah"=>$data['jumlah'].'',"ket"=>$data['ket'].'',"bahan"=>$data['bahan'].'',"luas"=>$data['luas'].'',"kubah"=>$data['kubah'].'',"txtw1"=>$data['wpembayaran1'].'',"txtw2"=>$data['wpembayaran2'].'',"txtw3"=>$data['wpembayaran3'].'',"txtw4"=>$data['wpembayaran4'].'',"txtp1"=>$data['persen1'].'',"txtp2"=>$data['persen2'].'',"txtp3"=>$data['persen3'].'',"txtp4"=>$data['persen4'].'',"color1"=>$data['color1'].'',"color2"=>$data['color2'].'',"color3"=>$data['color3'].'',"color4"=>$data['color4'].'',"color5"=>$data['color5']));
            $idx++;
        } 
        //echo json_encode($output);
        break;
    } 
break;
case "getpemasangan":
    $d = $_POST['d'];
    $return = '';
    if ($d > '0.5' and $d<='4'){
        $return = '8';
    }elseif ($d > '4' and $d<='6') {
        $return = '10';
    }
    elseif ($d > '6' and $d<='8') {
        $return = '15';
    }
    elseif ($d > '8' and $d<='9') {
        $return = '18';
    }
    elseif ($d > '9' and $d<='11') {
        $return = '20';
    }
    elseif ($d > '11' and $d<='13') {
        $return = '25';
    }
    elseif ($d > '13' and $d<='14') {
        $return = '30';
    }
    else{
        $return = '30';
    }
    echo json_encode(array("pemasangan"=>$return));
break;
case "getpabrikasi":
    $d = $_POST['d'];
    $bahan = $_POST['bahan'];
    $return = '';
    if ($bahan == 'Galvalume' || $bahan == 'Titanium'){
        if ($d > '0.5' and $d<='3'){
            $return = '28';
        }elseif ($d > '3' and $d<='5') {
            $return = '38';
        }
        elseif ($d > '5' and $d<='7') {
            $return = '58';
        }
        elseif ($d > '7' and $d<='9') {
            $return = '78';
        }
        elseif ($d > '9' and $d<='12') {
            $return = '83';
        }
        elseif ($d > '12' and $d<='14') {
            $return = '103';
        }
        elseif ($d > '14' and $d<='18') {
            $return = '123';
        }
        else{
            $return = '123';
        }
    }else{
        if ($d > '0.5' and $d<'3'){
            $return = '80';
        }elseif ($d > '3' and $d<'6') {
            $return = '110';
        }
        elseif ($d > '6' and $d<'12') {
            $return = '125';
        }
        else{
            $return = '125';
        }
    }
    echo json_encode(array("pabrikasi"=>$return));
break;
case "getproject":
    $noproject = secureParamAjax($_POST['noproject'], $dbLink2);

    $result = mysql_query("SELECT spk.*,kk.*, dkk.*,p.*,k.name as lokasi FROM aki_spk spk left join aki_kk kk on spk.nokk=kk.noKk right join aki_dkk dkk on kk.noKk=dkk.noKk left join aki_tabel_proyek p on spk.noproyek=p.noproyek left join kota k on kk.kota=k.id  WHERE spk.noproyek!='-' and spk.aktif=1 and spk.noproyek='".$noproject."' GROUP by spk.noproyek ORDER BY kk.noKk desc", $dbLink2);
    if (mysql_num_rows($result)>0) {
        while ( $data = mysql_fetch_assoc($result)) {
            echo json_encode(array("noproyek"=>$data['noproyek'],"alamat"=>$data['alamat']));
        } 
        break;
    } 
break;
}
?>
