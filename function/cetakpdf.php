<?php
function pdfRekap(&$params) {
    global $dbLink;
    
    require_once('./function/tcpdf/config/lang/eng.php');
    require_once('./function/tcpdf/tcpdf.php');
    require_once("./function/fungsi_formatdate.php");
    require_once("./function/fungsi_convertNumberToWord.php");
    
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf = new TCPDF('P', 'mm', 'A4');
    
    $noSph = secureParam($params["noSph"], $dbLink);
    $html="";
    $pdf->SetPrintHeader(false);
     $pdf->SetMargins(5,0,15);
    $pdf->AddPage();
    
    //HEADER        
    
    $html .='<style>
             h1, h2, h3, h4, h5{
                  font-family: Helvetica;
                }

                table {
                  font-family: Helvetica;
                  font-size: 10pt;
                  color: #666;
                  border: #ccc 1px solid;
                }

                table th {
                  border-left:1px solid #666666;
                  border-right:1px solid #666666;
                  border-top: 1px solid #666666;
                  border-bottom: 1px solid #666666;
                  background: #ededed;
                  v-align: middle;
                }

                table th:first-child{  
                  border-left:none;

                }

                table tr {
                  text-align: center;
                  padding-left: 20px;
                  border-bottom: 1px solid #666666;
                }

                table td:first-child {
                  text-align: left;
                  padding-left: 20px;
                  border-left: 0;
                  border-bottom: 1px solid #666666;
                }

                table td {
                  padding: 15px 35px;
                  border-top: 1px solid #666666;
                  border-bottom: 1px solid #666666;
                  border-left: 1px solid #666666;
                  border-right: 1px solid #666666;
                 
                }

                table tr:last-child td {
                  border-bottom: 1px solid #666666;
                }

             </style>';
    $q = 'SELECT * FROM `aki_sph` WHERE noSph = '.$noSph;
    $q_profil = mysql_query($q, $dbLink);
    $profil = mysql_fetch_array($q_profil);
    // $pdf->Image($gb, 10, 15, 60.78);
//    $pdf->Cell(1, 10, '', 0, 1, 'L'); //Pindah Baris

    $pdf->SetFont('Helvetica', '', 12);
    $pdf->setXY(10, 18);
    $pdf->image('dist/img/cop-aki.jpg',0,0,210,30);
    if ($filter==""){
        $pdf->Cell(200, 42, "Sampai dengan periode : ".date('d-m-Y',time()), 0, 1, 'C');
    }else{
        $pdf->Cell(200, 42, "Periode : ".$tglJurnal1." s/d ".$tglJurnal2, 0, 1, 'C');
    }
    $pdf->setXY(10, 18);
    $pdf->Cell(200, 32, "DATA TRANSAKSI JURNAL", 0, 1, 'C');
    //ISI
    $pdf->SetFont('helvetica', '', 12); 
    // $pdf->Cell(1, 5, '', 0, 1, 'L'); //Pindah Baris
    
    // $pdf->Ln();
    
    
    $html .= '<table >
                <thead>
                <tr>
                <th align="center" width="12%"><b>Tanggal Transaksi</b></th>
                <th align="center" width="15%"><b>Nomor Bukti </b></th>
                <th align="center" width="10%"><b>Kode Rekening </b></th>
                <th align="center" width="35%"><b>Keterangan</b></th>
                <th align="center" width="17%"><b>Debet</b></th>
                <th align="center" width="17%"><b>Kredit</b></th>
                </tr>                            
                </thead>
                <tbody>
            ';


    //database
    $q = "SELECT t.tanggal_transaksi, t.kode_transaksi, t.kode_rekening, t.keterangan_transaksi, t.debet, t.kredit ";
    $q.= "FROM aki_tabel_transaksi t ";
    $q.= "WHERE 1=1 ".$filter;
    $q.= " ORDER BY t.tanggal_transaksi, id_transaksi ";
    
    // $no = 1;
    $totDebet = $totKredit = 0;
    $rsLap = mysql_query($q, $dbLink);
    while ($lap = mysql_fetch_array($rsLap)) {
        $html .= '
            <tr height="20px">
                <td align="center" width="12%" >' . $lap['tanggal_transaksi'] . '</td>
                <td align="center" width="15%">' . $lap["kode_transaksi"] . '</td>
                <td align="center" width="10%">' . $lap["kode_rekening"] . '</td>
                <td align="left" width="35%">' . $lap["keterangan_transaksi"]. '</td>
                <td align="right" width="17%">Rp. ' . number_format($lap["debet"], 2) . '</td>               
                <td align="right" width="17%">Rp. ' . number_format($lap["kredit"], 2) . '</td>               
            </tr>
         ';
        // $no++; 
         $totDebet += $lap["debet"];
         $totKredit += $lap["kredit"];
    }
    $html .= '</tbody>';
    $html .= '<tfoot>';
    $html .= '<tr>';
    $html .= '<td colspan="4" align="right">JUMLAH</td><td align="right">Rp. '.number_format($totDebet,2).'</td>
    <td align="right">'.number_format($totKredit,2).' </td>';
    $html .= '</tr>';
    $html .= '</tfoot>';
    $html .='</table>';

    $pdf->writeHTML($html, true, false, true, false, '');

    //output file PDF
    $pdf->Output('BukuJurnal.pdf', 'I'); //download file pdf
}
?>