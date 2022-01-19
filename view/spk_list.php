<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/SPK_list";

//Periksa hak user pada modul/menu ini
$judulMenu = 'SPK';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}

//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    require_once("./class/c_SPK.php");
    $tmpSPK = new c_SPK;

//Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpSPK->add($_POST);
    }

//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpSPK->edit($_POST);
    }

//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpSPK->delete($_GET["kode"]);
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
<!-- Include script date di bawah jika ada field tanggal -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="dist/js/jquery-ui.min.js"></script>
<script src="plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript" charset="utf-8">

    $(function () {
        $('#tglTransaksi').daterangepicker({ 
            locale: { format: 'DD-MM-YYYY' } });
    //$('#tglTransaksi').val('00-00-0000');
});

</script>

<!-- End of Script Tanggal -->
<section class="content-header">
    <h1>
        SPK
        <small>List SPK</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">SPK</li>
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
                    <h3 class="box-title">Search SPK </h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form name="frmCariJurnalMasuk" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>"autocomplete="off">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="noSPK" id="noSPK" placeholder="Cari . . . ."
                            <?php
                            if (isset($_GET["noSPK"])) {
                                echo("value='" . $_GET["noSPK"] . "'");
                            }
                            ?>
                            onKeyPress="return handleEnter(this, event)">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                    <?php
                    if ($hakUser==90 or $hakUser==80){
                        ?>
                        <a href="<?php echo $_SERVER['PHP_SELF']."?page=html/spk_detail&mode=add";?>"><button type="button" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add SPK</button></a>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <!-- /.box -->
        </section>
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
                
                $filter="";$snum="";
                if(isset($_GET["noSPK"]) ){
                    $noSPK = secureParam($_GET["noSPK"], $dbLink);
                    $snum = secureParam($_GET["noSPK"], $dbLink)." : ";
                    if ($noSPK)
                        $filter = $filter . " AND p.name LIKE '%" . $noSPK . "%'  or s.nama_cust LIKE '%" . $noSPK . "%'  or s.noSPK LIKE '%" . $noSPK . "%'  or k.name LIKE '%" . $noSPK . "%'";
                }else{
                    $filter = '';
                }
                $filter2 = '';
                if ($_SESSION['my']->privilege == 'SALES') {
                    $filter2 =  " AND s.kodeUser='".$_SESSION['my']->id."' ";
                }
            //database
                $q = "SELECT * FROM `aki_spk` WHERE 1";
            //Paging
                $rs = new MySQLPagedResultSet($q, 50, $dbLink);
                ?>
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <ul class="pagination pagination-sm inline"><?php echo $snum.$rs->getPageNav($_SERVER['QUERY_STRING']) ?></ul>
                    <?php
                    if ($_SESSION['my']->privilege == 'ADMIN') {
                        echo '<a href="class/c_exportexcel.php?"><button class="btn btn-info pull-right"><i class="ion ion-ios-download"></i> Export Excel</button></a>';
                    }
                    ?>
                </div>

                <div class="box-body" style="width: 100%;overflow-x: scroll;">
                    <table class="table table-bordered table-striped table-hover" >
                        <thead>
                            <tr>
                                <th width="3%">Action</th>
                                <th style="width: 10%">No Proyek</th>
                                <th style="width: 20%">No SPK</th>
                                <th style="width: 20%">No Kontrak</th>
                                <th style="width: 10%">Client</th>
                                <th style="width: 20%">Address</th>
                                <th style="width: 5%">Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rowCounter=1;
                            while ($query_data = $rs->fetchArray()) {
                                echo "<tr>";
                                if (empty($query_data["approve"])) {
                                    if($hakUser == 90){
                                        if ($_SESSION["my"]->id == $query_data["kodeUser"] || $_SESSION["my"]->privilege == "GODMODE"|| $_SESSION["my"]->privilege == "ADMIN") {
                                            echo '<td><div class="dropdown">
                                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                            <i class="fa fa-fw fa-angle-double-down"></i></button>
                                            <ul class="dropdown-menu" style="border-color:#000;">';
                                            echo "<li><a style='cursor:pointer;' onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/SPK_detail&mode=edit&noSPK=" . md5($query_data["noSPK"]) . "'><i class='fa fa-edit'></i>&nbsp;Edit</a></li>";
                                            echo "<li><a onclick=\"if(confirm('Apakah anda yakin akan menghapus SPK ".$query_data["noSPK"]."?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&kode=" . ($query_data["noSPK"]) . "'}\" style='cursor:pointer;'><i class='fa fa-trash'></i>&nbsp;Delete</a></li>";
                                            echo "</ul></div></td>";
                                        }else{
                                            echo '<td><div class="dropdown">
                                            <button class="btn btn-danger dropdown-toggle" type="button" data-toggle="dropdown">
                                            <i class="fa fa-fw fa-exclamation"></i></button>
                                            <ul class="dropdown-menu" style="border-color:#000;">';
                                            echo "<li><a ><i class='fa fa-fw fa-remove'></i></i>Akun Tidak Punya Akses Edit</a></li>";
                                            echo "</ul></div></td>";
                                        }
                                    } else{
                                        echo '<td><div class="dropdown">
                                        <button class="btn btn-danger dropdown-toggle" type="button" data-toggle="dropdown">
                                        <i class="fa fa-fw fa-exclamation"></i></button>
                                        <ul class="dropdown-menu" style="border-color:#000;">';
                                        echo "<li><a><i class='fa fa-fw fa-money'></i>SPK Approve</a></li>";
                                        echo "</ul></div></td>";
                                    }

                                } else {
                                    echo '<td><div class="dropdown">
                                        <button class="btn btn-success dropdown-toggle" type="button" data-toggle="dropdown">
                                        <i class="fa fa-fw fa-check"></i></button>
                                        <ul class="dropdown-menu" style="border-color:#000;">';
                                        echo "<li><a style='cursor:pointer;' onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/SPK_detail&mode=edit&noSPK=" . md5($query_data["noSPK"]) . "'><i class='fa fa-edit'></i>&nbsp;Edit</a></li>";
                                        echo "<li><a style='cursor:pointer;' onclick=location.href=location.href='pdf/pdf_SPK.php?&noSPK=" . md5($query_data["noSPK"]) . "'><i class='fa fa-fw fa-money'></i>SPK Approve</a></li>";
                                        echo "</ul></div></td>";
                                }
                                echo "<td><a onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/SPKreview_detail&mode=addNote&noSPK=" . md5($query_data["noSPK"])."'>
                                <button type='button' class='btn btn-block btn-info'>".($query_data["noSPK"])."</button></a></td>";
                                echo "<td><button type='button' class='btn btn-block btn-default'>" . tgl_ind($query_data["tanggal"]) . "</button></td>";
                                echo "<td>" . ($query_data["nama_cust"]) . "</td>";
                                echo "<td>" . $query_data["kn"] . ", ". $query_data["pn"] ."</td>";
                                $kel = '';
                                if ($query_data["plafon"] == 0){
                                    $kel = 'Full';
                                }else if ($query_data["plafon"] == 1){
                                    $kel = 'Tanpa Plafon';
                                }else{
                                    $kel = 'Waterproof';
                                }
                                $dt = '';
                                if ($query_data["dt"] != 0){
                                    $dt = ', DT : '.$query_data["dt"];
                                }
                                $spek = 'MODEL : '.strtoupper($query_data["model"]).', D: '.$query_data["d"].', T : '.$query_data["t"].$dt.', '.strtoupper($kel);
                                echo "<td>" . $spek ."</td>";
                                echo "<td>" . strtoupper($query_data["kodeUser"]) . "</td>";
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
