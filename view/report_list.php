<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/report_list";
error_reporting( error_reporting() & ~E_NOTICE );
error_reporting(E_ERROR | E_PARSE);
//Periksa hak user pada modul/menu ini
$judulMenu = 'Report';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User cannot access this page!');
    echo "</p>";
}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {
//Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
//Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Warning!!, please text to " . $mailSupport . " for support this error!.";
    }
    header("Location:index.php?page=$curPage&pesan=" . $pesan);
    exit;
}
?>
<script type="text/javascript" charset="utf-8">
    function myFchange() {
        if ($("#txtJenis").val()==2) {
            $("#txtBulan").prop('disabled', true);
        }else if($("#txtJenis").val()==3){
            $("#txtBulan").prop('disabled', true);
        }else{
            $("#txtBulan").prop('disabled', false);
        }
        if ($("#txtJenis").val()!=7) {
            $("#txthari").prop('disabled', true);
        }else{
            $("#txthari").prop('disabled', false);
        }
        if ($("#txtJenis").val() != 1) {
            $(".btnexcel").prop('disabled', true);
        }
        if ($("#txtJenis").val() == 1) {
            $(".btnexcel").prop('disabled', false);
        }
    }

    $(document).ready(function () {
        $('#tgl').daterangepicker({ 
            locale: { format: 'DD-MM-YYYY' } });
        if ($("#txtJenis").val() != 1) {
            $(".btnexcel").prop('disabled', true);
        }
        $("#myModal").modal({backdrop: 'static'});
        $(".select2").select2();
        $("#stanggal").datepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
        $('#btnClose').click(function(){
            location.href='index.php';
        });
        $("#example1").DataTable({
            "scrollX": true,
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
    function toexcel() {
        var gol = '';
        if ($("#txtGol").val()==2) {
            gol = 'Manajemen';
        }else if($("#txtGol").val()==3){
            gol = 'Produksi';
        }
        if ($("#txtJenis").val() == 1) {
            location.href='excel/export.php?&month='+$("#txtBulan").val()+'&years='+$("#txtTahun").val()+'&gol='+gol;
        }
    }
    function topdf() {
        if ($("#txtJenis").val() == 1) {
            location.href='pdf/pdf_lapwh.php?&brg='+$("#txtbrg").val()+'&tgl='+$("#tgl").val();
        }else if($("#txtJenis").val() == 2){
            location.href='pdf/pdf_lapin.php?&brg='+$("#txtbrg").val()+'&tgl='+$("#tgl").val();
        }else if($("#txtJenis").val() == 3){
            location.href='pdf/pdf_lapout.php?&brg='+$("#txtbrg").val()+'&tgl='+$("#tgl").val();
        }else if($("#txtJenis").val() == 4){
            location.href='pdf/pdf_lapretur.php?&brg='+$("#txtbrg").val()+'&tgl='+$("#tgl").val();
        }else if($("#txtJenis").val() == 5){
            location.href='pdf/pdf_lapso.php?&brg='+$("#txtbrg").val()+'&tgl='+$("#tgl").val();
        }   
    }
</script>
<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="btnClose">&times;</button>
                <h4 class="modal-title">Report</h4>
            </div>
            <div class="modal-body">
                <select class="form-control" name="txtJenis" id="txtJenis" onchange="myFchange()">
                    <option value="1">Laporan Gudang</option>
                    <option value="2">Laporan Barang Masuk</option>
                    <option value="3">Laporan Barang Keluar</option>
                    <option value="4">Laporan Barang Retur</option>
                    <option value="5">Laporan Stok Opname</option>
                </select><br>
                <div class="form-group">
                    <select class="form-control select2" name="txtbrg" id="txtbrg" style="width: 100%">
                        <option value="">Semua Barang</option>;
                        <?php 
                            $q = "SELECT * FROM `aki_barang`";
                            $listbrg = mysql_query($q, $dbLink);
                            while ($dbrg = mysql_fetch_array($listbrg)) {
                                echo '<option value="'.$dbrg['kode'].'">'.$dbrg['kode'].' - '.$dbrg['nama'].'</option>';
                            }
                        ?>
                    </select>
                </div>
                <input type="text" class="form-control" name="tgl" id="tgl" 
                <?php
                if (isset($_GET["tanggal"])) {
                    echo("value='" . $_GET["tgl"] . "'");
                }
                ?>
                onKeyPress="return handleEnter(this, event)" placeholder="Range Date">
            </div>
            <div class="modal-footer">
            <?php
                //echo '<div class="input-group input-group-sm col-lg-1 pull-left"><a><button class="btn btn-info pull-right btnexcel" onclick="toexcel()"><i class="ion ion-ios-download"></i> Export Excel</button></a></div><div><span></span></div>';
                echo '<div class="input-group input-group-sm col-lg-1 pull-right"><a><button class="btn btn-info pull-right" onclick="topdf()"><i class="ion ion-ios-download"></i> PDF</button></a></div>';
            ?>
            </div>
        </div>
    </div>
</div> 
<section class="content">
    <div class="row">
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
                $q = "SELECT spk.*,kk.*, dkk.*,p.* FROM aki_spk spk left join aki_kk kk on spk.nokk=kk.noKk right join aki_dkk dkk on kk.noKk=dkk.noKk left join aki_tabel_proyek p on spk.noproyek=p.noproyek WHERE spk.noproyek!='-' and spk.aktif=1 GROUP by spk.noproyek ORDER BY kk.noKk desc";
                $rs = new MySQLPagedResultSet($q, 50, $dbLink2);
                ?>
                <div class="box-header">
                    <?php
                    if ($_SESSION['my']->privilege == 'ADMIN') {
                        echo '<a href="class/c_exportexcel.php?"><button class="btn btn-info pull-right"><i class="ion ion-ios-download"></i> Export Excel</button></a>';
                    }
                    ?>
                </div>
            </div>
        </section>
    </div>
</section>