<?php
    require_once('../config.php');
    require('../function/fpdf/html_table2.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    $pdf=new PDF('P','mm',array(215,330));
    $pdf->AddPage();
    $pdf->SetMargins(5, 10, 15, true);
    $pdf->AddFont('Calibri','','Calibri_Regular.php');
    $pdf->AddFont('Calibri','B','Calibri_Bold.php');
    $pdf->AddFont('Calibri','I','Calibri_Italic.php');
    $pdf->SetFont('Calibri', 'B', 16);
    $tgl = explode(" - ",$_GET['tgl']);
    
    $tgl1 = date('Y-m-d', strtotime($tgl[0]));
    $tgl2 = date('Y-m-d', strtotime($tgl[1]));
    $brg = $_GET['brg'];
    $pdf->Cell(0, 5, "Laporan Transaksi : ".$tgl[0].' sampai '.$tgl[1], 0, 1, 'C');
    
    //ISI
    $pdf->SetFont('Calibri', '', 12);
    $totDebet = $totKredit = 0; $selisih = 0;
    $pdf->Ln(5);
    $pdf->Cell(40,6,'No Transaksi',1,0,'C',0);
    $pdf->Cell(25,6,'Tanggal',1,0,'C',0);
    $pdf->Cell(25,6,'User',1,0,'C',0);
    $pdf->Cell(25,6,'Proyek',1,0,'C',0);
    $pdf->Cell(70,6,'Barang',1,0,'C',0);
    $pdf->Cell(20,6,'Qty',1,1,'C',0);
    $filter = '';
    if ($brg != '?') {
        $filter = "BETWEEN '".$tgl1."' and '".$tgl2."' and kode='".$brg."'";
    }else{
        $filter = "BETWEEN '".$tgl1."' and '".$tgl2."'";
    }
    //database
    $q2 = "SELECT b.kode,b.nama,cust,'-' as kodeproyek,bb.nobeli as no,qty,tgl_beli as tgl,'in' as ket FROM `aki_barang` b RIGHT join aki_dbeli db on b.kode=db.kode_barang RIGHT join aki_beli bb on bb.nobeli=db.nobeli where tgl_beli ".$filter." UNION ALL SELECT b.kode,b.nama,cust,kodeproyek,bk.nobkeluar as no,qty,tgl_bkeluar as tgl,'out' as ket FROM `aki_barang` b RIGHT join aki_dbkeluar dbk on b.kode=dbk.kode_barang RIGHT join aki_bkeluar bk on bk.nobkeluar=dbk.nobkeluar where tgl_bkeluar ".$filter." UNION ALL SELECT b.kode,b.nama,'-' as cust,'-' as kodeproyek,bs.nobso as no,qty,tgl_bso as tgl,'so' as ket FROM `aki_barang` b RIGHT join aki_dbso dbs on b.kode=dbs.kode_barang RIGHT join aki_bso bs on bs.nobso=dbs.nobso where tgl_bso ".$filter." UNION ALL SELECT b.kode,b.nama,'-' as cust,'-' as kodeproyek,br.nobretur as no,qty,tgl_bretur as tgl,'re' as ket FROM `aki_barang` b RIGHT join aki_dbretur dbr on b.kode=dbr.kode_barang RIGHT join aki_bretur br on br.nobretur=dbr.nobretur where tgl_bretur ".$filter;
    $rs2 = mysql_query($q2, $dbLink);
    $total=0;
    while ($query_data = mysql_fetch_array($rs2)) {
        if ($query_data["ket"] == 'in') {
            $pdf->SetFillColor(172, 203, 252);
        }elseif($query_data["ket"] == 'out'){
            $pdf->SetFillColor(230, 147, 153);
        }elseif($query_data["ket"] == 're'){
            $pdf->SetFillColor(234, 247, 193);
        }else{
            $pdf->SetFillColor(252, 252, 250);
        }
        $pdf->Cell(40,6,$query_data["no"],1,0,'L',1);
        $pdf->Cell(25,6,$query_data["tgl"],1,0,'C',1);
        $pdf->Cell(25,6,$query_data["cust"],1,0,'C',1);
        $pdf->Cell(25,6,$query_data["kodeproyek"],1,0,'C',1);
        $pdf->Cell(70,6,$query_data["nama"],1,0,'L',1);
        if ($query_data["ket"] == 'out') {
            $pdf->Cell(20,6,'-'.$query_data["qty"],1,1,'R',1);
        }else{
            $pdf->Cell(20,6,$query_data["qty"],1,1,'R',1);
        }
        $total++;
    }
    if ($total==0){
        $pdf->Cell(0, 15, "Tidak Ada Data", 0, 1, 'C');
    }
    //output file PDF
    $pdf->Output('TransaksiWH.pdf', 'I'); //download file pdf
?>