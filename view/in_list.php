<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/in_list";
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

    require_once("./class/c_order.php");
    $tmporder = new c_order;

//Jika Mode Ubah/Edit
    if (isset($_POST["txtMode"]) == "Edit") {
        $pesan = $tmporder->edit($_POST);
    }

//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmporder->delete($_GET["nobeli"]);
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
        $(".select2").select2();
        $('#tglTransaksi').daterangepicker({ 
            locale: { format: 'DD-MM-YYYY' } 
        });
        $('.datepicker').datepicker({
            autoclose: true
        });
        $("#example1").DataTable({
            responsive: true,
            "autoWidth": false,
            "scrollX": true,
            "buttons": ["copy", "csv", "excel"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        
    });
</script>

<!-- End of Script Tanggal -->
<section class="content-header">
    <h1>
        Barang Masuk
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">Barang Masuk</li>
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
                          <?php
                          echo '<button type="button" id="btnpo" class="btn btn-primary btn-flat"';
                           echo "onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/in_detail&mode=add'>";
                          echo '<i class="fa fa-plus"> </i> Barang Masuk</button>';
                          ?>
                        </span>
                    </div></center>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </section>
        <section class="col-lg-2">
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
            <div class="box box-primary">
                <?php
            //database
                 $q = "SELECT b.*,supp.* FROM `aki_beli` b left join aki_supplier supp on b.id_supplier=supp.kodesupp left join aki_po po on b.nopo=po.nopo WHERE b.aktif=0 order by b.id desc";
            //Paging
                $rs = new MySQLPagedResultSet($q, 50, $dbLink);
                ?>
                <div class="box-header">
                </div>
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
                                    <th>Kode</th>
                                    <th>Jenis</th>
                                    <th>Tgl Barang Masuk</th>
                                    <th>Pemohon</th>
                                    <th>Supplier</th>
                                    <th>Total Harga</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rowCounter=1;
                                while ($query_data = $rs->fetchArray()) {
                                    echo "<tr>";
                                    echo "<td>" . $rowCounter . "</td>";
                                    echo "<td>" . $query_data["nobeli"] . "</td>";
                                    echo "<td>Stok</td>";
                                    echo "<td>" . date('d/m/Y', strtotime($query_data["tgl_beli"])) . "</td>";
                                    echo "<td>" . $query_data["cust"] . "</td>";
                                    echo "<td>" . $query_data["supplier"] . "</td>";
                                    echo "<td>" . number_format($query_data["totalharga"]) . "</td>";
                                    echo "<td><a class='btn btn-default btn-sm' href='".$_SERVER['PHP_SELF']."?page=view/in_detail&mode=edit&nobeli=" . md5($query_data["nobeli"])."'><i class='fa fa-fw fa-pencil color-black'></i></a>";
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
        </section>
    </div>
</section>
