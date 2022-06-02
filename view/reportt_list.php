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
                $q = "SELECT spk.*,kk.*, dkk.*,p.* FROM aki_spk spk left join aki_kk kk on spk.nokk=kk.noKk right join aki_dkk dkk on kk.noKk=dkk.noKk left join aki_proyek p on spk.noproyek=p.noproyek WHERE spk.noproyek!='-' and spk.aktif=1 GROUP by spk.noproyek ORDER BY kk.noKk desc";
                $rs = new MySQLPagedResultSet($q, 50, $dbLink2);
                ?>
                <div class="box-header">
                    <?php
                    if ($_SESSION['my']->privilege == 'ADMIN') {
                        echo '<a href="class/c_exportexcel.php?"><button class="btn btn-info pull-right"><i class="ion ion-ios-download"></i> Export Excel</button></a>';
                    }
                    ?>
                </div>
                <div class="box-body">
                    <div class="wrappert">
                        <table id="example1" class="table table-bordered display nowrap" >
                            <thead>
                                <tr>
                                    <th rowspan="2">KODE</th>
                                    <th rowspan="2">MASJID</th>
                                    <th rowspan="2">LOKASI</th>
                                    <th rowspan="2">TYPE</th>
                                    <th rowspan="2">DLL</th>
                                    <th rowspan="2">DATELINE</th>
                                    <th rowspan="2">STATUS</th>
                                    <th rowspan="2">RKG</th>
                                    <th colspan="2">HLW</th>
                                    <th rowspan="2">MAL</th>
                                    <th rowspan="2">GMB</th>
                                    <th rowspan="2">GAL/ENM</th>
                                    <th colspan="2">CAT</th>
                                    <th colspan="2">MKR</th>
                                    <th colspan="2">PKG</th>
                                </tr>
                                <tr>
                                    <th>PN</th>
                                    <th>PL</th>
                                    <th>PN</th>
                                    <th>MK</th>
                                    <th>MK</th>
                                    <th>RG</th>
                                    <th>PN</th>
                                    <th>MK</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rowCounter=1;
                                while ($query_data = $rs->fetchArray()) {
                                    echo "<tr>";
                                    echo "<td>" . ($query_data["noproyek"]) . "</td>";
                                    echo "<td>" . $query_data["masjid"] . "</td>";
                                  
                                    echo "<td>" . $query_data["alamat"] . "</td>";
                                    echo "<td>" . $query_data["model"] . "</td>";
                                    echo "<td>" . 'dll' . "</td>";
                                    echo "<td>" . $query_data["tgl_deadline"] . "</td>";
                                    echo "<td>" . $query_data["status"] . "</td>";
                                    if ($query_data["rangka_out"]!='0000-00-00') {
                                        echo "<td style='background-color: #ff0000;'>" . '&#10004' . "</td>";
                                    }elseif ($query_data["rangka_out"]=='0000-00-00' && $query_data["rangka_in"]!='0000-00-00') {
                                        echo "<td style='background-color: #a6a6a6;'>" . '&#9203' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["hollow_out"]!='0000-00-00') {
                                        echo "<td style='background-color: #ff0000;'>" . '&#10004' . "</td>";
                                    }elseif ($query_data["hollow_out"]=='0000-00-00' && $query_data["hollow_in"]!='0000-00-00') {
                                        echo "<td style='background-color: #a6a6a6;'>" . '&#9203' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["hl_plafon"]!='0000-00-00') {
                                        echo "<td style='background-color: #ff0000;'>" . '&#10004' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["mal_out"]!='0000-00-00') {
                                        echo "<td style='background-color: #ff0000;'>" . '&#10004' . "</td>";
                                    }elseif ($query_data["mal_out"]=='0000-00-00' && $query_data["mal_in"]!='0000-00-00') {
                                        echo "<td style='background-color: #a6a6a6;'>" . '&#9203' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["gambarp_out"]!='0000-00-00') {
                                        echo "<td style='background-color: #ff0000;'>" . '&#10004' . "</td>";
                                    }elseif ($query_data["gambarp_out"]=='0000-00-00' && $query_data["gambarp_in"]!='0000-00-00') {
                                        echo "<td style='background-color: #a6a6a6;'>" . '&#9203' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    
                                    if ($query_data["bahan_out"]!='0000-00-00') {
                                        echo "<td style='background-color: #ff0000;'>" . '&#10004 ' .$query_data["bahan"]. "</td>";
                                    }elseif ($query_data["bahan_out"]=='0000-00-00' && $query_data["bahan_in"]!='0000-00-00') {
                                        echo "<td style='background-color: #a6a6a6;'>" . '&#9203 ' .$query_data["bahan"]. "</td>";
                                    }else{
                                        echo "<td>".$query_data["bahan"]."</td>";
                                    }
                                    if ($query_data["cat_out"]!='0000-00-00') {
                                        echo "<td style='background-color: #ff0000;'>" . '&#10004' . "</td>";
                                    }elseif ($query_data["cat_out"]=='0000-00-00' && $query_data["cat_in"]!='0000-00-00') {
                                        echo "<td style='background-color: #a6a6a6;'>" . '&#9203' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["catmakara"]!='0000-00-00') {
                                        echo "<td style='background-color: #ff0000;'>" . '&#10004' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["makara_out"]!='0000-00-00') {
                                        echo "<td style='background-color: #ff0000;'>" . '&#10004' . "</td>";
                                    }elseif ($query_data["makara_out"]=='0000-00-00' && $query_data["makara_in"]!='0000-00-00') {
                                        echo "<td style='background-color: #a6a6a6;'>" . '&#9203' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["rangka_ga"]!='0000-00-00') {
                                        echo "<td style='background-color: #ff0000;'>" . '&#10004' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["packing_out"]!='0000-00-00') {
                                        echo "<td style='background-color: #ff0000;'>" . '&#10004' . "</td>";
                                    }elseif ($query_data["packing_out"]=='0000-00-00' && $query_data["packing_in"]!='0000-00-00') {
                                        echo "<td style='background-color: #a6a6a6;'>" . '&#9203' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["pk_makara"]!='0000-00-00') {
                                        echo "<td style='background-color: #ff0000;'>" . '&#10004' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
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