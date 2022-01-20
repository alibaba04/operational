<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/kk_list";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Kontrak Kerja';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}

//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    require_once("./class/c_kk.php");
    $tmpkk = new c_kk;

//Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpkk->add($_POST);
    }

//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpkk->edit($_POST);
    }

//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpkk->delete($_GET["kode"]);
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
        KONTRAK KERJA
        <small>List KK</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">KK</li>
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
                    <h3 class="box-title">Search KK </h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form name="frmCariJurnalMasuk" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>"autocomplete="off">
                        <input type="hidden" name="page" value="<?php echo $curPage; ?>">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="noKK" id="noKK" placeholder="Cari . . . ."
                            <?php
                            if (isset($_GET["noKK"])) {
                                echo("value='" . $_GET["noKK"] . "'");
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
                        <a href="<?php echo $_SERVER['PHP_SELF']."?page=html/kk_detail&mode=add";?>"><button type="button" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add KK</button></a>
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
                if(isset($_GET["noKK"]) ){
                    $noKK = secureParam($_GET["noKK"], $dbLink2);
                    $snum = secureParam($_GET["noKK"], $dbLink2)." : ";
                    if ($noKK)
                        $filter = $filter . " AND p.name LIKE '%" . $noKK . "%'  or s.nama_cust LIKE '%" . $noKK . "%'  or s.noKK LIKE '%" . $noKK . "%'  or k.name LIKE '%" . $noKK . "%'";
                }else{
                    $filter = '';
                }
                $filter2 = '';
                if ($_SESSION['my']->privilege == 'SALES') {
                    $filter2 =  " AND s.kodeUser='".$_SESSION['my']->id."' ";
                }
            //database
                $q = "SELECT kk.*,dk.* ,p.name as pn,k.name as kn FROM aki_kk kk right join aki_dkk dk on kk.noKK=dk.noKK left join aki_user u on kk.kodeUser=u.kodeUser  ";
                $q.= "left join provinsi p on kk.provinsi=p.id LEFT join kota k on kk.kota=k.id ";
                $q.= "WHERE 1=1 and kk.aktif=1  " .$filter2. $filter ;
                $q.= " group by kk.noKK ORDER BY kk.idKk desc ";
            //Paging
                $rs = new MySQLPagedResultSet($q, 50, $dbLink2);
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
                                <th style="width: 1%">Approve</th>
                                <th style="width: 20%">No KK</th>
                                <th style="width: 10%">Date</th>
                                <th style="width: 10%">Client</th>
                                <th style="width: 20%">Address</th>
                                <th style="width: 30%">Information</th>
                                <th style="width: 15%">Operator</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rowCounter=1;
                            while ($query_data = $rs->fetchArray()) {
                                echo "<tr>";
                                if ($query_data["approve_koperational"]!='-') {
                                    echo "<td><center><i class='fa fa-fw fa-check'></i></center></td>";
                                }else{
                                    echo "<td></td>";
                                }
                                
                                echo "<td><a onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/kkreview_detail&mode=addNote&noKK=" . md5($query_data["noKK"])."'>
                                <button type='button' class='btn btn-block btn-info'>".($query_data["noKk"])."</button></a></td>";
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
