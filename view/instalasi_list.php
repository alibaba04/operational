<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/instalasi_list";

//Periksa hak user pada modul/menu ini
$judulMenu = 'Data Profil ';
$hakUser = getUserPrivilege($curPage);
if ($hakUser < 10) {
    unset($_SESSION['my']);
    echo "<p class='error'>";
    die('User anda tidak terdaftar untuk mengakses halaman ini!');
    echo "</p>";
}

//Periksa apakah merupakan proses headerless (tambah, edit atau hapus) dan apakah hak user cukup
if (substr($_SERVER['PHP_SELF'], -10, 10) == "index2.php" && $hakUser == 90) {

    require_once("./class/c_profil.php");
    $tmpProfil = new c_profil();

    //Jika Mode Tambah/Add
    if ($_POST["txtMode"] == "Add") {
        $pesan = $tmpProfil->add($_POST);
    }

    //Jika Mode Ubah/Edit
    if ($_POST["txtMode"] == "Edit") {
        $pesan = $tmpProfil->edit($_POST);
    }

    //Jika Mode Hapus/Delete
    if ($_GET["txtMode"] == "Delete") {
        $pesan = $tmpProfil->delete($_GET["kodeProfil"]);
    }

    //Seharusnya semua transaksi Add dan Edit Sukses karena data sudah tervalidasi dengan javascript di form detail.
    //Jika masih ada masalah, berarti ada exception/masalah yang belum teridentifikasi dan harus segera diperbaiki!
    if (strtoupper(substr($pesan, 0, 5)) == "GAGAL") {
        global $mailSupport;
        $pesan.="Gagal simpan data, mohon hubungi " . $mailSupport . " untuk keterangan lebih lanjut terkait masalah ini.";
    }
    header("Location:index.php?page=$curPage&pesan=" . $pesan);
    exit;
}
?>
<script type="text/javascript" charset="utf-8">
    function mymodald($noproject){
        var link = window.location.href;
        location.href=link+"&noproject="+$noproject;
    }
    $(document).ready(function () {
        var link = window.location.href;
        if (link.match(/noproject=/g)) {
            $("#modaldetail").modal({backdrop: 'static'});
        }
        // $("#modaldetail").modal({backdrop: 'static'});
    });
    
</script>
<section class="content-header">
    <h1>
        DATA INSTALASI
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Input</li>
        <li class="active">Data Instalasi</li>
    </ol>
</section>
<style type="text/css">
    .table-avatar{
        border-radius: 50%;
        display: inline;
        width: 4rem;
    }
    a{
        color: inherit;
    }
</style>

    <!-- Main row -->
    <section class="content">
      <div class="box box-primary">
        <div class="card-header col-lg-12">
          <h3 class="card-title"> Projects</h3>
        </div>
        <div class="card-body p-0">
          <table class="table table-striped projects">
              <thead>
                <tr>
                    <th style="width: 1%">#</th>
                    <th width="15%" >Project Name </th>
                    <th width="30%" >Team Members</th>
                    <th width="15%">Project Progress</th>
                    <th width="10%">Status</th>
                    <th width="20%">Action</th>

                </tr>
                <?php
                    $q = "SELECT spk.*,kk.*, dkk.*,p.*,k.name as lokasi FROM aki_spk spk left join aki_kk kk on spk.nokk=kk.noKk right join aki_dkk dkk on kk.noKk=dkk.noKk left join aki_tabel_proyek p on spk.noproyek=p.noproyek left join kota k on kk.kota=k.id  WHERE spk.noproyek!='-' and spk.aktif=1 and pk_makara!='0000-00-00' GROUP by spk.noproyek ORDER BY kk.noKk desc";
                    $rs = new MySQLPagedResultSet($q, 50, $dbLink2);

                    $rowCounter=1;

                ?>
              </thead>
              <tbody>
                  <?php  
                      while ($query_data = $rs->fetchArray()) {
                        echo '<tr><td>#></td>';
                        echo '<td><b><a>'.$query_data["noproyek"].'</a></b><br/>
                        <small>'.$query_data["lokasi"].'</small></td>';
                        echo '<td><ul class="list-inline">
                        <li class="list-inline-item">
                        <img alt="Avatar" class="table-avatar" src="dist/img/avatar3.png">
                        </li>
                        <li class="list-inline-item">
                        <img alt="Avatar" class="table-avatar" src="dist/img/avatar3.png">
                        </li>
                        <li class="list-inline-item">
                        <img alt="Avatar" class="table-avatar" src="dist/img/avatar3.png">
                        </li>
                        <li class="list-inline-item">
                        <img alt="Avatar" class="table-avatar" src="dist/img/avatar3.png">
                        </li>
                        </ul></td>';
                        echo '<td class="project_progress"><div class="progress progress-sm">
                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="57" aria-valuemin="0" aria-valuemax="100" style="width: 57%">
                        </div>
                        </div>
                        <small>
                        57% Complete
                        </small></td>';
                        $status='';
                        if ($query_data["pk_makara"]=='0000-00-00'){
                            $status='Produksi';
                        }elseif ($query_data["pk_makara"]!='0000-00-00') {
                            $status='Siap Kirim';
                        }
                        if ($query_data["ekspedisi_out"]=='0000-00-00' && $query_data["ekspedisi_in"]!='0000-00-00') {
                            $status='On The Way';
                        }elseif ($query_data["ekspedisi_out"]!='0000-00-00') {
                            $status='On Site';
                        }
                        if ($query_data["pemasangan_out"]=='0000-00-00' && $query_data["pemasangan_in"]!='0000-00-00') {
                            $status='Proses Pemasangan';
                        }elseif ($query_data["pemasangan_out"]!='0000-00-00') {
                            $status='Terpasang';
                        }
                        echo '<td class="project-state"><span class="badge badge-success">'.$status.'</span></td>';
                        echo '<td class="project-actions text-right">
                        <a class="btn btn-primary btn-sm" onclick=mymodald("'.$query_data["noproyek"].'")>
                        <i class="fas fa-folder">
                        </i>
                        View
                        </a>
                        <a class="btn btn-info btn-sm" href="#">
                        <i class="fas fa-pencil-alt">
                        </i>
                        Edit
                        </a>
                        <a class="btn btn-danger btn-sm" href="#">
                        <i class="fas fa-trash">
                        </i>
                        Delete
                        </a>
                        </td>';
                    }
                  ?>
              </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>

    </section>

<div class="modal fade" id="modaldetail" role="dialog">
    <?php
    $noproject='';
    if(isset($_GET["noproject"]) ){
        $noproject = secureParam($_GET["noproject"], $dbLink2);
    }
    $q = "SELECT spk.*,kk.*, dkk.*,p.*,k.name as lokasi FROM aki_spk spk left join aki_kk kk on spk.nokk=kk.noKk right join aki_dkk dkk on kk.noKk=dkk.noKk left join aki_tabel_proyek p on spk.noproyek=p.noproyek left join kota k on kk.kota=k.id  WHERE spk.noproyek!='-' and spk.aktif=1 and spk.noproyek='".$noproject."' GROUP by spk.noproyek ORDER BY kk.noKk desc";
    $rsTemp = mysql_query($q, $dbLink2);
    if ($dProject = mysql_fetch_array($rsTemp)) {
        
    } 
    ?>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Project details </h4>
            </div>
            <div class="modal-body">

                <div class="col-lg-8">
                    <label class="lblproject">Kode Project :</label> <?php echo $dProject['noproyek']; ?><br>
                    <label class="lblproject">Alamat :</label> <?php echo $dProject['alamat']; ?>
                </div>
                <div class="col-lg-4">
                    <?php
                        $status='';
                        if ($dProject["pk_makara"]!='0000-00-00') {
                            $status='Siap Kirim ';
                        }elseif ($dProject["ekspedisi_out"]!='0000-00-00') {
                            $status='Pengiriman';
                        }elseif ($query_data["ekspedisi_out"]=='0000-00-00' && $dProject["ekspedisi_in"]!='0000-00-00') {
                            $status='Pengiriman ';
                        }elseif ($dProject["pemasangan_out"]!='0000-00-00') {
                            $status='Pemasangan';
                        }elseif ($dProject["pemasangan_out"]=='0000-00-00' && $dProject["pemasangan_in"]!='0000-00-00') {
                            $status='Pemasangan';
                        }else{
                            $status='Produksi';
                        }
                    ?>
                    <label class="lblproject">Status :</label> <?php echo $status; ?>
                </div>
                <div class="col-lg-6">
                    <div class="user-panel">
                        <div class="pull-left image" style="margin-right: 10px;">
                            <img alt="Avatar" class="table-avatar" src="dist/img/avatar3.png">
                        </div>
                        <div class="pull-left ">
                            <p style="font-weight: 100;margin-bottom: 0px;"><?php echo $dProject['masjid']; ?></p>
                            <h4><b><?php echo $dProject['nama_cust']; ?></b></h4>
                            <p class="lblproject"><?php echo $dProject['no_phone']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="user-panel">
                        <div class="pull-left ">
                            <i class="fa fa-calendar"> <?php echo $dProject['tgl_deadline']; ?></i>
                        </div>
                    </div>
                </div>
                <style type="text/css">
                    .lblproject{
                        font-weight: 100;
                    }
                </style>
                <hr width="100%" align="center">
                <div class="col-lg-8">
                    <div class="col-lg-2">
                        <label class="lblproject" >Jenis </label><br>
                        <label class="lblproject">Model </label><br>
                        <label class="lblproject">Ukuran </label>
                    </div>    
                    <div class="col-lg-6">
                        <label><?php echo ": ".$dProject['kubah']; ?></label><br>
                        <label><?php echo ": ".$dProject['model']; ?> </label><br>
                        <label class="lblproject" >-Diameter </label><br>
                        <label class="lblproject">-Diameter Tengah </label><br>
                        <label class="lblproject">-Tinggi </label>
                        <label class="lblproject">-Luas </label>
                    </div> 
                </div>
                <div class="clearfix"></div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary pull-right" id="createspkn"><i class="fa fa-plus"></i> Create</button>
            </div>
        </div>
    </div>
</div>
