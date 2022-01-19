<?php
//Menggabungkan dengan file koneksi yang telah kita buat
include '../config_pdf.php';

// Load library phpspreadsheet
require('vendor/autoload.php');
require_once('../function/fungsi_formatdate.php');

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
// End load library phpspreadsheet

$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()->setCreator('sikubah.com')
->setLastModifiedBy('sikubah.com')
->setTitle('Office sikubah.com')
->setSubject('Office sikubah.com')
->setDescription('Document for Office sikubah.com')
->setKeywords('Office sikubah.com')
->setCategory('Result file sikubah.com');

$spreadsheet->getActiveSheet()->mergeCells('A1:G1');
$spreadsheet->setActiveSheetIndex(0)->setCellValue('A1', 'Export Data SPH Tanggal '.($_GET["tgl1"]).' s/d '.$_GET["tgl2"]);


//Font Color
$spreadsheet->getActiveSheet()->getStyle('A3:L3')
    ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

// Background color
    $spreadsheet->getActiveSheet()->getStyle('A3:L3')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('ADD8E6');


// Header Tabel
$spreadsheet->setActiveSheetIndex(0)
->setCellValue('A3', 'No')
->setCellValue('B3', 'Nomer SPH')
->setCellValue('C3', 'Tanggal')
->setCellValue('D3', 'Diameter')
->setCellValue('E3', 'Tinggi')
->setCellValue('F3', 'Diameter Tengah')
->setCellValue('G3', 'Kelengkapan')
->setCellValue('H3', 'Kabupaten/Kota')
->setCellValue('I3', 'Provinsi')
->setCellValue('J3', 'Klien')
->setCellValue('K3', 'Operator')
->setCellValue('L3', 'Affiliate')
;

$i=4; 
$no=1;
$tgl1 = $_GET["tgl1"];
$tgl2 = $_GET["tgl2"];
$filter = "";
$filter3 = "";
if ($tgl1 && $tgl2)
	$filter = $filter . " AND s.tanggal BETWEEN '" . tgl_mysql($tgl1) . "' AND '" . tgl_mysql($tgl2) . "'  ";
$filter3 = $filter3 . " AND s1.tanggal BETWEEN '" . tgl_mysql($tgl1) . "' AND '" . tgl_mysql($tgl2) . "'  ";
$q = "SELECT s.*,ds.bahan,ds.model,ds.d,ds.t,ds.dt,ds.plafon,ds.harga,ds.harga2,ds.jumlah,ds.ket,ds.transport,u.kodeUser,u.nama,p.name as pn,k.name as kn ";
$q.= "FROM aki_sph s right join aki_dsph ds on s.noSph=ds.noSph left join aki_user u on s.kodeUser=u.kodeUser left join provinsi p on s.provinsi=p.id LEFT join kota k on s.kota=k.id ";
$q.= "WHERE s.aktif=1 " . $filter."group by s.noSph Union All" ;
$q.= " SELECT s1.*,'Kaligrafi' as bahan,'Kaligrafi' as model,ds1.d,ds1.t,'-' as dt,'-' as plafon,ds1.harga,'-' as harga2,'-' as jumlah,'-' as ket,'-' as transport, u1.kodeUser, u1.nama, p1.name as pn, k1.name as kn ";
$q.= "FROM aki_sph s1 right join aki_dkaligrafi ds1 on s1.noSph=ds1.noSph left join aki_user u1 on s1.kodeUser=u1.kodeUser left join provinsi p1 on s1.provinsi=p1.id LEFT join kota k1 on s1.kota=k1.id ";
$q.= "WHERE s1.aktif=1 " . $filter3."group by s1.noSph" ;
$q.= " ORDER BY idSph desc ";

/*
$q = "SELECT s.*,ds.model,ds.d,ds.t,ds.dt,ds.plafon,ds.harga,ds.harga2,ds.jumlah,ds.ket,ds.transport,u.nama,p.name as pn,k.name as kn ";
$q.= "FROM aki_sph s right join aki_dsph ds on s.noSph=ds.noSph left join aki_user u on s.kodeUser=u.kodeUser left join provinsi p on s.provinsi=p.id LEFT join kota k on s.kota=k.id ";
$q.= "WHERE 1=1 and month(tanggal)=08 group by s.noSph ";
$q.= " ORDER BY s.noSph desc "; */
$result = $dbLink->prepare($q);
$result->execute();
$res1 = $result->get_result();
while ($row = $res1->fetch_assoc()) {
	$kel='';
	if ($row["plafon"] == 0){
		$kel = 'Full';
	}else if ($row["plafon"] == 1){
		$kel = 'Tanpa Plafon';
	}else{
		$kel = 'Waterproof';
	}
	$spreadsheet->setActiveSheetIndex(0)
	->setCellValue('A'.$i, $no)
	->setCellValue('B'.$i, $row['noSph'])
	->setCellValue('C'.$i, $row['tanggal'])
	->setCellValue('D'.$i, $row['d'])
	->setCellValue('E'.$i, $row['t'])
	->setCellValue('F'.$i, $row['dt'])
	->setCellValue('G'.$i, $kel)
	->setCellValue('H'.$i, $row['kn'])
	->setCellValue('I'.$i, $row['pn'])
	->setCellValue('J'.$i, $row['nama_cust'])
	->setCellValue('K'.$i, $row['nama'])
	->setCellValue('L'.$i, $row['affiliate']);
	$i++; $no++;
}

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Report Excel '.date('d-m-Y H'));

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// We'll be outputting an excel file
// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Report Excel-.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$write = IOFactory::createWriter($spreadsheet, 'Xlsx');
$write->save('php://output');

?>
