<?php
//Author : Kristoforus H. Abadi
//Untuk memastikan bahwa setiap sesi web dimulai dari halaman ini
define('validSession', 1);

//Periksa keberadaan file config.php. Jika ada, load file tersebut untuk memasukkan variable konfigurasi umum
if (!file_exists('config.php')) {
    exit();
}


require_once( 'config.php' );
require_once('./class/c_user.php');

//ini_set('display_errors', 1);

session_name("alibaba");
session_start();
global $dbLink;

require_once('./function/fungsi_menu.php');
require_once('./function/getUserPrivilege.php');
require_once('./function/pagedresults.php');
require_once('./function/secureParam.php');
require_once('./function/fungsi_formatdate.php');
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Akuntansi -</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
<!--        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">-->
        <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
        <!-- Ionicons -->
<!--        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">-->
        <link rel="stylesheet" href="ionicons/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
        <!-- iCheck for checkboxes and radio inputs -->
        <link rel="stylesheet" href="plugins/iCheck/all.css">
        <!-- iCheck -->
<!--        <link rel="stylesheet" href="plugins/iCheck/flat/blue.css">-->
        <!-- Morris chart -->
        <link rel="stylesheet" href="plugins/morris/morris.css">
        <!-- jvectormap -->
        <link rel="stylesheet" href="plugins/jvectormap/jquery-jvectormap-1.2.2.css">
        <!-- Date Picker -->
        <link rel="stylesheet" href="plugins/datepicker/datepicker3.css">
        <!-- Daterange picker -->
        <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
        <!-- bootstrap wysihtml5 - text editor -->
        <link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
        <!-- Select2 -->
        <link rel="stylesheet" href="plugins/select2/select2.min.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    
	<body class="hold-transition login-page">
   
		<div class="wrapper">
            

            <?php
            
            $result=mysql_query("UPDATE `aki_user` SET `ip`='0' where kodeUser='".$_SESSION["my"]->id."'" , $dbLink);
            /* Periksa session $my, jika belum teregistrasi load modul login */
            if (isset($_GET["page"]) == "login_detail") {
                
                unset($_SESSION['my']);
            } 
                ?>   
            <meta http-equiv="refresh" content="0;URL='index.php?page=login_detail&eventCode=20'" />
          
        </div>
        
        
        
    </body>
    
    
</html>
