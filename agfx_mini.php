<?php

/*
	Graphics drawing code for minimal sticker
	Included by main script atmosticker.php
	
	See main script for licensing info.
	
	(c)2018 Astrogenic Systems

*/

// Load the background sticker template
$bgMap = imagecreatefrompng('res/stkmini.png');

//Draw data on template
if($bgMap && count($data[0]) > 0)
{	
	// Switch antialiasing on
	imageantialias($bgMap, true);
	
	$white = imagecolorallocate($bgMap, 255, 255, 255);
	$gray = imagecolorallocate($bgMap, 90, 90, 90);
	$ltgray = imagecolorallocate($bgMap, 130, 130, 130);
	$hazegray = imagecolorallocate($bgMap, 180, 180, 180);

	$limegreen =imagecolorallocate($bgMap, 76,244,0);
	$skyblue = imagecolorallocate($bgMap, 94, 206, 255);
	$cyan = imagecolorallocate($bgMap, 0,255,255);

	$red = imagecolorallocate($bgMap, 255, 0, 0);
	$blue = imagecolorallocate($bgMap, 0, 0, 255);
	$green = imagecolorallocate($bgMap, 76, 255, 0);
	$yellow = imagecolorallocate($bgMap, 255, 255, 0);
	$orange = imagecolorallocate($bgMap, 255, 216, 0);
	$violet = imagecolorallocate($bgMap, 214, 127, 255);


	$textCol = imagecolorallocate($bgMap, 240, 240, 0);
	$textCol2 = imagecolorallocate($bgMap, 0, 174, 239);
	$textCol3 = imagecolorallocate($bgMap, 239, 174, 0);

	$line1x = 43;
	$line2x = 70;

	$datetime = $data[0]['DATE'] . ' ' . $data[0]['TIME'];
	
	//Text on top panes
 	imagettftext($bgMap, 6, 0, 8, 85, $white, $font2, $pws_text);
	imagettftext($bgMap, 6, 0, 96, 85, $hazegray, $font2, $pwsloc . ' ' . $datetime . ' ' . $tmzone);
	
	foreach($data[0] as $key => $val)
	{
		if($key == 'TEMP') {
			imagettftext($bgMap, 9, 0, 98, $line1x, $white, $font1, round($val,1) . $t_unit);
		}
		else if($key == 'DEWPT') {
			imagettftext($bgMap, 9, 0, 159, $line1x, $white, $font1, round($val,1) . $t_unit);
		}
		else if($key == 'RHUM') {
			imagettftext($bgMap, 9, 0, 159, $line2x, $skyblue, $font1, round($val,0) . $h_unit);
		}		
		else if($key == 'BARO') {
			imagettftext($bgMap, 9, 0, 98, $line2x, $yellow, $font1, round($val,0) . $p_unit);
		}
		else if($key == 'WINDDIR') {
			$txt = round($val,0);
			$txt = str_pad($txt, 3, '0', STR_PAD_LEFT);
			imagettftext($bgMap, 10, 0, 40, $line1x, $limegreen, $font1, $txt . $wd_unit);
			
			$wt = calc_wind_arrow(21,38,floatval($val), 5);
			// draw the wind arrow
			imagefilledpolygon($bgMap, $wt, 3, $limegreen);
		}	
		else if($key == 'WINDVEL') {
			imagettftext($bgMap, 8, 0, 10, $line2x, $white, $font1, round($val,0) . $ws_unit);
		}		
		else if($key == 'WGUSTVEL') {
			imagettftext($bgMap, 8, 0, 55, $line2x, $white, $font1, round($val,0) . $ws_unit);
		}		
		
		else if($key == 'INTEMP') {
			imagettftext($bgMap, 9, 0, 351, $line1x, $white, $font1, round($val,0) . $t_unit);
		}		
		else if($key == 'INRHUM') {
			imagettftext($bgMap, 9, 0, 351, $line2x, $skyblue, $font1, round($val,0) . $h_unit);
		}		
		else if($key == 'UVIDX') {
			$iv = floatval($val);
			$cc = $limegreen;
			if($iv >= 3 &&  $iv < 6) $cc=$yellow;
			else if($iv >= 6 &&  $iv < 8) $cc=$orange;
			else if($iv >= 8 &&  $iv < 11) $cc=$red;
			else if($iv >= 11) $cc=$violet;
			
			imagettftext($bgMap, 9, 0, 215, $line1x, $cc, $font1, round($val,1));
		}
		else if($key == 'SOLAR') {
			$txt = "---";
			if($val != '') $txt = round($val,0);
			imagettftext($bgMap, 9, 0, 215, $line2x, $yellow, $font1, $txt);
		}
		else if($key == 'PRECIP') {
			$vrain = round($val,1);
			if($val == "") $vrain = 0.0;
			imagettftext($bgMap, 9, 0, 284, $line1x, $skyblue, $font1, round($val,1) . $r_unit);
		}
		else if($key == 'PRECIPDAY') {
			$vrain = round($val,1);
			if($val == "") $vrain = 0.0;
			imagettftext($bgMap, 9, 0, 284, $line2x, $skyblue, $font1, round($val,1) . $r_unit);
		}		
		else if($key == 'RES5') { 
			//Reserve 5 field holds ATMOCOM installed firmware revision
			//Print it out so we know which revision data is based on
			if($val != '0x0') imagettftext($bgMap, 6, 0, 283, 85, $ltgray, $font2, 'FW '.$val);
		}		
		
	}
	
	//create PNG image to show	
	imagepng($bgMap, '_badge.png');
}

?>