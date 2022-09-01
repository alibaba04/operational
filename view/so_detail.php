<?php
/* ==================================================
//=======  : Alibaba
==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/so_detail";
//Periksa hak user pada modul/menu ini
$judulMenu = 'so';
$hakUser = getUserPrivilege($curPage);
if ($hakUser != 90) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";

}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {
    require_once("./class/c_so.php");
    $tmpso = new c_so;
//Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpso->add($_POST);
    }
//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpso->edit($_POST);
    }
//Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
//Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Gagal simpan data, mohon hubungi " . $mailSupport . " untuk keterangan lebih lanjut terkait masalah ini.";
    }
    header("Location:index.php?page=view/gudang_list&pesan=" . $pesan);
    exit;
}
?>
<script>

    function inumber(tcounter) {
        var input = document.getElementById("txtqty_"+tcounter);
        input.onkeypress = function(e) {    e = e || window.event;
            var charCode = (typeof e.which == "number") ? e.which : e.keyCode;
            if (!charCode || charCode == 8 /* Backspace */ ) {
                return;
            }
            var typedChar = String.fromCharCode(charCode);
            if (/\d/.test(typedChar)) {
                return;
            }
            if (typedChar == "-" && this.value == "") {
                return;
            }
            return false;
        };
    }
    $(function () {
        $(".select2").select2();
        $("#txttglso").datepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
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
        document.getElementById(r).style.display = "none";
        $("#chkAddJurnal_"+param[1]).val('');
        $("#txtSatuan_"+param[1]).val('-');
    }

    function addJurnal(){   
        tcounter = $("#jumso").val();
        var ttable = document.getElementById("kendali");
        var trow = document.createElement("TR");
        trow.setAttribute("id", "trid_"+tcounter);
        kodeb(tcounter); 

        //Kolom 1 Checkbox
        var td = document.createElement("TD");
        td.setAttribute("align","center");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><a class="btn btn-default btn-sm" onclick=deleteRow("trid_'+tcounter+'")><i class="fa fa-fw fa-trash"></i></a><input type="hidden" class="minimal" name="chkAddJurnal_'+tcounter+'" id="chkAddJurnal_'+tcounter+'" value="1" checked /></div>';
        trow.appendChild(td);

        //Kolom 3 Barang 
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><select class="form-control select2" name="txtkodeb_'+tcounter+'" id="txtkodeb_'+tcounter+'" onchange="selectbrg('+tcounter+')"><option>- Nama Barang- </option></select></div>';
        trow.appendChild(td);

        //Kolom 4 cQty
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input name="txtcqty_'+tcounter+'" id="txtcqty_'+tcounter+'" class="form-control" value="0" style="text-align:right" onkeydown="return numbersonly(this, event);" onfocusout="subtotal('+tcounter+')" disabled></div>';
        trow.appendChild(td);
        //Kolom 4 Qty
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input type="text" name="txtqty_'+tcounter+'" id="txtqty_'+tcounter+'" class="form-control" value="0" style="text-align:right" onfocusout="subtotal('+tcounter+')" onkeydown="inumber('+tcounter+')" required></div>';
        trow.appendChild(td);

        //Kolom 5 Satuan
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input name="txtSatuan_'+tcounter+'" id="txtSatuan_'+tcounter+'" class="form-control" style="text-align:right" required></div>';
        trow.appendChild(td);

        ttable.appendChild(trow);
        tcounter = $("#jumso").val();
        $("#jumso").val(parseInt($("#jumso").val())+1); 
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
        Stock Opname
        <small>Detail Barang SO</small>
    </h1>
</section>
<form action="index2.php?page=view/so_detail" method="post" name="frmKasKeluarDetail" onSubmit="return validasiForm(this);" autocomplete="off">
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
                            <label class="control-label" for="txtnoso">No</label>
                        </div>
                        <?php
                        if ($_GET["mode"] == "edit") {
                            echo "<input type='hidden' name='txtMode' value='Edit'>";
                            if (isset($_GET["nobso"])){
                                $nobso = secureParam($_GET["nobso"], $dbLink);
                            }else{
                                $nobso = "";
                            }
                            $q= "SELECT * FROM `aki_bso` b left join aki_dbso db on b.nobso=db.nobso WHERE md5(b.nobso)='".$nobso."'";
                            $rsTemp = mysql_query($q, $dbLink);
                            if ($dataSO = mysql_fetch_array($rsTemp)) {
                                $noSo = $dataSO["nobso"];
                                echo "<input type='hidden' name='nopo' value='" . $dataSO["nobso"] . "'>";
                            }
                        }else{
                            $nopo = "";
                            $q = "SELECT max(nobso) as nobso FROM `aki_bso`";
                            $rsTemp = mysql_query($q, $dbLink);
                            $tglpo = date("dmy");
                            if ($kode_ = mysql_fetch_array($rsTemp)) {
                                $urut = "";
                                $nonSo = "";
                                if ($kode_['nobso'] != ''){
                                    $urut = substr($kode_['nobso'],-4);
                                    $kode = $urut + 1;
                                    if (strlen($kode)==1) {
                                        $kode = '000'.$kode;
                                    }else if (strlen($kode)==2){
                                        $kode = '00'.$kode;
                                    }else if (strlen($kode)==3){
                                        $kode = '0'.$kode;
                                    }
                                    $nonSo = 'PTAKISO'.$tglpo.$kode;
                                }else{
                                    $nonSo = 'PTAKISO'.$tglpo.'0001';
                                }
                            }else{
                                $nonSo = 'PTAKISO'.$tglpo.'0001';
                            }
                        }

                        ?>
                        <input name="txtnoso" id="txtnoso" maxlength="30" class="form-control" readonly value="<?php if($_GET["mode"]=='edit'){ echo $dataSO["nobso"]; }else{ echo $nonSo;}?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <label class="control-label" for="txttglso">Tanggal </label>
                        </div>
                        <input name="txttglso" id="txttglso" class="form-control" value="<?php 
                        if($_GET["mode"]=='edit'){ 
                            echo date("d-m-Y", strtotime($dataSO['tgl_bso'])); 
                        }else{
                            echo date("d-m-Y");
                        }?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <label class="control-label" for="txtcust">Keterangan</label>
                        </div>
                        <textarea name="txtket" id="txtket" class="form-control"><?php if($_GET["mode"]=='edit'){ echo $dataSO["ket"]; }?></textarea>
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
                            <th style="width: 15%">Current Stock </th>
                            <th style="width: 15%">Qty SO</th>
                            <th style="width: 15%">Satuan</th>
                        </tr>
                    </thead>
                    <tbody id="kendali">
                        <?php
                        if ($_GET['mode']=='edit'){
                            $q = "SELECT db.*,ba.kode,ba.nama FROM aki_dbso db left join aki_barang ba on db.kode_barang=ba.kode WHERE md5(db.nobso)='".$nobso."' order by db.id asc";
                            $rsdpolist = mysql_query($q, $dbLink);
                            $iPO = 0;
                            while ($dpolist = mysql_fetch_array($rsdpolist)) {
                                $kel = '';
                                echo "<tr id='trid_".$iPO."'>";
                                echo '<td align="center" valign="top"><div class="form-group"><a class="btn btn-default btn-sm" onclick=deleteRow("trid_' . $iPO . '")><i class="fa fa-fw fa-trash"></i></a>
                                <input type="hidden" checked class="minimal"  name="chkAddJurnal_' . $iPO . '" id="chkAddJurnal_' . $iPO . '" value="1"/></div></td>';
                                $q = "SELECT * FROM `aki_barang`";
                                $listbrg = mysql_query($q, $dbLink);
                                echo '<td align="" valign="top" width=><div class="form-group"><select class="form-control select2" name="txtkodeb_' . $iPO . '" id="txtkodeb_' . $iPO . '" onchange="selectbrg(' . $iPO . ')">
                                <option value="'.$dpolist['kode'].'">'.$dpolist['kode'].' - '.$dpolist['nama'].'</option>';
                                while ($dbrg = mysql_fetch_array($listbrg)) {
                                    echo '<option value="'.$dbrg['kode'].'">'.$dbrg['kode'].' - '.$dbrg['nama'].'</option>';
                                }
                                echo '<td><input type="text" onkeydown="return numbersonly(this, event);"  class="form-control" name="txtcqty_' . $iPO . '" id="txtcqty_' . $iPO . '" "style="text-align:right"/ disabled></td>';
                                echo '<td align="center" valign="top" width=><div class="form-group"><input type="text" onkeydown="return numbersonly(this, event);"  class="form-control" name="txtqty_' . $iPO . '" id="txtqty_' . $iPO . '" value="' . $dpolist["qty"]. '"style="text-align:right"/ onfocusout="subtotal(' . $iPO . ')"></div></td>';
                                echo '<td align="center" valign="top" width=><div class="form-group"><input type="text" class="form-control" name="txtSatuan_' . $iPO . '" id="txtSatuan_' . $iPO . '" value="' . $dpolist["satuan"]. '"style="text-align:right"/></div></td>';
                                echo "</tr>";
                                $iPO++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <input type="hidden" value="<?php if($_GET['mode']=='edit'){echo $iPO;}else{echo '0';} ?>" id="jumso" name="jumso"/>
                <center><button type="button" class="btn btn-primary" onclick="javascript:addJurnal()">Add Detail</button></center>
                <button type="submit" id="btnsavepo" class="btn btn-success pull-right">Save</button>
            </div>
        </div>
    </section>   
</form>