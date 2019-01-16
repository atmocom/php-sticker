<?php
/*
	== ATMOCOM demo stickers ==
	Renders a graphical sticker with current weather data, either in regular or minimal format depending on URL parameter
	
	To draw regular, slightly larger sized sticker, call with 'sz=norm' or no URL parameter, e.g.:
	http://mywebsite.com/atmocom/atmosticker.php
	
	OR:	
	http://mywebsite.com/atmocom/atmosticker.php?sz=norm
	
	To draw a smaller, minimalistic sticker, call with 'sz=mini', e.g.:
	http://mywebsite.com/atmocom/atmosticker.php?sz=mini
	

	This is the main script which include sticker specific drawing code 
	depending on URL parameter. Main scriptis  dependent of agfx_mini.php
	and agfx_norm.php scripts which must exist in the same folder.
	
	MIT License, see repo for specifics.
	This code is provided as is with no warranties or support.
	Tntended for ATMOCOM proof-of-concept and demonstration purposes only. 
	Permission is hereby given to hack up, alter, re-invent or mutate as required.
	
	Original script (c)2018 Astrogenic Systems
*/

if (array_key_exists('sz', $_REQUEST)) $size=$_REQUEST['sz'];
else $size='norm';

//Check that URL parameter is valid
if($size !== 'norm' && $size !== 'mini') die();

$dataFolder = 'wxdb/';
$dbFile = $dataFolder . 'wx'. date('Ym'). '.db';

//Common font
$font1 = 'res/Roboto-Black.ttf';

//Secondary font is based selected sticker size
if($size === 'mini')
	$font2 = 'res/04B_03__.TTF';
else 
	$font2 = 'res/pixelmix.ttf';

//Define your weather station model between quotes below
$pws_text = 'PWS MODEL-123';

//Change copyright text if needed or leave as is
$copy_text = '©' . date('Y') . ' atmocom.com';

//Location of weather station. 
//Be mindful of text length, available space on
//sticker is limited and text could overflow edges if too long
$pwsloc = 'MYLOCATION, CNTRY';

//Time zone label
$tmzone = 'CET';

//Units used in sticker output
//This should be changed to reflect units of the 
//data stored in your SQLite database
$t_unit = '°C';
$wd_unit = '°';
$ws_unit = ' KT';
$p_unit = ' hPa';
$r_unit = ' mm';
$h_unit = '%';

// Gets latest weather data record from SQLite database
$data = dbget_last();


//Include custom drawing code for respective sticker size, 
//depending on URL parameter
if($size === 'mini') 
	include('agfx_mini.php');
else 
	include('agfx_norm.php');

$imgname = imagecreatefrompng('_badge.png');
header('Content-Type: image/png');
imagepng($imgname);
imagedestroy($imgname);

function calc_wind_arrow($x, $y, $d, $px)
{
	$d_rad = deg2rad($d);
	
	//Calculate wind arrow (triangle) top point on wind rose
	$x1 = $x + ($px * sin($d_rad));
	$y1 = $y - ($px * cos($d_rad));

	//Calculate 2x base points, each angled 16 degrees, triangle height = 4 pixels
	$d_rad = deg2rad($d-19);
	$px-=9;
	$x2 = $x + ($px * sin($d_rad));
	$y2 = $y - ($px * cos($d_rad));

	$d_rad = deg2rad($d+19);
	$x3 = $x + ($px * sin($d_rad));
	$y3 = $y - ($px * cos($d_rad));

	//Return array with points for poly-line draw
	return array($x1, $y1, $x2, $y2, $x3, $y3);
}

function dbget_last()
{
	global $dbFile;
	try {
		$db = new PDO('sqlite:' . $dbFile);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql_qry = 'SELECT * FROM wxdata WHERE ID >= (SELECT MAX(ID) FROM wxdata)';
		
		$stmt = $db->prepare($sql_qry);
		$stmt->execute();

		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	} catch(PDOException $e) {
		// Print PDOException message
		echo $e->getMessage();
		die('DB ERROR: ' . $e->getMessage());
	}
	$db=null;
	return $res;
}

//Define parameters supported by DB table
abstract class Params 
{
	const STATIONID = 1;
	const TEMP = 2;
	const DEWPT = 3;
	const RHUM = 4;
	const BARO = 5;
	const WINDDIR = 6;
	const WINDVEL = 7;
	const WGUSTDIR = 8;
	const WGUSTVEL = 9;
	const PRECIP = 10;
	const PRECIPDAY = 11;
	const UVIDX = 12;
	const SOLAR = 13;
	const INTEMP = 14;
	const INRHUM = 15;
	const SOILTEMP = 16;
	const SOILMOIST = 17;
	const LEAFWET = 18;	
	const WEATHER = 19;
	const CLOUDS = 20;
	const VISNM = 21;
	const RES1 = 22;
	const RES2 = 23;
	const RES3 = 24;
	const RES4 = 25;
	const RES5 = 26;
}
?>