<?php
/* ==================================================
//=======  : Alibaba
==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/proyek_detail";
//Periksa hak user pada modul/menu ini
$judulMenu = 'Proyek';
$hakUser = getUserPrivilege($curPage);
if ($hakUser != 90) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User cannot access this page!');
    echo "</p>";
}
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {
    require_once("./class/c_proyek.php");
    $tmpproyek = new c_proyek;
//Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpproyek->add($_POST);
    }
//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpproyek->edit($_POST);
    }
//Jika Mode Upload
    if ($_POST["txtMode"] == "Upload") {
        $pesan = $tmpproyek->upload($_POST);
    }
//Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpproyek->delete($_GET["kodeTransaksi"]);
    }
//Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
//Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Warning!!, please text to " . $mailSupport . " for support this error!.";
    }
    header("Location:index.php?page=view/proyek_list&pesan=" . $pesan);
    exit;
}
?>
<!-- Include script date di bawah jika ada field tanggal -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="dist/js/jquery-ui.min.js"></script>
<script src="plugins/iCheck/icheck.min.js"></script>
<script src="js/angka.js"></script>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () { 
        $(".tgl").datepicker({ format: 'dd-mm-yyyy', autoclose:true }); 
        $("#txttgldeadline").datepicker({ format: 'dd-mm-yyyy', autoclose:true });
        $("#txttglinternal").datepicker({ format: 'dd-mm-yyyy', autoclose:true });
    });
</script>
<!-- Include script untuk function auto complete -->
<script type="text/javascript" src="js/autoCompletebox.js"></script>
<SCRIPT language="JavaScript" TYPE="text/javascript">

function validasiForm(form)
{
   
return true;
}
</SCRIPT>

<section class="content-header">
    <h1>
        Update Project
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Update</li>
        <li class="active">Update Project</li>
    </ol>
</section>

<form action="index2.php?page=view/proyek_detail" method="post" name="frmSiswaDetail" onSubmit="return validasiForm(this);" autocomplete="off">
    <section class="content">
        <!-- Main row -->
        <div class="row">
            <section class="col-lg-8">
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <?php
                        if ($_GET["mode"] == "edit") {
                            echo '<h3 class="box-title">Upadate Project</h3>';
                            echo "<input type='hidden' name='txtMode' value='Edit'>";
                            //Secure parameter from SQL injection
                            if (isset($_GET["nospk"])){
                                $nospk = secureParam($_GET["nospk"], $dbLink2);
                            }else{
                                $nospk = "";
                            }
                            $q = "SELECT spk.*,kk.*, dkk.* FROM aki_spk spk left join aki_kk kk on spk.nokk=kk.noKk right join aki_dkk dkk on kk.noKk=dkk.noKk WHERE spk.noproyek!='-' and spk.aktif=1 and md5(spk.nospk)='".$nospk."' GROUP by spk.noproyek ORDER BY kk.noKk desc";
                            $rsTemp = mysql_query($q, $dbLink2);
                            if ($dataspk = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' name='kodeTransaksi' value='" . $dataspk["nospk"] . "'>";
                            } else {
                                ?>
                                <script language="javascript">
                                    alert("Invalid Code! ");
                                    history.go(-1);
                                </script>
                                <?php
                            }
                        } else {
                            echo '<h3 class="box-title">Upadate Project</h3>';
                            echo "<input type='hidden' name='txtMode'  value='Add'>";
                        }
                        ?>
                    </div>
                    <div class="box-body">
                        <div class="form-group" >
                            <div class="col-lg-4" style="padding-left: 0px;">
                                <label class="control-label" for="txtTglTransaksi">No Proyek</label>
                                <input name="txtnoproyek" id="txtnoproyek" class="form-control"value="<?php echo $dataspk['noproyek']; ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                            </div>
                            <div class="col-lg-8" style="padding-left: 0px;padding-right: 0px;">
                                <label class="control-label" for="txtTglTransaksi">No SPK</label>
                                <input name="txtnospk" id="txtnospk" class="form-control" value="<?php echo $dataspk['nospk']; ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtTglTransaksi">Customer</label>
                            <input name="txtcust" id="txtcust" class="form-control" 
                            value="<?php echo $dataspk['nama_cust']; ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtTglTransaksi">Masjid</label>
                            <input name="txtmasjid" id="txtmasjid" class="form-control" 
                            value="<?php echo $dataspk['masjid']; ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtTglTransaksi">Alamat</label>
                            <textarea name="txtalamat" id="txtalamat" class="form-control" ><?php echo $dataspk['alamat_proyek']; ?></textarea>
                        </div>
                        <div class="form-group" >
                            <div class="col-lg-5" style="padding-left: 0px;">
                                <label class="control-label" for="txtTglTransaksi">No HP</label>
                                 <input type="phone" name="txtphone" id="txtphone" class="form-control" data-inputmask='"mask": "9999 9999 99999"' data-mask value="<?php echo $dataspk['no_phone']; ?>" >
                            </div>
                        </div>
                        <div class="form-group" >
                            <div class="col-lg-8" style="padding-left: 0px;">
                                <label class="control-label" for="txtTglTransaksi">Sales</label>
                                <input name="txtsales" id="txtsales" class="form-control" value="<?php echo $dataspk['sales']; ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                            </div>
                        </div>
                    </div>
                </div>    
            </section>
            <section class="col-lg-12">
                <div class="box box-primary collapsed-box">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <h3 class="box-title">Detail</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label" for="txtjpekerjaan">Jenis Pekerjaan</label>
                                <input name="txtjpekerjaan" id="txtjpekerjaan" class="form-control" 
                                value="<?php echo $dataspk['status_proyek']; ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                                <div class="col-lg-6" id="dt">
                                    <label class="control-label" for="txtDt">Diameter Tengah</label><div class="input-group">
                                    <input type="text"  onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" name="txtDt" id="txtDt" class="form-control" value="0" ><span class="input-group-addon">meter</span></div>
                                    <label class="control-label" for="txtD">Diameter</label><div class="input-group">
                                    <input type="text" onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))"  name="txtD" id="txtD" class="form-control" placeholder="0"value="0" onfocus="this.value=''"><span class="input-group-addon">meter</span></div>
                                    <label class="control-label" for="txtT">Tinggi</label><div class="input-group">
                                    <input type="text" onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))"  name="txtT" id="txtT" class="form-control" placeholder="0"value="0" onfocus="this.value=''"><span class="input-group-addon">meter</span></div>
                                </div>
                                <div class="col-lg-6" id="dt">
                                   <label class="control-label" for="idluas">Luas</label><div class="input-group"><input onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))"  type="text" name="idluas" id="idluas" class="form-control" placeholder="0" value=""><span class="input-group-addon">m<sup>2</sup></span></div>
                                    <label class="control-label" for="txtqty">Jumlah</label>
                                    <input type="number" min='1' name="txtqty" id="txtqty" class="form-control" value="1" value="">
                                    <br><br><br>
                                </div>
                                <label class="control-label" for="cbomodel">Bahan</label>
                                <select name="cbomodel" id="cbomodel" class="form-control">
                                    <option value=setbola>Setengah Bola</option>
                                    <option value=pinang>Pinang</option>
                                    <option value=madinah>Madinah</option>
                                    <option value=bawang>Bawang</option>
                                    <option value=custom>Custom</option>
                                </select>
                                <label class="control-label" for="cbomodel">Jenis Kubah</label>
                                <select name="cbomodel" id="cbomodel" class="form-control">
                                    <option value='Kubah Utama'>Kubah Utama</option>
                                    <option value='Mahrab'>Mahrab</option>
                                    <option value='Anakan'>Anakan</option>
                                    <option value='Menara'>Menara</option>
                                    <option value='Atap'>Atap</option>
                                </select>
                                <label class="control-label" for="cbomodel">Tipe Kubah</label>
                                <select name="cbomodel" id="cbomodel" class="form-control">
                                    <option value=setbola>Setengah Bola</option>
                                    <option value=pinang>Pinang</option>
                                    <option value=madinah>Madinah</option>
                                    <option value=bawang>Bawang</option>
                                    <option value=custom>Custom</option>
                                </select>
                                <label class="control-label" for="txtwarna">Warna</label>
                                <input name="txtwarna" id="txtwarna" class="form-control" 
                                value="<?php if ($_GET["mode"]=='edit'){ echo tgl_ind($dataspk["tanggal_transaksi"]); } ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                                <label class="control-label" for="txtddalam">Diameter Dalam</label>
                                <input name="txtddalam" id="txtddalam" class="form-control" 
                                value="<?php if ($_GET["mode"]=='edit'){ echo tgl_ind($dataspk["tanggal_transaksi"]); } ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                                <label class="control-label" for="txtrangka">Rangka</label>
                                <input name="txtrangka" id="txtrangka" class="form-control" 
                                value="<?php if ($_GET["mode"]=='edit'){ echo tgl_ind($dataspk["tanggal_transaksi"]); } ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label" for="txttglspk">Tgl SPK</label>
                                <input name="txttglspk" id="txttglspk" class="form-control" 
                                value="<?php if ($_GET["mode"]=='edit'){ echo tgl_ind($dataspk["tanggal_transaksi"]); } ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                                <label class="control-label" for="txttgldeadline">Deadline</label>
                                <input name="txttgldeadline" id="txttgldeadline" class="form-control" 
                                value="<?php if ($_GET["mode"]=='edit'){ echo tgl_ind($dataspk["tanggal_transaksi"]); } ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                                <label class="control-label" for="txttglinternal">Internal</label>
                                <input name="txttglinternal" id="txttglinternal" class="form-control" 
                                value="<?php if ($_GET["mode"]=='edit'){ echo tgl_ind($dataspk["tanggal_transaksi"]); } ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                                <label class="control-label" for="cbokproject">Project</label>
                                    <select class="form-control" name="cbokproject" id="cbokproject">
                                        <?php
                                        $selected = "";
                                        if ($dataKk['kproyek'] == 'nonpatas') {
                                            $selected = " selected";
                                            echo '<option value="nonpatas"'.$selected.'>Non Patas</option>';
                                            echo '<option value="patas">Patas</option>';
                                        }elseif ($dataKk['kproyek']=="patas") {
                                            $selected = " selected";
                                            echo '<option value="nonpatas">Non Patas</option>';
                                            echo '<option value="patas"'.$selected.'>Patas</option>';
                                        }else{
                                            echo '<option value="nonpatas">Non Patas</option>';
                                            echo '<option value="patas">Patas</option>';
                                        }
                                        ?>
                                    </select>
                                <label class="control-label" for="txtmakara">Makara</label>
                                <input name="txtmakara" id="txtmakara" class="form-control" 
                                value="<?php if ($_GET["mode"]=='edit'){ echo tgl_ind($dataspk["tanggal_transaksi"]); } ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                                <label class="control-label" for="txtsmakara">Spek Makara</label>
                                <input name="txtsmakara" id="txtsmakara" class="form-control" 
                                value="<?php if ($_GET["mode"]=='edit'){ echo tgl_ind($dataspk["tanggal_transaksi"]); } ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                                <label class="control-label" for="txtpenangkal">Penangkal Petir</label>
                                <input name="txtpenangkal" id="txtpenangkal" class="form-control" 
                                value="<?php if ($_GET["mode"]=='edit'){ echo tgl_ind($dataspk["tanggal_transaksi"]); } ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                                <label class="control-label" for="txtlampusorot">Lampu sorot</label>
                                <input name="txtlampusorot" id="txtlampusorot" class="form-control" 
                                value="<?php if ($_GET["mode"]=='edit'){ echo tgl_ind($dataspk["tanggal_transaksi"]); } ?>" placeholder="Empty" onKeyPress="return handleEnter(this, event)">
                                <label class="control-label" for="txtlain">Lain-lain</label>
                                <textarea name="txtlain" id="txtlain" class="form-control" ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="col-lg-12">
                <div class="box box-primary collapsed-box">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <h3 class="box-title">Termin</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="txtjpekerjaan">DP</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><input type="checkbox"></span>
                                    <input type="text"  onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" name="txtDt" id="txtDt" class="form-control" value="0" >
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="txtT2">T2</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><input type="checkbox"></span>
                                    <input type="text"  onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" name="txtT2" id="txtT2" class="form-control" value="0" >
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="txtT3">T3</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><input type="checkbox"></span>
                                    <input type="text"  onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" name="txtT3" id="txtT3" class="form-control" value="0" >
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="txtT4">T4</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><input type="checkbox"></span>
                                    <input type="text"  onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" name="txtT4" id="txtT4" class="form-control" value="0" >
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="txttransport">Biaya Transport</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><input type="checkbox"></span>
                                    <input type="text"  onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" name="txttransport" id="txttransport" class="form-control" value="0" >
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="txtkaligrafi">Biaya Kaligrafi</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><input type="checkbox"></span>
                                    <input type="text"  onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" name="txtkaligrafi" id="txtkaligrafi" class="form-control" value="0" >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="col-lg-12">
                <div class="box box-primary collapssed-box">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <h3 class="box-title">Detail</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label" for="rangka_in">Rangka</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="rangka_in" id="rangka_in" class="form-control tgl" >
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="rangka_out" id="rangka_out" class="form-control tgl" >
                                </div>
                                <label class="control-label" for="hollow_in">Hollow</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="hollow_in" id="hollow_in" class="form-control tgl" >
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="hollow_out" id="hollow_out" class="form-control tgl" >
                                    <span class="input-group-addon">HL Plafon</span>
                                    <input type="text" name="hollow_plafon" id="hollow_plafon" class="form-control tgl" >
                                </div>
                                <label class="control-label" for="mal_in">Mal</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="mal_in" id="mal_in" class="form-control tgl" >
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="mal_out" id="mal_out" class="form-control tgl" >
                                </div>
                                <label class="control-label" for="gambar_in">Gambar Panel</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="gambar_in" id="gambar_in" class="form-control tgl" >
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="gambar_out" id="gambar_out" class="form-control tgl" >
                                </div>
                                <label class="control-label" for="gambar_in">Galvalume</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="bahan_in" id="bahan_in" class="form-control tgl" >
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="bahan_out" id="bahan_out" class="form-control tgl" >
                                </div>
                            </div> 
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label" for="cat_in">Cat</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="cat_in" id="cat_in" class="form-control tgl" >
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="cat_out" id="cat_out" class="form-control tgl" >
                                    <span class="input-group-addon">Cat Makara</span>
                                    <input type="text" name="catmakara" id="catmakara" class="form-control tgl" >
                                </div>
                                <label class="control-label" for="makara_in">Makara</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="makara_in" id="makara_in" class="form-control tgl" >
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="makara_out" id="makara_out" class="form-control tgl" >
                                    <span class="input-group-addon">Rangka GA</span>
                                    <input type="text" name="rangka_ga" id="rangka_ga" class="form-control tgl" >
                                </div>
                                <label class="control-label" for="packing_in">Packing</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="packing_in" id="packing_in" class="form-control tgl" >
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="packing_out" id="packing_out" class="form-control tgl" >
                                    <span class="input-group-addon">PK Makara</span>
                                    <input type="text" name="pk_packing" id="pk_packing" class="form-control tgl" >
                                </div>
                                <label class="control-label" for="packing_in">Ekspedisi</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="packing_in" id="packing_in" class="form-control tgl" >
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="packing_out" id="packing_out" class="form-control tgl" >
                                </div>
                                <label class="control-label" for="packing_in">Pemasangan</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="packing_in" id="packing_in" class="form-control tgl" >
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="packing_out" id="packing_out" class="form-control tgl" >
                                    <span class="input-group-addon">Out</span>
                                    <select class="form-control select2" name="txtketuatim" id="txtketuatim">
                                        <?php
                                        $q = 'SELECT * FROM `aki_tabel_master` WHERE tanggal_nonaktif="0000-00-00"';
                                        $sql_nik = mysql_query($q,$dbLink3);
                                        $selected = "";
                                        if ($_GET['mode'] == 'edit') {
                                            echo '<option value="'.$dataSph["nik"].'" selected>'.$dataSph["pn"].' - '.$dataSph["kn"].'</option>';
                                            while($rs_nik = mysql_fetch_assoc($sql_nik)){ 
                                                echo '<option value="'.$rs_nik['nik'].'">'.$rs_nik['pname'].' - '.$rs_nik['kname'].'</option>';
                                            }  
                                        }else{
                                            echo '<option value="">Address</option>';
                                            while($rs_nik = mysql_fetch_assoc($sql_nik)){ 
                                                echo '<option value="'.$rs_nik['nik'].'">'.$rs_nik['pname'].' - '.$rs_nik['kname'].'</option>';
                                            }  
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            </section>
            <section class="col-lg-12">
                <div class="box box-default">
                    <div class="box-footer">
                        <?php 
                        if ($_GET['mode']=='edit'){
                            echo '<input type="button" class="btn btn-primary" onclick="omodal()" value="Save">';
                        }else{
                            echo '<input type="submit" class="btn btn-primary" value="Save">';
                        }
                        ?>
                        <a href="index.php?page=html/proyek_list">
                            <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Cancel&nbsp;&nbsp;</button>    
                        </a>
                    </div>
                </div>
            </section>
            
        </div>
    </section>
</form>
