<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/proyek_list";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Proyek';
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
        $("#example1").DataTable({
            "scrollX": true,
            "buttons": ["copy", "csv", "excel"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });

</script>

<!-- End of Script Tanggal -->
<section class="content-header">
    <h1>
        Instalasi
        <small>Instalasi Qoobah</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">Instalasi</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
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
                 $q = "SELECT spk.*,kk.*, dkk.*,p.*,k.name as lokasi FROM aki_spk spk left join aki_kk kk on spk.nokk=kk.noKk right join aki_dkk dkk on kk.noKk=dkk.noKk left join aki_tabel_proyek p on spk.noproyek=p.noproyek left join kota k on kk.kota=k.id  WHERE spk.noproyek!='-' and spk.aktif=1 GROUP by spk.noproyek ORDER BY kk.noKk desc";
            //Paging
                $rs = new MySQLPagedResultSet($q, 50, $dbLink2);
                ?>
                <div class="box-header">
                    <?php
                    //if ($_SESSION['my']->privilege == 'ADMIN') {
                        echo '';
                    //}
                    ?>
                </div>
                <div class="box-body">
                    <a href="pdf/pdf_absensi.php?"><button class="btn btn-info pull-right"><i class="ion ion-ios-download"></i> PDF</button></a>
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

                        <table id="example1" class="table table-bordered display nowrap" >
                            <thead>
                                <tr>
                                    <th style="width: 1%">#</th>
                                    <th width="15%" >Project Name </th>
                                    <th width="30%" >Team Members</th>
                                    <th width="15%">Project Progress</th>
                                    <th width="10%">Status</th>
                                    <th width="20%">Dateline</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rowCounter=1;
                                while ($query_data = $rs->fetchArray()) {
                                    $status='';
                                    echo "<tr ";
                                    if ($query_data["kproyek"]=='patas') {
                                        echo "style='background-color: #ffff00;'";
                                    }
                                    if ($query_data["pk_makara"]!='0000-00-00' && $query_data["ekspedisi_out"]=='0000-00-00') {
                                        echo "style='background-color: #8ea9db;'";
                                    }
                                    echo "onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/proyek_detail&mode=edit&nospk=" . md5($query_data["nospk"])."'>";
                                    echo "<td>" . ($rowCounter) . "</td>";
                                    echo "<td>" . ($query_data["noproyek"]) . "</td>";
                                    
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
