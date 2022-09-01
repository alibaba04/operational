<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/po_list";
require_once( './config.php' );
global $dbLink;
//Periksa hak user pada modul/menu ini
$judulMenu = 'PO';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}

//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    require_once("./class/c_supp.php");
    $tmpsupp = new c_supp;
    require_once("./class/c_barang.php");
    $tmpbrg = new c_brg;
    require_once("./class/c_po.php");
    $tmppo = new c_po;

//Jika Mode Tambah/Add
    if (($_POST["txtMode"]) == "Addsupp") {
        $pesan = $tmpsupp->addsupp($_POST);
    }else if (($_POST["txtMode"]) == "Addbrg") {
        $pesan = $tmpbrg->addbrg($_POST);
    }

//Jika Mode Ubah/Edit
    if (($_POST["txtMode"]) == "Editsupp") {
        $pesan = $tmpsupp->edit($_POST);
    }else if(($_POST["txtMode"]) == "Editbrg") {
        $pesan = $tmpbrg->edit($_POST);
    }

//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmppo->delete($_GET["nopo"]);
    }else if($_GET["txtMode"] == "Accop") {
        $pesan = $tmppo->accop($_GET["nopo"]);
    }else if($_GET["txtMode"] == "Cancelop") {
        $pesan = $tmppo->cancelop($_GET["nopo"]);
    }else if($_GET["txtMode"] == "Accfa") {
        $pesan = $tmppo->accfa($_GET["nopo"]);
    }else if($_GET["txtMode"] == "Cancelfa") {
        $pesan = $tmppo->cancelfa($_GET["nopo"]);
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
    function editbrg($kode){
        $.post("function/ajax_function.php",{ fungsi: "editbrg",kode:$kode},function(data)
        {
            document.getElementById("nokodeb").setAttribute("readonly", ""); 
            $("#nokodeb").val(data.kode);
            $("#txtnamab").val(data.nama);
            $("#txtsatuan").val(data.satuan);
            $("#txtbjenis").val(data.jenis);
            $("#txtlok").val(data.lokasi);
            $("#txtrack").val(data.rack);
            $("#txtMode").val('Editbrg');
        },"json");
        document.getElementById("txtastok").disabled = true;
        $("#myBarang").modal({backdrop: 'static'});
    }
    function editsupp($kode){
        $.post("function/ajax_function.php",{ fungsi: "editsupp",kode:$kode},function(data)
        {
            $("#txtksupp").val(data.kodesupp);
            $("#txtsupp").val(data.supplier);
            $("#txtalamat").val(data.alamat);
            $("#txtsjenis").val(data.jenis);
            $("#txtnohp").val(data.nomor);
            $("#txtnameb").val(data.kontak);
            $("#txtnorek").val(data.norek);
            $("#txtMode").val('Editsupp');
            $("#cbobank option:first").remove();
            var x = document.getElementById("cbobank");
            $('#cbobank').prepend($('<option val="'+data.nrek+'"selected>'+data.nrek+'</option>')); 
        },"json");
        $("#mySupp").modal({backdrop: 'static'});
    }
    function checkkode() {
      var $kode = $("#nokodeb").val();
      $.post("function/ajax_function.php",{ fungsi: "checknkode",nkode:$kode},function(data)
        {
            alert('data');
        },"json");
    }
    
    $(function () {
        $("#example2").DataTable({
          "autoWidth": false
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $("#example3").DataTable({
           responsive: true,
           "autoWidth": false
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $(".select2").select2();
        $("#btnlsupp").click(function(){ 
            $("#mylSupp").modal({backdrop: 'static'});
        });
        $("#btnlbrg").click(function(){ 
            $("#mylBarang").modal({backdrop: 'static'});
        });
        $("#btnbrg").click(function(){ 
            document.getElementById("nokodeb").readOnly = false; 
            $("#txtMode").val('Addbrg');
            $("#myBarang").modal({backdrop: 'static'});
        });
        $("#btnsupp").click(function(){ 
            $.post("function/ajax_function.php",{ fungsi: "getkodesupp"},function(data)
            {
                document.getElementById("txtksupp").value = 'supp'+data;
            },"json");
            $("#txtMode").val('Addsupp');
            $("#mySupp").modal({backdrop: 'static'});
        });

        $('#tglTransaksi').daterangepicker({ 
            locale: { format: 'DD-MM-YYYY' } 
        });
        $('.datepicker').datepicker({
            autoclose: true
        });
        $("#example1").DataTable({
            responsive: true,
            "scrollX": true,
            "buttons": ["copy", "csv", "excel"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<!-- End of Script Tanggal -->
<section class="content-header">
    <h1>
        Purchase Order
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">PO</li>
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
                        <?php
                            if ($hakUser == 90) {
                        ?>
                        <span class="input-group-btn">
                            <button type="button" id="btnlsupp" class="btn btn-primary btn-flat"><i class="fa fa-plus"> </i> Supplier</button>
                        </span>
                        <span class="input-group-btn">
                            <button type="button" id="btnlbrg" class="btn btn-primary btn-flat"><i class="fa fa-plus"> </i> Barang</button>
                        </span>
                        <span class="input-group-btn">
                          <?php
                          echo '<button type="button" id="btnpo" class="btn btn-primary btn-flat"';
                          echo "onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/po_detail&mode=add'>";
                          echo '<i class="fa fa-plus"> </i> PO</button>';
                          ?>
                        </span>
                        <?php
                            }
                        ?>
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
                 $q = "SELECT po.*,b.tgl_beli,supp.*  FROM `aki_po` po left join aki_beli b on po.nopo=b.nopo left join aki_supplier supp on po.id_supplier=supp.kodesupp WHERE po.aktif=0 order by po.id desc";
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
                                    <th>No PO</th>
                                    <th>Pemohon</th>
                                    <th>Supplier</th>
                                    <th>Total Harga</th>
                                    <th>Tanggal PO</th>
                                    <th>Tanggal Beli</th>
                                    <th>Acc OP</th>
                                    <th>Acc FA</th>
                                    <?php 
                                    if ($hakUser == 90) {
                                        echo "<th>Action</th>";
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rowCounter=1;
                                while ($query_data = $rs->fetchArray()) {
                                    echo "<tr>";
                                    echo "<td>" . $rowCounter . "</td>";
                                    echo "<td><a class='btn btn-default btn-sm pull-right' href='pdf/pdf_po.php?&nopo=" . md5($query_data["nopo"])."' style='cursor:pointer;'>" . $query_data["nopo"] . "</a></td>";
                                    echo "<td>" . $query_data["cust"] . "</td>";
                                    echo "<td>" . $query_data["supplier"] . "</td>";
                                    echo "<td>" . number_format($query_data["totalharga"]) . "</td>";
                                    echo "<td>" . date('d/m/Y', strtotime($query_data["tgl_po"])) . "</td>";
                                    echo "<td>" ; if (isset($query_data["tgl_beli"])) {
                                        echo date('d/m/Y', strtotime($query_data["tgl_beli"])) . "</td>";
                                    }
                                    
                                    if ($_SESSION["my"]->privilege=='koperational' || $_SESSION["my"]->privilege=='GODMODE' ) {
                                        echo "<td><center>";
                                        if ($query_data["acc_op"]==1) {
                                            echo "<a class='btn btn-default btn-sm' onclick=\"if(confirm('Cancel Permohonan Belanja? ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Cancelop&nopo=" . md5($query_data["nopo"]) . "'}\" style='cursor:pointer;'><i class='fa fa-fw fa-check'></a>";
                                        }else{
                                            echo "<a class='btn btn-default btn-sm' onclick=\"if(confirm('Approve Permohonan Belanja? ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Accop&nopo=" . md5($query_data["nopo"]) . "'}\" style='cursor:pointer;'></a>";
                                        }
                                        echo "</center></td>";
                                    }else{
                                        echo "<td><center>";
                                        if ($query_data["acc_op"]==1) {
                                            echo "<i class='fa fa-fw fa-check'>";
                                        }
                                        echo "</center></td>";
                                    }
                                    
                                    if ($_SESSION["my"]->privilege=='kfinance' || $_SESSION["my"]->privilege=='GODMODE' ) {
                                        echo "<td><center>";
                                        if ($query_data["acc_fa"]==1) {
                                            echo "<a class='btn btn-default btn-sm' onclick=\"if(confirm('Cancel Permohonan Belanja? ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Cancelfa&nopo=" . md5($query_data["nopo"]) . "'}\" style='cursor:pointer;'><i class='fa fa-fw fa-check'></a>";
                                        }else{
                                            echo "<a class='btn btn-default btn-sm' onclick=\"if(confirm('Approve Permohonan Belanja? ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Accfa&nopo=" . md5($query_data["nopo"]) . "'}\" style='cursor:pointer;'></a>";
                                        }
                                        echo "</center></td>";
                                    }else{
                                        echo "<td><center>";
                                        if ($query_data["acc_fa"]==1) {
                                            echo "<i class='fa fa-fw fa-check'>";
                                        }
                                        echo "</center></td>";
                                    }
                                    echo "<td>";
                                    if ($hakUser == 90 && $query_data["acc_fa"]!=1 && $query_data["acc_op"]!=1) {
                                        echo "<a class='btn btn-default btn-sm' href='".$_SERVER['PHP_SELF']."?page=view/po_detail&mode=edit&nopo=" . md5($query_data["nopo"])."'><i class='fa fa-fw fa-pencil color-black'></i></a>";
                                        echo "<a class='btn btn-default btn-sm' onclick=\"if(confirm('Apakah anda yakin akan menghapus data PO ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&nopo=" . md5($query_data["nopo"]) . "'}\" style='cursor:pointer;'><i class='fa fa-fw fa-trash'></i></a>";
                                    }
                                    echo "</td></tr>";
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
<form action="index2.php?page=view/po_list" method="post" name="frmpo" onSubmit="return validasiForm(this);">
    <input type='hidden' name='txtMode' id='txtMode' value=''>
<div class="modal fade" id="mylBarang" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">List Barang <label id="labelclr"></label></h4>
            </div>
            <div class="modal-body">
              <table id="example2" class="table table-bordered table-hover dataTable dtr-inline"width="100%" >
                <thead>
                  <tr>
                    <th>id</th>
                    <th>Kode</th>
                    <th width="100px;">Nama</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Jenis</th>
                    <th>Lokasi</th>
                    <th>Rak</th>
                    <th>TglBeli</th>
                    <th>HargaBeli</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $q = "SELECT b.*,masuk,keluar,retur,so,tgl_beli FROM `aki_barang` b left join (SELECT kode_barang,sum(db.qty) as masuk,nobeli FROM aki_dbeli as db group by db.kode_barang) as db on b.kode=db.kode_barang left join (SELECT kode_barang,sum(dk.qty) as keluar FROM aki_dbkeluar as dk group by dk.kode_barang) as dk on b.kode=dk.kode_barang left join (SELECT kode_barang,sum(dr.qty) as retur FROM aki_dbretur as dr group by dr.kode_barang) as dr on b.kode=dr.kode_barang left join (SELECT kode_barang,sum(dso.qty) as so FROM aki_dbso as dso group by dso.kode_barang) as dso on b.kode=dso.kode_barang left join aki_beli as beli on db.nobeli=beli.nobeli group by b.kode ORDER BY `dso`.`so`  DESC";
                  $rs = new MySQLPagedResultSet($q,500, $dbLink);
                  $rowCounter=1;
                  while ($query_data = $rs->fetchArray()) {
                    echo "<tr>";
                    echo "<td>" . $query_data["id"] ."</td>";
                    echo '<td onclick=editbrg("'.$query_data["kode"].'") style="cursor:pointer;">'. $query_data["kode"] .'</td>';
                    echo "<td>" . $query_data["nama"] ."</td>";
                    echo "<td>" . strtoupper($query_data["astok"]+$query_data["masuk"]-$query_data["keluar"]+$query_data["retur"]+($query_data["so"])) . "</td>";
                    echo "<td>" . $query_data["satuan"] ."</td>";
                    echo "<td>" . $query_data["jenis"] ."</td>";
                    echo "<td>" . $query_data["lokasi"] ."</td>";
                    echo "<td>" . $query_data["rack"] ."</td>";
                    echo "<td>" . date('d/m/Y', strtotime($query_data["tgl_beli"])) ."</td>";
                    echo "<td>Rp " . number_format($query_data["harga"]) ."</td>";
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
            <div class="modal-footer">
              <button type="button" id="btnbrg" class="btn btn-primary"><i class="fa fa-plus"> </i> Barang</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mylSupp" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">List Supplier <label id="labelclr"></label></h4>
            </div>
            <div class="modal-body">
              <table id="example3" class="table table-bordered table-hover dataTable dtr-inline" width="100%">
                <thead>
                  <tr>
                    <th>id</th>
                    <th>Kode</th>
                    <th>Supplier</th>
                    <th>Jenis</th>
                    <th>Alamat</th>
                    <th>NoHP</th>
                    <th>A.n</th>
                    <th>NoRekening</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $q = "SELECT * FROM `aki_supplier` ";
                  $rs = new MySQLPagedResultSet($q, 500, $dbLink);
                  $rowCounter=1;
                  while ($query_data = $rs->fetchArray()) {
                    echo "<tr>";
                    echo "<td>" . $query_data["id"] ."</td>";
                    echo '<td onclick=editsupp("'.$query_data["kodesupp"].'") style="cursor:pointer;">'. $query_data["kodesupp"] .'</td>';
                    echo "<td>" . $query_data["supplier"] ."</td>";
                    echo "<td>" . $query_data["jenis"] . "</td>";
                    echo "<td>" . $query_data["alamat"] . "</td>";
                    echo "<td>" . $query_data["nomor"] ."</td>";
                    echo "<td>" . $query_data["nrek"] ."</td>";
                    echo "<td>" . $query_data["norek"] ."</td>";
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
            <div class="modal-footer">
              <button type="button" id="btnsupp" class="btn btn-primary"><i class="fa fa-plus"> </i> Supplier</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mySupp" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Supplier <label id="labelclr"></label></h4>
            </div>
            <div class="modal-body">
                <div class="card-body">
                  <div class="form-group row">
                    <label for="nopo" class="col-sm-2 col-form-label">Kode Supplier</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="txtksupp" name="txtksupp" readonly >
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="txtsupp" class="col-sm-2 col-form-label">Supplier</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="txtsupp" name="txtsupp">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="txtalamat" class="col-sm-2 col-form-label">Alamat</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="txtalamat" name="txtalamat" ></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="txtsjenis" class="col-sm-2 col-form-label">Jenis</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="txtsjenis" name="txtsjenis" ></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="txtnohp" class="col-sm-2 col-form-label">No HP</label>
                    <div class="col-sm-10">
                      <input type="phone" class="form-control" id="txtnohp" name="txtnohp" >
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="txtbank" class="col-sm-2 col-form-label">Bank</label>
                    <div class="col-sm-10">
                        <select class="form-control select2" id="cbobank" name="cbobank" style="width: 100%;">
                          <option value="">--Nama Bank--</option>
                          <option value="Bank Mandiri">Bank Mandiri</option>
                          <option value="Bank Bukopin">Bank Bukopin</option>
                          <option value="Bank Danamon">Bank Danamon</option>
                          <option value="Bank Mega">Bank Mega</option>
                          <option value="Bank CIMB Niaga">Bank CIMB Niaga</option>
                          <option value="Bank Permata">Bank Permata</option>
                          <option value="Bank Sinarmas">Bank Sinarmas</option>
                          <option value="Bank QNB">Bank QNB</option>
                          <option value="Bank Lippo">Bank Lippo</option>
                          <option value="Bank UOB">Bank UOB</option>
                          <option value="Panin Bank">Panin Bank</option>
                          <option value="Citibank">Citibank</option>
                          <option value="Bank ANZ">Bank ANZ</option>
                          <option value="Bank Commonwealth">Bank Commonwealth</option>
                          <option value="Bank Maybank">Bank Maybank</option>
                          <option value="Bank Maspion">Bank Maspion</option>
                          <option value="Bank J Trust">Bank J Trust</option>
                          <option value="Bank QNB">Bank QNB</option>
                          <option value="Bank KEB Hana">Bank KEB Hana</option>
                          <option value="Bank Artha Graha">Bank Artha Graha</option>
                          <option value="Bank OCBC NISP">Bank OCBC NISP</option>
                          <option value="Bank MNC">Bank MNC</option>
                          <option value="Bank DBS">Bank DBS</option>
                          <option value="BCA">BCA</option>
                          <option value="BNI">BNI</option>
                          <option value="BRI">BRI</option>
                          <option value="BTN">BTN</option>
                          <option value="Bank DKI">Bank DKI</option>
                          <option value="Bank BJB">Bank BJB</option>
                          <option value="Bank BPD DIY">Bank BPD DIY</option>
                          <option value="Bank Jateng">Bank Jateng</option>
                          <option value="Bank Jatim">Bank Jatim</option>
                          <option value="Bank BPD Bali">Bank BPD Bali</option>
                          <option value="Bank Sumut">Bank Sumut</option>
                          <option value="Bank Nagari">Bank Nagari</option>
                          <option value="Bank Riau Kepri">Bank Riau Kepri</option>
                          <option value="Bank Sumsel Babel">Bank Sumsel Babel</option>
                          <option value="Bank Lampung">Bank Lampung</option>
                          <option value="Bank Jambi">Bank Jambi</option>
                          <option value="Bank Kalbar">Bank Kalbar</option>
                          <option value="Bank Kalteng">Bank Kalteng</option>
                          <option value="Bank Kalsel">Bank Kalsel</option>
                          <option value="Bank Kaltim">Bank Kaltim</option>
                          <option value="Bank Sulsel">Bank Sulsel</option>
                          <option value="Bank Sultra">Bank Sultra</option>
                          <option value="Bank BPD Sulteng">Bank BPD Sulteng</option>
                          <option value="Bank Sulut">Bank Sulut</option>
                          <option value="Bank NTB">Bank NTB</option>
                          <option value="Bank NTT">Bank NTT</option>
                          <option value="Bank Maluku">Bank Maluku</option>
                          <option value="Bank Papua">Bank Papua</option>
                        </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="txtnohp" class="col-sm-2 col-form-label">A.n</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="txtnameb" name="txtnameb" >
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="txtnorek" class="col-sm-2 col-form-label">No Rekening</label>
                    <div class="col-sm-10">
                      <input type="number" class="form-control" id="txtnorek" name="txtnorek">
                    </div>
                  </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" value="Add"  id="btnaddSupp" name="btnaddSupp">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myBarang" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Barang <label id="labelclr"></label></h4>
            </div>
            <div class="modal-body">
                <div class="card-body">
                  <div class="form-group row">
                    <label for="nokodeb" class="col-sm-2 col-form-label">Kode Barang</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="nokodeb" name="nokodeb" >
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="txtnamab" class="col-sm-2 col-form-label">Nama</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="txtnamab" name="txtnamab">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="txtsatuan" class="col-sm-2 col-form-label">Satuan</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="txtsatuan" name="txtsatuan">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="txtbjenis" class="col-sm-2 col-form-label">Jenis</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="txtbjenis" name="txtbjenis">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="txtlok" class="col-sm-2 col-form-label">Lokasi</label>
                    <div class="col-sm-10">
                        <input type="phone" class="form-control" id="txtlok" name="txtlok">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="txtrack" class="col-sm-2 col-form-label">No Rak</label>
                    <div class="col-sm-10">
                        <input type="phone" class="form-control" id="txtrack" name="txtrack">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="txtastok" class="col-sm-2 col-form-label">Stok Awal</label>
                    <div class="col-sm-10">
                      <input type="number"  class="form-control" id="txtastok" name="txtastok">
                    </div>
                  </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" value="Add"  id="btnaddbarang">
            </div>
        </div>
    </div>
</div>
</form>
