<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/gudang_list";
require_once( './config.php' );
global $dbLink;
//Periksa hak user pada modul/menu ini
$judulMenu = 'Oder';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}

//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    require_once("./class/c_gudang.php");
    $tmpgudang = new c_gudang;

//Jika Mode Ubah/Edit
    if (isset($_POST["txtMode"]) == "Edit") {
        $pesan = $tmpgudang->edit($_POST);
    }

//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpgudang->delete($_GET["nobeli"]);
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
        $("#example3").DataTable({
          "autoWidth": false
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $(".select2").select2();
        $('#tglTransaksi').daterangepicker({ 
            locale: { format: 'DD-MM-YYYY' } 
        });
        $('.datepicker').datepicker({
            autoclose: true
        });
        $("#btnlbrg").click(function(){ 
            $("#mylBarang").modal({backdrop: 'static'});
        });
        $("#example2").DataTable({
            responsive: true,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $("#example1").DataTable({
            responsive: true,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        
    });
</script>

<!-- End of Script Tanggal -->
<section class="content-header">
    <h1>
        Gudang
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">Gudang</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Main row -->
    <div class="row">
        <section class="col-sm-4">
            <!-- TO DO List -->
            <div class="box box-primary">
                <!-- /.box-header -->
                <div class="box-body"><center>
                    <div class="input-group input-group-sm">
                        <span class="input-group-btn">
                            <button type="button" id="btnpengajuan" class="btn btn-primary btn-flat" 
                            <?php
                            echo "onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/pengajuan_detail&mode=add'";
                            ?>
                            ><i class="fa fa-minus"> </i> Pengajuan</button>
                        </span>
                        <span class="input-group-btn">
                            <button type="button" id="btnlbrg" class="btn btn-primary btn-flat"><i class="fa fa-plus"> </i> Barang</button>
                        </span>
                        <span class="input-group-btn">
                            <button type="button" id="btnpengajuan" class="btn btn-primary btn-flat"  <?php
                            echo "onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/retur_detail&mode=add'";
                            ?>
                            ><i class="fa fa-plus"> </i> Retur</button>
                        </span>
                    </div></center>
                </div>
                <!-- /.box-body -->
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
        <section class="col-lg-12 connectedSortable">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#pengajuan" data-toggle="tab">Pengajuan</a></li>
                    <li><a href="#retur" data-toggle="tab">Retur</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="pengajuan">
                        <div class="box box-primary">
                            <?php
                            $q = "SELECT * FROM `aki_bkeluar` bk left join aki_dbkeluar dbk on bk.nobkeluar=dbk.nobkeluar WHERE 1 group by bk.nobkeluar";
                            $rs = new MySQLPagedResultSet($q, 50, $dbLink);
                            ?>
                            <div class="box-body">
                                <div class="wrappert">
                                    <style type="text/css">
                                        .psymbol{
                                            text-align: center;
                                        }
                                        .pdone{
                                            background-color: #ff0000;
                                        }
                                        .pprogress{
                                            background-color: #a6a6a6;
                                        }
                                    </style>
                                    <table id="example1" class="table table-bordered display nowrap" width="100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>No Pengajuan</th>
                                                <th>Pemohon</th>
                                                <th>Tanggal</th>
                                                <th>Kode/Proyek</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $rowCounter=1;
                                            while ($query_data = $rs->fetchArray()) {
                                                echo "<tr>";
                                                echo "<td>" . $rowCounter . "</td>";
                                                echo '<td onclick=editbrg("'.$query_data["nobkeluar"].'")>'. $query_data["nobkeluar"] .'</td>';
                                                echo "<td>".$query_data["cust"]."</td>";
                                                echo "<td>" . date('d/m/Y', strtotime($query_data["tgl_bkeluar"])) . "</td>";
                                                echo "<td>".$query_data["kodeproyek"]."</td>";
                                                echo "<td><a class='btn btn-default btn-sm' href='".$_SERVER['PHP_SELF']."?page=view/pengajuan_detail&mode=edit&nobkeluar=" . md5($query_data["nobkeluar"])."'><i class='fa fa-fw fa-pencil color-black'></i></a>";
                                                echo "<a class='btn btn-default btn-sm' onclick=\"if(confirm('Apakah anda yakin akan menghapus data Order ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&nobeli=" . md5($query_data["nobeli"]) . "'}\" style='cursor:pointer;'><i class='fa fa-fw fa-trash'></i></a>";
                                                echo "</tr>";
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
                    </div>
                    <div class="tab-pane" id="retur">
                        <div class="box box-primary">
                            <?php
                            $q = "SELECT * FROM `aki_bretur` bk left join aki_dbretur dbk on bk.nobretur=dbk.nobretur WHERE 1 group by bk.nobretur";
                            $rs = new MySQLPagedResultSet($q, 50, $dbLink);
                            ?>
                            <div class="box-body">
                                <div class="wrappert">
                                    <style type="text/css">
                                        .psymbol{
                                            text-align: center;
                                        }
                                        .pdone{
                                            background-color: #ff0000;
                                        }
                                        .pprogress{
                                            background-color: #a6a6a6;
                                        }
                                    </style>
                                    <table id="example2" class="table table-bordered display nowrap">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>No Retur</th>
                                                <th>Pemohon</th>
                                                <th>Tanggal</th>
                                                <th>Kode/Proyek</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $rowCounter=1;
                                            while ($query_data = $rs->fetchArray()) {
                                                echo "<tr>";
                                                echo "<td>" . $rowCounter . "</td>";
                                                echo '<td onclick=editbrg("'.$query_data["nobretur"].'")>'. $query_data["nobretur"] .'</td>';
                                                echo "<td>".$query_data["cust"]."</td>";
                                                echo "<td>" . date('d/m/Y', strtotime($query_data["tgl_bretur"])) . "</td>";
                                                echo "<td>".$query_data["kodeproyek"]."</td>";
                                                echo "<td><a class='btn btn-default btn-sm' href='".$_SERVER['PHP_SELF']."?page=view/retur_detail&mode=edit&nobretur=" . md5($query_data["nobretur"])."'><i class='fa fa-fw fa-pencil color-black'></i></a>";
                                                echo "<a class='btn btn-default btn-sm' onclick=\"if(confirm('Apakah anda yakin akan menghapus data Retur ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&nobretur=" . md5($query_data["nobretur"]) . "'}\" style='cursor:pointer;'><i class='fa fa-fw fa-trash'></i></a>";
                                                echo "</tr>";
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
                    </div>
                </div>
            </div>
        </section>
    </div>
</section>
<div class="modal fade" id="mylBarang" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <form action="index2.php?page=view/po_list" method="post" name="frmpo" onSubmit="return validasiForm(this);">
                <input type='hidden' name='txtMode' value='Addbrg'>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">List Barang <label id="labelclr"></label></h4>

                </div>
                <div class="modal-body">
                    <table id="example3" class="table table-bordered table-hover dataTable dtr-inline"width="100%" >
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th width="100px;">Nama</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                                <th>Golongan</th>
                                <th>Jenis</th>
                                <th>Lokasi</th>
                                <th>TglBeli</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q = "SELECT b.*,db.*,bl.tgl_beli,sum(qty) as stok FROM `aki_barang` b left join aki_dbeli db on b.kode=db.kode_barang left join aki_beli bl on db.nobeli=bl.nobeli group by b.kode";
                            $rs = new MySQLPagedResultSet($q, 50, $dbLink);
                            $rowCounter=1;
                            while ($query_data = $rs->fetchArray()) {
                                echo "<tr>";
                                echo '<td onclick=editbrg("'.$query_data["kode"].'")>'. $query_data["kode"] .'</td>';
                                echo "<td>" . $query_data["nama"] ."</td>";
                                echo "<td>" . strtoupper($query_data["astok"]+$query_data["stok"]) . "</td>";
                                echo "<td>" . $query_data["satuan"] ."</td>";
                                echo "<td>" . $query_data["golongan"] ."</td>";
                                echo "<td>" . $query_data["jenis"] ."</td>";
                                echo "<td>" . $query_data["lokasi"] ."</td>";
                                echo "<td>" . date('d/m/Y', strtotime($query_data["tgl_beli"])) ."</td>";
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
            </form>
        </div>
    </div>
</div>