<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined('validSession') or die('Restricted access');
$curPage = "view/profil_list";

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
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<section class="content-header">
    <h1>
        DATA PROFIL
        <small>List Profil</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pengaturan</li>
        <li class="active">Data Profil</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- Main row -->
    <div class="row">
        <section class="col-lg-6">
        </section>
        <section class="col-lg-6">
            <?php
            //informasi hasil input/update Sukses atau Gagal
            if (isset($_GET["pesan"]) != "") {
                ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <i class="fa fa-warning"></i>
                    <h3 class="box-title">Pesan</h3>
                </div>
                <div class="box-body">
                    <?php
                    if (substr($_GET["pesan"],0,5) == "Gagal") { 
                        echo '<div class="callout callout-danger">';
                    }else{
                        echo '<div class="callout callout-success">';
                    }
                    if ($_GET["pesan"] != "") {

                            echo $_GET["pesan"];
                        }
                    echo '</div>';
                    ?>
                </div>
            </div>
            <?php } ?>
        </section>
        <section class="col-lg-12 connectedSortable">
            <div class="box box-primary">
                <?php
                if (isset($_GET["namaPerusahaan"])){
                    $namaPerusahaan = secureParam($_GET["namaPerusahaan"], $dbLink);
                }else{
                    $namaPerusahaan = "";
                }

                //database
                $q = "SELECT * FROM aki_kk_user ";
                $q.= "WHERE 1=1 ";
                
                //Paging
                //$rs = new MySQLPagedResultSet($q, $recordPerPage, $dbLink);
                $rs = new MySQLPagedResultSet($q, 100, $dbLink);
                ?>
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <ul class="pagination pagination-sm inline"><?php echo $rs->getPageNav($_SERVER['QUERY_STRING']) ?></ul>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped table-hover" >
                        <thead>
                            <tr>
                                <th style="width: 15%">Nama</th>
                                <th style="width: 1%">ID</th>
                                <th style="width: 10%">Nomor ID</th>
                                <th style="width: 25%">Alamat</th>
                                <th colspan="2" width="3%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $rowCounter=1;
                            while ($query_data = $rs->fetchArray()) {
                                echo "<tr>";
                                echo "<td>" . $query_data["name"] . " ( ". $query_data["title"] ." )</td>";
                                echo "<td>" . $query_data["jenis"] . "</td>";
                                echo "<td>" . $query_data["no_id"] . "</td>";
                                echo "<td>" . $query_data["address"] . "</td>";
                                
                                
                                if ($hakUser == 90) {
                                    echo "<td><span class='label label-success' style='cursor:pointer;' onclick=location.href='" . $_SERVER['PHP_SELF'] . "?page=view/profil_detail&mode=edit&kode=" . md5($query_data["id"]) . "'><i class='fa fa-edit'></i>&nbsp;Ubah</span></td>";
                                    //echo("<td><span class='label label-danger' onclick=\"if(confirm('Apakah anda yakin akan menghapus data Profil Kopkar" . $query_data["nama_perusahaan"] . " ?')){location.href='index2.php?page=" . $curPage . "&txtMode=Delete&kodePerusahaan=" . md5($query_data["id"]) . "'}\" style='cursor:pointer;'><i class='fa fa-trash'></i>&nbsp;Hapus</span></td>");
                                    
                                } else {
                                    echo("<td>&nbsp;</td>");
                                    echo("<td>&nbsp;</td>");
                                }
                                echo("</tr>");
                                $rowCounter++;
                            }
                            if (!$rs->getNumPages()) {
                                echo("<tr class='even'>");
                                echo ("<td colspan='10' align='center'>Maaf, data tidak ditemukan</td>");
                                echo("</tr>");
                            }
                            ?>
                        </tbody>
                    </table>
                </div> 
            </div>
        </section>
    </div>
    <!-- /.row -->
</section>