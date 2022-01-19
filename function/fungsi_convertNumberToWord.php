<?php
function convertNumberToWord($digit)
{
	$kalimat="";
	$bagi = 1000000;
	
	$digitAsli = $digit;
	$angka = $digit;
	while($digit>1)
	{		
		if(floor($digit/$bagi)>=1)
		{
			$angka = floor($digit/$bagi); 
			$digit = $digit - ($angka*$bagi);
			$sisa = floor($angka/100);
			if($sisa==1)
				$kalimat.= "Seratus ";
			elseif($sisa>1)
				$kalimat.=spellNumber($sisa)."Ratus ";
			
			$angka = $angka-($sisa*100);
			$sisa = floor($angka/10);
			
			if($angka==11)
			{
				$kalimat.= "Sebelas ";
				$sisa = 0;
			}
			elseif($angka>11 and $angka<20)
			{
				$belasan = $angka%10;
				$kalimat.= spellNumber($belasan)."Belas ";
				$sisa = 0;
			}
			else
			{			
				if($angka==10)
					$kalimat.= "Sepuluh ";
				elseif($angka>=20)
					$kalimat.=spellNumber($sisa)."Puluh ";
					
				$angka = $angka-($sisa*10);
				$sisa = $angka;
			}
			if($sisa==1 && $bagi==1000)
			{
				if($digitAsli>=2000)
					$kalimat.= "Satu Ribu ";
				else
					$kalimat.="Seribu ";
			}
			elseif($sisa>0)
				$kalimat.=spellNumber($sisa);
				
			if($bagi == 1000000000)
				$kalimat.="Milyar ";
			elseif($bagi == 1000000)
				$kalimat.="Juta ";
			elseif($bagi == 1000 && $sisa<>1)
				$kalimat.="Ribu ";
		}
		$bagi = $bagi/1000;	
	}
	return $kalimat;
}

function spellNumber($angka)
{
	$kata = "";
	switch($angka)
	{
	case 1:
		$kata = "Satu ";
		break;
	case 2:
		$kata = "Dua ";
		break;
	case 3:
		$kata = "Tiga ";
		break;
	case 4:
		$kata = "Empat ";
		break;
	case 5:
		$kata = "Lima ";
		break;
	case 6:
		$kata = "Enam ";
		break;
	case 7:
		$kata = "Tujuh ";
		break;
	case 8:
		$kata = "Delapan ";
		break;
	case 9:
		$kata = "Sembilan ";
		break;
	};
	return $kata;												
}
?>
