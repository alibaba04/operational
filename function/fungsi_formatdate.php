<?php
function getkota($id){
	require_once( 'config.php' );
	global $dbLink;
	$id_prov = $id;
	$sql = "SELECT * FROM kota WHERE `provinsi_id` = '$id_prov'";
	$query = mysql_query($sql,$dbLink);
	$data = array();
	while($row =mysql_fetch_assoc($query)){
		$data[] = array("id" => $row['id'], "name" => $row['name']);
	}
	return json_encode($data);
}
function sendNotification($message,$title)
{
    $url ="https://fcm.googleapis.com/fcm/send";
    $sql = "SELECT s.*,g.kodeGroup FROM `aki_user` s left join aki_usergroup g on s.kodeUser=g.kodeUser where g.kodeGroup='GODMODE'";
	$query = mysql_query($sql,$dbLink);
    while ( $data = mysql_fetch_assoc($result)) {
        $fields=array(
            "to"=>$data['token'],
            "notification"=>array(
                "body"=>$message,
                "title"=>$title,
                "click_action"=>"https://sikubah.com"
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
    } 
}
function margin($d,$t,$dt)
{
	$margin = '';
	$luas = 0;
	if ($luas <= 15) {$margin = 100;}
	else if($luas <= 25){$margin = 80;}
	else if($luas <= 40){$margin = 60;}
	else if($luas <= 60){$margin = 50;}
	else if($luas <= 100){$margin = 40;}
	else{$margin = 33;}
	return $margin;
}
function luas($d,$t,$dt){
	$luas = 0;
	if ($dt == 0) {
		$luas = ($d * $t * 3.14);
	}else{
		$luas = ($dt * $t * 3.14);
	}
	return $luas;
}

function kalkulatorharga($d,$luas,$pmargin,$kel,$t)
{
    //GA
	$xtp = 0;
	if($d >= 4){ $xtp = 800000;}else{$xtp = 850000;} 
	$xwa = 0;
	if($d >= 4){ $xwa = 850000;}else{$xwa = 900000;} 
	$xfull = 0;
	if($d >= 4){ $xfull = 900000;}else{$xfull = 950000;} 
	$x = 0;
	if( $kel == '0'){$x = $xfull;}else if($kel == '2'){$x = $xwa;}else{$x = $xwa;}
	$modal = $luas * $x;
	$margin = $modal * $pmargin;
	$hpp = $modal + $margin;
	$affiliate = $hpp * 0.05;
	$marketing = $hpp * 0.01;
	$harga = $hpp + $affiliate + $marketing + $t;
    //EN
	$xtp2 = 0;
	if($d >= 4){ $xtp2 = 1700000;}else{$xtp2 = 1900000;} 
	$xwa2 = 0;
	if($d >= 4){ $xwa2 = 1800000;}else{$xwa2 = 1950000;} 
	$xfull2 = 0;
	if($d >= 4){ $xfull2 = 1900000;}else{$xfull2 = 2000000;} 
	$x2 = 0;
	if( $kel == '0'){$x2 = $xfull2;}else if($kel == '2'){$x2 = $xwa2;}else{$x2 = $xtp2;}
	$modal2 = $luas * $x2;
	$margin2 = $modal2 * $pmargin;
	$hpp2 = $modal2 + $margin2;
	$affiliate2 = $hpp2 * 0.05;
	$marketing2 = $hpp2 * 0.01;
	$harga2 = $hpp2 + $affiliate2 + $marketing2 + $t;
    return $harga.'-'.$harga2;
}
function lamapabrikasi($d,$bahan){
    $return = '';
    if ($bahan == 'Galvalume'){
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
    return($return);
}
function cekrangka($d)
{
	$return = '';
	if ($d >= '0.5' and $d<='0.9'){
		$return = '1 inchi tebal 1,6 mm';
	}elseif ($d >= '1' and $d<='2.9') {
		$return = '1,25 inchi tebal 1,6 mm';
	}
	elseif ($d >= '3' and $d<='4.9') {
		$return = '1,5 inchi tebal 1,6 mm';
	}
	elseif ($d >= '5' and $d<='5.9') {
		$return = '2 inchi tebal 1,6 mm';
	}
	elseif ($d >= '6' and $d<='10.9') {
		$return = '1,5 inchi tebal 1,6 mm';
	}
	elseif ($d >= '11' and $d<='16.9') {
		$return = '2 inchi tebal 1,6 mm';
	}
	elseif ($d >= '17' and $d<='20.9') {
		$return = '2,5 inchi tebal 1,6 mm';
	}
	elseif ($d >= '21' and $d<='27.9') {
		$return = '3 inchi tebal 1,6 mm';
	}
	elseif ($d >= '28' and $d<='30') {
		$return = '4 inchi tebal 1,6 mm';
	}
	return $return;
}
function formatDate_id($value="01/01/1970", $pemisah="-", $hurufbesar=false, $formatbulan="MM", $format="dd/mm/yyyy")
{
	$tgl = substr($value, 0, 2);
	$bulan = substr($value, 3, 2);
	$tahun = substr($value, 6, 4);

	if($formatbulan=="MM")
	{
		$bulan = namaBulan_id($bulan);
		if($hurufbesar)
			$bulan=strtoupper($bulan);
	}
	elseif($formatbulan=="M")
	{
		$bulan = substr(namaBulan_id($bulan), 0, 3);
		if($hurufbesar)
			$bulan=strtoupper($bulan);
	}

	if($format=='d/mm/yyyy')
	{
		if(substr($tgl, 0, 1)=="0")
			$tgl = substr($tgl, 1, 1);
		return $tgl.$pemisah.$bulan.$pemisah.$tahun;
	}
	elseif($format=="mm/yyyy")
		return $bulan.$pemisah.$tahun;
	elseif($format=="mm/dd/yyyy")
		return $bulan.$pemisah.$tgl.$tahun;
	elseif($format=="m/d/yyyy")
	{
		if(substr($tgl, 0, 1)=="0")
			$tgl = substr($tgl, 1, 1);
		if(substr($bulan, 0, 1)=="0")
			$tgl = substr($bulan, 1, 1);
		return $bulan.$pemisah.$tgl.$tahun;
	}
	else
		return $tgl.$pemisah.$bulan.$pemisah.$tahun;

}

function formatDate_en($value="01/01/1970", $pemisah="-", $hurufbesar=false, $formatbulan="MM", $format="dd/mm/yyyy")
{
	$tgl = substr($value, 0, 2);
	$bulan = substr($value, 3, 2);
	$tahun = substr($value, 6, 4);

	if($formatbulan=="MM")
		$bulan = namaBulan_en($bulan);
	elseif($formatbulan=="M")
		$bulan = substr(namaBulan_en($bulan), 0, 3);

	if($hurufbesar)
		$bulan=strtoupper($bulan);

	if($format=='y/m/d')		
		return $tahun.$pemisah.$bulan.$pemisah.$tgl;
	elseif($format=='d/m/yyyy')
	{
		if(substr($tgl, 0, 1)=="0")
			$tgl = substr($tgl, 1, 1);

		if(substr($bulan, 0, 1)=="0")
			$tgl = substr($bulan, 1, 1);	
		return $tahun.$pemisah.$bulan.$pemisah.$tgl;
	}
	elseif($format=="MM d, yyyy")
		return $bulan." ".$tgl.", ".$tahun;
	elseif($format=='m/d/y')
	{
		if($formatbulan=='MM' || $formatbulan=='M')
			return $bulan.$pemisah.$tgl.", ".$tahun;
		else
			return $bulan.$pemisah.$tgl.$pemisah.$tahun;
	}
	else
		return $tgl.$pemisah.$bulan.$pemisah.$tahun;		
}

function namaBulan_id($bulan="01")
{
	switch($bulan)
	{
		case '01':
		$nama = "Januari";
		break;
		case '02':
		$nama = "Februari";
		break;
		case '03':
		$nama = "Maret";
		break;
		case '04':
		$nama = "April";
		break;				
		case '05':
		$nama = "Mei";
		break;
		case '06':
		$nama = "Juni";
		break;
		case '07':
		$nama = "Juli";
		break;
		case '08':
		$nama = "Agustus";
		break;
		case '09':
		$nama = "September";
		break;
		case '10':
		$nama = "Oktober";
		break;
		case '11':
		$nama = "November";
		break;
		case '12':
		$nama = "Desember";
		break;			
		default:
		$nama = "Error";
		break;																						
	}
	return $nama;
}

function namaBulan_en($bulan="01")
{
	switch($bulan)
	{
		case '01':
		$nama = "January";
		break;
		case '02':
		$nama = "February";
		break;
		case '03':
		$nama = "March";
		break;
		case '04':
		$nama = "April";
		break;				
		case '05':
		$nama = "May";
		break;
		case '06':
		$nama = "June";
		break;
		case '07':
		$nama = "July";
		break;
		case '08':
		$nama = "August";
		break;
		case '09':
		$nama = "September";
		break;
		case '10':
		$nama = "October";
		break;
		case '11':
		$nama = "November";
		break;
		case '12':
		$nama = "December";
		break;			
		default:
		$nama = "Error";
		break;																						
	}
	return $nama;
}

function namaHari($hari = '0')
{
	switch($hari)
	{
		case '0':
		$nama = "Minggu";
		break;
		case '1':
		$nama = "Senin";
		break;
		case '2':
		$nama = "Selasa";
		break;
		case '3':
		$nama = "Rabu";
		break;				
		case '4':
		$nama = "Kamis";
		break;
		case '5':
		$nama = "Jumat";
		break;
		case '6':
		$nama = "Sabtu";
		break;		
		default:
		$nama = "Error";
		break;																						
	}
	return $nama;
}
function hariIndo ($hariInggris) {
  switch ($hariInggris) {
    case 'Sunday':
      return 'Minggu';
    case 'Monday':
      return 'Senin';
    case 'Tuesday':
      return 'Selasa';
    case 'Wednesday':
      return 'Rabu';
    case 'Thursday':
      return 'Kamis';
    case 'Friday':
      return 'Jumat';
    case 'Saturday':
      return 'Sabtu';
    default:
      return 'hari tidak valid';
  }
}
function cekFormatTanggal( $tanggal, $format= "ind" )
{
	if(substr_count($tanggal,"-") > 0 )
	{
		$delimiter = "-";
	}
	else if(substr_count($tanggal,"/") > 0 )
	{
		$delimiter = "/";
	}
	else
	{
		return false;
	}
	$data = explode($delimiter,$tanggal,3);
	$tgl = $data[0];
	$bln = $data[1];
	$thn = $data[2];

	if( $bln == "1" || $bln == "3" || $bln == "5" || $bln == "7" || $bln == "8" || $bln == "10" || $bln == "12")
		{ $maxHari = 31; }
	else if( $bln == "4" || $bln == "6" || $bln == "9" || $bln == "11" )
		{ $maxHari = 30; }
	else if( $bln == "2" )
	{
		if( $thn%4 == 0 )
			{ $maxHari = 29; }
		else
			{ $maxHari = 28; }
	}
	else
		{ return false; }

	if( $tgl > $maxHari )
		{ return false; }

	if( strlen($thn) == 4 )
	{
		if(substr($thn,0,1) <= 0 )
			{ return false; }
	}
	else
		{ return false; }

	if(strlen($tgl) < 2)
		{ $tgl = "0".$tgl; }
	if(strlen($bln) < 2)
		{ $bln = "0".$bln; }

	if( $format == "en" )
	{
		$returnTanggal = $thn.$delimiter.$bln.$delimiter.$tgl;
	}
	else if($format == "ind" )
	{
		$returnTanggal = $tgl.$delimiter.$bln.$delimiter.$thn;
	}
	else
	{
		$returnTanggal = $thn.$delimiter.$bln.$delimiter.$tgl;
	}

	return $returnTanggal;
}

function bulanRomawi($bulan = '1')
{
	if($bulan=='1' || $bulan=='01')
		$romawi = "I";
	elseif($bulan=='2' || $bulan=='02')
		$romawi = "II";
	elseif($bulan=='3' || $bulan=='03')
		$romawi = "III";
	elseif($bulan=='4' || $bulan=='04')
		$romawi = "IV";
	elseif($bulan=='5' || $bulan=='05')
		$romawi = "V";
	elseif($bulan=='6' || $bulan=='06')
		$romawi = "VI";
	elseif($bulan=='7' || $bulan=='07')
		$romawi = "VII";
	elseif($bulan=='8' || $bulan=='08')
		$romawi = "VIII";
	elseif($bulan=='9' || $bulan=='09')
		$romawi = "IX";
	elseif($bulan=='10')
		$romawi = "X";
	elseif($bulan=='11')
		$romawi = "XI";
	elseif($bulan=='12')
		$romawi = "XII";
	else
		$romawi = "ERR";

	return $romawi;
}

function datetomysql($datecheck) {

	list($Day, $Month, $Year) = split("-", $datecheck);

	$stampeddate = mktime(12, 0, 0, (int) $Month, (int) $Day, (int) $Year);
	if (checkdate((int) $Month, (int) $Day, (int) $Year)) {
		return date("Y-m-d", $stampeddate);
	} else {
                return 0; //not ok
            }
        }
        
        function datetoind($datecheck) {

        	list($Year, $Month, $Day) = split("-", $datecheck);

        	$stampeddate = mktime(12, 0, 0, (int) $Month, (int) $Day, (int) $Year);
        	if (checkdate((int) $Month, (int) $Day, (int) $Year)) {
        		return date("d-m-Y", $stampeddate);
        	} else {
                return 0; //not ok
            }
        }
        
        function tgl_mysql($tgl){
        	$tgl_mysql=substr($tgl,6,4)."-".substr($tgl,3,2)."-".substr($tgl,0,2);
        	return $tgl_mysql;
        }
        
        function tgl_ind($tgl){
        	$tgl_indo=substr($tgl,8,2)."-".substr($tgl,5,2)."-".substr($tgl,0,4);
        	return $tgl_indo;
        }
        ?>
