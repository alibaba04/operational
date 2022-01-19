<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/user_list";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Pengaturan User';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}

//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser >= 50) {

    require_once("./class/c_user.php");
    $tmpUser=new c_user();
    //Jika Mode Tambah/Add

    if ($_POST["txtMode"]=="Add")
    {
        $pesan=$tmpUser->add($_POST);
    }

    //Jika Mode Ubah/Edit
    if ($_POST["txtMode"]=="Edit")
    {
        $pesan=$tmpUser->edit($_POST); 
    }

    if ($_POST["txtMode"]==md5("ChangePassword"))
    {
        $pesan=$tmpUser->ChangePassword($_POST); 
    }

    //Jika Mode Hapus/Delete
    if ($_GET["txtMode"]=="Delete")
    {
        $pesan=$tmpUser->delete($_GET["kodeUser"]);
    }

    //Jika Mode ChangeProfile untuk file html/ubahProfil_list.php
    if ($_POST["txtMode"]=="ChangeProfile")
    {
       $pesan=$tmpUser->ChangeProfile($_POST);
   }

    //Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
    //Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
   if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
    global $mailSupport;
    $pesan.="Gagal simpan data, mohon hubungi " . $mailSupport . " untuk keterangan lebih lanjut terkait masalah ini.";
}
header("Location:index.php?page=$curPage&pesan=" . $pesan);
exit;
}
?>
<section class="content-header">
    <h1>
        PENGATURAN USER
        <small>List User</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pengaturan</li>
        <li class="active">User</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <section class="col-lg-6">

            <!-- TO DO List -->
            <div class="box box-primary">
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <h3 class="box-title">Pencarian User </h3>
                </div>


                <!-- /.box-header -->
                <div class="box-body">
                    <form name="frmCariInisiasi" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">

                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="namaUser" id="namaUser" placeholder="Nama User..."
                            <?php
                            if (isset($_GET["namaUser"])) {
                                echo("value='" . $_GET["namaUser"] . "'");
                            }
                            ?>
                            onKeyPress="return handleEnter(this, event)">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-info btn-flat">Go!</button>
                            </span>
                        </div>
                        
                    </form>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                    <?php
                    if ($hakUser == 90 ) {
                        ?>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . "?page=html/user_detail&mode=add"; ?>"><button type="button" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Tambah Data</button></a>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <!-- /.box -->
        </section>
        <!-- /.Left col -->
        <!-- right col -->
        <section class="col-lg-6">
            <?php
            //informasi hasil input/update Sukses atau Gagal
            if (isset($_GET["pesan"]) != "") {
                ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <i class="fa fa-warning"></i>
                        <h3 class="box-title">Pesan</h3>
                    </div>
                    <div class="box-body">
                        
                        <?php
                        if (substr($_GET["pesan"], 0, 5) == "Gagal") {
                            echo '<div class="callout callout-danger">'. $_GET["pesan"] . '</div>';
                        } else {
                            echo '<div class="callout callout-success">'. $_GET["pesan"] . '</div>';
                        }
                        ?>

                        
                    </div>
                </div>
            <?php } ?>
        </section>
        <!-- /.right col -->

        <section class="col-lg-12 connectedSortable">
            <div class="box box-primary">
                <?php
                if (isset($_GET["namaUser"])){
                    $namaUser = secureParam($_GET["namaUser"], $dbLink);
                }else{
                    $namaUser = "";
                }

                //Set Filter berdasarkan query string
                $filter = "";
                if($namaUser)
                    $filter= $filter." AND u.kodeUser LIKE '%".$namaUser."%'";
                if ($hakUser >=50) {
                    $filter= $filter." AND u.kodeUser LIKE '%".$_SESSION["my"]->id."%'";
                } 
                //Query
                $q = "SELECT u.kodeUser, u.nama, u.aktif ";
                $q.= "FROM aki_user u ";
                $q.= "WHERE 1 ".$filter;
                $q.= " ORDER BY u.kodeUser";

                //Paging
                $rs = new MySQLPagedResultSet($q, $recordPerPage, $dbLink);
                ?>
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <ul class="pagination pagination-sm inline"><?= $rs->getPageNav($_SERVER['QUERY_STRING']) ?></ul>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped table-hover" >
                        <thead>
                            <tr>
                                <th style="width: 3%">#</th>
                                <th width="20%" class="sort-alpha">Username</th>
                                <th width="40%" class="sort-alpha">Name</th>
                                <th width="15%">Active</th>
                                <th colspan="3" width="3%">Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rowCounter = 1;
                            while ($query_data = $rs->fetchArray()) {
                                echo "<tr>";
                                echo "<td>" . $rowCounter . "</td>";
                                echo "<td>" . $query_data["kodeUser"] . "</td>";
                                echo "<td>" . $query_data["nama"] . "</td>";
                                echo "<td>" . $query_data["aktif"] . "</td>";
                                

                                if ($hakUser == 90) {
                                    echo "<td><span class='label label-success' style='cursor:pointer;' onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/user_detail&mode=edit&kode=" . md5($query_data["kodeUser"]) . "'><i class='fa fa-edit'></i>&nbsp;Edit</span></td>";
                                    echo("<td><span class='label label-warning' border=0 style='cursor:pointer;' onclick=location.href='".$_SERVER['PHP_SELF']."?page=view/ubahPassword_detail&mode=edit&kode=".md5($query_data["kodeUser"])."'><i class='fa fa-repeat'></i>&nbsp;Change Password</span></td>");
                                    echo("<td><span class='label label-danger' onclick=\"if(confirm('Apakah anda yakin akan menghapus data User " . $query_data["nama"] . " ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&kodeUser=" . md5($query_data["kodeUser"]) . "'}\" style='cursor:pointer;'><i class='fa fa-trash'></i>&nbsp;Delete</span></td>");
                                    
                                } elseif ($hakUser == 50){

                                    echo("<td><span class='label label-warning' border=0 style='cursor:pointer;' onclick=location.href='".$_SERVER['PHP_SELF']."?page=view/ubahPassword_detail&mode=edit&kode=".md5($query_data["kodeUser"])."'><i class='fa fa-repeat'></i>&nbsp;Ubah Password</span></td>");
                                } else {
                                    echo("<td>&nbsp;</td>");
                                    echo("<td>&nbsp;</td>");
                                    echo("<td>&nbsp;</td>");
                                }
                                echo("</tr>");
                                $rowCounter++;
                            }
                            if (!$rs->getNumPages()) {
                                echo("<tr class='even'>");
                                echo ("<td colspan='10' align='center'>Maaf, data tidak ditemukan</td>");
                                echo("</tr>");
                            }
                            ?>
                        </tbody>
                    </table>
                </div> 
            </div>
        </section>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <div class="box-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <i class="ion ion-clipboard"></i>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
    </div>
</div>