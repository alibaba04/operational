function margin($d,$t,$dt)
{
	var luas = (d*t*3.14).toFixed(2);
	var margin = '';
	if (luas <= 15) {margin = '100%';}
	else if(luas <= 25){margin = '80%';}
	else if(luas <= 40){margin = '60%';}
	else if(luas <= 60){margin = '50%';}
	else if(luas <= 100){margin = '40%';}
	else{margin = '33%';}
	return margin;
}
function kalkulatorharga($d,$t,$dt,$pmargin,$kel)
    {
        var $luas = 0;
        if ($dt == 0) {
            $luas = ($d * $t * 3.14);
        }else{
            $luas = ($dt * $t * 3.14);
        }
        $transport = 6000000;

    //GA
        var $xtp = 0;
        if($d >= 4){ $xtp = 800000;}else{$xtp = 850000;} 
        var $xwa = 0;
        if($d >= 4){ $xwa = 850000;}else{$xwa = 900000;} 
        var $xfull = 0;
        if($d >= 4){ $xfull = 900000;}else{$xfull = 950000;} 
        var $x = 0;
        if( $kel == 'full'){$x = $xfull;}else if($kel == 'waterproof'){$x = $xwa;}else{$x = $xtp;}
        var $modal = $luas * $x;
        var $margin = $modal * $pmargin;
        var $hpp = $modal + $margin;
        var $affiliate = $hpp * 0.05;
        var $marketing = $hpp * 0.01;
        var $harga = $hpp + $affiliate + $marketing + $transport;
    //EN
        var $xtp2 = 0;
        if($d >= 4){ $xtp2 = 1700000;}else{$xtp2 = 1900000;} 
        var $xwa2 = 0;
        if($d >= 4){ $xwa2 = 1800000;}else{$xwa2 = 1950000;} 
        var $xfull2 = 0;
        if($d >= 4){ $xfull2 = 1900000;}else{$xfull2 = 2000000;} 
        var $x2 = 0;
        if( $kel == 'full'){$x2 = $xfull2;}else if($kel == 'waterproof'){$x2 = $xwa2;}else{$x2 = $xtp2;}
        var $modal2 = $luas * $x2;
        var $margin2 = $modal2 * $pmargin;
        var $hpp2 = $modal2 + $margin2;
        var $affiliate2 = $hpp2 * 0.05;
        var $marketing2 = $hpp2 * 0.01;
        var $harga2 = $hpp2 + $affiliate2 + $marketing2 + $transport;

        return $harga+'-'+$harga2;
    //return $modal2.'+'.$margin2 .'+'.$hpp2 .'+'. $affiliate2 .'+'. $marketing2 .'+'. $transport;
    }