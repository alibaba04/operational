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
            locale: { format: 'DD-MM-YYYY' } 
        });
        $('.datepicker').datepicker({
            autoclose: true
        });
    });

    function termin(){
        $("#myModal").modal({backdrop: 'static'});
    }
</script>

<!-- End of Script Tanggal -->
<section class="content-header">
    <h1>
        Proyek
        <small>Proyek Qoobah</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">Proyek</li>
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
                    <h3 class="box-title">Search Proyek </h3>
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
                        <div class="box-footer clearfix">
                            <?php
                            if ($hakUser==90 or $hakUser==80){
                                ?>
                                <a href="<?php echo $_SERVER['PHP_SELF']."?page=html/proyek_detail&mode=add";?>"><button type="button" class="btn btn-primary pull-right"><i class="fa fa-pencil-square-o"></i> Update</button></a>
                                <?php
                            }
                            ?>
                        </div>
                    </form>
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
                    $noSPK = secureParam($_GET["noSPK"], $dbLink2);
                    $snum = secureParam($_GET["noSPK"], $dbLink2)." : ";
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
                $q = "SELECT spk.*,kk.*, dkk.* FROM aki_spk spk left join aki_kk kk on spk.nokk=kk.noKk right join aki_dkk dkk on kk.noKk=dkk.noKk WHERE spk.noproyek!='-' and spk.aktif=1 GROUP by spk.noproyek ORDER BY kk.noKk desc ";
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
                <style type="text/css">
                    table{
                        background-color: white;
                        text-align:center;
                    }
                    th{
                        text-align:center;
                    }
                    .viewt {
                        margin: auto;
                        width: 600px;
                    }

                    .wrappert {
                        position: relative;
                        overflow: auto;
                        white-space: nowrap;
                    }

                    .sticky-col {
                        position: -webkit-sticky;
                        position: sticky;
                        background-color: white;
                    }

                    .first-col {
                        width: 100px;
                        min-width: 100px;
                        max-width: 100px;
                        left: 0px;
                    }

                    .second-col {
                        width: 150px;
                        min-width: 200px;
                        max-width: 200px;
                        left: 100px;
                    }
                    #ptgl{
                        min-width: 155px;
                    }
                </style>
                <div class="box-body viewt" style="width: 100%;overflow-x: scroll;background-color: white;">
                    <div class="wrappert">
                        <table class="table table-bordered table-striped table-hover" >
                            <thead>
                                <tr>
                                    <th class='sticky-col first-col'>Kode Proyek</th>
                                    <th class='sticky-col second-col'>No SPK</th>
                                    <th>Customer</th>
                                    <th>Masjid</th>
                                    <th>Detail</th>
                                    <th>Bahan</th>
                                    <th>Status</th>
                                    <th>Tgl SPK</th>
                                    <th>Sales</th>
                                    <!-- <th colspan='5'>Termin</th>
                                    <th colspan='2'>Ekspedisi</th>
                                    <th colspan='3'>Pemasangan</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rowCounter=1;
                                while ($query_data = $rs->fetchArray()) {
                                    echo "<tr>";
                                    echo "<td class='sticky-col first-col'><a onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/proyek_detail&mode=edit&nospk=" . md5($query_data["nospk"])."'>
                                    <button type='button' class='btn btn-block btn-info'>".($query_data["noproyek"])."</button></a></td>";
                                    echo "<td class='sticky-col second-col'>" . ($query_data["nospk"]) . "</td>";
                                    echo "<td>" . $query_data["nama_cust"] . "</td>";
                                    echo "<td>" . $query_data["masjid"] . "</td>";
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
                                    $spek = 'D : '.$query_data["d"].', T : '.$query_data["t"].$dt.', '.ucfirst($query_data["model"]).', '.ucfirst($kel).', qty : '.$query_data["jumlah"];
                                    echo "<td>" . $spek . "</td>";
                                    echo "<td>" . $query_data["bahan"] . "</td>";
                                    echo "<td>" . $query_data["status_proyek"] . "</td>";
                                    echo "<td><center>" . tgl_ind($query_data["tgl_spk"]) . "</center></td>";
                                    echo "<td>" . $query_data["sales"] . "</td>";
                                    /*echo "<td><div class='form-check'><input type='checkbox' id='checkt1' class='custom-control-input'/><label class='form-check-label' for='checkt1'> Sudah T1</label></div></td>";
                                    echo "<td><div class='form-check'><input type='checkbox' id='checkt1' class='custom-control-input'/><label class='form-check-label' for='checkt2'> Sudah T2</label></div></td>";
                                    echo "<td><div class='form-check'><input type='checkbox' id='checkt1' class='custom-control-input'/><label class='form-check-label' for='checkt3'> Sudah T3</label></div></td>";
                                    echo "<td><div class='form-check'><input type='checkbox' id='checkt1' class='custom-control-input'/><label class='form-check-label' for='checkt4'> Sudah T4</label></div></td>";
                                    echo "<td><div class='form-check'><label class='form-check-label' for='checkt1'> Lunas</label></div></td>";
                                    echo '<td id="ptgl"><div class="input-group date">
                                    <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right datepicker" id="tglEkspedisi">
                                    </div></td>';
                                    echo "<td><div class='form-check'><input type='text' id='driver' class='custom-control-input' placeholder='Driver'></div></td>";
                                    echo '<td id="ptgl"><div class="input-group date">
                                    <div class="input-group-addon" >
                                    <label class="form-check-label">In</label> 
                                    </div>
                                    <input type="text" class="form-control pull-right datepicker" id="inPemasangan">
                                    </div></td>';
                                    echo '<td id="ptgl"><div class="input-group date">
                                    <div class="input-group-addon">
                                    <label class="form-check-label">Out</label>
                                    </div>
                                    <input type="text" class="form-control pull-right datepicker" id="outPemasangan">
                                    </div></td>';
                                    echo "<td><div class='form-check'><input type='text' id='ketuatim' class='custom-control-input' placeholder='Leader'/></div></td>";*/
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
            </div>
        </section>
    </div>
</section>
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Warna <label id="labelclr"></label></h4>
                <input type="hidden" class="form-control" id="txtnomer" value="">
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-lg-6">
                        <label class="control-label" for="chkPPN">Warna</label>
                        <input type="text" class="form-control" id="color1" value="-" placeholder="">
                    </div>
                    <div class="col-lg-6">
                        <label class="control-label" for="chkPPN">Kode</label>
                        <input type="text" class="form-control" id="kcolor1" value="-" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-6">
                        <input type="text" class="form-control" id="color2" value="-" placeholder="">
                    </div>
                    <div class="col-lg-6">
                        <input type="text" class="form-control" id="kcolor2" value="-" placeholder="-">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-6">
                        <input type="text" class="form-control" id="color3" value="-" placeholder="">
                    </div>
                    <div class="col-lg-6">
                        <input type="text" class="form-control" id="kcolor3" value="-" placeholder="-">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-6">
                        <input type="text" class="form-control" id="color4" value="-" placeholder="">
                    </div>
                    <div class="col-lg-6">
                        <input type="text" class="form-control" id="kcolor4" value="-" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-6">
                        <input type="text" class="form-control" id="color5" value="-" placeholder="">
                    </div>
                    <div class="col-lg-6">
                        <input type="text" class="form-control" id="kcolor5" value="-" placeholder="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary" value="Add"  id="btncolor">
            </div>
        </div>
    </div>
</div>