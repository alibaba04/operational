<?php
/* ==================================================
  //=======  : Alibaba
  //Created : 8 Januari 2017
  ==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/setting_detail";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Master Setting';
$hakUser = getUserPrivilege($curPage);

if ($hakUser != 90) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}
?>

<!-- Include script date di bawah jika ada field tanggal -->
<script type="text/javascript" src="js/date.js"></script>
<script type="text/javascript" src="js/jquery.datePicker.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="css/datePicker.css">

<script type="text/javascript" charset="utf-8">
    $(function()
    {
        $('.date-pick').datePicker({startDate:'01/01/1970'});
    });
</script>
<!-- End of Script Tanggal -->

<!-- Include script di bawah jika ada field yang Huruf Besar semua -->
<script src="js/jquery.bestupper.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".bestupper").bestupper();
    });
</script>

<SCRIPT language="JavaScript" TYPE="text/javascript">
    function validasiForm(form)
    {
       
        if(form.txtNamaSetting.value=='' )
        {
            alert("Nama Setting harus diisi!");
            form.txtNamaSetting.focus();
            return false;
        }
        if(form.txtNilai.value=='' )
        {
            alert("Nilai harus diisi!");
            form.txtNilai.focus();
            return false;
        }  
        return true;
    }
</SCRIPT>

<section class="content-header">
    <h1>
        MASTER SETTING
        <small>Detail Master Setting</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pengaturan</li>
        <li class="active">Master Setting</li>
    </ol>
</section>

<section class="content">
    <!-- Main row -->
    <div class="row">
        <section class="col-lg-6">
            <div class="box box-primary">
                <form action="index2.php?page=view/setting_list" method="post" name="frmSettingDetail" onSubmit="return validasiForm(this);">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <?php
                        
                        if ($_GET["mode"] == "edit") {
                            echo '<h3 class="box-title">UBAH DATA SETTING </h3>';
                            echo "<input type='hidden' name='txtMode' value='Edit'>";

                            //Secure parameter from SQL injection
                            $kode = secureParam($_GET["kode"], $dbLink);

                            $q = "SELECT idSetting, namaSetting, Nilai FROM aki_setting WHERE md5(idSetting)='".$kode."'";
                            
                            $rsTemp = mysql_query($q, $dbLink);

                            if ($dataSetting = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' name='kodeSetting' value='" . $dataSetting[0] . "'>";
                            } else {
                                ?>
                                <script language="javascript">
                                    alert("Kode Tidak Valid");
                                    history.go(-1);
                                </script>
                                <?php
                            }
                        } else {
                            echo '<h3 class="box-title">TAMBAH DATA SETTING </h3>';
                            echo "<input type='hidden' name='txtMode'  value='Add'>";
                        }
                        ?>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label" for="txtNamaSetting">Nama Setting</label>
                            
                            <script language="javascript">
                            function cekKode(){
                                            $("#msgbox").text('Checking...');

                                            $.post("function/ajax_function.php",{ fungsi: "checkNamaSetting", namaSetting:$("#txtNamaSetting").val() } ,function(data)
                                            {
                                                    if(data=='yes') 
                                                    {
                                                            $("#msgbox").removeClass().addClass('messageboxerror').text('Nama Setting telah ada. Gunakan kode lain.').fadeIn("slow");
                                                    }
                                                    else if (data=='no') 
                                                    {
                                                            $("#msgbox").removeClass().addClass('messageboxok').text('Nama Setting belum tercatat - data baru.').fadeIn("slow");
                                                    }
                                                    else if (data=='none') 
                                                    {
                                                        $("#msgbox").removeClass().addClass('messageboxok').text('Nama Setting harus diisi.').fadeIn("slow");
                                                    }
                                                    else {
                                                        $("#msgbox").removeClass().addClass('messageboxerror').text('Maaf, terjadi error pada System').fadeIn("slow");

                                                    }

                                            });
                                    }

                            </script>
                            <style type="text/css">
                            .messageboxok{
                            font-weight:bold;
                            color:#008000;
                            }
                            .messageboxerror{
                            font-weight:bold;
                            color:#CC0000;
                            }
                            </style>
                            
                            <input name="txtNamaSetting" id="txtNamaSetting" maxlength="45" 
                                   class="form-control" value="<?php if ($_GET["mode"]=='edit'){ echo $dataSetting[1]; }?>" 
                                   placeholder="Wajib diisi" 
                                       <?php if ($_GET["mode"]=='edit'){ echo "readonly";}?>
                                   onKeyPress="return handleEnter(this, event)" onblur="cekKode();"><span id="msgbox"></span>
                            
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtNilai">Nilai</label>

                            <input name="txtNilai" id="txtNilai" maxlength="20" class="form-control" 
                                   value="<?php if ($_GET["mode"]=='edit'){ echo $dataSetting[2]; } ?>" placeholder="Wajib diisi" 
                                   onKeyPress="return handleEnter(this, event)">

                        </div>
                        
                        
                        
                    </div>
                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="Simpan">

                        <a href="index.php?page=view/setting_list">
                            <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Batal&nbsp;&nbsp;</button>    
                        </a>

                    </div>
                </form>
            </div>    
        </section>
    </div>
</section>
