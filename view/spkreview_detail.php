<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/spkreview_detail";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Review SPK';
$hakUser = getUserPrivilege($curPage);

if ($hakUser < 10) {
    session_unregister("my");
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}

//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" ) {

    require_once("./class/c_spk.php");
    $tmpkk = new c_spk;

//Jika Mode Tambah/Add Note
    if ($_POST["txtMode"] == "edit") {
        $pesan = $tmpkk->edit($_POST);
    }

//Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
//Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Warning!!, please text to " . $mailSupport . " for support this error!.";
    }
    header("Location:index.php?page=view/spk_list&pesan=" . $pesan);
    exit;
}
$datacolor1 ='';
$datakcolor1 = '';
?>
<SCRIPT language="JavaScript" TYPE="text/javascript">
  $(document).ready(function () {
    //$("#myNoteAcc").modal({backdrop: 'static'});
    var link = window.location.href;
    $('#btnSend').click(function(){
      $('#txtNote').val($('#txtmNote').val());
      sendnotif('Send');
    });
    $('#btnApprove').click(function(){
      sendnotif('Approve');
    });
  });
  function omodal() {
    $("#myNoteAcc").modal({backdrop: 'static'});
  }
  function accproject() {
    $("#myAcc").modal({backdrop: 'static'});
    $('#txtMode').val('edit');
  }
</SCRIPT>
<form action="index2.php?page=view/spkreview_detail" method="post" name="frmSiswaDetail" onSubmit="return validasiForm(this);" autocomplete="off">
  <?php

  if (isset($_GET["noKK"])){
    $noKk = secureParam($_GET["noKK"], $dbLink2);
  }else{
    $noKk = "";
  }
  $q = "SELECT ROW_NUMBER() OVER(PARTITION BY dkk.model ORDER BY kk.idKk) AS id,spk.*,kk.*, dkk.*,u.nama,p.name as pn,p.id as idP,k.name as kn,k.id as idK ";
  $q.= "FROM aki_spk spk left join aki_kk kk on spk.nokk=kk.noKk right join aki_dkk dkk on kk.noKk=dkk.noKk left join aki_user u on kk.kodeUser=u.kodeUser left join provinsi p on kk.provinsi=p.id LEFT join kota k on kk.kota=k.id ";
  $q.= "WHERE 1=1 and MD5(kk.noKk)='" . $noKk."'";
  $q.= " ORDER BY kk.noKk desc ";
  $txtnospk='';
  $approvekk = '';
  $rsTemp = mysql_query($q, $dbLink2);
  if ($dataspk = mysql_fetch_array($rsTemp)) {
    $kota = '';$kode = '';
    $kode = substr($dataspk["nospk"],0,4);
    if (strpos($dataspk["kn"], 'KOTA') !== false){
      $kota = substr($dataspk["kn"],5,1);
    }else if(strpos($dataspk["kn"], 'KABUPATEN') !== false){
      $kota = substr($dataspk["kn"],9,1);
    }
    echo "<input type='hidden' name='txtnoproject' id='txtnoproject' value='" . $kota.$kode . "'>";
    echo "<input type='hidden' name='txtnoKk' id='txtnoKk' value='" . $dataspk["noKk"] . "'>";
    echo "<input type='hidden' name='txtnoSph' id='txtnoSph' value='" . $dataspk["noSph"] . "'>";
    echo "<input type='hidden' name='txtnoKkEn' id='txtnoKkEn' value='" . $_GET["noKK"] . "'>";
    echo "<input type='hidden' name='txtnoSpk' id='txtnoSpk' value='" . $dataspk["nospk"] . "'>";
    $txtnospk=$dataspk["nospk"];
    $filekubah=$dataspk["filekubah"];
    $filekaligrafi=$dataspk["filekaligrafi"];
    $approvekk = $dataspk["approve"];
    $hargaKubah = $dataspk["harga"];
  }
  if ($_GET["mode"] == "addNote") {
    echo "<input type='hidden' name='txtMode' id='txtMode' value='addNote'>";
  }
  echo "<input type='hidden' name='txtuser' id='txtuser' value='" . $_SESSION["my"]->privilege . "'>";
  ?>
  <section class="invoice">
    <div class="row">
      <div class="col-sm-6 invoice-col">
        <center>
          <?php 
          if ($filekubah!='') {
            echo '<img src="../uploads/'.$filekubah.'" alt="First slide" width="300" height="200">'; 
          }
          if ($filekaligrafi!='') {
            echo '<img src="../uploads/'.$filekaligrafi.'" alt="First slide" width="300" height="200">'; 
          }
          if ($filekubah=='' && $filekaligrafi=='') {
            echo '<img src="../uploads/blank.png" alt="First slide" width="300" height="200">'; 
          }
          echo '<input type="hidden" name="datadesain" id="datadesain" class="form-control" value="'.$filekubah.'">';
          ?>
        </center>
        <p class="lead"> </p>
      </div>
      <div class="col-sm-6 invoice-col">
        <h2 class="page-header" style="margin: 0;">
          <b><i class="fa fa-globe"></i> <?php  echo $dataspk["nospk"]; ?></b>
          <small>No Kontrak: <?php  echo $dataspk["noKk"]; ?></small>
          <input type="hidden" name="txtNote" id="txtNote" class="form-control" value="" placeholder="Empty" >
        </h2>
        <h4>Proyek <?php if ( $dataspk["project_pemerintah"]=='1') {
         echo 'Pemerintah '.$dataspk["nproyek"];
         }else{
          echo $dataspk["nproyek"];
        }  
        $mproduksi=0;
        $mpemasangan=0;
        $q2="SELECT kk.mproduksi,kk.mpemasangan, dkk.*FROM aki_kk kk right join aki_dkk dkk on kk.noKk=dkk.noKk WHERE 1=1 and MD5(kk.noKk)='".$noKk."'";
        $rsTemp = mysql_query($q2, $dbLink2);
        $i=1;
        while ($datadSph = mysql_fetch_array($rsTemp)) {
          $mproduksi=$datadSph['mproduksi'];
          $mpemasangan=$datadSph['mpemasangan'];
          $rangka='';
          if ($datadSph['dt'] != 0){
            $rangka = cekrangka($datadSph['dt']);
          }else{
            $rangka = cekrangka($datadSph['d']);
          }
          $rangkad='';
          if ($datadSph['d']>=6) {
            $rangkad=chr(12).'  Model Rangka <b>Double Frame (Kremona)</b><br>';
          }
          $bahan='';
          $Finishing='';
          if ($datadSph['bahan']>='Galvalume') {
            $Finishing=chr(12).'  Finishing coating Enamel dengan suhu 800-900<sup>o</sup> Celcius<br>';
            $bahan=chr(12).'  Bahan terbuat dari plat besi SPCC SD 0,9 - 1 mm (Spek Enamel Grade)<br>';
          }else{
            if ($datadSph['d']>=1 ) {
              $bahan=chr(12).'  Bahan terbuat dari plat Galvalume 0,4 - 0,5 mm<br>';
            }else{
              $bahan=chr(12).'  Bahan terbuat dari plat Galvalume 0,4 mm<br>';
            }
            $Finishing=chr(12).'  Finishing <b>Cat PU</b> dengan 2 komponen pengecatan :<br>&nbsp&nbsp&nbsp'.chr(45).'  Epoxy<br>&nbsp&nbsp&nbsp'.chr(45).'  Cat PU 2 Komponen <br>';
          }
          $plafon='';
          if ($datadSph['plafon']==0) {
            $plafon=chr(12).'  Plafon kalsiboard 3 mm motif <b>AWAN</b>.<br>'.chr(12).'  Kedap air menggunakan membran bakar 3 mm<br>';
          }else if($datadSph['plafon']==2){
            $plafon=chr(12).'  Kedap air menggunakan membran bakar 3 mm<br>';
          }
          $aksesoris='';
          if ($datadSph['d']>=5 ){
            $aksesoris=chr(12).'  Makara bahan galvalume bola full warna gold bentuk <b>Lafadz Allah</b><br>'.chr(12).'  Penangkal Petir (Panjang Kabel 25 m)<br>';
            if ($datadSph['d']>=6){
              $lampu='';
              if ($datadSph['d']>=15) {
                $lampu='8';
              }else{
                $lampu='4';
              }
              $aksesoris=$aksesoris.chr(12).'  Lampu Sorot '.$lampu.' Sisi (Panjang Kabel 5 m)<br>';
            }
          }else{
            $aksesoris=chr(12).'  Makara bahan galvalume warna gold bentuk <b>Lafadz Allah</b><br>';
          }
          $i++;
        }
        ?></h4>
        <h5><address><th>Alamat Masjid: </th><br><?php  echo $dataspk["alamat_proyek"]; ?></td><address></h5>
          <?php
            if ($dataspk["noproyek"]!='-') {
              echo '<div class="col-lg-3"><button type="" class="btn btn-block btn-default btn-lg" disabled="">'.$dataspk["noproyek"].'</button></div>';
            }
            if ($dataspk["ket"]!='-') {
              echo '<div class="col-lg-3"><button type="" class="btn btn-block btn-default btn-lg" disabled="">'.strtoupper($dataspk["ket"]).'</button></div>';
            }
          ?>
        <div class="col-lg-3">
          <button type="" class="btn btn-block btn-default btn-lg" disabled="">
          <?php
            if ($dataspk["project_pemerintah"]=='1') {
              echo "PPN";
            }else{
              echo "Non PPN";
            }
          ?>
          </button>
        </div>
        <p class="lead"> </p>
      </div>
    </div>
  </section>
  <section class="invoice">
    <div class="row">
      <div class="nav-tabs-custo">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#cust" data-toggle="tab">Customer</a></li>
          <li><a href="#spec" data-toggle="tab">Specification</a></li>
          <div class="col-lg-6 pull-right">
            <?php 
            if ($hakUser > 60 ) {
              if ($dataspk["noproyek"] == '-') {
                echo '<button type="button" class="btn btn-success pull-right" onclick="accproject()"><i class="fa fa-thumbs-up"></i> Add to Project</button>';
              }
            }?>
          </div>
        </ul>
        <div class="tab-content">
          <div class="active tab-pane" id="cust">
            <ul class="timeline timeline-inverse">
              <li>
                <i class="fa fa-user bg-aqua"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i> </span>
                  <h5 class="timeline-header">Nama </h5>
                  <div class="timeline-body"><?php  echo $dataspk["nama_cust"].' ('.$dataspk["jabatan"].')'; ?>
                  </div>
                </div>
              </li>
              <li>
                <i class="fa fa-id-card-o bg-aqua"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i></span>
                  <h5 class="timeline-header">No Identitas </h5>
                  <div class="timeline-body"><?php  echo $dataspk["no_id"].' ('.$dataspk["jenis_id"].')'; ?>
                  </div>
                </div>
              </li>
              <li>
                <i class="fa fa-phone bg-aqua"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i></span>
                  <h5 class="timeline-header">No HP </h5>
                  <div class="timeline-body">
                    <?php  echo $dataspk["no_phone"]; ?>
                  </div>
                </div>
              </li>
              <li>
                <i class="fa fa-map-marker bg-aqua"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i></span>
                  <h5 class="timeline-header">Alamat Customer </h5>
                  <div class="timeline-body">
                    <?php  echo $dataspk["alamat"].' '.$dataspk["kn"].', '.$dataspk["pn"]; ?>
                  </div>
                </div>
              </li>
              <li>
                <i class="fa fa-clock-o bg-gray"></i>
              </li>
            </ul>
          </div>
          <div class="tab-pane" id="spec">
            <?php
            $q4="SELECT dkk.*,col.* FROM aki_dkk dkk left join aki_kkcolor col on dkk.noKK=col.noKK and dkk.nomer=col.nomer WHERE 1=1 and MD5(dkk.noKk)='".$noKk."' group by dkk.nomer order by dkk.nomer asc";
            $rsTemp3 = mysql_query($q4, $dbLink2);
            while ($detail = mysql_fetch_array($rsTemp3)) {
              ?>
              <ul class="timeline timeline-inverse" style="margin: 1%;">
                <li style="margin-bottom: 0;">
                  <i class="fa fa-dot-circle-o bg-aqua"></i>
                  <div class="timeline-item">
                    <span class="time"><i class="fa fa-clock-o"></i> </span>
                    <h5 class="timeline-header">Proyek <?php  echo $detail["nomer"]+1; ?> </h5>
                    <div class="timeline-body"><h4><b>Kubah <?php echo $detail["bahan"].', '.strtoupper($detail["model"]); ?>
                  </b></h4></div>
                  <div class="timeline-body"><h4>Diameter <b><?php  echo $detail["d"]; ?> meter,</b> Tinggi <b><?php  echo $detail["t"]; ?> meter,</b> Luas <b><?php  echo $detail["luas"]; ?> meter<sup>2</sup></b></h4>
                  </div>
                  <div class="timeline-body" style="margin-left: 1.5%;">
                    <?php  
                    echo chr(12).'  Rangka utama pipa galvanis '.$rangka.'<br>'.$rangkad.chr(12).'  Hollow 1,5 x 3,5 cm tebal 0,7 mm<br>';
                    echo $bahan.$Finishing; 
                    echo $plafon; 
                    echo $aksesoris; ?></div>
                    <div class="timeline-body"><h4><b><?php  echo 'Rp '.number_format($detail['ntransport']).' ('.$detail['ktransport'].')'; ?></h4></b>
                    </div>
                    <div class="timeline-body"><table class="table">
                      <tr>
                        <th><center>Warna</center></th>
                        <th><center>Kode</center></th>
                      </tr>
                      <?php 

                      if ($detail['color1']!='-') {
                        $datacolor1 = $detail['color1'];
                        $datakcolor1 = $detail['kcolor1'];
                        echo '<tr><td style="text-align: center;">'.$detail['color1'];
                        echo '<td style="text-align: center;">'.$detail['kcolor1'].'</tr>';
                      }else{
                        echo '<tr><td style="text-align: center;">-';
                        echo '<td style="text-align: center;">-</tr>';
                      }
                      if ($detail['color2']!='-') {
                        echo '<tr><td style="text-align: center;">'.$detail['color2'];
                        echo '<td style="text-align: center;">'.$detail['kcolor2'].'</tr>';
                      }
                      if ($detail['color3']!='-') {
                        echo '<tr><td style="text-align: center;">'.$detail['color3'];
                        echo '<td style="text-align: center;">'.$detail['kcolor3'].'</tr>';
                      }
                      if ($detail['color4']!='-') {
                        echo '<tr><td style="text-align: center;">'.$detail['color4'];
                        echo '<td style="text-align: center;">'.$detail['kcolor4'].'</tr>';
                      }
                      if ($detail['color5']!='-') {
                        echo '<tr><td style="text-align: center;">'.$detail['color5'];
                        echo '<td style="text-align: center;">'.$detail['kcolor5'].'</tr>';
                      }

                      echo '<input type="hidden" name="color1" id="color1" class="form-control" value="'.$datacolor1.'">';
                      echo '<input type="hidden" name="kcolor1" id="kcolor1" class="form-control" value="'.$datakcolor1.'">';
                      ?></table>
                    </div>
                    <div class="timeline-body"><h4>Masa Produksi <b><?php  echo $mproduksi; ?> hari,</b> Masa Pemasangan <b><?php  echo $mpemasangan; ?> hari</b></h4></div>
                  </div>
                </li>
                <li>
                  <i class="fa fa-clock-o bg-gray"></i>
                </li>
              </ul>
              <?php
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </section>
    <div class="clearfix"></div>
    
  <div class="modal" id="myAcc" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Add Project </h4>
            </div>
            <div class="modal-body">
              <p><h3><?php echo $txtnospk; ?></h3></p>
            </div>
            <div class="modal-footer">
              <button type="Submit" class="btn btn-primary" id="btnApprove">Add</button>
            </div>
        </div>
      </div>
    </div>
</form>
<div class="clearfix"></div>
