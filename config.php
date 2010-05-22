<?php
date_default_timezone_set('Pacific/Auckland'); // Sets timezone.
// // // // //
// SETTINGS //
// // // // //

	// Slingshot login	
	$addressName = 'user'; // SS login username
	$addressPass = 'pass'; // SS login password (if you have a ( ' ) in your password then put a ( \ ) infront of it. eg. "user'pass" should be "user\'pass")
	
	// Change the 25 to what ever your data cap is.
	$DataCap = "25"; 
	
	// Off peak times (24 hour format)
	$startOffPeak = "2:00";
	$endOffPeak = "8:00";
	
	// How often to update (refresh) the page [leave blank to disable]
	$update = ''; // CURRENTLY NOT IMPLEMENTED

	// How many history lines...
	$history = "5";
	
	// MySQL database settings....
	$host = 'localhost'; // The host name where your MySQL is hosted at..
	$user = 'user';      // Your MySQL username
	$pass = 'pass';     // Your MySQL password
	$db = 'sumdb';      // Choose the name you want your database to have (or give a pre made one)
	$table = 'sumtable';     // Choose what the table will be called.
	
	// Auto set size? (e.g. 0.46 GB will be automatically showen as 471.04 MB) [0=off, 1=on]
	// Note to dev, change to BOOL...?
	$autoSizeMode = 1;
	
	// CSV output file name format:
	$fileStartName = 'Archive_of_SUM'; 						//File name when saved to .csv 
	$filename = $fileStartName."_".date("j-M-Y_g.i.s"); 	//File name formate (only change this if you know what your doing)
	
	// Current vertion (for my own use)
	$version = "v0.5";
	
	// Set the user Agent to be used when fetching the SS Data.
	$userAgent = "Slingshot API - SSU $version";
	
	// Title
	$siteTitle = "Slingshot Usage Monitor (SUM)";
	
	// Turn on debug info [0=off, 1=on]
	$debugInfo = 1;
	
// End of settings!!
$address = "https://www.slingshot.co.nz/MyAccount/API/?username=".urlencode("$addressName")."&pwd=".urlencode("$addressPass");
?>