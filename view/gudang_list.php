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
          "autoWidth": false,
          "buttons": ["copy", "csv", "excel"]
        }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');
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
        $("#example4").DataTable({
            responsive: true,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel"]
        }).buttons().container().appendTo('#example4_wrapper .col-md-6:eq(0)');
        $("#example2").DataTable({
            responsive: true,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel"]
        }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
        $("#example1").DataTable({
            responsive: true,
            "autoWidth": false,
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
                            ><i class="fa fa-minus"> </i> Keluar</button>
                        </span>
                        <span class="input-group-btn">
                            <button type="button" id="btnlbrg" class="btn btn-primary btn-flat"><i class="fa fa-plus"> </i> Barang</button>
                        </span>
                        <span class="input-group-btn">
                            <button type="button" id="btnso" class="btn btn-primary btn-flat"  <?php
                            echo "onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/so_detail&mode=add'";
                            ?>
                            ><i class="fa fa-plus"> </i> SO</button>
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
                    <li class="active"><a href="#pengajuan" data-toggle="tab">Keluar</a></li>
                    <!-- <li><a href="#retur" data-toggle="tab">Retur</a></li>
                    <li><a href="#so" data-toggle="tab">SO</a></li> -->
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="pengajuan">
                        <div class="box box-primary">
                            <?php
                            $q = "SELECT bk.nobretur as no,cust,tgl_bretur as tgl,kodeproyek,'re' as ket FROM `aki_bretur` bk left join aki_dbretur dbk on bk.nobretur=dbk.nobretur WHERE 1 group by bk.nobretur union all SELECT bk.nobkeluar as no,cust,tgl_bkeluar as tgl,kodeproyek,'ke' as ket FROM `aki_bkeluar` bk left join aki_dbkeluar dbk on bk.nobkeluar=dbk.nobkeluar WHERE 1 group by bk.nobkeluar union all SELECT bk.nobso as no,'-' as cust,tgl_bso as tgl,'-' as kodeproyek,'so' as ket FROM `aki_bso` bk left join aki_dbso dbk on bk.nobso=dbk.nobso WHERE 1 group by bk.nobso";
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
                                                <th>No Transaksi</th>
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
                                                echo '<td onclick=editbrg("'.$query_data["no"].'")>'. $query_data["no"] .'</td>';
                                                echo "<td>".$query_data["cust"]."</td>";
                                                echo "<td>" . date('d/m/Y', strtotime($query_data["tgl"])) . "</td>";
                                                echo "<td>".$query_data["kodeproyek"]."</td>";
                                                if ($query_data["ket"] == 're') {
                                                    echo "<td><a class='btn btn-default btn-sm' href='".$_SERVER['PHP_SELF']."?page=view/retur_detail&mode=edit&nobretur=" . md5($query_data["no"])."'><i class='fa fa-fw fa-pencil color-black'></i></a>";
                                                }else if($query_data["ket"] == 'ke'){
                                                    echo "<td><a class='btn btn-default btn-sm' href='".$_SERVER['PHP_SELF']."?page=view/pengajuan_detail&mode=edit&nobkeluar=" . md5($query_data["no"])."'><i class='fa fa-fw fa-pencil color-black'></i></a>";
                                                }else{
                                                    echo "<td><a class='btn btn-default btn-sm' href='".$_SERVER['PHP_SELF']."?page=view/so_detail&mode=edit&nobso=" . md5($query_data["no"])."'><i class='fa fa-fw fa-pencil color-black'></i></a>";
                                                }
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
                    <!-- <div class="tab-pane" id="retur">
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
                    <div class="tab-pane" id="so">
                        <div class="box box-primary">
                            <?php
                            $q = "SELECT * FROM `aki_bso` bk left join aki_dbso dbk on bk.nobso=dbk.nobso WHERE 1 group by bk.nobso";
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
                                    <table id="example4" class="table table-bordered display nowrap">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>No SO</th>
                                                <th>Tanggal</th>
                                                <th>User</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $rowCounter=1;
                                            while ($query_data = $rs->fetchArray()) {
                                                echo "<tr>";
                                                echo "<td>" . $rowCounter . "</td>";
                                                echo '<td onclick=editbrg("'.$query_data["nobretur"].'")>'. $query_data["nobso"] .'</td>';
                                                echo "<td>" . date('d/m/Y', strtotime($query_data["tgl_bso"])) . "</td>";
                                                echo "<td>".$query_data["kodeUser"]."</td>";
                                                echo "<td><a class='btn btn-default btn-sm' href='".$_SERVER['PHP_SELF']."?page=view/so_detail&mode=edit&nobso=" . md5($query_data["nobso"])."'><i class='fa fa-fw fa-pencil color-black'></i></a>";
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
                    </div> -->
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
                                <th>Jenis</th>
                                <th>Lokasi</th>
                                <th>Rak</th>
                                <th>TglBeli</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q = "SELECT b.*,masuk,keluar,retur,so,tgl_beli FROM `aki_barang` b left join (SELECT kode_barang,sum(db.qty) as masuk,nobeli FROM aki_dbeli as db group by db.kode_barang) as db on b.kode=db.kode_barang left join (SELECT kode_barang,sum(dk.qty) as keluar FROM aki_dbkeluar as dk group by dk.kode_barang) as dk on b.kode=dk.kode_barang left join (SELECT kode_barang,sum(dr.qty) as retur FROM aki_dbretur as dr group by dr.kode_barang) as dr on b.kode=dr.kode_barang left join (SELECT kode_barang,sum(dso.qty) as so FROM aki_dbso as dso group by dso.kode_barang) as dso on b.kode=dso.kode_barang left join aki_beli as beli on db.nobeli=beli.nobeli group by b.kode ORDER BY `dso`.`so`  DESC";
                            $rs = new MySQLPagedResultSet($q, 500, $dbLink);
                            $rowCounter=1;
                            while ($query_data = $rs->fetchArray()) {
                                echo "<tr>";
                                echo '<td onclick=editbrg("'.$query_data["kode"].'")>'. $query_data["kode"] .'</td>';
                                echo "<td>" . $query_data["nama"] ."</td>";
                                echo "<td>" . strtoupper($query_data["astok"]+$query_data["masuk"]-$query_data["keluar"]+$query_data["retur"]+($query_data["so"])) . "</td>";
                                echo "<td>" . $query_data["satuan"] ."</td>";
                                echo "<td>" . $query_data["jenis"] ."</td>";
                                echo "<td>" . $query_data["rack"] ."</td>";
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