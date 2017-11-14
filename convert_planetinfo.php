<?php

// Only stop on errors
error_reporting(E_ERROR);

// -----------------------------------------------------------------------------
// DATABASE INFO - ADJUST TO YOUR NEEDS!
// -----------------------------------------------------------------------------
$OO_DB_HOST = "localhost";
$OO_DB_USER = "root";
$OO_DB_PASS = "YOURPASS";

// The database to store map data into
$OO_DB_DB   = "oolite_data";

// -----------------------------------------------------------------------------
// NO TRESPASSING BEYOND THIS POINT!
// (unless you know what you're doing)
// -----------------------------------------------------------------------------

// The links
$OO_DB_LINK = mysqli_connect($GLOBALS['OO_DB_HOST'], $GLOBALS['OO_DB_USER'], $GLOBALS['OO_DB_PASS']);
$OO_DB_SELC = mysqli_select_db($GLOBALS['OO_DB_LINK'], $GLOBALS['OO_DB_DB']);

// OK so first drop all existing data
echo "Wiping existing data...<br>";
$wipeqry = "DELETE FROM oolite_maps;";
mysqli_query($OO_DB_LINK, $wipeqry);


// Get the plist as it is in the game, except the { and } at the beginning and
// end of file.
$raw = file_get_contents("./planetinfo.plist");

// We need to be careful what we parse... the first few lines are universal
// settings not relating to systems.

// Get lines.
$lines = explode("\n", $raw);

// Remove all tabs
for ($i=1; $i<=count($lines); $i++)
{ $lines[$i-1] = trim(preg_replace('/\t+/', '', $lines[$i-1])); }

// We store single system data in here
$systemdata = array();
// Separate line data
$linedata = array();
// At which line of the section we are at
$curline = 0;

// Current galaxy and system to check
$curgal = 0;
$cursys = 0;

// We have to extract the data for 2048 systems:
echo "Extracting system info into usable arrays...<br>";
for ($i=1; $i<=2048; $i++)
{
	// Search for current system data
	$q = '"'.$curgal.' '.$cursys.'" = {';
	
	// Line position
	$linepos = 0;
	for ($j=1; $j<=count($lines); $j++)
	{ if ($lines[$j-1] == $q) { $linepos = $j-1; break; } }

	// Extract the info from those lines into their respective arrays
	for ($j=1; $j<=39; $j++)
	{
		if ($j==1)
		{ $linedata[] = $lines[$linepos+($j-1)]; }
		else
		{
			$lineexp = explode(" = ", $lines[$linepos+($j-1)]);
			// Remove trailing semicolons
			$lineexp[1] = str_replace(";", "", $lineexp[1]);
			// Remove quotes
			$lineexp[1] = str_replace('"', '', $lineexp[1]);
			$linedata[] = $lineexp[1];
		}
	}
	$systemdata[] = $linedata;
	$linedata = array();

	// Increase numbers
	$cursys++;
	if ($cursys == 256) { $cursys = 0; $curgal++; }
}

// For now only insert the galaxy and system numbers, so that we can identify a
// row to insert data into later
$curgal = 0;
$cursys = 0;
echo "Inserting galaxy and system identifiers...<br>";
for ($i=1; $i<=count($systemdata); $i++)
{
	$qry = "INSERT INTO oolite_maps (galaxy, system) VALUES(".$curgal.", ".$cursys.");";
	mysqli_query($OO_DB_LINK, $qry);
	
	// Increase numbers
	$cursys++;
	if ($cursys == 256) { $cursys = 0; $curgal++; }
}

// Now reset the numbers again and begin to populate the corresponding values.
echo "Inserting solar system info into oolite_maps (time-consuming!) ...<br>";
$curgal = 0;
$cursys = 0;
for ($i=1; $i<=count($systemdata); $i++)
{
	//echo $systemdata[$i-1][0]."<br>";
	//"7 209" = {
	
	// Find the array position containing the current system info
	$syspos = 0;
	$q = '"'.$curgal.' '.$cursys.'" = {';
	for ($j=1; $j<=count($systemdata); $j++)
	{ if ($systemdata[$i-1][0] == $q) { $syspos = $i-1; break; } }

	// Since we already have the raw line data extracted, we can go ahead and
	// build a query that will insert the data into the DB as we would expect it
	// to be. This query is gonna a bit longer.
	$qry = 'UPDATE oolite_maps SET 
air_color="'.$systemdata[$i-1][1].'",
ambient_level='.$systemdata[$i-1][2].',
cloud_alpha='.$systemdata[$i-1][3].',
cloud_color="'.$systemdata[$i-1][4].'",
cloud_fraction='.$systemdata[$i-1][5].',
coordinates="'.$systemdata[$i-1][6].'",
corona_flare='.$systemdata[$i-1][7].',
corona_hues='.$systemdata[$i-1][8].',
corona_shimmer='.$systemdata[$i-1][9].',
description="'.$systemdata[$i-1][10].'",
economy='.$systemdata[$i-1][11].',
government='.$systemdata[$i-1][12].',
inhabitant="'.$systemdata[$i-1][13].'",
inhabitants="'.$systemdata[$i-1][14].'",
land_color="'.$systemdata[$i-1][15].'",
land_fraction='.$systemdata[$i-1][16].',
layer='.$systemdata[$i-1][17].',
name="'.$systemdata[$i-1][18].'",
planet_distance='.$systemdata[$i-1][19].',
polar_cloud_color="'.$systemdata[$i-1][20].'",
polar_land_color="'.$systemdata[$i-1][21].'",
polar_sea_color="'.$systemdata[$i-1][22].'",
population='.$systemdata[$i-1][23].',
productivity='.$systemdata[$i-1][24].',
radius='.$systemdata[$i-1][25].',
random_seed="'.$systemdata[$i-1][26].'",
rotation_speed='.$systemdata[$i-1][27].',
sea_color="'.$systemdata[$i-1][28].'",
sky_n_blurs='.$systemdata[$i-1][29].',
sky_n_stars='.$systemdata[$i-1][30].',
station="'.$systemdata[$i-1][31].'",
station_vector="'.$systemdata[$i-1][32].'",
sun_color="'.$systemdata[$i-1][33].'",
sun_distance='.$systemdata[$i-1][34].',
sun_radius='.$systemdata[$i-1][35].',
sun_vector="'.$systemdata[$i-1][36].'",
techlevel='.$systemdata[$i-1][37].' 
WHERE galaxy='.$curgal.' AND system='.$cursys.';';

	// F I R E
	mysqli_query($OO_DB_LINK, $qry);
	
	// Increase numbers
	$cursys++;
	if ($cursys == 256) { $cursys = 0; $curgal++; }
}

// That's it
echo "<br><br>COMPLETE.";
?>