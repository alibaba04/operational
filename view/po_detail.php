<?php
/* ==================================================
//=======  : Alibaba
==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/po_detail";
//Periksa hak user pada modul/menu ini
$judulMenu = 'PO';
$hakUser = getUserPrivilege($curPage);
if ($hakUser != 90) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";

}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {
    require_once("./class/c_po.php");
    $tmppo = new c_po;
//Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmppo->addpo($_POST);
    }
//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmppo->editpo($_POST);
    }
//Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
//Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Gagal simpan data, mohon hubungi " . $mailSupport . " untuk keterangan lebih lanjut terkait masalah ini.";
    }
    header("Location:index.php?page=view/po_list&pesan=" . $pesan);
    exit;
}
?>
<script>
    $(document).ready(function () {
        $("#txtjbrg").click(function(){
            var x = document.getElementById("txtjbrg").value;
            var y = document.getElementById("idsupp");
            if (x == 'penunjang') {
                y.style.display = "none";
            } else {
                y.style.display = "block";
            }
        });
    });
    function deleteRow(r) {
        var param = r.split("_");
        $("#txtTotal_"+param[1]).val(0);
        document.getElementById(r).style.display = "none";
        $("#chkAddJurnal_"+param[1]).val('');
        $("#txtSatuan_"+param[1]).val('-');
        total();
    }

    $(function () {
        $("[data-mask]").inputmask();
        $(".select2").select2();
        $("#txttglpo").datepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
        var x = document.getElementById("btnsavepo");
        if ($("#totalh").val()=='0' ) {
            x.setAttribute("disabled", ""); 
        }
        
    });
    function selectbrg(tcounter) {
        var x = $("#txtkodeb_"+tcounter).val();
        $.post("function/ajax_function.php",{ fungsi: "getsatuan",kode:x },function(data)
        {
            $("#txtSatuan_"+tcounter).val(data.satuan);
        },"json");
    }
    function kodeb(tcounter) {
        $.post("function/ajax_function.php",{ fungsi: "ambilkodeb" },function(data)
        {
            for(var i=0; i<274; ++i) {
                var x = document.getElementById("txtkodeb_"+tcounter);
                var option = document.createElement("option");
                option.innerHTML  = data[i].text;
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
    function total() {
        var jml = $("#jumAddPo").val();
        var total = 0;
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
        tcounter = $("#jumAddPo").val();
        
        var ttable = document.getElementById("kendali");
        var trow = document.createElement("TR");
        trow.setAttribute("id", "trid_"+tcounter);

        //Kolom 1 Checkbox
        var td = document.createElement("TD");
        td.setAttribute("align","center");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><a class="btn btn-default btn-sm" onclick=deleteRow("trid_'+tcounter+'")><i class="fa fa-fw fa-trash"></i></a><input type="hidden" class="minimal" name="chkAddJurnal_'+tcounter+'" id="chkAddJurnal_'+tcounter+'" value="1" /><input  type="hidden" name="txtjbrg_'+tcounter+'" id="txtjbrg_'+tcounter+'" value="'+$("#txtjbrg").val()+'"></div>';
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
        tcounter = $("#jumAddPo").val();
        $("#jumAddPo").val(parseInt($("#jumAddPo").val())+1); 
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
        Purchase Order
        <small>Detail PO</small>
    </h1>
</section>
<form action="index2.php?page=view/po_detail" method="post" name="frmKasKeluarDetail" onSubmit="return validasiForm(this);" autocomplete="off">
    <section class="col-lg-6">
        <div class="box box-primary">
            <div class="box-body">
                <?php 
                    if ($_GET["mode"] == "edit") {
                        echo "<input type='hidden' name='txtMode' value='Edit'>";
                    }else{
                        echo "<input type='hidden' name='txtMode' value='Add'>";
                    }
                ?>
                <div class="form-group" >
                    <div class="input-group">
                        <div class="input-group-addon">
                            <label class="control-label" for="txtnopo">No PO</label>
                        </div>
                        <?php
                        if ($_GET["mode"] == "edit") {
                            echo "<input type='hidden' name='txtMode' value='Edit'>";
                            if (isset($_GET["nopo"])){
                                $nopo = secureParam($_GET["nopo"], $dbLink);
                            }else{
                                $nopo = "";
                            }
                            $q= "SELECT * FROM `aki_po` po left join aki_supplier supp on po.id_supplier=supp.kodesupp WHERE md5(nopo)='".$nopo."'";
                            $rsTemp = mysql_query($q, $dbLink);
                            if ($dataPo = mysql_fetch_array($rsTemp)) {
                                $noPo = $dataPo["nopo"];
                                echo "<input type='hidden' name='nopo' value='" . $dataPo["nopo"] . "'>";
                            }
                        }else{
                            $q = "SELECT * FROM aki_po where id=( SELECT max(id) FROM aki_po )";
                            $rsTemp = mysql_query($q, $dbLink);
                            $tglpo = date("dmy");
                            if ($kode_ = mysql_fetch_array($rsTemp)) {
                                $urut = "";
                                $newnoPo = "";
                                if ($kode_['nopo'] != ''){
                                    $urut = substr($kode_['nopo'],-4);
                                    $kode = $urut + 1;
                                    if (strlen($kode)==1) {
                                        $kode = '000'.$kode;
                                    }else if (strlen($kode)==2){
                                        $kode = '00'.$kode;
                                    }else if (strlen($kode)==3){
                                        $kode = '0'.$kode;
                                    }
                                    $newnoPo = 'PTAKIPO'.$tglpo.$kode;
                                }else{
                                    $newnoPo = 'PTAKIPO'.$tglpo.'0001';
                                }
                            }else{
                                $newnoPo = 'PTAKIPO'.$tglpo.'0001';
                            }
                        }

                        ?>
                        <input name="txtnopo" id="txtnopo" maxlength="30" class="form-control" readonly value="<?php if($_GET["mode"]=='edit'){ echo $noPo; }else{echo $newnoPo;}?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group ">
                        <div class="input-group-addon">
                            <label class="control-label" for="txtjbrg">Barang</label>
                        </div>
                        <select class="form-control" name="txtjbrg" id="txtjbrg">
                            <option value="persediaan">Persedian Produksi</option>
                            <option value="penunjang">Penunjang</option>
                        </select>
                    </div>
                </div>
                <div class="form-group ">
                    <div class="lsupp" style="padding-bottom: 10px;padding-right: 0px;padding-left: 5px;">
                        <select class="form-control select2" name="idsupp" id="idsupp"required>
                            <?php
                            $q = 'SELECT * FROM `aki_supplier`';
                            $sql_supp = mysql_query($q,$dbLink);
                            $selected = "";
                            if ($_GET['mode'] == 'edit') {
                                echo '<option value="'.$dataPo["kodesupp"].'" selected>'.$dataPo["kodesupp"].' - '.$dataPo["supplier"].'</option>';
                                while($rs_po = mysql_fetch_assoc($sql_supp)){ 
                                    echo '<option value="'.$rs_po['kodesupp'].'">'.$rs_po['kodesupp'].' - '.$rs_po['supplier'].'</option>';
                                }  
                            }else{
                                echo '<option value="">Supplier</option>';
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
                            <label class="control-label" for="txttglpo">Tanggal PO</label>
                        </div>
                        <input name="txttglpo" id="txttglpo" class="form-control" value="<?php if($_GET["mode"]=='edit'){ echo date("d-m-Y", strtotime($dataPo['tgl_po'])); }else{echo date("d-m-Y");}?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <label class="control-label" for="txtcust">Cust</label>
                        </div>
                        <input name="txtcust" id="txtcust" class="form-control" value="<?php if($_GET["mode"]=='edit'){ echo $dataPo["cust"]; }?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <label class="control-label" for="txtcust">Keterangan</label>
                        </div>
                        <textarea name="txtket" id="txtket" class="form-control"><?php if($_GET["mode"]=='edit'){ echo $dataPo["ket"]; }?></textarea>
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
                <table class="table table-bordered table-striped table-hover" id="mtable">
                    <thead>
                        <tr>
                            <th style="width: 2%"><i class='fa fa-edit'></i></th>
                            <th style="width: ">Barang</th>
                            <th style="width: 8%">Qty</th>
                            <th style="width: 10%">Satuan</th>
                            <th style="width: 15%">Harga (Rp)</th>
                            <th style="width: 15%">Sub Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody id="kendali">
                        <?php
                        if ($_GET['mode']=='edit'){
                            $q = "SELECT dpo.*,b.kode,b.nama FROM `aki_dpo` dpo left join aki_barang b on dpo.kode_barang=b.kode WHERE md5(dpo.nopo)='".$nopo."' order by id asc";
                            $rsdpolist = mysql_query($q, $dbLink);
                            $iPO = 0;
                            while ($dpolist = mysql_fetch_array($rsdpolist)) {
                                $kel = '';
                                echo "<tr id='trid_".$iPO."'>";
                                echo '<td align="center" valign="top"><div class="form-group"><a class="btn btn-default btn-sm" onclick=deleteRow("trid_' . $iPO . '")><i class="fa fa-fw fa-trash"></i></a>
                                <input type="hidden" class="minimal"  name="chkAddJurnal_' . $iPO . '" id="chkAddJurnal_' . $iPO . '" value="1"/></div></td>';
                                echo '<input  type="hidden" name="txtjbrg_'. $iPO .'" id="txtjbrg_'. $iPO .'" value="'.$dpolist["jbarang"].'">';
                                $q = "SELECT * FROM `aki_barang`";
                                $listbrg = mysql_query($q, $dbLink);
                                if ($dpolist["jbarang"] == 'penunjang') {
                                    echo '<td align="center" valign="top" width=><div class="form-group"><input type="text" class="form-control" name="txtkodeb_' . $iPO . '" id="txtkodeb_' . $iPO . '" value="' . $dpolist["id_barang"]. '"style="text-align:left"/></div></td>';
                                }else{
                                    echo '<td align="" valign="top" width=><div class="form-group"><select class="form-control select2" name="txtkodeb_' . $iPO . '" id="txtkodeb_' . $iPO . '">
                                    <option value="'.$dpolist['kode'].'">'.$dpolist['kode'].' - '.$dpolist['nama'].'</option>';
                                    while ($dbrg = mysql_fetch_array($listbrg)) {
                                        echo '<option value="'.$dbrg['kode'].'">'.$dbrg['kode'].' - '.$dbrg['nama'].'</option>';
                                    }
                                }
                                
                                echo '<td align="center" valign="top" width=><div class="form-group"><input type="text" onkeydown="return numbersonly(this, event);"  class="form-control" name="txtqty_' . $iPO . '" id="txtqty_' . $iPO . '" value="' . $dpolist["qty"]. '"style="text-align:right"/ onfocusout="subtotal(' . $iPO . ')"></div></td>';
                                echo '<td align="center" valign="top" width=><div class="form-group"><input type="text" class="form-control" name="txtSatuan_' . $iPO . '" id="txtSatuan_' . $iPO . '" value="' . $dpolist["satuan"]. '"style="text-align:right"/></div></td>';
                                echo '<td align="center" valign="top" width=><div class="form-group"><input type="text" onkeydown="return numbersonly(this, event);"  onfocusout="subtotal(' . $iPO . ')"class="form-control" name="txtHarga_' . $iPO . '" id="txtHarga_' . $iPO . '" value="' . number_format($dpolist["harga"], 0, ",", "."). '"style="text-align:right" /></div></td>';
                                echo '<td align="center" valign="top" width=><div class="form-group"><input type="text" onkeydown="return numbersonly(this, event);"  class="form-control" name="txtTotal_' . $iPO . '" id="txtTotal_' . $iPO . '" value="' . number_format($dpolist["subtotal"], 0, ",", "."). '"style="text-align:right" readonly/></div></td>';
                                echo "</tr>";
                                $iPO++;
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                          <td colspan="4"></td>
                          <td>Total :</td>
                          <td><input type="text" name="txttotalh" id="txttotalh" readonly="" value="<?php if($_GET['mode']=='edit'){echo number_format($dataPo['totalharga'], 0, ",", ".");}else{echo '0';} ?>"></td>
                        </tr>
                        <tr style="background-color: white;">
                        
                          <td colspan="6"><button type="submit" id="btnsavepo" class="btn btn-success pull-right">Save</button></td>
                        </tr>
                    </tfoot>
                </table>
                <input type="hidden" value="<?php if($_GET['mode']=='edit'){echo $iPO;}else{echo '0';} ?>" id="jumAddPo" name="jumAddPo"/>
                <center><button type="button" class="btn btn-primary" onclick="javascript:addJurnal()">Add Detail</button></center>
            </div>
        </div>
    </section>    
</form>