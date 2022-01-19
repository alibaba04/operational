<?php
/* ==================================================
  //=======  : Alibaba
==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/user_detail";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Pengaturan User';
$hakUser = getUserPrivilege($curPage);

if ($hakUser != 90 ) {
    if ($hakUser != 50) {
        unset($_SESSION['my']);
        echo "<p class='error'>";
        die('User anda tidak terdaftar untuk mengakses halaman ini!');
        echo "</p>";
    }
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
function validasiForm(form){
    if(form.txtKodeUser.value=="")
       {
        alert("Kode User harus diisi !");
        form.txtKodeUser.focus();
        return false;
    }
    if(form.txtNama.value=="")
    {
        alert("Nama harus diisi !");
        form.txtNama.focus();
        return false;
    }
    
    if(form.txtPassword.value=="")
    {
        alert("Password harus diisi !");
        form.txtPassword.focus();
        return false;
    }
    if(form.txtConfirmPassword.value=="")
    {
        alert("Konfirmasi Password harus diisi !");
        form.txtConfirmPassword.focus();
        return false;
    }
    if(form.txtConfirmPassword.value!=form.txtPassword.value)
    {
        alert("Password tidak sesuai dengan konfirmasi. Silakan ulangi !");
        form.txtConfirmPassword.value = "";
        form.txtConfirmPassword.focus();
        return false;
    }
    if(form.cbogroup.value=='')
    {
        alert("Pilih User Group !");
        form.cbogroup.focus();
        return false;
    }
    return true;
}
</SCRIPT>

<section class="content-header">
    <h1>
        PENGATURAN USER
        <small>Detail Pengaturan User</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pengaturan</li>
        <li class="active">User</li>
    </ol>
</section>

<section class="content">
    <!-- Main row -->
    <div class="row">
        <section class="col-lg-6">
            <div class="box box-primary">
                <form action="index2.php?page=view/user_list" method="post" name="frmUserDetail" onSubmit="return validasiForm(this);">
                    <div class="box-header">
                        <i class="ion ion-clipboard"></i>
                        <?php
                        if ($_GET["mode"] == "edit") {
                            echo '<h3 class="box-title">UBAH DATA USER </h3>';
                            echo "<input type='hidden' name='txtMode' value='Edit'>";

                            //Secure parameter from SQL injection
                            $kode = secureParam($_GET["kode"], $dbLink);

                            $q = "SELECT a.kodeUser, a.nama, a.aktif, a.password, u.kodeGroup ";
                            $q.= "FROM aki_user a left join aki_usergroup u on a.kodeUser=u.kodeUser WHERE md5(a.kodeUser)='".$kode."'";

                            $rsTemp = mysql_query($q, $dbLink);
                            if ($dataUser = mysql_fetch_array($rsTemp)) {
                                echo "<input type='hidden' name='kodeUser' value='" . $dataUser["kodeUser"] . "'>";
                            } else {
                                ?>
                                <script language="javascript">
                                    alert("Kode Tidak Valid");
                                    history.go(-1);
                                </script>
                                <?php
                            }
                        } else {
                            echo '<h3 class="box-title">TAMBAH DATA USER </h3>';
                            echo "<input type='hidden' name='txtMode'  value='Add'>";
                        }
                        ?>
                    </div>
                    <div class="box-body">

                        <div class="form-group">
                            <label class="control-label" for="txtKodeUser">Username</label>
                            <script language="javascript">
                                function cekKode()
                                {
                                    $("#msgbox").text('Checking...');

                                    $.post("function/ajax_function.php",{ fungsi: "checkKodeUser", kodeUser:$("#txtKodeUser").val() } ,function(data)
                                    {
                                     if(data=='yes') 
                                     {
                                        $("#msgbox").removeClass().addClass('messageboxerror').text('Kode User telah ada. Gunakan kode lain.').fadeIn("slow");
                                    }
                                    else if (data=='no') 
                                    {
                                        $("#msgbox").removeClass().addClass('messageboxok').text('Kode User belum tercatat - data baru.').fadeIn("slow");
                                    } else {
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
                           <input name="txtKodeUser" id="txtKodeUser" maxlength="15" class="form-control" onblur="cekKode();"
                           value="<?php if($_GET['mode']=="edit") { echo $dataUser["kodeUser"]; }?>" placeholder="Username" onKeyPress="return handleEnter(this, event)"><span id="msgbox"></span>

                       </div>

                       <div class="form-group">
                        <label class="control-label" for="txtNama">Nickname</label>

                        <input name="txtNama" id="txtNama" maxlength="20" class="form-control" 
                        value="<?php if($_GET['mode']=="edit") { echo $dataUser["nama"]; } ?>" placeholder="Nickname" onKeyPress="return handleEnter(this, event)">

                    </div>
                    <?php 
                    if($_GET["mode"]!="edit")
                    {
                        ?>
                        <div class="form-group">
                            <label class="control-label" for="txtPassword">Password</label>

                            <input type="password" name="txtPassword" id="txtPassword" maxlength="50" class="form-control" 
                            value="" placeholder="Password" onKeyPress="return handleEnter(this, event)">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="txtConfirmPassword">Confirm Password</label>

                            <input type="password" name="txtConfirmPassword" id="txtConfirmPassword" class="form-control" 
                            value="" placeholder="Confirm Password" onKeyPress="return handleEnter(this, event)">
                        </div>
                        <?php
                    }
                    ?>
                    <div class="form-group">
                        <select name="cbogroup" id="cbogroup" class="form-control" onKeyPress="return handleEnter(this, event)">
                            <?php
                            $q = "SELECT * FROM `aki_groups`";
                            $sql_g = mysql_query($q,$dbLink);
                            if($_GET['mode']=='edit'){
                                echo '<option value="'.$dataUser['kodeGroup'].'" selected>'.$dataUser['kodeGroup'].'</option>';
                            }else{
                                echo "<option value=''>User Group</option>";
                            }
                            while ($rs_rangka = mysql_fetch_array($sql_g)) {
                                if ($dataUser['kodeGroup']!=$rs_rangka['kodeGroup']) {
                                    echo '<option value="'.$rs_rangka['kodeGroup'].'">'.$rs_rangka['nama'].'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="rdoStatus">Status</label>

                        <input name="rdoStatus" id="rdoStatus" type="radio" value="Y"  <?php if($_GET['mode']=="edit") { if($dataUser[2]=="Y") {echo "checked"; }} else {echo "checked";} ?> onKeyPress="return handleEnter(this, event)">&nbsp;Active&nbsp;&nbsp;
                        <input name="rdoStatus" id="rdoStatus" type="radio" value="T" <?php if($_GET['mode']=="edit") { if($dataUser[2]=="T") {echo "checked"; }} ?> onKeyPress="return handleEnter(this, event)">&nbsp;Non Active&nbsp;&nbsp;
                    </div>

                </div>
                <div class="box-footer">
                    <input type="submit" class="btn btn-primary" value="Simpan">

                    <a href="index.php?page=html/user_list">
                        <button type="button" class="btn btn-default pull-right">&nbsp;&nbsp;Batal&nbsp;&nbsp;</button>    
                    </a>

                </div>
            </form>
        </div>    
    </section>
</div>
</section>
