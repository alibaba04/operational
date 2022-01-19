<?php
/* ==================================================
  ==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/profil_detail";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Data Profil';
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
       
        if(form.txtName.value=='' )
        {
            alert("Nama harus diisi!");
            form.txtName.focus();
            return false;
        }
        if(form.txtalamat.value=='' )
        {
            alert("Alamat harus diisi!");
            form.txtalamat.focus();
            return false;
        }
        if(form.txtid.value=='' )
        {
            alert("ID harus diisi!");
            form.txtid.focus();
            return false;
        }
        if(form.txtnoid.value=='0' )
        {
            alert("no id harus diisi!");
            form.txtnoid.focus();
            return false;
        }
        
        return true;
    }
</SCRIPT>

<section class="content-header">
    <h1>
        DATA PROFIL PERUSAHAAN
        <small>Detail Data </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pengaturan</li>
        <li class="active">Data Profil </li>
    </ol>
</section>

<section class="content">
    <!-- Main row -->
    <div class="row">
        <section class="col-lg-6">
            <div class="box box-primary">
                <form action="index2.php?page=view/profil_list" method="post" name="frmProfilDetail" onSubmit="return validasiForm(this);">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <?php
                        $dataRekening = "";
                        if ($_GET["mode"] == "edit") {
                            echo '<h3 class="box-title">UBAH DATA PROFIL PERUSAHAAN </h3>';
                            echo "<input type='hidden' name='txtMode' value='Edit'>";

                            $q = "SELECT * FROM aki_kk_user ";
                            $q.= "WHERE 1=1 ";
                            
                            $rsTemp = mysql_query($q, $dbLink);
                            if ($dataProfil = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' name='id' value='" . $dataProfil[0] . "'>";
                            } 
                        } else {
                            echo '<h3 class="box-title">TAMBAH DATA PROFIL PERUSAHAAN </h3>';
                            echo "<input type='hidden' name='txtMode'  value='Add'>";
                        }
                        ?>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label" for="txtName">Nama</label>
                            
                            <input name="txtName" id="txtName" maxlength="50" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['name']; } ?>" placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">    
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtGedung">Title</label>

                            <input name="txtGedung" id="txtGedung"  class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['title']; } ?>" placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtid">id</label>

                            <input name="txtid" id="txtid" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['jenis']; } ?>" placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtnoid">No</label>

                            <input name="txtnoid" id="txtnoid" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['no_id']; } ?>" placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtalamat">Alamat</label>

                            <input name="txtalamat" id="txtalamat" class="form-control" value="<?php if ($_GET['mode']=='edit') { echo $dataProfil['address']; } ?>" placeholder="Wajib diisi" onKeyPress="return handleEnter(this, event)">

                        </div>
                    </div>
                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="Simpan">

                        <a href="index.php?page=view/profil_list">
                            <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Batal&nbsp;&nbsp;</button>    
                        </a>

                    </div>
                </form>
            </div>    
        </section>
    </div>
</section>
