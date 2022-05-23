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
error_reporting(0);
//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {
    require_once("./class/c_spk.php");
    $tmpproyek = new c_spk;
//Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpproyek->add($_POST);
    }
//Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpproyek->update($_POST);
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
        //$(".select2").select2(); 
        $(".tgl").datepicker({ format: 'yyyy-mm-dd', autoclose:true }); 
        $("#txttgldeadline").datepicker({ format: 'yyyy-mm-dd', autoclose:true });
        $("#txttglinternal").datepicker({ format: 'yyyy-mm-dd', autoclose:true });
        var btransport = document.getElementById('biayatransport');
        btransport.addEventListener('keyup', function(e){
            btransport.value = formatRupiah(this.value,'');
        });
        var bkaligrafi = document.getElementById('biayakaligrafi');
        bkaligrafi.addEventListener('keyup', function(e){
            bkaligrafi.value = formatRupiah(this.value,'');
        });
    });
    function formatRupiah(angka, prefix){
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split           = number_string.split(','),
        sisa            = split[0].length % 3,
        rupiah          = split[0].substr(0, sisa),
        ribuan          = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if(ribuan){
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? '' + rupiah : '');
    }
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
                        <h3 class="box-title">Termin</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <?php
                    if ($_GET["mode"] == "edit") {
                        if (isset($_GET["nospk"])){
                            $nospk = secureParam($_GET["nospk"], $dbLink);
                        }else{
                            $nospk = "";
                        }
                        $q = "SELECT * FROM `aki_proyek` WHERE md5(nospk)='".$nospk."'";
                        $rsTemp = mysql_query($q, $dbLink);
                        if ($dataproyek = mysql_fetch_array($rsTemp)) {
                           $qnik = "SELECT * FROM `aki_tabel_master` WHERE nik='".$dataproyek['ketuatim']."'";
                           $rsnTemp = mysql_query($qnik, $dbLink2);
                           if ($datanik = mysql_fetch_array($rsnTemp)) {
                                echo "<input type='hidden' name='kodeTransaksi' value='" . $dataproyek["nospk"] . "'>";
                           }
                            
                        } 
                    } 
                    ?>
                    <div class="box-body">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="txtjpekerjaan">DP</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <?php
                                            $chk='';
                                            if ($dataproyek['dp']==1) {
                                                $chk = "checked";
                                            }
                                            echo '<input type="checkbox" name="chkdp" id="chkdp" '.$chk.'>';
                                        ?>
                                    </span>
                                    <input type="text" value="<?php echo $dataproyek['ket_dp']; ?>" class="form-control" name="ketdp" id="ketdp">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="txtT2">T2</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <?php
                                            $chk='';
                                            if ($dataproyek['t2']==1) {
                                                $chk = "checked";
                                            }
                                            echo '<input type="checkbox" name="chkt2" id="chkt2" '.$chk.'>';
                                        ?></span>
                                    <input type="text" value="<?php echo $dataproyek['ket_t2']; ?>" name="kett2" id="kett2" class="form-control" >
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="txtT3">T3</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <?php
                                            $chk='';
                                            if ($dataproyek['t3']==1) {
                                                $chk = "checked";
                                            }
                                            echo '<input type="checkbox" name="chkt3" id="chkt3" '.$chk.'>';
                                        ?>
                                    </span>
                                    <input type="text" value="<?php echo $dataproyek['ket_t3']; ?>" name="kett3" id="kett3" class="form-control" >
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="txtT4">T4</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <?php
                                            $chk='';
                                            if ($dataproyek['t4']==1) {
                                                $chk = "checked";
                                            }
                                            echo '<input type="checkbox" name="chkt4" id="chkt4" '.$chk.'>';
                                        ?>
                                    </span>
                                    <input type="text" value="<?php echo $dataproyek['ket_t4']; ?>" name="kett4" id="kett4" class="form-control" >
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="txttransport">Biaya Transport</label>
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="text" value="<?php echo number_format($dataproyek['biaya_transportasi']); ?>" name="biayatransport" id="biayatransport" class="form-control" >
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="txtkaligrafi">Biaya Kaligrafi</label>
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="text" value="<?php echo number_format($dataproyek['biaya_kaligrafi']); ?>" name="biayakaligrafi" id="biayakaligrafi" class="form-control" >                                
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
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label" for="rangka_in">Rangka</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="rangka_in" id="rangka_in" class="form-control tgl" value="<?php if($dataproyek['rangka_in']!='0000-00-00'){echo $dataproyek['rangka_in'];} ?>">
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="rangka_out" id="rangka_out" class="form-control tgl" value="<?php if($dataproyek['rangka_out']!='0000-00-00'){echo $dataproyek['rangka_out'];} ?>">
                                </div>
                                <label class="control-label" for="hollow_in">Hollow</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="hollow_in" id="hollow_in" class="form-control tgl" value="<?php if($dataproyek['hollow_in']!='0000-00-00'){echo $dataproyek['hollow_in'];} ?>">
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="hollow_out" id="hollow_out" class="form-control tgl" value="<?php if($dataproyek['hollow_out']!='0000-00-00'){echo $dataproyek['hollow_out'];} ?>">
                                    <span class="input-group-addon">HL Plafon</span>
                                    <input type="text" name="hollow_plafon" id="hollow_plafon" class="form-control tgl" value="<?php if($dataproyek['hl_plafon']!='0000-00-00'){echo $dataproyek['hl_plafon'];} ?>">
                                </div>
                                <label class="control-label" for="mal_in">Mal</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="mal_in" id="mal_in" class="form-control tgl" value="<?php if($dataproyek['hl_plafon']!='0000-00-00'){echo $dataproyek['mal_in'];} ?>">
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="mal_out" id="mal_out" class="form-control tgl" value="<?php if($dataproyek['mal_out']!='0000-00-00'){echo $dataproyek['mal_out'];} ?>">
                                </div>
                                <label class="control-label" for="gambar_in">Gambar Panel</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="gambarp_in" id="gambarp_in" class="form-control tgl" value="<?php if($dataproyek['gambarp_in']!='0000-00-00'){echo $dataproyek['gambarp_in'];} ?>">
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="gambarp_out" id="gambarp_out" class="form-control tgl" value="<?php if($dataproyek['gambarp_out']!='0000-00-00'){ echo $dataproyek['gambarp_out'];} ?>">
                                </div>
                                <label class="control-label" for="gambar_in">Galvalume</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="bahan_in" id="bahan_in" class="form-control tgl" value="<?php if($dataproyek['bahan_in']!='0000-00-00'){echo $dataproyek['bahan_in'];} ?>">
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="bahan_out" id="bahan_out" class="form-control tgl" value="<?php if($dataproyek['bahan_out']!='0000-00-00'){echo $dataproyek['bahan_out'];} ?>">
                                </div>
                            </div> 
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label" for="cat_in">Cat</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="cat_in" id="cat_in" class="form-control tgl" value="<?php if($dataproyek['cat_in']!='0000-00-00'){echo $dataproyek['cat_in'];} ?>">
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="cat_out" id="cat_out" class="form-control tgl" value="<?php if($dataproyek['cat_out']!='0000-00-00'){echo $dataproyek['cat_out'];} ?>">
                                    <span class="input-group-addon">Cat Makara</span>
                                    <input type="text" name="catmakara" id="catmakara" class="form-control tgl" value="<?php if($dataproyek['catmakara']!='0000-00-00'){echo $dataproyek['catmakara'];} ?>">
                                </div>
                                <label class="control-label" for="makara_in">Makara</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="makara_in" id="makara_in" class="form-control tgl" value="<?php if($dataproyek['makara_in']!='0000-00-00'){echo $dataproyek['makara_in'];} ?>">
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="makara_out" id="makara_out" class="form-control tgl" value="<?php if($dataproyek['makara_out']!='0000-00-00'){echo $dataproyek['makara_out'];} ?>">
                                    <span class="input-group-addon">Rangka GA</span>
                                    <input type="text" name="rangka_ga" id="rangka_ga" class="form-control tgl" value="<?php if($dataproyek['rangka_ga']!='0000-00-00'){echo $dataproyek['rangka_ga'];} ?>">
                                </div>
                                <label class="control-label" for="packing_in">Packing</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="packing_in" id="packing_in" class="form-control tgl" value="<?php if($dataproyek['packing_in']!='0000-00-00'){echo $dataproyek['packing_in'];} ?>">
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="packing_out" id="packing_out" class="form-control tgl" value="<?php if($dataproyek['packing_out']!='0000-00-00'){echo $dataproyek['packing_out'];} ?>">
                                    <span class="input-group-addon">PK Makara</span>
                                    <input type="text" name="pk_makara" id="pk_makara" class="form-control tgl" value="<?php if($dataproyek['pk_makara']!='0000-00-00'){echo $dataproyek['pk_makara'];} ?>">
                                </div>
                                <label class="control-label" for="packing_in">Ekspedisi</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="ekspedisi_in" id="ekspedisi_in" class="form-control tgl" value="<?php if($dataproyek['ekspedisi_in']!='0000-00-00'){echo $dataproyek['ekspedisi_in'];} ?>">
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="ekspedisi_out" id="ekspedisi_out" class="form-control tgl" value="<?php if($dataproyek['ekspedisi_out']!='0000-00-00'){echo $dataproyek['ekspedisi_out'];} ?>">
                                </div>
                                <label class="control-label" for="packing_in">Pemasangan</label>
                                <div class="input-group">
                                    <span class="input-group-addon">In</span>
                                    <input type="text" name="pemasangan_in" id="pemasangan_in" class="form-control tgl" value="<?php if($dataproyek['pemasangan_in']!='0000-00-00'){echo $dataproyek['pemasangan_in'];} ?>">
                                    <span class="input-group-addon">Out</span>
                                    <input type="text" name="pemasangan_out" id="pemasangan_out" class="form-control tgl" value="<?php if($dataproyek['pemasangan_out']!='0000-00-00'){echo $dataproyek['pemasangan_out'];} ?>" >
                                    <span class="input-group-addon">Out</span>
                                    <select class="form-control select2" name="txtketuatim" id="txtketuatim">
                                        <?php
                                        $qnik = 'SELECT * FROM `aki_tabel_master` WHERE tanggal_nonaktif="0000-00-00"';
                                        $sql_nik = mysql_query($qnik,$dbLink3);
                                        $selected = "";
                                        if ($_GET['mode'] == 'edit') {
                                            if ($dataproyek['ketuatim']=='') {
                                                echo '<option value="'.$dataproyek["ketuatim"].'" selected>'.$dataproyek['ketuatim'].' - '.$datanik['kname'].'</option>';
                                            }else{
                                                echo '<option value="'.$dataproyek["ketuatim"].'" selected>'.$dataproyek['ketuatim'].' - '.$datanik['kname'].'</option>';
                                            }
                                            
                                            while($rs_nik = mysql_fetch_assoc($sql_nik)){ 
                                                echo '<option value="'.$rs_nik['nik'].'">'.$rs_nik['nik'].' - '.$rs_nik['kname'].'</option>';
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
                        <a href="index.php?page=html/proyek_list">
                            <button type="button" class="btn btn-default">&nbsp;&nbsp;Cancel&nbsp;&nbsp;</button>    
                        </a>
                        <input type="submit" class="btn btn-primary pull-right" value="Save">
                        <!-- <?php 
                        if ($_GET['mode']=='edit'){
                            echo '<input type="button" class="btn btn-primary pull-right" onclick="omodal()" value="Save">';
                        }else{
                            echo '<input type="submit" class="btn btn-primary pull-right" value="Save">';
                        }
                        ?> -->
                    </div>
                </div>
            </section>
            
        </div>
    </section>
</form>
