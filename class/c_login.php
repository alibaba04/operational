<?php
/* ==================================================
  //=======  : Alibaba
  ==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_login
{
	//Constructor
	function c_login() 
	{

	}
	function validateUser($userId=0,$token)
	{
            global $dbLink;
            global $SITUS;
            global $passSalt;

            //Secure all parameters from SQL injection

           $userId= secureParam($userId,$dbLink);
           $token= secureParam($token,$dbLink);

            //Ambil semua data user yang perlu dimasukkan ke Session 		
            $result=mysql_query("SELECT kodeUser, nama FROM aki_user WHERE kodeUser='".$userId."' and aktif='Y'" , $dbLink);
            if($query_data=mysql_fetch_row($result)){
                $result=mysql_query("SELECT kodeUser, nama, avatar,phone,token FROM aki_user WHERE kodeUser='".$userId."' AND  password='".HASH('SHA512',$passSalt.$_POST[txtPassword])."' AND aktif='Y'" , $dbLink);

                if($query_data=mysql_fetch_row($result))
                {
                    //Ambil semua Kode Group yang dimiliki oleh user
                    $tempGroup="";
                    $result=mysql_query("SELECT kodeGroup FROM aki_usergroup WHERE kodeUser='".$userId."'" , $dbLink);
                    while($dataGroup=mysql_fetch_row($result))
                    {
                        $tempGroup.=",'".$dataGroup[0]."'";
                    }

                    //Ambil semua Kode Menu yang boleh diakses oleh user        
                    $tempMenu="";
                    //Jika merupakan anggota group ADMIN, maka boleh akses semua menu       
                    if(in_array("'GODMODE'",explode(',',$tempGroup)))
                    {
                        $q = "SELECT m.kodeMenu ";
                        $q.= "FROM aki_menu m ";
                        $q.= "WHERE m.aktif='Y'";
                    }
                    else
                    {
                        $q = "SELECT m.kodeMenu ";
                        $q.= "FROM aki_groupprivilege gp, aki_menu m ";
                        $q.= "WHERE gp.kodeGroup=m.kodeMenu AND m.aktif='Y' AND gp.level>=10 AND 
                        gp.kodeMenu IN ('0'".$tempGroup.")";

                    }
                    $result=mysql_query($q, $dbLink);

                    while($dataMenu=mysql_fetch_row($result))
                    {
                        $tempMenu.=",'".$dataMenu[0]."'";
                    }

                    require_once('./class/c_user.php');
                    session_name("akunkobama");

                    $_SESSION["my"] = new c_user($query_data[0], $query_data[1], $tempGroup, $tempMenu,"",$query_data[2],$query_data[3],$query_data[4]);
                    $_SESSION["my"]->timeout =  time();
                    
                    $rsGroup = mysql_query("SELECT kodeGroup FROM aki_usergroup WHERE kodeUser='".$query_data[0]."';", $dbLink);
                    if(mysql_num_rows($rsGroup)<=1)
                    {
                        $group = mysql_fetch_row($rsGroup);
                        $_SESSION["my"]->privilege = $group["0"];
                    }
                    
                    //update token,ip
                    $qIp = "UPDATE `aki_user` SET `ip`='".$_SERVER['REMOTE_ADDR']."',`token`='".$token."' where kodeUser='".$userId."'";
                    if (!mysql_query($qIp, $dbLink))
                        throw new Exception($qIp.'Gagal update token. ');

                    date_default_timezone_set("Asia/Jakarta");
                    $tgl = date("Y-m-d h:i:sa");
                    $q4 = "INSERT INTO `aki_report`( `kodeUser`, `datetime`, `ket`) VALUES";
                    $q4.= "('".$userId."','".$tgl."','User Login ip : ".$_SERVER['REMOTE_ADDR']."');";
                    if (!mysql_query( $q4, $dbLink))
                        throw new Exception('Gagal update report. ');
                    return "Sukses";
                }
                else
                {
                    return "10";
                }
            }else{
                return "30";
            }
	}
}
?>
