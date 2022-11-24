<?php
/* ==================================================
//=======  : Alibaba
==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/in_detail";
//Periksa hak user pada modul/menu ini
$judulMenu = 'Order';
$hakUser = getUserPrivilege($curPage);
if ($hakUser != 90) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";

}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {
    require_once("./class/c_order.php");
    $tmporder = new c_order;
//Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmporder->add($_POST);
    }
//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmporder->edit($_POST);
    }
//Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
//Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Gagal simpan data, mohon hubungi " . $mailSupport . " untuk keterangan lebih lanjut terkait masalah ini.";
    }
    header("Location:index.php?page=view/in_list&pesan=" . $pesan);
    exit;
}
?>
<script>
    $(document).ready(function () {
        var link = window.location.href;
        var res = link.match(/mode=edit/g);
        if (res != 'mode=edit') {
            if (link.match(/nopo=/g)) {
                $("#mydPo").modal('hide');
            }else{
                $("#mydPo").modal({backdrop: 'static'});
                $("#createpo").click(function(){ 
                    location.href=link+"&nopo="+ $("#snopo").val();
                });
            }
        }
    });
    $(function () {
        $("[data-mask]").inputmask();
        $(".select2").select2();
        $("#txttglpo").datepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
        /*$('#txttglpo').datepicker({ 
             locale: { format: 'DD-MM-YYYY' }
        });*/
        var x = document.getElementById("btnsavepo");
        if ($("#totalh").val()=='0' ) {
            x.setAttribute("disabled", ""); 
        }
        
    });
    function kodeb(tcounter) {
        $.post("function/ajax_function.php",{ fungsi: "ambilkodeb" },function(data)
        {
            for(var i=0; i<274; ++i) {
                var x = document.getElementById("txtkodeb_"+tcounter);
                var option = document.createElement("option");
                option.text = data[i].text;
                option.value = data[i].val;
                x.add(option);
            }
            
        },"json"); 
    }
    function subtotal($tcounter) {
        var qty = $("#txtqty_"+$tcounter).val(); 
        var harga = $("#txtHarga_"+$tcounter).val();
        var h1 = harga.replace(/\D+/g, "");
        var tharga = parseFloat(qty)*parseFloat(h1);
        tharga = tharga.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        $("#txtTotal_"+$tcounter).val(tharga); 
        total();
    }
    function cekval($tcounter) {
        var qty = $("#txtqty_"+$tcounter).val(); 
        var qtym = $("#txtqtym_"+$tcounter).val();
        var sisa = qty-qtym;
        if (sisa <0) {
            alert('Barang masuk melebihi Pemesanan');
            $("#txtqtym_"+$tcounter).onfocus();
        }
    }
    function selectbrg(tcounter) {
        var x = $("#txtkodeb_"+tcounter).val();
        $.post("function/ajax_function.php",{ fungsi: "getsatuan",kode:x },function(data)
        {
            $("#txtSatuan_"+tcounter).val(data.satuan);
            $("#txtcqty_"+tcounter).val(data.stok);
        },"json");
    }
    function deleteRow(r) {
        var param = r.split("_");
        $("#txtTotal_"+param[1]).val(0);
        document.getElementById(r).style.display = "none";
        $("#chkAddJurnal_"+param[1]).val('');
        $("#txtSatuan_"+param[1]).val('-');
        total();
    }
    function total() {
        var jml = $("#jumaddOrder").val();
        var total = 0
        for (var i = 0; i < jml; i++) {
            var chkboxid = $("#chkAddJurnal_"+i);
            if (chkboxid.val() != null) {
                var tharga = $("#txtTotal_"+i).val();
                var h1 = tharga.replace(/\D+/g, "");
                total += parseInt(h1);
            }
        }
        total = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        $("#txttotalh").val(total); 
        var x = document.getElementById("btnsavepo");
        x.removeAttribute("disabled"); 
    }


    function addJurnal(){    
        tcounter = $("#jumaddOrder").val();
        //kodeb(tcounter);
        var ttable = document.getElementById("kendali");
        var trow = document.createElement("TR");
        trow.setAttribute("id", "trid_"+tcounter);

        //Kolom 1 Checkbox
        var td = document.createElement("TD");
        td.setAttribute("align","center");
        td.setAttribute('onclick','chkadddetail('+tcounter+');');
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><a class="btn btn-default btn-sm" onclick=deleteRow("trid_'+tcounter+'")><i class="fa fa-fw fa-trash"></i></a><input type="hidden" class="minimal" name="chkAddJurnal_'+tcounter+'" id="chkAddJurnal_'+tcounter+'" value="1" checked /><input  type="hidden" name="txtjbrg_'+tcounter+'" id="txtjbrg_'+tcounter+'" value="'+$("#txtjbrg").val()+'"></div>';
        trow.appendChild(td);

        //Kolom 2 Barang 
        if ($("#txtjbrg").val() == 'penunjang') {
            var td = document.createElement("TD");
            td.setAttribute("align","left");
            td.style.verticalAlign = 'top';
            td.innerHTML+='<div class="form-group"><input name="txtkodeb_'+tcounter+'" id="txtkodeb_'+tcounter+'" class="form-control" style="text-align:left" required></div>';
            trow.appendChild(td);
        }else{
            kodeb(tcounter);
            var td = document.createElement("TD");
            td.setAttribute("align","left");
            td.style.verticalAlign = 'top';
            td.innerHTML+='<div class="form-group"><select class="form-control select2" name="txtkodeb_'+tcounter+'" id="txtkodeb_'+tcounter+'" onchange="selectbrg('+tcounter+')"><option>- Nama Barang- </option></select></div>';
            trow.appendChild(td);
        }

        //Kolom 3 Qty
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input name="txtqty_'+tcounter+'" id="txtqty_'+tcounter+'" class="form-control" value="1" style="text-align:right" onkeydown="return numbersonly(this, event);" onfocusout="subtotal('+tcounter+')" required></div>';
        trow.appendChild(td);

        //Kolom 4 Satuan
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input name="txtSatuan_'+tcounter+'" id="txtSatuan_'+tcounter+'" class="form-control" style="text-align:right" required></div>';
        trow.appendChild(td);

        //Kolom 5 Harga
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input name="txtHarga_'+tcounter+'" id="txtHarga_'+tcounter+'" class="form-control" " onkeydown="return numbersonly(this, event);"  value="0" onfocusout="subtotal('+tcounter+')" style="text-align:right" required></div>';
        trow.appendChild(td);

        //Kolom 6 tharga
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input name="txtTotal_'+tcounter+'" id="txtTotal_'+tcounter+'" class="form-control" " onkeydown="return numbersonly(this, event);" value="0" style="text-align:right" readonly></div>';
        trow.appendChild(td);

        ttable.appendChild(trow);
        tcounter = $("#jumaddOrder").val();
        $("#jumaddOrder").val(parseInt($("#jumaddOrder").val())+1); 
        $(".select2").select2();
    }
</script>
<style type="text/css">
    .select2{
        width: 100%;
    }
</style>
<section class="content-header">
    <h1>
        Barang Masuk
        <small>Detail Barang Masuk</small>
    </h1>
</section>
<form action="index2.php?page=view/in_detail" method="post" name="frmKasKeluarDetail" onSubmit="return validasiForm(this);" autocomplete="off">
    <section class="col-lg-6">
        <div class="box box-primary">
            <div class="box-body">
                <?php 
                    $nomerpo = ''; 
                    if ($_GET["mode"] == "edit") {
                        echo "<input type='hidden' name='txtMode' value='Edit'>";
                    }else{
                        echo "<input type='hidden' name='txtMode' value='Add'>";
                    }
                ?>
                <div class="form-group" >
                    <div class="input-group">
                        <div class="input-group-addon">
                            <label class="control-label" for="txtnopo">Kode</label>
                        </div>
                        <?php
                        if ($_GET["mode"] == "edit") {
                            echo "<input type='hidden' name='txtMode' value='Edit'>";
                            if (isset($_GET["nobeli"])){
                                $nobeli = secureParam($_GET["nobeli"], $dbLink);
                            }else{
                                $nobeli = "";
                            }
                            $q= "SELECT * FROM `aki_beli` b left join aki_dbeli db on b.nobeli=db.nobeli left join aki_supplier supp on b.id_supplier=supp.kodesupp WHERE md5(b.nobeli)='".$nobeli."'";
                            $rsTemp = mysql_query($q, $dbLink);
                            if ($dataOrder = mysql_fetch_array($rsTemp)) {
                                $noPo = $dataOrder["nobeli"];
                                echo "<input type='hidden' name='nopo' value='" . $dataOrder["nobeli"] . "'>";
                            }
                        }else{
                            $nopo = "";
                            if (isset($_GET["nopo"])){
                                $nopo = secureParam($_GET["nopo"], $dbLink);
                            }
                            $q = "SELECT * FROM `aki_po` po left join aki_supplier supp on po.id_supplier=supp.kodesupp WHERE md5(nopo)='".$nopo."'";

                            $rsTemp = mysql_query($q, $dbLink);
                            if ($dataPo = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' name='nopo' value='" . $dataPo["nopo"] . "'>";
                            } 
                            $q = "SELECT max(nobeli) as nobeli FROM `aki_beli`";
                            $rsTemp = mysql_query($q, $dbLink);
                            $tglpo = date("dmy");
                            if ($kode_ = mysql_fetch_array($rsTemp)) {
                                $urut = "";
                                $noPo = "";
                                if ($kode_['nobeli'] != ''){
                                    $urut = substr($kode_['nobeli'],-4);
                                    $kode = $urut + 1;
                                    if (strlen($kode)==1) {
                                        $kode = '000'.$kode;
                                    }else if (strlen($kode)==2){
                                        $kode = '00'.$kode;
                                    }else if (strlen($kode)==3){
                                        $kode = '0'.$kode;
                                    }
                                    $noPo = 'PTAKIIN'.$tglpo.$kode;
                                }else{
                                    $noPo = 'PTAKIIN'.$tglpo.'0001';
                                }
                            }else{
                                $noPo = 'PTAKIIN'.$tglpo.'0001';
                            }
                        }

                        ?>
                        <input name="txtnobeli" id="txtnobeli" maxlength="30" class="form-control" readonly value="<?php if($_GET["mode"]=='edit'){ echo $noPo; }else{echo $noPo;}?>">
                    </div>
                </div>
                <div class="form-group" >
                    <div class="input-group">
                        <div class="input-group-addon">
                            <label class="control-label" for="txtnopo">No Po</label>
                        </div>
                        <input name="txtnopo" id="txtnopo" maxlength="30" class="form-control" readonly value="<?php if($_GET["mode"]=='edit'){ echo $dataOrder['nopo']; $nomerpo=$dataOrder['nopo'];}else{echo $dataPo["nopo"];}
                        ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group ">
                        <div class="input-group-addon">
                            <label class="control-label" for="txtjbrg">Barang</label>
                        </div>
                        <select class="form-control" name="txtjbrg" id="txtjbrg" readonly>
                            <option value="persediaan">Persedian Produksi</option>
                            <option value="penunjang">Penunjang</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="" style="padding-bottom: 10px;padding-right: 0px;padding-left: 5px;">
                        <select class="form-control " name="idsupp" id="idsupp" readonly>
                            <?php
                            $q = 'SELECT * FROM `aki_supplier`';
                            $sql_supp = mysql_query($q,$dbLink);
                            $selected = "";
                            if ($_GET['mode'] == 'edit') {
                                echo '<option value="'.$dataOrder["kodesupp"].'" selected>'.$dataOrder["kodesupp"].' - '.$dataOrder["supplier"].'</option>';
                                while($rs_order = mysql_fetch_assoc($sql_supp)){ 
                                    echo '<option value="'.$rs_order['kodesupp'].'">'.$rs_order['kodesupp'].' - '.$rs_order['supplier'].'</option>';
                                }  
                            }else{
                                if (isset($_GET["nopo"])!=''){
                                    echo '<option value="'.$dataPo["kodesupp"].'" selected>'.$dataPo["kodesupp"].' - '.$dataPo["supplier"].'</option>';
                                }else{
                                    echo '<option value="" >Supplier</option>';
                                }
                                while($rs_po = mysql_fetch_assoc($sql_supp)){ 
                                    echo '<option value="'.$rs_po['kodesupp'].'">'.$rs_po['kodesupp'].' - '.$rs_po['supplier'].'</option>';
                                }  
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <label class="control-label" for="txttglpo">Tanggal </label>
                        </div>
                        <input name="txttglpo" id="txttglpo" class="form-control" value="<?php 
                        if($_GET["mode"]=='edit'){ 
                            echo date("d-m-Y", strtotime($dataOrder['tgl_beli'])); 
                        }else{
                            if (($_GET["nopo"])==''){
                                echo date("d-m-Y");
                            }else{
                                echo date("d-m-Y", strtotime($dataPo['tgl_po']));
                            }
                        }
                        echo'"';
                            echo 'disabled>';
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <label class="control-label" for="txtcust">Cust</label>
                        </div>
                        <input name="txtcust" id="txtcust" class="form-control" value="<?php if($_GET["mode"]=='edit'){ echo $dataOrder["cust"]; }else{echo $dataPo["cust"];}?>" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <label class="control-label" for="txtcust">Keterangan</label>
                        </div>
                        <textarea name="txtket" id="txtket" class="form-control"><?php if($_GET["mode"]=='edit'){ echo $dataOrder["ket"]; }else{echo $dataPo["ket"];}?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="col-lg-6">
        <div id="pesandel"></div>
    </section> 
    <section class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header">
                <i class="ion ion-clipboard"></i>
                <h3 class="box-title">DETAIL BARANG </h3>
                <span id="msgbox"> </span>
            </div>
            <div class="box-body"style="width: 100%;overflow-x: scroll;">
                <table class="table table-bordered table-striped table-hover" >
                    <thead>
                        <tr>
                            <th style="width: 2%"><i class='fa fa-edit'></i></th>
                            <th style="width: ">Barang</th>
                            <th style="width: 8%">Qty</th>
                            <th style="width: 10%">Satuan</th>
                            <th style="width: 15%">Qty Masuk</th>
                        </tr>
                    </thead>
                    <tbody id="kendali">
                        <?php
                        if ($_GET['mode']=='edit'){
                            $q = "SELECT db.*,ba.kode,ba.nama ,dp.qty as qtypo,(SELECT qty FROM aki_dbeli db left join aki_beli b on b.nobeli=db.nobeli WHERE md5(b.nobeli)!='".$nobeli."' and b.nopo='".$nomerpo."' ) as qtypakai FROM aki_dbeli db left join aki_barang ba on db.kode_barang=ba.kode left join aki_beli b on b.nobeli=db.nobeli left join aki_dpo dp on b.nopo=dp.nopo WHERE md5(db.nobeli)='".$nobeli."'";
                            $rsdpolist = mysql_query($q, $dbLink);
                            $iPO = 0;
                            while ($dpolist = mysql_fetch_array($rsdpolist)) {
                                $kel = '';
                                echo "<tr id='trid_".$iPO."'>";
                                echo '<td align="center" valign="top"><div class="form-group"><a class="btn btn-default btn-sm" onclick=deleteRow("trid_' . $iPO . '")><i class="fa fa-fw fa-trash"></i></a>
                                <input type="hidden" checked class="minimal"  name="chkAddJurnal_' . $iPO . '" id="chkAddJurnal_' . $iPO . '" value="1"/></div></td>';
                                echo '<input  type="hidden" name="txtjbrg_'. $iPO .'" id="txtjbrg_'. $iPO .'" value="'.$dpolist["jbarang"].'">';
                                if ($dpolist["jbarang"] == 'penunjang') {
                                    echo '<td align="center" valign="top" width=><div class="form-group"><input type="text" class="form-control" name="txtkodeb_' . $iPO . '" id="txtkodeb_' . $iPO . '" value="' . $dpolist["kode_barang"]. '"style="text-align:left" readonly/></div></td>';
                                }else{
                                    $q = "SELECT * FROM `aki_barang`";
                                    $listbrg = mysql_query($q, $dbLink);
                                    echo '<td align="" valign="top" width=><div class="form-group"><select class="form-control " name="txtkodeb_' . $iPO . '" id="txtkodeb_' . $iPO . '">
                                    <option value="'.$dpolist['kode'].'">'.$dpolist['kode'].' - '.$dpolist['nama'].'</option>';
                                }
                                echo '<td align="center" valign="top" width=><div class="form-group"><input type="text" onkeydown="return numbersonly(this, event);"  class="form-control" name="txtqty_' . $iPO . '" id="txtqty_' . $iPO . '" value="' . ($dpolist["qtypo"]-$dpolist["qtypakai"]). '"style="text-align:right"/ onfocusout="subtotal(' . $iPO . ')" readonly></div></td>';
                                echo '<td align="center" valign="top" width=><div class="form-group"><input type="text" class="form-control" name="txtSatuan_' . $iPO . '" id="txtSatuan_' . $iPO . '" value="' . $dpolist["satuan"]. '"style="text-align:right" readonly/></div></td>';
                                echo '<td align="center" valign="top" width=><div class="form-group"><input type="text" class="form-control" name="txtqtym_' . $iPO . '" id="txtqtym_' . $iPO . '" value="' . $dpolist["qty"]. '"style="text-align:right" onfocusout="cekval(' . $iPO . ')"/>';
                                echo '<input type="hidden"  name="txtqtypakai_' . $iPO . '" id="txtqtypakai_' . $iPO . '" value="' . $dpolist["qtypakai"]. '/>';
                                echo '<input type="hidden"  name="txtHarga_' . $iPO . '" id="txtHarga_' . $iPO . '" value="' . number_format($dpolist["harga"], 0, ",", "."). '"style="text-align:right"/>';
                                echo '<input type="hidden" name="txtTotal_' . $iPO . '" id="txtTotal_' . $iPO . '" value="' . number_format($dpolist["subtotal"], 0, ",", "."). '"style="text-align:right"/></div></td>';
                                echo "</tr>";
                                $iPO++;
                            }
                        }else{
                            if (isset($_GET["nopo"])){
                                $q = "SELECT dpo.*,b.kode,b.nama FROM `aki_dpo` dpo left join aki_barang b on dpo.kode_barang=b.kode WHERE md5(dpo.nopo)='".$nopo."' order by id asc";
                                $rsdpolist = mysql_query($q, $dbLink);
                                $iPO = 0;
                                while ($dpolist = mysql_fetch_array($rsdpolist)) {
                                    $kel = '';
                                    echo "<tr id='trid_".$iPO."'>";
                                    echo '<td align="center" valign="top"><div class="form-group"><a class="btn btn-default btn-sm" onclick=deleteRow("trid_' . $iPO . '")><i class="fa fa-fw fa-trash"></i></a>
                                    <input type="hidden" checked class="minimal"  name="chkAddJurnal_' . $iPO . '" id="chkAddJurnal_' . $iPO . '" value="1"/></div></td>';
                                    echo '<input  type="hidden" name="txtjbrg_'. $iPO .'" id="txtjbrg_'. $iPO .'" value="'.$dpolist["jbarang"].'">';
                                    if ($dpolist["jbarang"] == 'penunjang') {
                                        echo '<td align="center" valign="top" width=><div class="form-group"><input type="text" class="form-control" name="txtkodeb_' . $iPO . '" id="txtkodeb_' . $iPO . '" value="' . $dpolist["kode_barang"]. '"style="text-align:left"/ readonly></div></td>';
                                    }else{
                                        echo '<td align="" valign="top" width=><div class="form-group"><select class="form-control " name="txtkodeb_' . $iPO . '" id="txtkodeb_' . $iPO . '" readonly>
                                        <option value="'.$dpolist['kode'].'">'.$dpolist['kode'].' - '.$dpolist['nama'].'</option>';
                                    }
                                    echo '<td align="center" valign="top" width=><div class="form-group"><input type="text" onkeydown="return numbersonly(this, event);"  class="form-control" name="txtqty_' . $iPO . '" id="txtqty_' . $iPO . '" value="' . ($dpolist["qty"]-$dpolist["qtymasuk"]). '"style="text-align:right"/ onfocusout="subtotal(' . $iPO . ')"readonly></div></td>';
                                    echo '<td align="center" valign="top" width=><div class="form-group"><input type="text" class="form-control" name="txtSatuan_' . $iPO . '" id="txtSatuan_' . $iPO . '" value="' . $dpolist["satuan"]. '"style="text-align:right" readonly/></div></td>';
                                    
                                    echo '<td align="center" valign="top" width=><div class="form-group"><input type="number" class="form-control" name="txtqtym_' . $iPO . '" id="txtqtym_' . $iPO . '" onfocusout="cekval(' . $iPO . ')" value="0"style="text-align:right"/>';
                                    echo '<input type="hidden" name="txtHarga_' . $iPO . '" id="txtHarga_' . $iPO . '" value="' . number_format($dpolist["harga"], 0, ",", "."). '"/>';
                                    echo '<input type="hidden" name="txtTotal_' . $iPO . '" id="txtTotal_' . $iPO . '" value="' . number_format($dpolist["subtotal"], 0, ",", "."). '"/>';
                                    echo '</div></td>';
                                    
                                    echo "</tr>";
                                    $iPO++;
                                }
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                          <td colspan="5"></td>
                        </tr>
                        <tr style="background-color: white;">
                        
                          <td colspan="6"><button type="submit" id="btnsavepo" class="btn btn-success pull-right">Save</button></td>
                        </tr>
                    </tfoot>
                </table>
                <input type="hidden" value="<?php echo $iPO;?>" id="jumaddOrder" name="jumaddOrder"/>
                <!-- <center><button type="button" class="btn btn-primary" onclick="javascript:addJurnal()">Add Detail</button></center> -->
            </div>
        </div>
    </section>   
    <div class="modal fade" id="mydPo" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">No PO</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <?php  
                        $q = 'SELECT * FROM `aki_po` WHERE aktif=0 and acc_op="1" and acc_fa="1" ORDER BY id desc';
                        $sql_po = mysql_query($q,$dbLink);
                        ?>
                        <select class="form-control select2" name="snopo" id="snopo" style="width: 100%">
                            <?php

                            $selected = "";
                            echo '<option value="">No PO</option>';
                            while($rs_po = mysql_fetch_assoc($sql_po)){ 
                                echo '<option value="'.md5($rs_po['nopo']).'">'.$rs_po['nopo'].'</option>';
                            }  
                            ?>
                        </select>   
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary pull-right" id="createpo"><i class="fa fa-plus"></i> Create</button>
                </div>
            </div>
        </div>
    </div> 
</form>