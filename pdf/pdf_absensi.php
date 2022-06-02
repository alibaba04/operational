<?php
    require_once('../function/fpdf/mc_table.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    error_reporting(0);

    $pdf=new PDF_MC_Table();
    $pdf->AddPage('L');
    $pdf->SetMargins(5, 0, 10, true);
    $pdf->AddFont('Calibri','','Calibri_Regular.php');
    $pdf->AddFont('Calibri','B','Calibri_Bold.php');
    $pdf->AddFont('Calibri','I','Calibri_Italic.php');
    $month = $_GET['month'];
    $years = $_GET['years'];

    $tgl1 = $years.'-'.((int)$month-1).'-26';
    $tgl2 = $years.'-'.$month.'-25';
    $pdf->SetFont('Calibri', '', 13);
    $pdf->Cell(0, 2, "LAPORAN PRODUKSI", 0, 1, 'C');
    $pdf->SetFillColor(172, 189, 176);
    $pdf->Ln(5);
    $pdf->Cell(15,9,'KODE',1,0,'C',1);
    $pdf->Cell(35,9,'MASJID',1,0,'C',1);
    $pdf->Cell(45,9,'LOKASI',1,0,'C',1);
    $pdf->Cell(10,9,'BHN',1,0,'C',1);
    $pdf->Cell(35,9,'DLL',1,0,'C',1);
    $pdf->Cell(25,9,'DATE LINE',1,0,'C',1);
    $pdf->Cell(20,9,'STATUS',1,0,'C',1);
    $pdf->Cell(10,9,'RKG',1,0,'C',1);
    $pdf->SetFont('Calibri', '', 11.5);
    $pdf->Cell(14,5,'HLW',1,0,'C',1);
    $pdf->SetFont('Calibri', '', 13);
    $pdf->Cell(10,9,'MAL',1,0,'C',1);
    $pdf->Cell(10,9,'GMB',1,0,'C',1);
    $pdf->Cell(10,9,'G/E',1,0,'C',1);
    $pdf->SetFont('Calibri', '', 11.5);
    $pdf->Cell(14,5,'CAT',1,0,'C',1);
    $pdf->Cell(14,5,'MKR',1,0,'C',1);
    $pdf->Cell(14,5,'PKG',1,1,'C',1);
    $pdf->SetMargins(200, 0, 10, true);
    $pdf->Ln(0);
    $pdf->Cell(7,4,'PN',1,0,'C',1);
    $pdf->Cell(7,4,'PL',1,0,'C',1);
    $pdf->SetMargins(244, 0, 10, true);
    $pdf->Ln(0);
    $pdf->Cell(7,4,'PN',1,0,'C',1);
    $pdf->Cell(7,4,'MK',1,0,'C',1);
    $pdf->SetMargins(258, 0, 10, true);
    $pdf->Ln(0);
    $pdf->Cell(7,4,'MK',1,0,'C',1);
    $pdf->Cell(7,4,'RG',1,0,'C',1);
    $pdf->SetMargins(272, 0, 10, true);
    $pdf->Ln(0);
    $pdf->Cell(7,4,'PN',1,0,'C',1);
    $pdf->Cell(7,4,'MK',1,1,'C',1);
    $pdf->SetMargins(5, 0, 10, true);
    $pdf->Ln(0);

    $q2 = "SELECT spk.*,kk.*, dkk.*,p.* FROM aki_spk spk left join aki_kk kk on spk.nokk=kk.noKk right join aki_dkk dkk on kk.noKk=dkk.noKk left join aki_proyek p on spk.noproyek=p.noproyek WHERE spk.noproyek!='-' and spk.aktif=1 GROUP by spk.noproyek ORDER BY kk.noKk desc ";
    $rs2 = mysql_query($q2, $dbLink2);
    $jml=0;
    $pdf->SetFont('Calibri', '', 11);
    $pdf->SetWidths(array(15,35,45,10,35,25,20,10));
    while ($hasil2 = mysql_fetch_array($rs2)) {
        $rg = '';
        if($hasil2['rangka_out']!='0000-00-00'){
            $rg = chr(52);
        }
        $pdf->Row(array($hasil2['noproyek'],$hasil2['masjid'],$hasil2['alamat'],substr($hasil2['bahan'],0,1),'-',$hasil2['tgl_deadline'],'-',$rg));
    }

    //output file PDF
    $pdf->Output('TransaksiKas.pdf', 'I'); //download file pdf
?>