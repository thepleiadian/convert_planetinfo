<?php

// This converts the contents of oolite_maps back to planetinfo.plist format.
// Script created upon suggestion of another_commander.
// http://aegidian.org/bb/viewtopic.php?f=2&t=19199

// Only stop on errors
error_reporting(E_ERROR);

// -----------------------------------------------------------------------------
// DATABASE INFO - ADJUST TO YOUR NEEDS!
// -----------------------------------------------------------------------------
$OO_DB_HOST = "localhost";
$OO_DB_USER = "root";
$OO_DB_PASS = "Password12";

// The database to store map data into
$OO_DB_DB   = "oolite_data";

// -----------------------------------------------------------------------------
// NO TRESPASSING BEYOND THIS POINT!
// (unless you know what you're doing)
// -----------------------------------------------------------------------------

// The links
$OO_DB_LINK = mysqli_connect($GLOBALS['OO_DB_HOST'], $GLOBALS['OO_DB_USER'], $GLOBALS['OO_DB_PASS']);
$OO_DB_SELC = mysqli_select_db($GLOBALS['OO_DB_LINK'], $GLOBALS['OO_DB_DB']);

// First, get column names
$qry = "SHOW COLUMNS FROM oolite_maps";
$sql = mysqli_query($OO_DB_LINK, $qry);
$rows = mysqli_num_rows($sql);
$fieldnames = array();
for ($i=1; $i<=$rows; $i++)
{
	$row = mysqli_fetch_row($sql);
	$fieldnames[] = $row;
}

// OK so begin to create the text to output
$planetinfo_plist = '{

	"interstellar space" =
	{
		sky_color_1 = (0, 1, 0.5);
		sky_color_2 = (0, 1, 0);
		nebula_color_1 = (0, 1, 0.5);
		nebula_color_2 = (0, 1, 0);
		sky_n_stars = 2048;
		sky_n_blurs = 256;
	};

	"universal" = 
	{
		sky_color_1 = (0.75,0.8,1);
		sky_color_2 = (1.0,0.85,0.6);
		stations_require_docking_clearance = no;
	};

	';

// Now go through all systems and output the strings as they are in the
// original plist file.

// First get ALL rows
$qry = "SELECT * FROM oolite_maps;";
$sql = mysqli_query($OO_DB_LINK, $qry);
$rows = mysqli_num_rows($sql); // This should normally be 2048

// Go through the rows
for ($i=1; $i<=$rows; $i++)
{
	// Row data
	$row = mysqli_fetch_row($sql);

	// Enhance the plist string
	$planetinfo_plist = $planetinfo_plist . '"'.$row[1].' '.$row[2].'" = {
		'.$fieldnames[3][0].' = "'.$row[3].'";
		'.$fieldnames[4][0].' = '.$row[4].';
		'.$fieldnames[5][0].' = '.$row[5].';
		'.$fieldnames[6][0].' = "'.$row[6].'";
		'.$fieldnames[7][0].' = '.$row[7].';
		'.$fieldnames[8][0].' = "'.$row[8].'";
		'.$fieldnames[9][0].' = '.$row[9].';
		'.$fieldnames[10][0].' = '.$row[10].';
		'.$fieldnames[11][0].' = '.$row[11].';
		'.$fieldnames[12][0].' = "'.$row[12].'";
		'.$fieldnames[13][0].' = '.$row[13].';
		'.$fieldnames[14][0].' = '.$row[14].';
		'.$fieldnames[15][0].' = "'.$row[15].'";
		'.$fieldnames[16][0].' = "'.$row[16].'";
		'.$fieldnames[17][0].' = "'.$row[17].'";
		'.$fieldnames[18][0].' = '.$row[18].';
		'.$fieldnames[19][0].' = '.$row[19].';
		'.$fieldnames[20][0].' = "'.$row[20].'";
		'.$fieldnames[21][0].' = '.$row[21].';
		'.$fieldnames[22][0].' = "'.$row[22].'";
		'.$fieldnames[23][0].' = "'.$row[23].'";
		'.$fieldnames[24][0].' = "'.$row[24].'";
		'.$fieldnames[25][0].' = '.$row[25].';
		'.$fieldnames[26][0].' = '.$row[26].';
		'.$fieldnames[27][0].' = '.$row[27].';
		'.$fieldnames[28][0].' = "'.$row[28].'";
		'.$fieldnames[29][0].' = '.$row[29].';
		'.$fieldnames[30][0].' = "'.$row[30].'";
		'.$fieldnames[31][0].' = '.$row[31].';
		'.$fieldnames[32][0].' = '.$row[32].';
		'.$fieldnames[33][0].' = "'.$row[33].'";
		'.$fieldnames[34][0].' = "'.$row[34].'";
		'.$fieldnames[35][0].' = "'.$row[35].'";
		'.$fieldnames[36][0].' = '.$row[36].';
		'.$fieldnames[37][0].' = '.$row[37].';
		'.$fieldnames[38][0].' = "'.$row[38].'";
		'.$fieldnames[39][0].' = '.$row[39].';
	};
	
	';
}

$planetinfo_plist = $planetinfo_plist."

}";

echo "<pre>".$planetinfo_plist."</pre>";

?>