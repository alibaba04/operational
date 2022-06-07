<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/reportt_list";
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
        if ($("#txtJenis").val() != 1) {
            $(".btnexcel").prop('disabled', true);
        }
        //$("#myModal").modal({backdrop: 'static'});
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
        var gol = '';
        if ($("#txtGol").val()==2) {
            gol = 'Manajemen';
        }else if($("#txtGol").val()==3){
            gol = 'Produksi';
        }
        if ($("#txtJenis").val() == 1) {
            location.href='pdf/pdf_absensi.php?&month='+$("#txtBulan").val()+'&years='+$("#txtTahun").val()+'&gol='+gol;
        }else if($("#txtJenis").val() == 2){
            location.href='pdf/pdf_absensi2.php?&years='+$("#txtTahun").val()+'&gol='+gol;
        }else if($("#txtJenis").val() == 3){
            location.href='pdf/pdf_absensi3.php?&years='+$("#txtTahun").val()+'&gol='+gol;
        }else if($("#txtJenis").val() == 4){
            location.href='pdf/pdf_absensi4.php?&years='+$("#txtTahun").val()+'&gol='+gol;
        }else if($("#txtJenis").val() == 5){
            location.href='pdf/pdf_absensi5.php?&years='+$("#txtTahun").val()+'&gol='+gol;
        }else if($("#txtJenis").val() == 6){
            location.href='pdf/pdf_absensi6.php?&years='+$("#txtTahun").val()+'&gol='+gol;
        }
        else if($("#txtJenis").val() == 7){
            location.href='pdf/pdf_absensi8.php?&month='+$("#txtBulan").val()+'&years='+$("#txtTahun").val()+'&gol='+gol+'&day='+$("#txthari").val();
        }else if($("#txtJenis").val() == 8){
            location.href='pdf/pdf_absensi7.php?&month='+$("#txtBulan").val()+'&years='+$("#txtTahun").val()+'&gol='+gol+'&day='+$("#txthari").val();
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
                    <option value="1">Laporan Produksi</option>
                    <option value="2">Absensi Per Tahun</option>
                    <option value="3">Izin Per Tahun</option>
                    <option value="4">Izin 1/2 Hari Per Tahun</option>
                    <option value="5">Cuti Per Tahun</option>
                    <option value="6">Dinas Per Tahun</option>
                    <option value="7">Insentif</option>
                    <option value="8">Insentif Detail</option>
                </select><label></label>
                <div class="input-group" id="dday"><span class="input-group-addon">Hari Efektif</span>
                    <input type="number" name="txthari" id="txthari" class="form-control" placeholder="0" >
                </div>
                <select class="form-control" name="txtTahun" id="txtTahun">
                    <?php
                        for ($i = 0; $i < 5; ) {
                            $date_str = date('Y', strtotime('-'.$i." years"));
                            echo "<option value=".$date_str .">".$date_str ."</option>";
                            $i++;
                        }
                    ?>
                </select>
                <select class="form-control" name="txtBulan" id="txtBulan">
                    <option value='01'>January</option>
                    <option value='02'>February</option>
                    <option value='03'>March</option>
                    <option value='04'>April</option>
                    <option value='05'>May</option>
                    <option value='06'>June</option>
                    <option value='07'>July</option>
                    <option value='08'>August</option>
                    <option value='09'>September</option>
                    <option value='10'>October</option>
                    <option value='11'>November</option>
                    <option value='12'>December</option>
                    <!-- <?php
                        for ($i = 0; $i < 12;$i++ ) {
                            $month_val = date('m', strtotime($i." months"));
                            $month_str = date('F', strtotime($i." months"));
                            echo "<option value=".$month_val .">".$month_str ."</option>";
                        } 
                    ?> -->
                </select><br>
                <select class="form-control" name="txtGol" id="txtGol" >
                    <option value="1">All</option>
                    <option value="2">Kantor</option>
                    <option value="3">Produksi</option>
                </select>
            </div>
            <div class="modal-footer">
            <?php
                echo '<div class="input-group input-group-sm col-lg-1 pull-left"><a><button class="btn btn-info pull-right btnexcel" onclick="toexcel()"><i class="ion ion-ios-download"></i> Export Excel</button></a></div><div><span></span></div>';
                echo '<div class="input-group input-group-sm col-lg-1 pull-right"><a><button class="btn btn-info pull-right" onclick="topdf()"><i class="ion ion-ios-download"></i> Download</button></a></div>';
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
                <!-- <div class="box-body">
                    <div class="wrappert">
                        <table id="example1" class="table table-bordered table-striped table-hover" >
                            <thead>
                                <tr>
                                    <th class='sticky-col first-col'>Kode Proyek</th>
                                    <th class='sticky-col second-col'>No SPK</th> 
                                    <th>KODE</th>
                                    <th>No SPK</th>
                                    <th>Customer</th>
                                    <th>Masjid</th>
                                    <th>Detail</th>
                                    <th>Bahan</th>
                                    <th>Status</th>
                                    <th>Tgl SPK</th>
                                    <th>Sales</th>
                                    <!-- <th colspan='5'>Termin</th>
                                    <th colspan='2'>Ekspedisi</th>
                                    <th colspan='3'>Pemasangan</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rowCounter=1;
                                while ($query_data = $rs->fetchArray()) {
                                    echo "<tr>";
                                    echo "<td><a onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/proyek_detail&mode=edit&nospk=" . md5($query_data["nospk"])."'>".($query_data["noproyek"])."</a></td>";
                                    echo "<td>" . ($query_data["nospk"]) . "</td>";
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
                </div>  -->
            </div>
        </section>
    </div>
</section>