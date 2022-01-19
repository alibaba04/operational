<?php
/* ==================================================
  //=======  : Alibaba
==================================================== */
/**
 * 
 */
class spekrangka
{
	
	function cekranka($d)
	{
		$return = '';
		if ($d >= '0.5' and $d<='0.9'){
			$return = '3';
		}elseif ($d >= '1' and $d<='2') {
			$return = '5';
		}
		return $return;
	}
}
?>