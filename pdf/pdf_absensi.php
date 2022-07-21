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
    $pdf->Cell(13,9,'K/T',1,0,'C',1);
    $pdf->Cell(32,9,'DLL',1,0,'C',1);
    $pdf->Cell(22,9,'DATE LINE',1,0,'C',1);
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
    $pdf->SetMargins(187, 0, 10, true);
    $pdf->Ln(0);
    $pdf->Cell(7,4,'PN',1,0,'C',1);
    $pdf->Cell(7,4,'PL',1,0,'C',1);
    $pdf->SetMargins(231, 0, 10, true);
    $pdf->Ln(0);
    $pdf->Cell(7,4,'PN',1,0,'C',1);
    $pdf->Cell(7,4,'MK',1,0,'C',1);
    $pdf->SetMargins(245, 0, 10, true);
    $pdf->Ln(0);
    $pdf->Cell(7,4,'MK',1,0,'C',1);
    $pdf->Cell(7,4,'RG',1,0,'C',1);
    $pdf->SetMargins(259, 0, 10, true);
    $pdf->Ln(0);
    $pdf->Cell(7,4,'PN',1,0,'C',1);
    $pdf->Cell(7,4,'MK',1,0,'C',1);
    $pdf->SetMargins(273, 0, 10, true);
    $pdf->Ln(-5);
    $pdf->Cell(22,9,'STATUS',1,1,'C',1);
    $pdf->SetMargins(5, 0, 10, true);
    $pdf->Ln(0);

    $q2 = "SELECT spk.*,kk.*, dkk.*,p.*,k.name as lokasi FROM aki_spk spk left join aki_kk kk on spk.nokk=kk.noKk right join aki_dkk dkk on kk.noKk=dkk.noKk left join aki_tabel_proyek p on spk.noproyek=p.noproyek left join kota k on kk.kota=k.id  WHERE spk.noproyek!='-' and spk.aktif=1 GROUP by spk.noproyek ORDER BY kk.noKk desc";
    $rs2 = mysql_query($q2, $dbLink2);
    $jml=0;
    $pdf->SetFont('Calibri', '', 10.5);
    $pdf->SetFillColor(255, 255,255);
    $pdf->SetWidths(array(15,35,45,10,13,32,22));
    while ($query_data = mysql_fetch_array($rs2)) {
        $rg = '';
        if ($query_data["kproyek"]=='patas') {
            $pdf->SetFillColor(255, 255,0);
        }
        if ($query_data["pk_makara"]!='0000-00-00' && $query_data["ekspedisi_out"]=='0000-00-00') {
            $pdf->SetFillColor(142, 169, 219);
        }
        if($query_data['rangka_out']!='0000-00-00'){
            $rg = chr(52);
        }
        $status = '';
        $pdf->Row(array($query_data['noproyek'],$query_data['masjid'],$query_data['lokasi'],substr($query_data['bahan'],0,1),$query_data['jml_tiang'],'-',$query_data['tgl_deadline']));
        $tt = $pdf->lengthdata(array($query_data['noproyek'],$query_data['masjid'],$query_data['lokasi']));
        //RKG
        $pdf->SetMargins(177, 0, 10, true);
        $pdf->Ln('-'.$tt);
        $pdf->SetFont('ZapfDingbats', '', 11);
        if ($query_data["rangka_out"]!='0000-00-00') {
            $status='Rangka ';
            $pdf->SetFillColor(255, 0,0);
            $pdf->MultiCell(10,$tt,chr(52),1,"C",true);
        }elseif ($query_data["rangka_out"]=='0000-00-00' && $query_data["rangka_in"]!='0000-00-00') {
            $status='Rangka ';
            $pdf->SetFillColor(166, 166, 166);
            $pdf->MultiCell(10,$tt,chr(90),1,"C",true);
        }else{
            $pdf->MultiCell(10,$tt,'',1,"C");
        }
        //HLW
        $pdf->SetMargins(187, 0, 10, true);
        $pdf->Ln('-'.$tt);
        if ($query_data["hollow_out"]!='0000-00-00') {
            $status='Hollow ';
            $pdf->SetFillColor(255, 0,0);
            $pdf->MultiCell(7,$tt,chr(52),1,"C",true);
        }elseif ($query_data["hollow_out"]=='0000-00-00' && $query_data["hollow_in"]!='0000-00-00') {
            $status='Hollow ';
            $pdf->SetFillColor(166, 166, 166);
            $pdf->MultiCell(7,$tt,chr(90),1,"C",true);
        }else{
            $pdf->MultiCell(7,$tt,'',1,"C");
        }
        $pdf->SetMargins(194, 0, 10, true);
        $pdf->Ln('-'.$tt);
        if ($query_data["hl_plafon"]!='0000-00-00') {
            $status='Hollow Plafon ';
            $pdf->SetFillColor(255, 0,0);
            $pdf->MultiCell(7,$tt,chr(52),1,"C",true);
        }else{
            $pdf->MultiCell(7,$tt,'',1,"C");
        }
        //MAL
        $pdf->SetMargins(201, 0, 10, true);
        $pdf->Ln('-'.$tt);
        if ($query_data["mal_out"]!='0000-00-00') {
            $status='Mal ';
            $pdf->SetFillColor(255, 0,0);
            $pdf->MultiCell(10,$tt,chr(52),1,"C",true);
        }elseif ($query_data["mal_out"]=='0000-00-00' && $query_data["mal_in"]!='0000-00-00') {
            $status='Mal ';
            $pdf->SetFillColor(166, 166, 166);
            $pdf->MultiCell(10,$tt,chr(90),1,"C",true);
        }else{
            $pdf->MultiCell(10,$tt,'',1,"C");
        }
        //GMB
        $pdf->SetMargins(211, 0, 10, true);
        $pdf->Ln('-'.$tt);
        if ($query_data["gambarp_out"]!='0000-00-00') {
            $status='Gambar Panel ';
            $pdf->SetFillColor(255, 0,0);
            $pdf->MultiCell(10,$tt,chr(52),1,"C",true);
        }elseif ($query_data["gambarp_out"]=='0000-00-00' && $query_data["gambarp_in"]!='0000-00-00') {
            $status='Gambar Panel ';
            $pdf->SetFillColor(166, 166, 166);
            $pdf->MultiCell(10,$tt,chr(90),1,"C",true);
        }else{
            $pdf->MultiCell(10,$tt,'',1,"C");
        }
        //BAHAN
        $pdf->SetFont('Calibri', '', 10.5);
        $pdf->SetMargins(221, 0, 10, true);
        $pdf->Ln('-'.$tt);
        if ($query_data["bahan_out"]!='0000-00-00') {
            $status=$query_data["bahan"];
            $pdf->SetFillColor(255, 0,0);
            $pdf->MultiCell(10,$tt,substr($query_data['bahan'],0,1),1,"C",true);
        }elseif ($query_data["bahan_out"]=='0000-00-00' && $query_data["bahan_in"]!='0000-00-00') {
            $status=$query_data["bahan"];
            $pdf->SetFillColor(166, 166, 166);
            $pdf->MultiCell(10,$tt,substr($query_data['bahan'],0,1),1,"C",true);
        }else{
            $pdf->MultiCell(10,$tt,substr($query_data['bahan'],0,1),1,"C");
        }
        $pdf->SetFont('ZapfDingbats', '', 11);
        //CAT
        $pdf->SetMargins(231, 0, 10, true);
        $pdf->Ln('-'.$tt);
        if ($query_data["cat_out"]!='0000-00-00') {
            $status='Cat ';
            $pdf->SetFillColor(255, 0,0);
            $pdf->MultiCell(7,$tt,chr(52),1,"C",true);
        }elseif ($query_data["cat_out"]=='0000-00-00' && $query_data["cat_in"]!='0000-00-00') {
            $status='Cat ';
            $pdf->SetFillColor(166, 166, 166);
            $pdf->MultiCell(7,$tt,chr(90),1,"C",true);
        }else{
            $pdf->MultiCell(7,$tt,'',1,"C");
        }
        $pdf->SetMargins(238, 0, 10, true);
        $pdf->Ln('-'.$tt);
        if ($query_data["catmakara"]!='0000-00-00') {
            $status='Cat Makara ';
            $pdf->SetFillColor(255, 0,0);
            $pdf->MultiCell(7,$tt,chr(52),1,"C",true);
        }else{
            $pdf->MultiCell(7,$tt,'',1,"C");
        }
        //MKR
        $pdf->SetMargins(245, 0, 10, true);
        $pdf->Ln('-'.$tt);
        if ($query_data["makara_out"]!='0000-00-00') {
            $status='Makara ';
            $pdf->SetFillColor(255, 0,0);
            $pdf->MultiCell(7,$tt,chr(52),1,"C",true);
        }elseif ($query_data["makara_out"]=='0000-00-00' && $query_data["makara_in"]!='0000-00-00') {
            $status='Makara ';
            $pdf->SetFillColor(166, 166, 166);
            $pdf->MultiCell(7,$tt,chr(90),1,"C",true);
        }else{
            $pdf->MultiCell(7,$tt,'',1,"C");
        }
        $pdf->SetMargins(252, 0, 10, true);
        $pdf->Ln('-'.$tt);
        if ($query_data["rangka_ga"]!='0000-00-00') {
            $status='RG ';
            $pdf->SetFillColor(255, 0,0);
            $pdf->MultiCell(7,$tt,chr(52),1,"C",true);
        }else{
            $pdf->MultiCell(7,$tt,'',1,"C");
        }
        //PKG
        $pdf->SetMargins(259, 0, 10, true);
        $pdf->Ln('-'.$tt);
        if ($query_data["packing_out"]!='0000-00-00') {
            $status='Packing ';
            $pdf->SetFillColor(255, 0,0);
            $pdf->MultiCell(7,$tt,chr(52),1,"C",true);
        }elseif ($query_data["packing_out"]=='0000-00-00' && $query_data["packing_in"]!='0000-00-00') {
            $status='Packing ';
            $pdf->SetFillColor(166, 166, 166);
            $pdf->MultiCell(7,$tt,chr(90),1,"C",true);
        }else{
            $pdf->MultiCell(7,$tt,'',1,"C");
        }
        $pdf->SetMargins(266, 0, 10, true);
        $pdf->Ln('-'.$tt);
        if ($query_data["pk_makara"]!='0000-00-00') {
            $status='Siap Kirim ';
            $pdf->SetFillColor(255, 0,0);
            $pdf->MultiCell(7,$tt,chr(52),1,"C",true);
        }else{
            $pdf->MultiCell(7,$tt,'',1,"C");
        }
        if ($query_data["ekspedisi_out"]!='0000-00-00') {
            $status='Pengiriman';
        }elseif ($query_data["ekspedisi_out"]=='0000-00-00' && $query_data["ekspedisi_in"]!='0000-00-00') {
            $status='Pengiriman ';
        }
        if ($query_data["pemasangan_out"]!='0000-00-00') {
            $status='Pemasangan';
        }elseif ($query_data["pemasangan_out"]=='0000-00-00' && $query_data["pemasangan_in"]!='0000-00-00') {
            $status='Pemasangan';
        }
        $pdf->SetMargins(273, 0, 10, true);
        $pdf->Ln('-'.$tt);
        $pdf->SetFont('Calibri', '', 10.5);
        $pdf->MultiCell(22,$tt,$status,1,"C");
        $pdf->SetMargins(5, 0, 10, true);
        $pdf->Ln(0);
        /*$pdf->SetMargins(200, 0, 10, true);
        $pdf->Ln('-'.$tt);
        $pdf->MultiCell(7,$tt,$tt,1,"L",true);
        $pdf->SetMargins(5, 0, 10, true);
        $pdf->Ln(0);
        $pdf->SetMargins(207, 0, 10, true);
        $pdf->Ln('-'.$tt);
        $pdf->MultiCell(7,$tt,$tt,1,"L",true);
        $pdf->SetMargins(5, 0, 10, true);
        $pdf->Ln(0);*/
        $pdf->SetFont('Calibri', '', 10.5);
    }

    //output file PDF
    $pdf->Output('TransaksiKas.pdf', 'I'); //download file pdf
?>