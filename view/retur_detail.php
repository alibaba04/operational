<?php
/* ==================================================
//=======  : Alibaba
==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/retur_detail";
//Periksa hak user pada modul/menu ini
$judulMenu = 'retur';
$hakUser = getUserPrivilege($curPage);
if ($hakUser != 90) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";

}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {
    require_once("./class/c_retur.php");
    $tmpretur = new c_retur;
//Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpretur->add($_POST);
    }
//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpretur->edit($_POST);
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
   
    $(function () {
        $(".select2").select2();
        $("#txttglretur").datepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
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
        tcounter = $("#jumretur").val();
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

        //Kolom 4 Qty
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input name="txtqty_'+tcounter+'" id="txtqty_'+tcounter+'" class="form-control" value="1" style="text-align:right" onkeydown="return numbersonly(this, event);" onfocusout="subtotal('+tcounter+')" required></div>';
        trow.appendChild(td);

        //Kolom 5 Satuan
        var td = document.createElement("TD");
        td.setAttribute("align","left");
        td.style.verticalAlign = 'top';
        td.innerHTML+='<div class="form-group"><input name="txtSatuan_'+tcounter+'" id="txtSatuan_'+tcounter+'" class="form-control" style="text-align:right" required></div>';
        trow.appendChild(td);

        ttable.appendChild(trow);
        tcounter = $("#jumretur").val();
        $("#jumretur").val(parseInt($("#jumretur").val())+1); 
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
        Retur
        <small>Detail Retur</small>
    </h1>
</section>
<form action="index2.php?page=view/retur_detail" method="post" name="frmKasKeluarDetail" onSubmit="return validasiForm(this);" autocomplete="off">
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
                            <label class="control-label" for="txtnopo">No Retur</label>
                        </div>
                        <?php
                        if ($_GET["mode"] == "edit") {
                            echo "<input type='hidden' name='txtMode' value='Edit'>";
                            if (isset($_GET["nobretur"])){
                                $nobretur = secureParam($_GET["nobretur"], $dbLink);
                            }else{
                                $nobretur = "";
                            }
                            $q= "SELECT * FROM `aki_bretur` b left join aki_dbretur db on b.nobretur=db.nobretur WHERE md5(b.nobretur)='".$nobretur."'";
                            $rsTemp = mysql_query($q, $dbLink);
                            if ($dataretur = mysql_fetch_array($rsTemp)) {
                                $noPo = $dataretur["nobretur"];
                                echo "<input type='hidden' name='nopo' value='" . $dataretur["nobretur"] . "'>";
                            }
                        }else{
                            $nopo = "";
                            $q = "SELECT max(nobretur) as nobretur FROM `aki_bretur`";
                            $rsTemp = mysql_query($q, $dbLink);
                            $tglpo = date("dmy");
                            if ($kode_ = mysql_fetch_array($rsTemp)) {
                                $urut = "";
                                $noPo = "";
                                if ($kode_['nobretur'] != ''){
                                    $urut = substr($kode_['nobretur'],-4);
                                    $kode = $urut + 1;
                                    if (strlen($kode)==1) {
                                        $kode = '000'.$kode;
                                    }else if (strlen($kode)==2){
                                        $kode = '00'.$kode;
                                    }else if (strlen($kode)==3){
                                        $kode = '0'.$kode;
                                    }
                                    $noPo = 'PTAKIRE'.$tglpo.$kode;
                                }else{
                                    $noPo = 'PTAKIRE'.$tglpo.'0001';
                                }
                            }else{
                                $noPo = 'PTAKIRE'.$tglpo.'0001';
                            }
                        }

                        ?>
                        <input name="txtnoretur" id="txtnoretur" maxlength="30" class="form-control" readonly value="<?php if($_GET["mode"]=='edit'){ echo $noPo; }else{echo $noPo;}?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <label class="control-label" for="txttglretur">Tanggal Retur</label>
                        </div>
                        <input name="txttglretur" id="txttglretur" class="form-control" value="<?php 
                        if($_GET["mode"]=='edit'){ 
                            echo date("d-m-Y", strtotime($dataretur['tgl_beli'])); 
                        }else{
                            if (($_GET["nopo"])==''){
                                echo date("d-m-Y");
                            }else{
                                echo date("d-m-Y", strtotime($dataPo['tgl_po']));
                            }
                        }?>">
                    </div>
                </div>
                <!-- <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <label class="control-label" for="txtcust">Pemohon</label>
                        </div>
                        <input name="txtcust" id="txtcust" class="form-control" value="<?php if($_GET["mode"]=='edit'){ echo $dataretur["cust"]; }else{echo $dataPo["cust"];}?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <label class="control-label" for="txtproyek">Kode/Proyek</label>
                        </div>
                        <input name="txtproyek" id="txtproyek" class="form-control" value="<?php if($_GET["mode"]=='edit'){ echo $dataretur["cust"]; }else{echo $dataPo["cust"];}?>" required>
                    </div>
                </div> -->
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <label class="control-label" for="txtcust">Keterangan</label>
                        </div>
                        <textarea name="txtket" id="txtket" class="form-control"><?php if($_GET["mode"]=='edit'){ echo $dataretur["ket"]; }else{echo $dataPo["ket"];}?></textarea>
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
                            <th style="width: 10%">Qty</th>
                            <th style="width: 10%">Satuan</th>
                        </tr>
                    </thead>
                    <tbody id="kendali">
                        <?php
                        if ($_GET['mode']=='edit'){
                            $q =  "SELECT db.*,ba.kode,ba.nama FROM aki_dbretur db left join aki_barang ba on db.kode_barang=ba.kode WHERE md5(db.nobretur)='".$nobretur."' order by db.id asc";
                            $rsdpolist = mysql_query($q, $dbLink);
                            $iPO = 0;
                            while ($dpolist = mysql_fetch_array($rsdpolist)) {
                                $kel = '';
                                echo "<tr id='trid_".$iPO."'>";
                                echo '<td align="center" valign="top"><div class="form-group"><a class="btn btn-default btn-sm" onclick=deleteRow("trid_' . $iPO . '")><i class="fa fa-fw fa-trash"></i></a>
                                <input type="hidden" checked class="minimal"  name="chkAddJurnal_' . $iPO . '" id="chkAddJurnal_' . $iPO . '" value="1"/></div></td>';
                                $q = "SELECT * FROM `aki_barang`";
                                $listbrg = mysql_query($q, $dbLink);
                                echo '<td align="" valign="top" width=><div class="form-group"><select class="form-control select2" name="txtkodeb_' . $iPO . '" id="txtkodeb_' . $iPO . '">
                                <option value="'.$dpolist['kode'].'">'.$dpolist['kode'].' - '.$dpolist['nama'].'</option>';
                                while ($dbrg = mysql_fetch_array($listbrg)) {
                                    echo '<option value="'.$dbrg['kode'].'">'.$dbrg['kode'].' - '.$dbrg['nama'].'</option>';
                                }
                                echo '<td align="center" valign="top" width=><div class="form-group"><input type="text" onkeydown="return numbersonly(this, event);"  class="form-control" name="txtqty_' . $iPO . '" id="txtqty_' . $iPO . '" value="' . $dpolist["qty"]. '"style="text-align:right"/ onfocusout="subtotal(' . $iPO . ')"></div></td>';
                                echo '<td align="center" valign="top" width=><div class="form-group"><input type="text" class="form-control" name="txtSatuan_' . $iPO . '" id="txtSatuan_' . $iPO . '" value="' . $dpolist["satuan"]. '"style="text-align:right"/></div></td>';
                                echo "</tr>";
                                $iPO++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <input type="hidden" value="<?php if($_GET['mode']=='edit'){echo $iPO;}else{echo '0';} ?>" id="jumretur" name="jumretur"/>
                <center><button type="button" class="btn btn-primary" onclick="javascript:addJurnal()">Add Detail</button></center>
                <button type="submit" id="btnsavepo" class="btn btn-success pull-right">Save</button>
            </div>
        </div>
    </section>   
</form>