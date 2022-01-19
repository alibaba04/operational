<?php
//=======  : Alibaba
//Created : 8 Januari 2017
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/setting_list";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Master Kategori';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}

//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    require_once("./class/c_setting.php");
    $tmpSetting = new c_setting();

    //Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpSetting->addSetting($_POST);
    }

    //Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpSetting->editSetting($_POST);
    }

    //Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpSetting->deleteSetting($_GET["kodeSetting"]);
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
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<section class="content-header">
    <h1>
        MASTER SETTING
        <small>List Setting</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pengaturan</li>
        <li class="active">Master Setting</li>
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
                    <h3 class="box-title">Kriteria Pencarian Setting </h3>
                </div>


                <!-- /.box-header -->
                <div class="box-body">
                    <form name="frmCariSetting" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">

                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="namaSetting" id="namaSetting" placeholder="Nama Setting..."
                            <?php
                            if (isset($_GET["namaSetting"])) {
                                echo("value='" . $_GET["namaSetting"] . "'");
                            }
                            ?>
                                   onKeyPress="return handleEnter(this, event)">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-info btn-flat">Go!</button>
                            </span>
                        </div>
                        <p>- atau -</p>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="nilai" id="nilai" placeholder="Nilai..."
                            <?php
                            if (isset($_GET["nilai"])) {
                                echo("value='" . $_GET["nilai"] . "'");
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
                        if ($hakUser==90){
                    ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']."?page=view/setting_detail&mode=add";?>"><button type="button" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Tambah Data</button></a>
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
                    if (substr($_GET["pesan"],0,5) == "Gagal") { 
                        echo '<div class="callout callout-danger">';
                    }else{
                        echo '<div class="callout callout-success">';
                    }
                    if ($_GET["pesan"] != "") {

                            echo $_GET["pesan"];

                        }
                    echo '</div>';
                    ?>
                        
                    
                </div>
            </div>
            <?php } ?>
        </section>
        
        <!-- /.right col -->

        <section class="col-lg-12 connectedSortable">
            <div class="box box-primary">
                <?php
                if (isset($_GET["namaSetting"])){
                    $namaSetting = secureParam($_GET["namaSetting"], $dbLink);
                }else{
                    $namaSetting = "";
                }
                
                if (isset($_GET["nilai"])){
                    $nilai = secureParam($_GET["nilai"], $dbLink);
                }else{
                    $nilai = "";
                }
                

                //Set Filter berdasarkan query string
                $filter="";
                if ($namaSetting)
                    $filter = $filter . " AND s.namaSetting LIKE '%" . $namaSetting . "%'";
                if ($nilai)
                    $filter = $filter . " AND s.nilai LIKE '%" . $nilai . "%'";

                //database
                $q = "SELECT idSetting, namaSetting, nilai FROM aki_setting WHERE 1=1 " . $filter;
                $q.= " ORDER BY namaSetting ";
                
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
                                <th style="width: 5%">#</th>
                                <th style="width: 40%">Nama Setting</th>
                                <th style="width: 40%">Nilai</th>
                                <th colspan="2" width="5%">Aksi</th>
                                
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $rowCounter=1;
                            if (isset($_GET["resultpage"])>1){
                                $rowCounter = ($_GET["resultpage"] * 10) - 10 + $rowCounter; //per halaman 10 list
                            }
                            while ($query_data = $rs->fetchArray()) {
                                echo "<tr>";
                                echo "<td>" . $rowCounter . "</td>";
                                echo "<td>" . $query_data[1] . "</td>";
                                echo "<td>" . $query_data[2] . "</td>";
                                
                                if ($hakUser == 90) {
                                    echo "<td><span class='label label-success' style='cursor:pointer;' onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/setting_detail&mode=edit&kode=" . md5($query_data[0]) . "'><i class='fa fa-edit'></i>&nbsp;Ubah</span></td>";
                                    
                                    echo("<td><span class='label label-danger' onclick=\"if(confirm('Apakah anda yakin akan menghapus data " . $query_data[1] . " ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&kodeSetting=" . md5($query_data[0]) . "'}\" style='cursor:pointer;'><i class='fa fa-trash'></i>&nbsp;Hapus</span></td>");
                                    
                                } else {
                                    echo("<td>&nbsp;</td>");
                                    echo("<td>&nbsp;</td>");
                                }
                                echo("</tr>");
                                $rowCounter++;
                            }
                            if (!$rs->getNumPages()) {
                                echo("<tr class='even'>");
                                echo ("<td colspan='5' align='center'>Maaf, data tidak ditemukan</td>");
                                echo("</tr>");
                            }
                            ?>
                        </tbody>
                    </table>
                </div> 
            </div>
        </section>

    </div>
    <!-- /.row -->
</section>