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
                                    <th rowspan="2">KODE</th>
                                    <th rowspan="2">MASJID</th>
                                    <th rowspan="2">LOKASI</th>
                                    <th rowspan="2">BHN</th>
                                    <th rowspan="2">JENIS</th>
                                    <th rowspan="2">TYPE</th>
                                    <th colspan="4"><center>DIMENSI</center></th>
                                    <th rowspan="2">JUMLAH<br>PANEL</th>
                                    <th rowspan="2">RING/TIANG</th>
                                    <th rowspan="2">QTY</th>
                                    <th rowspan="2">PLAFON</th>
                                    <th rowspan="2">MAKARA</th>
                                    <th rowspan="2">DLL</th>
                                    <th rowspan="2">DATELINE</th>
                                    <th rowspan="2">RKG</th>
                                    <th colspan="2"><center>HLW</center></th>
                                    <th rowspan="2">MAL</th>
                                    <th rowspan="2">GMB</th>
                                    <th rowspan="2">GAL/ENM</th>
                                    <th colspan="2"><center>CAT</center></th>
                                    <th colspan="2"><center>MKR</center></th>
                                    <th colspan="2"><center>PKG</center></th>
                                    <th rowspan="2">STATUS</th>
                                </tr>
                                <tr>
                                    <th>D</th>
                                    <th>DT</th>
                                    <th>T</th>
                                    <th>L</th>
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
                                    $status='';
                                    echo "<tr ";
                                    if ($query_data["kproyek"]=='patas') {
                                        echo "style='background-color: #ffff00;'";
                                    }
                                    if ($query_data["pk_makara"]!='0000-00-00' && $query_data["ekspedisi_out"]=='0000-00-00') {
                                        echo "style='background-color: #8ea9db;'";
                                    }
                                    echo "onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/proyek_detail&mode=edit&nospk=" . md5($query_data["nospk"])."'>";
                                    echo "<td>" . ($query_data["noproyek"]) . "</td>";
                                    echo "<td>" . $query_data["masjid"] . "</td>";
                                    echo "<td>" . $query_data["lokasi"] . "</td>";
                                    echo "<td>" . substr($query_data['bahan'],0,1) . "</td>";
                                    echo "<td>" . $query_data["kubah"] . "</td>";
                                    echo "<td>" . $query_data["model"] . "</td>";
                                    echo "<td>" . $query_data["d"] . "</td>";
                                    echo "<td>" . $query_data["dt"] . "</td>";
                                    echo "<td>" . $query_data["t"] . "</td>";
                                    echo "<td>" . $query_data["luas"] . "</td>";
                                    echo "<td>" . $query_data["jml_panel"] . "</td>";
                                    echo "<td>" . $query_data["jml_tiang"] . "</td>";
                                    echo "<td>" . $query_data["jumlah"] . "</td>";
                                    if ($query_data["plafon"]==0) {
                                        echo "<td>Awan</td>";
                                    }else{
                                        echo "<td>" . $query_data["plafon"] . "</td>";
                                    }
                                    echo "<td>" . $query_data["makara"] . "</td>";
                                    echo "<td>" . 'dll' . "</td>";
                                    echo "<td>" . date("d-m-Y", strtotime($query_data["tgl_deadline"])) . "</td>";
                                    if ($query_data["rangka_out"]!='0000-00-00') {
                                        $status='Rangka '. '&#10004';
                                        echo "<td class='psymbol pdone'>" . '&#10004' . "</td>";
                                    }elseif ($query_data["rangka_out"]=='0000-00-00' && $query_data["rangka_in"]!='0000-00-00') {
                                        $status='Rangka '. '&#9203';
                                        echo "<td class='psymbol pprogress'>" . '&#9203' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["hollow_out"]!='0000-00-00') {
                                        $status='Hollow '. '&#10004';
                                        echo "<td class='psymbol pdone'>" . '&#10004' . "</td>";
                                    }elseif ($query_data["hollow_out"]=='0000-00-00' && $query_data["hollow_in"]!='0000-00-00') {
                                        $status='Hollow '. '&#9203';
                                        echo "<td class='psymbol pprogress'>" . '&#9203' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["hl_plafon"]!='0000-00-00') {
                                        $status='Hollow Plafon '. '&#10004';
                                        echo "<td class='psymbol pdone'>" . '&#10004' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["mal_out"]!='0000-00-00') {
                                        $status='Mal '. '&#10004';
                                        echo "<td class='psymbol pdone'>" . '&#10004' . "</td>";
                                    }elseif ($query_data["mal_out"]=='0000-00-00' && $query_data["mal_in"]!='0000-00-00') {
                                        $status='Mal '. '&#9203';
                                        echo "<td class='psymbol pprogress'>" . '&#9203' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["gambarp_out"]!='0000-00-00') {
                                        $status='Gambar Panel '. '&#10004';
                                        echo "<td class='psymbol pdone'>" . '&#10004' . "</td>";
                                    }elseif ($query_data["gambarp_out"]=='0000-00-00' && $query_data["gambarp_in"]!='0000-00-00') {
                                        $status='Gambar Panel '. '&#9203';
                                        echo "<td class='psymbol pprogress'>" . '&#9203' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    
                                    if ($query_data["bahan_out"]!='0000-00-00') {
                                        $status=$query_data["bahan"]. ' &#10004';
                                        echo "<td class='psymbol pdone'>" . '&#10004 ' .$query_data["bahan"]. "</td>";
                                    }elseif ($query_data["bahan_out"]=='0000-00-00' && $query_data["bahan_in"]!='0000-00-00') {
                                        $status=$query_data["bahan"]. ' &#9203';
                                        echo "<td class='psymbol pprogress'>" . '&#9203 ' .$query_data["bahan"]. "</td>";
                                    }else{
                                        echo "<td>".$query_data["bahan"]."</td>";
                                    }
                                    if ($query_data["cat_out"]!='0000-00-00') {
                                        $status='Cat '. ' &#10004';
                                        echo "<td class='psymbol pdone'>" . '&#10004' . "</td>";
                                    }elseif ($query_data["cat_out"]=='0000-00-00' && $query_data["cat_in"]!='0000-00-00') {
                                        $status='Cat '. ' &#9203';
                                        echo "<td class='psymbol pprogress'>" . '&#9203' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["catmakara"]!='0000-00-00') {
                                        $status='Cat '. ' &#10004';
                                        echo "<td class='psymbol pdone'>" . '&#10004' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["makara_out"]!='0000-00-00') {
                                        $status='Makara '. ' &#10004';
                                        echo "<td class='psymbol pdone'>" . '&#10004' . "</td>";
                                    }elseif ($query_data["makara_out"]=='0000-00-00' && $query_data["makara_in"]!='0000-00-00') {
                                        $status='Makara '. ' &#9203';
                                        echo "<td class='psymbol pprogress'>" . '&#9203' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["rangka_ga"]!='0000-00-00') {
                                        $status='RG '. ' &#10004';
                                        echo "<td class='psymbol pdone'>" . '&#10004' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["packing_out"]!='0000-00-00') {
                                        $status='Packing '. ' &#10004';
                                        echo "<td class='psymbol pdone'>" . '&#10004' . "</td>";
                                    }elseif ($query_data["packing_out"]=='0000-00-00' && $query_data["packing_in"]!='0000-00-00') {
                                        $status='Packing '. ' &#9203';
                                        echo "<td class='psymbol pprogress'>" . '&#9203' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["pk_makara"]!='0000-00-00') {
                                        $status='Siap Kirim';
                                        echo "<td class='psymbol pdone'>" . '&#10004' . "</td>";
                                    }else{
                                        echo "<td></td>";
                                    }
                                    if ($query_data["ekspedisi_out"]!='0000-00-00') {
                                        $status='Pengiriman'. ' &#10004';
                                    }elseif ($query_data["ekspedisi_out"]=='0000-00-00' && $query_data["ekspedisi_in"]!='0000-00-00') {
                                        $status='Pengiriman '. ' &#9203';
                                    }
                                    if ($query_data["pemasangan_out"]!='0000-00-00') {
                                        $status='Pemasangan'. ' &#10004';
                                    }elseif ($query_data["pemasangan_out"]=='0000-00-00' && $query_data["pemasangan_in"]!='0000-00-00') {
                                        $status='Pemasangan'. ' &#9203';
                                    }
                                    echo "<td>" . $status . "</td>";
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