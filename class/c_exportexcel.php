<?php
require_once('../config.php');
 
// Load library phpspreadsheet
require('vendor/autoload.php');
 
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
// End load library phpspreadsheet
 
$spreadsheet = new Spreadsheet();
 
// Set document properties
$spreadsheet->getProperties()->setCreator('sikubah.com')
->setLastModifiedBy('ikubah.com')
->setTitle('Office 2007 XLSX Dewan Komputer')
->setSubject('Office 2007 XLSX Dewan Komputer')
->setDescription('Test document for Office 2007 XLSX Dewan Komputer.')
->setKeywords('office 2007 openxml php Dewan Komputer')
->setCategory('Test result file Dewan Komputer');
 
$spreadsheet->getActiveSheet()->mergeCells('A1:G1');
$spreadsheet->setActiveSheetIndex(0)->setCellValue('A1', 'Cara Ekspor Laporan/Data dari Database MySQL ke dalam Excel (.xlsx) dengan plugin PHPOffice pada PHP');
 
 
//Font Color
$spreadsheet->getActiveSheet()->getStyle('A3:E3')
    ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
 
// Background color
    $spreadsheet->getActiveSheet()->getStyle('A3:E3')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FFFF0000');
 
 
// Header Tabel
$spreadsheet->setActiveSheetIndex(0)
->setCellValue('A3', 'NO')
->setCellValue('B3', 'NAMA MAHASISWA')
->setCellValue('C3', 'ALAMAT')
->setCellValue('D3', 'JENIS KELAMIN')
->setCellValue('E3', 'TANGGAL MASUK')
;
 
$i=4; 
$no=1; 
/*$query = "SELECT * FROM tbl_mahasiswa ORDER BY nama_mahasiswa ASC";
$dewan1 = $db1->prepare($query);
$dewan1->execute();
$res1 = $dewan1->get_result();
while ($row = $res1->fetch_assoc()) {
	$spreadsheet->setActiveSheetIndex(0)
	->setCellValue('A'.$i, $no)
	->setCellValue('B'.$i, $row['nama_mahasiswa'])
	->setCellValue('C'.$i, $row['alamat'])
	->setCellValue('D'.$i, $row['jenis_kelamin'])
	->setCellValue('E'.$i, $row['tgl_masuk']);
	$i++; $no++;
}
 */
 
// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Report Excel '.date('d-m-Y H'));
 
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);
 
// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Report Excel.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
 
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
 
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
?>
<html>
<head>
	<title></title>
</head>
<body>
	<style type="text/css">
	body{
		font-family: sans-serif;
	}
	table{
		margin: 20px auto;
		border-collapse: collapse;
	}
	table th,
	table td{
		border: 1px solid #3c3c3c;
		padding: 3px 8px;
 
	}
	a{
		background: blue;
		color: #fff;
		padding: 8px 10px;
		text-decoration: none;
		border-radius: 2px;
	}
	</style>
 
	<?php
	$tanggal = date("Y-m-d h:i:s", time());
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Report Excel.xlsx"');
	header('Cache-Control: max-age=0');
	header('Cache-Control: max-age=1');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); 
	header('Cache-Control: cache, must-revalidate'); 
	header('Pragma: public'); 

	?>
 
	<center>
		<h1>Export Data SPH<br/></h1>
	</center>
 
	<table border="1">
		<tr>
			<th width="3%">No</th>
			<th style="width: 20%">Nomer SPH</th>
			<th style="width: 10%">Tanggal</th>
			<th style="width: 10%">Klien</th>
			<th style="width: 20%">Kabupaten/Kota</th>
			<th style="width: 20%">Provinsi</th>
			<th style="width: 30%">Kelengkapan</th>
			<th style="width: 30%">Diameter</th>
			<th style="width: 30%">Tinggi</th>
			<th style="width: 30%">Diameter Tengah</th>
			<th style="width: 15%">Operator</th>
		</tr>
		<?php 
		// koneksi database
		
		?>
	</table>
</body>
</html>




