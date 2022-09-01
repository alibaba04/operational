<?php
    require_once('../function/fpdf/mc_table.php');
    require_once ("../function/fungsi_formatdate.php");
    require_once ("../function/fungsi_convertNumberToWord.php");
    error_reporting(0);

    $pdf=new PDF_MC_Table('P','mm',array(210,297));
    $pdf->AddPage();
    $nopo = $_GET['nopo'];
    $pdf->image('../dist/img/hitam-logo.png',0,0,90,55);
    $pdf->AddFont('Calibri','','Calibri_Regular.php');
    $pdf->AddFont('Calibri','B','Calibri_Bold.php');
    $pdf->AddFont('Calibri','I','Calibri_Italic.php');
    $pdf->SetFont('Calibri', 'B', 16);
    $pdf->Ln(-30);
    $pdf->Cell(0, 2, "PURCHASE ORDER", 0, 1, 'R');
    $pdf->SetFont('Calibri', '', 12);
    $q= "SELECT * FROM `aki_po` po left join aki_supplier supp on po.id_supplier=supp.kodesupp WHERE md5(po.nopo)='".$nopo."'";
    $rs = mysql_query($q, $dbLink);
    $hasil = mysql_fetch_array($rs);
    $pdf->Ln(8);
    $npemesanan = $hasil['nopo'];
    $pdf->Cell(145,2,'No. Pemesanan',0,0,'R',0);
    $pdf->Cell(0,2,$hasil['nopo'],0,1,'R',0);
    $pdf->Ln(3);
    $pdf->Cell(145,2,'Tanggal',0,0,'R',0);
    $pdf->Cell(0,2,date('d/m/Y', strtotime($hasil['tgl_po'])) ,0,0,'R',0);
    
    $pdf->Ln(15);
    $pdf->Cell(25,7,'Order Ke',0,1,'L',0);
    $pdf->Cell(80,0.5,'',1,1,'L',0.5);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->MultiCell(60,5,$hasil['supplier'],0,'J',0);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->Ln(3);
    $pdf->MultiCell(80,5,$hasil['alamat'],0,'J',0);
    $pdf->Ln(3);
    $pdf->Cell(25,7,'Telp. ',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(60,5,'62'.$hasil['nomor'],0,'J',0);
    $pdf->Cell(25,7,'FOB',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(60,5,'-',0,'J',0);
    $pdf->Cell(25,7,'No. Rekening',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(60,5,$hasil['norek'],0,'J',0);

    $pdf->SetMargins(122, 0, 10, true);
    if (strlen($hasil['alamat'])>49) {
        $pdf->Ln(-43.5);
    }else if (strlen($hasil['alamat'])<=49){
        $pdf->Ln(-39);
    }
    $pdf->Cell(25,7,strlen($hasil['alamat']).'Alamat Tujuan',0,1,'L',0);
    $pdf->Cell(80,0.5,'',1,1,'L',0.5);
    $pdf->SetFont('Calibri', 'B', 11);
    $pdf->MultiCell(60,5,'PT Anugerah Kubah Indonesia',0,'J',0);
    $pdf->Ln(3);
    $pdf->SetFont('Calibri', '', 11);
    $pdf->MultiCell(80,5,'Jl. Pramuka No.157, Purwokerto, Kec. Ngadiluwih, Kabupaten Kediri (64171)',0,'J',0);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->Ln(3);
    $pdf->Cell(25,7,'Detail Pemohon',0,1,'L',0);
    $pdf->SetFont('Calibri', '', 10);
    $pdf->Cell(25,7,'Nama Pemohon',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(60,5,$hasil['cust'],0,'J',0);
    $pdf->Cell(25,7,'Telp. ',0,0,'L',0);
    $pdf->Cell(2,5,':',0,0,'L',0);
    $pdf->MultiCell(60,5,$hasil['nomor'],0,'J',0);

    $pdf->SetMargins(5, 0, 10, true);
    $pdf->Ln(15);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->Cell(25,7,'Detail Pesanan Pembelian',0,1,'L',0);
    $pdf->SetFillColor(172, 189, 176);
    $pdf->Cell(8,7,'No.',1,0,'C',1);
    $pdf->Cell(75,7,'Item',1,0,'C',1);
    $pdf->Cell(15,7,'Qty',1,0,'C',1);
    $pdf->Cell(18,7,'Diskon',1,0,'C',1);
    $pdf->Cell(18,7,'Pajak',1,0,'C',1);
    $pdf->Cell(30,7,'Harga (Rp)',1,0,'C',1);
    $pdf->Cell(35,7,'Sub Total (Rp)',1,1,'C',1);
    $pdf->SetFont('Calibri', '', 10);

    $q2 = "SELECT dpo.*,b.kode,b.nama FROM `aki_dpo` dpo left join aki_barang b on dpo.kode_barang=b.kode WHERE md5(dpo.nopo)='".$nopo."' order by id asc";
    $rs2 = mysql_query($q2, $dbLink);
    $jml=1;
    $total=0;
    while ($query_data = mysql_fetch_array($rs2)) {
        $pdf->Cell(8,7,$jml,1,0,'C',0);
        if ($query_data["jbarang"] == 'penunjang') {
            $pdf->Cell(75,7, ucfirst($query_data["id_barang"]),1,0,'L',0);
        }else{
            $pdf->Cell(75,7, ucfirst($query_data["nama"]),1,0,'L',0);
        }
        
        $pdf->Cell(15,7,$query_data["qty"],1,0,'C',0);
        $pdf->Cell(18,7,'0 %',1,0,'C',0);
        $pdf->Cell(18,7,'-',1,0,'C',0);
        $pdf->Cell(30,7,number_format($query_data["harga"]),1,0,'R',0);
        $pdf->Cell(35,7,number_format($query_data["subtotal"]),1,1,'R',0);
        $jml++;
        $total+=$query_data["subtotal"];
    }
    if ($jml<8) {
        for ($jml; $jml <= 8; $jml++) { 
            $pdf->Cell(8,7,'',1,0,'C',0);
            $pdf->Cell(75,7, '',1,0,'L',0);
            $pdf->Cell(15,7,'',1,0,'C',0);
            $pdf->Cell(18,7,'',1,0,'C',0);
            $pdf->Cell(18,7,'',1,0,'C',0);
            $pdf->Cell(30,7,'',1,0,'R',0);
            $pdf->Cell(35,7,'',1,1,'R',0);
        }
    }
    $pdf->Ln(6);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->Cell(150,7,'Subtotal ',0,0,'R',0);
    $pdf->SetFont('Calibri', '', 10);
    $pdf->Cell(20,7,'Rp ',0,0,'R',0);
    $pdf->Cell(29,7,number_format($total),0,1,'R',0);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->Cell(150,7,'Pajak ',0,0,'R',0);
    $pdf->SetFont('Calibri', '', 10);
    $pdf->Cell(20,7,'Rp ',0,0,'R',0);
    $pdf->Cell(29,7,number_format(0),0,1,'R',0);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->Cell(150,7,'Diskon ',0,0,'R',0);
    $pdf->SetFont('Calibri', '', 10);
    $pdf->Cell(20,7,'Rp ',0,0,'R',0);
    $pdf->Cell(29,7,number_format(0),0,1,'R',0);
    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->Cell(150,7,'Total ',0,0,'R',0);
    $pdf->SetFont('Calibri', '', 10);
    $pdf->Cell(20,7,'Rp ',0,0,'R',0);
    $pdf->Cell(29,7,number_format($total),0,1,'R',0);
    $pdf->SetFont('Calibri', 'B', 14);
    $pdf->Cell(150,7,'Jumlah Tertagih: ',0,0,'R',0);
    $pdf->SetFont('Calibri', '', 14);
    $pdf->Cell(20,7,'Rp ',0,0,'R',0);
    $pdf->Cell(29,7,number_format($total),0,1,'R',0);
    //output file PDF
    $pdf->Output('po-'.$npemesanan.'.pdf', 'I'); //download file pdf
?>