<?php
/* ==================================================
  //=======  : Alibaba
==================================================== */
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/harga_detail";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Pengaturan User';
$hakUser = getUserPrivilege($curPage);

if ($hakUser != 90 ) {
    unset($_SESSION['my']);
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}
?>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function () { 
        $("#myModal").modal({backdrop: 'static'});
        $('#btnClose').click(function(){
            location.href='index.php';
        });
        $('#btnSave').click(function(){
            $("#modal-pass").modal({backdrop: 'static'});
        });
        $('#btnSubmit').click(function(){
            var password = $('#txtPass').val();
            $.post("function/ajax_function.php",{ fungsi: "cekpass", kodeUser:"alibaba",pass:password } ,function(data)
            {
                if(data=='yes') {
                    $.post("function/ajax_function.php",{ fungsi: "updatehpp", txtLessGa1:$('#txtLessGa1').val(),txtLessGa2:$('#txtLessGa2').val(),txtLessGa3:$('#txtLessGa3').val(),txtLessEn1:$('#txtLessEn1').val(),txtLessEn2:$('#txtLessEn2').val(),txtLessEn3:$('#txtLessEn3').val(),txtLessSt1:$('#txtLessSt1').val(),txtLessSt2:$('#txtLessSt2').val(),txtLessSt3:$('#txtLessSt3').val() } ,function(data)
                    {
                        if(data=='yes') {
                            toastr.success('Sukses Update Harga Awal . . . .')
                            $("#modal-pass").modal('hide');
                        }else{
                            toastr.error('Gagal Update Harga Awal . . . .')
                        }
                    });
                }else{
                    toastr.error('Gagal !!!<br> Password Level 1 Salah . . . .')
                    $("#txtPass").focus();
                }
            });
        });
    });
</script>
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="btnClose">&times;</button>
                <h4 class="modal-title">Set Harga Awal</h4>
            </div>
            <div class="modal-body">
                <?php
                $q= "SELECT * FROM `aki_hpp` WHERE ket='d<4'";
                $rsTemp = mysql_query($q, $dbLink);
                if ($datahppLess = mysql_fetch_array($rsTemp)) {}
                ?>
                <div class="modal-header">
                    <section class="col-lg-2">
                        <label class="control-label" for="txtTglTransaksi"><u>FULL</u></label>
                    </section>
                    <section class="col-lg-3">
                        <label class="control-label" for="txtTglTransaksi">Galvalume</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <label class="control-label" for="txtTglTransaksi">Rp.</label>
                            </div>
                            <input type="text" name="txtLessGa1" id="txtLessGa1" class="form-control" onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" value="<?php echo number_format($datahppLess['ga-full']);?>">
                        </div>
                    </section>
                    <section class="col-lg-3">
                        <label class="control-label" for="txtTglTransaksi">Stainless Gold</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <label class="control-label" for="txtTglTransaksi">Rp.</label>
                            </div>
                            <input type="text" name="txtLessSt1" id="txtLessSt1" class="form-control" onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" value="<?php echo number_format($datahppLess['stg-full']);?>">
                        </div>
                    </section>
                    <section class="col-lg-3">
                        <label class="control-label" for="txtTglTransaksi">Enamel</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <label class="control-label" for="txtTglTransaksi">Rp.</label>
                            </div>
                            <input type="text" name="txtLessEn1" id="txtLessEn1" class="form-control" onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" value="<?php echo number_format($datahppLess['en-full']);?>">
                        </div>
                    </section>
                </div>
                <div class="modal-header">
                    <section class="col-lg-2">
                        <label class="control-label" for="txtTglTransaksi"><u>WATERPROOF</u></label>
                    </section>
                    <section class="col-lg-3">
                        <label class="control-label" for="txtTglTransaksi">Galvalume</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <label class="control-label" for="txtTglTransaksi">Rp.</label>
                            </div>
                            <input type="text" name="txtLessGa2" id="txtLessGa2" class="form-control" onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" value="<?php echo number_format($datahppLess['ga-waterproof']);?>">
                        </div>
                    </section>
                    <section class="col-lg-3">
                        <label class="control-label" for="txtTglTransaksi">Stainless Gold</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <label class="control-label" for="txtTglTransaksi">Rp.</label>
                            </div>
                            <input type="text" name="txtLessSt2" id="txtLessSt2" class="form-control" onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" value="<?php echo number_format($datahppLess['stg-waterproof']);?>">
                        </div>
                    </section>
                    <section class="col-lg-3">
                        <label class="control-label" for="txtTglTransaksi">Enamel</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <label class="control-label" for="txtTglTransaksi">Rp.</label>
                            </div>
                            <input type="text" name="txtLessEn2" id="txtLessEn2" class="form-control" onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" value="<?php echo number_format($datahppLess['en-waterproof']);?>">
                        </div>
                    </section>
                </div>
                <div class="modal-header">
                    <section class="col-lg-2">
                        <label class="control-label" for="txtTglTransaksi"><u>TANPA PLAFON</u></label>
                    </section>
                    <section class="col-lg-3">
                        <label class="control-label" for="txtTglTransaksi">Galvalume</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <label class="control-label" for="txtTglTransaksi">Rp.</label>
                            </div>
                            <input type="text" name="txtLessGa3" id="txtLessGa3" class="form-control" onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" value="<?php echo number_format($datahppLess['ga-tplafon']);?>">
                        </div>
                    </section>
                    <section class="col-lg-3">
                        <label class="control-label" for="txtTglTransaksi">Stainless Gold</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <label class="control-label" for="txtTglTransaksi">Rp.</label>
                            </div>
                            <input type="text" name="txtLessSt3" id="txtLessSt3" class="form-control" onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" value="<?php echo number_format($datahppLess['stg-tplafon']);?>">
                        </div>
                    </section>
                    <section class="col-lg-3">
                        <label class="control-label" for="txtTglTransaksi">Enamel</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <label class="control-label" for="txtTglTransaksi">Rp.</label>
                            </div>
                            <input type="text" name="txtLessEn3" id="txtLessEn3" class="form-control" onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))" value="<?php echo number_format($datahppLess['en-tplafon']);?>">
                        </div>
                    </section>
                </div>
            </div>
            <div class="modal-footer">
            <?php
                echo '<button type="button" class="btn btn-primary" id="btnSave">Save changes</button>';
            ?>
            </div>
        </div>
    </div>
</div> 
<div class="modal fade" id="modal-pass">
    <div class="modal-dialog">
        <div class="modal-content bg-secondary">
            <div class="modal-header">
                <h4 class="modal-title">Input Password Level 1 (Owner)</h4>
            </div>
            <div class="modal-body">
                <div class="input-group">
                    <div class="input-group-addon">
                        <label class="control-label" for="txtTglTransaksi">Password</label>
                    </div>
                    <input type="password" name="txtPass" id="txtPass" class="form-control">
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSubmit">Save changes</button>
            </div>
        </div>
    </div>
</div>