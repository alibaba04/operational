<?php
//=======  : Alibaba
//Untuk memastikan bahwa setiap sesi web dimulai dari halaman ini
define('validSession', 1);
//Periksa keberadaan file config.php. Jika ada, load file tersebut untuk memasukkan variable konfigurasi umum
if (!file_exists('config.php')) {
    exit();
}
require_once( 'config3.php' );require_once( 'config2.php' );require_once( 'config.php' );
require_once('./class/c_user.php');
session_name("alibaba");
session_start();
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
    <title>Operational</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Font Awesome -->
    <!--        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">-->
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/AdminLTE.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
     folder instead of downloading all of them to reduce the load. -->
     <link rel="stylesheet" href="dist/css/skins/skin-qoobah.css">
     <!-- iCheck for checkboxes and radio inputs -->
     <link rel="stylesheet" href="plugins/iCheck/all.css">
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
     <link rel="stylesheet" href="plugins/colorpicker/bootstrap-colorpicker.min.css">
     <link rel="stylesheet" href="css/searchInput1.css">
     <link rel="icon" href="dist/img/logo-qoobah.png" type="image/png"/>
     <link rel="stylesheet" href="dist/css/bootstrap.min.css"> 
     <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
     <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
     <script src="js/angka.js"></script>
     <script type="text/javascript" src="js/autoCompletebox.js"></script>
     <link rel="stylesheet" href="ionicons/css/ionicons.min.css">
     <script src="https://www.gstatic.com/firebasejs/7.14.6/firebase-app.js"></script>
     <script src="https://www.gstatic.com/firebasejs/7.14.6/firebase-messaging.js"></script>
     <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
     <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
     <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
     <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
   </head>
   <?php
   if ((isset($_SESSION["my"]) === false) || (isset($_GET["page"]) === "login_detail")) {
    echo '<body class="hold-transition login-page">';
} else {
    ?>
    <body class="hold-transition skin-green sidebar-mini fixed">
        <?php
    }
    ?>
    <div class="wrapper">
        <header class="main-header">
            <!-- Logo -->
            <?php
            if ((isset($_SESSION["my"]) !== false) && (isset($_GET["page"]) !== "login_detail")) {
                ?>
                <a href="index.php" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini"><img src="dist/img/logo-qoobah4.png"></span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg"><img src="dist/img/logo-qoobah3.png" ></span>
                </a>
                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;">Operational</span>
                    </a>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown messages-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                  <i class="fa fa-envelope-o"></i>
                            <?php
                                $q3= "SELECT count(id) as jml FROM `aki_report` WHERE ket like '% read by ".$_SESSION["my"]->privilege."=1%'";
                                $rsTemp3 = mysql_query($q3, $dbLink);
                                if ($dataSph3 = mysql_fetch_array($rsTemp3)) {
                                    echo '<span class="label label-warning">'.$dataSph3['jml'].'</span></a>';
                                }
                            ?>
                              <ul class="dropdown-menu">
                                  <li class="header">You have new messages</li>
                                  <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                      
                                          
                                        <?php
                                            $q= "SELECT * FROM `aki_report` WHERE ket like '%read by ".$_SESSION["my"]->privilege."=1%' order by id desc";
                                            $rsTemp = mysql_query($q, $dbLink);
                                            while ($dataSph = mysql_fetch_array($rsTemp)) {
                                                $ket = explode(',', $dataSph['ket']);
                                                $nokk = explode('=', $ket[1]);
                                                $ket = explode('=', $ket[2]);
                                                echo '<li><a href="'.$_SERVER['PHP_SELF'].'?page=view/kkreview_detail&mode=addNote&noKK='.md5($nokk[1]).'"><div class="pull-left"><img src="dist/img/avt04.png" class="img-circle" alt="User Image"></div>';
                                                echo '<h4>'.$dataSph['kodeUser'].'</h4>';
                                               
                                                echo '<p>No KK : '.$nokk[1].'<br>'.$ket[1].'</p></a></li>';
                                            }
                                        ?>
                                        
                                    </ul>
                                   </li>
                              </ul>
                            </li>
                            <li>
                            <!-- Control Sidebar Toggle Button -->
                                <a href="logout.php?page=login_detail&eventCode=20">Logout&nbsp;&nbsp;<i class="fa fa-sign-out" aria-hidden="true" title="Logout"></i></a>
                            </li>
                        </ul>
                    </div>
                    <?php
                }
                ?>
            </nav>
        </header>
        <?php
        /* Periksa session $my, jika belum teregistrasi load modul login */
        if (isset($_SESSION["my"]) == false || isset($_GET["page"]) === "login_detail") {
            require_once('login_detail.php' );
        } else {
            $t = isset($_SESSION["my"]->timeout);
            if((time() - $_SESSION["my"]->timeout) > 18000) { 
                $result=mysql_query("UPDATE `aki_user` SET `ip`='0' where kodeUser='".$_SESSION["my"]->id."'" , $dbLink);
                $_SESSION["my"] = false;
                unset($_SESSION['my']);
                require_once('login_detail.php' );
                exit;
            }
            ?>   
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <?php echo '<img src="dist/img/'.$_SESSION["my"]->avatar.'" class="img-rounded" alt="User Image">'; ?>
                        </div>
                        <div class="pull-left info">
                            <p style="font-size: 17px;"><?php echo $_SESSION["my"]->name; ?></p>
                            <a href="#"><i class="fa fa-circle text-success"></i> <?php echo $_SESSION["my"]->privilege; ?></a>
                        </div>
                    </div>
                    <ul class="sidebar-menu">
                        <li class="treeview">
                            <?php echo menu(); ?>
                        </li>
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Pages Content take in here -->
                <?php
                    //Load module yang bersesuaian

                if (isset($_GET["page"])) {
                    require_once('view/' . substr($_GET["page"] . ".php", 5, strlen($_GET["page"] . '.php') - 5));
                } else {
                    require_once('view/dashboard.php');
                }
                ?>
                <!-- End Pages Content -->
            </div>
            <!-- content wrapper/.row (main row) -->
            <?php
        }
        ?>
        <!-- /.content-wrapper -->
        <?php
        if (isset($_SESSION["my"]) != false && isset($_GET["page"]) != "login_detail") {
            ?>
            <footer class="main-footer">

                <div class="pull-right hidden-xs">
                    
                    <b>Operational App</b> 0.0.1 &nbsp;&nbsp;<strong>Created by: <a href="http://instagram.com/baihaqial">alibaba</a>.
                    </div>
                    <strong>.</strong>
                    <!-- <strong>Copyright &copy; 2020 <a href="http://instagram.com/baihaqial">alibaba's</a>.</strong> All rights reserved.  -->
                </footer>
                <?php
            }
            ?>
        </div>

        <!-- ./wrapper -->
        <!-- jQuery 2.2.3 -->
        <script src="js/jquery.bestupper.min.js" type="text/javascript"></script>
        <script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
        <script src="dist/js/jquery-ui.min.js"></script>
        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
        <!-- <script>
            $.widget.bridge('uibutton', $.ui.button);
        </script> -->
        <!-- Select2 -->
        <script src="plugins/select2/select2.full.min.js"></script>
        <script src="dist/js/raphael-min.js"></script>
        <!-- Sparkline -->
        <script src="plugins/sparkline/jquery.sparkline.min.js"></script>
        <!-- jvectormap -->
        <script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
        <script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
        <!-- jQuery Knob Chart -->
        <script src="plugins/knob/jquery.knob.js"></script>
        <script src="dist/js/moment.min.js"></script>
        <script src="plugins/daterangepicker/daterangepicker.js"></script>
        <!-- datepicker -->
        <script src="plugins/datepicker/bootstrap-datepicker.js"></script>
        <script src="plugins/datepicker/locales/bootstrap-datepicker.id.js"></script>
        <!-- iCheck 1.0.1 -->
        <script src="plugins/iCheck/icheck.min.js"></script>
        <!-- Bootstrap WYSIHTML5 -->
        <script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
        <!-- Slimscroll -->
        <script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
        <!-- FastClick -->
        <script src="plugins/fastclick/fastclick.js"></script>
        <script src="plugins/morris/morris.min.js"></script>
        <!-- AdminLTE App -->
        <script src="dist/js/app.min.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="dist/js/demo.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <script src="plugins/input-mask/jquery.inputmask.js"></script>
        <script src="plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
        <script src="plugins/input-mask/jquery.inputmask.extensions.js"></script>
        <script src="plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
        <script src="plugins/toastr/toastr.min.js"></script>
        <script src="plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
        <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
        <script src="plugins/jszip/jszip.min.js"></script>
        <script src="plugins/pdfmake/pdfmake.min.js"></script>
        <script src="plugins/pdfmake/vfs_fonts.js"></script>
        <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
        <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
        <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
        <script>
            var firebaseConfig = {
                apiKey: "AIzaSyCmTZroIbWCnevV3O3vz-VMDWJaYY-hexs",
                authDomain: "sikubah-ce3f1.firebaseapp.com",
                projectId: "sikubah-ce3f1",
                storageBucket: "sikubah-ce3f1.appspot.com",
                messagingSenderId: "1073118020070",
                appId: "1:1073118020070:web:3fe92396be45a17ca11627",
                measurementId: "G-7251WCMR99"
            };
            firebase.initializeApp(firebaseConfig);
            const messaging=firebase.messaging();

            function IntitalizeFireBaseMessaging() {
                messaging
                .requestPermission()
                .then(function () {
                    console.log("Notification Permission");
                    return messaging.getToken();
                })
                .then(function (token) {
                    console.log("Token : "+token);
                    document.getElementById("token").innerHTML=token;

                })
                .catch(function (reason) {
                    console.log(reason);
                });

            }

            messaging.onMessage(function (payload) {
                console.log(payload);
                const notificationOption={
                    body:payload.notification.body,
                    icon:payload.notification.icon
                };

                if(Notification.permission==="granted"){
                    var notification=new Notification(payload.notification.title,notificationOption);

                    notification.onclick=function (ev) {
                        ev.preventDefault();
                        window.open(payload.notification.click_action,'_blank');
                        notification.close();
                    }
                }

            });
            messaging.onTokenRefresh(function () {
                messaging.getToken()
                .then(function (newtoken) {
                    console.log("New Token : "+ newtoken);
                })
                .catch(function (reason) {
                    console.log(reason);
                })
            })
            IntitalizeFireBaseMessaging();
        </script>
    </body>
    
    </html>
    