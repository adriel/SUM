<?php
include 'config.php'; //incudes the main settings file
include 'func.php'; //incudes the func.php file (where all the core functions are)

// ::Todays date::
$TodaysDate = date("j M Y"); // 10 Dec 2008 

// ::Todays time::
$TodaysTime = date("g:i:s a"); // 10:36:12 pm


// 
// 
// Change main site to a table
// 
// 

	debugIs("SQL database updated successfully.<br />\n");
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $address);

	$URLoutput = curl_exec($ch);
	if (!$URLoutput) {
		debugIs("Exit as no input was given.");
		exit;
	}
	
	//Debug
	//debugIs('<pre>');
	//debugIa(curl_getinfo($ch));
	//debugIs('</pre>');
	debugIs("Errors: " . curl_errno($ch) . " " . curl_error($ch) . "<br />\n");

	curl_close ($ch);
#*/

//Get output from SS API into a manageable array
	#debugIa(arrayCSV($URLoutput));
	$array = arrayCSV($URLoutput);					
#	debugIs($array['Username']);
if (!$array['Time']) {
	# No valid input given
	debugIs("No valid input given.");
	exit;
}
	// debugIs("<pre>$URLoutput</pre>");

	debugIs("<pre>");
	debugIa($array);
	debugIs("</pre>");
	
	
	//Calculations:
	//Change Slingshots date/time format
	$SSDateTime = dateTimeFormat(ssDateTimeFormate($array['Time'],"time"),"Y-m-d H:i:s"); // Stips out the data and split-seconds  (15 Dec 2008 02:23:23:627 to 02:23:23 [24 hour time])
#	$SSTime = dateTimeFormat("02:23:23","d/m/y h:i:s a"); // Stips out the data and split-seconds  (15 Dec 2008 02:23:23:627 to 02:23:23 [24 hour time])
	#debugIs($SSTime);
	
	//Days Left Till ReBill
	$nowdate = strtotime("$TodaysDate"); 
	$thendate = strtotime($array['NextBill']); 
	$datediff = ($thendate - $nowdate); 
	//debugIs(round($datediff / 60)." Minutes<br>"); 
	//debugIs(round($datediff / 3600)." Hours<br>"); 
	$DaysLeftTillReBill = round($datediff / 86400); //days
	$DaysLeftTillReBill = ($DaysLeftTillReBill +1); // Fix so that it shows the correct days left.
	// $DaysLeftTillReBill = ($DaysLeftTillReBill + 30 - $DaysLeftTillReBill);
	$DaysLeftTillReBillN = "DaysLeftTillReBill";

	// ::Total left GB::
	$TotalLeftGB = $array['DataQuotaGB'] - $array['DataUsedGB'];
	$TotalLeftGBN = "TotalLeftGB";

	// ::Total GB left from 15GB::
	$TotalLeftFromCapGB = $DataCap - $array['DataUsedGB'];
	$DataCap .= " GB";
	$TotalLeftFromCapGBN = "TotalGBLeftFrom$DataCap";

	// ::Use per day GB::
	$UsePerDayGB = $TotalLeftFromCapGB / $DaysLeftTillReBill;
	$UsePerDayGB = round("$UsePerDayGB", 2); //Rounds it off two 2 decimal places.
	$UsePerDayGBN = "UsePerDayGB";

	// ::Total sent onpeak today::
	$TotalSendOnPeakToday = $array['TodayDataSentTotalGB'] - $array['TodayDataSentOffPeakGB'];
	$TotalSendOnPeakTodayN = "TotalSendOnPeakToday";

	// ::Total received onpeak today::
	$TotalRcvdOnPeakToday = $array['TodayDataRcvdTotalGB'] - $array['TodayDataRcvdOffPeakGB'];
	$TotalRcvdOnPeakToday = round("$TotalRcvdOnPeakToday", 2); //Rounds it off two 2 decimal places.
	$TotalRcvdOnPeakTodayN = "TotalRcvdOnPeakToday";
// echo "$TotalRcvdOnPeakToday";
	// % of how much from the 15GB (data cap) is used. 
	$PercentLeft = round(($TotalLeftFromCapGB / $DataCap) * "100");

	####################################
	#### % used from total 15GB cap ####
	####################################
	$PercentUsed = round(($DataCap - $TotalLeftFromCapGB) / $DataCap * "100");
	//						(15GB	-	10.19)			/		15GB	* 100		

	####################################
	#### % used from daily cap 		####
	####################################
	$TodaysUsage = round(($TotalRcvdOnPeakToday / $UsePerDayGB) * "100");
	//					(0.05 / 0.51)*100

	//debugIs($TodaysUsage;

	// Formats the date correctly for MYSQL
	$LastBilled = dateTimeFormat(ssDateTimeFormate($array['LastBilled'],"date"),"Y-m-d"); // Stips out the data and split-seconds  (15 Dec 2008 02:23:23:627 to 02:23:23 [24 hour time])
	// debugIs($LastBilled);

	// Formats the date correctly for MYSQL
	$NextBill = dateTimeFormat(ssDateTimeFormate($array['NextBill'],"date"),"Y-m-d"); // Stips out the data and split-seconds  (15 Dec 2008 02:23:23:627 to 02:23:23 [24 hour time])
	// debugIs($NextBill);
	
		
	// Missing data?
	if (!"$array[TodayDataRcvdTotalGB]") {
		# 1 == YES missing data || 0 == data is here
		$MissingData = "1";
	}
	if ("$DaysLeftTillReBill" <= "1") {
		# Past 30 day monthly cap
		$MissingData = "2";
		
	}

	//DATABASE STUFF!!
//Connect to MYSQL DATABASE
$con = mysql_connect("$host","$user","$pass");

	
	// Create DATABASE var
	$sqlDatabaseCreate = "CREATE DATABASE IF NOT EXISTS $db";
	
		//Create TABLE var
		$sqlTableCreate = "CREATE TABLE IF NOT EXISTS $table (
		`No`                        INT (20)     NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`TodaysDateTime`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`SSDateTime`                TIMESTAMP NOT NULL,
-- YYYY-MM-DD HH:MM:SS

		`Username`                  VARCHAR( 25 ) NOT NULL,
		`Account`                   INT (11) NOT NULL,
		`AmountOwing`               DECIMAL (10,2) NOT NULL,

		`DataQuotaGB`               INT (11) NOT NULL,
		`DataUsedGB`                DECIMAL(15,2) NOT NULL,
		`DataOffPeakGB`             DECIMAL(15,2) NOT NULL,
		`TodayDataSentTotalGB`      DECIMAL(15,2) NOT NULL,
		`TodayDataRcvdTotalGB`      DECIMAL(15,2) NOT NULL,
		`TodayDataSentOffPeakGB`    DECIMAL(15,2) NOT NULL,
		`TodayDataRcvdOffPeakGB`    DECIMAL(15,2) NOT NULL,
		`DaysLeftTillReBill`        INT(4) NOT NULL,
		`TotalLeftGB`               DECIMAL(15,2) NOT NULL,
		`TotalLeftFromCapGB`        DECIMAL(15,2) NOT NULL,
		`UsePerDayGB`               DECIMAL(15,2) NOT NULL,
		`TotalSendOnPeakToday`      DECIMAL(15,2) NOT NULL,
		`TotalRcvdOnPeakToday`      DECIMAL(15,2) NOT NULL,
		`LastBilled`                DATE NOT NULL,
		`NextBill`                  DATE NOT NULL,
		`iTalkMinutes`				INT (11) NOT NULL,
		`PercentLeft`               INT( 3 ) NOT NULL,
		`PercentUsed`               INT( 3 ) NOT NULL,
		`TodaysUsage`				INT( 3 ) NOT NULL,
		`MissingData`				INT( 1 ) NOT NULL,
		`RAWinput`					TEXT NOT NULL
		)";
		
		
	//Update TABLE var
	$sqlTableUpdate = sprintf("INSERT INTO $table (
		`TodaysDateTime`, 
		`SSDateTime`, 
	
		`Username`,
		`Account`,
		`AmountOwing`,
	
		`DataQuotaGB`, 
		`DataUsedGB`, 
		`DataOffPeakGB`, 
		`TodayDataSentTotalGB`, 
		`TodayDataRcvdTotalGB`, 
		`TodayDataSentOffPeakGB`, 
		`TodayDataRcvdOffPeakGB`, 
		`DaysLeftTillReBill`, 
		`TotalLeftGB`,
		`TotalLeftFromCapGB`, 
		`UsePerDayGB`,
		`TotalSendOnPeakToday`, 
		`TotalRcvdOnPeakToday`, 
		`LastBilled`, 
		`NextBill`,
		`iTalkMinutes`,
		`PercentLeft`,
		`PercentUsed`,
		`TodaysUsage`,
		`MissingData`,
		`RAWinput` ) 
	VALUES ( 
		CURRENT_TIMESTAMP(),
		'%s', -- SSDateTime
	
		'%s', -- Username
		'%d', -- Account
		'%f', -- AmountOwing

		'%d', -- DataQuotaGB 
		'%f', -- DataUsedGB
		'%f', -- DataOffPeakGB
		'%f', -- TodayDataSentTotalGB
		'%f', -- TodayDataRcvdTotalGB
		'%f', -- TodayDataSentOffPeakGB
		'%f', -- TodayDataRcvdOffPeakGB
		'%d', -- DaysLeftTillReBill
		'%f', -- TotalLeftGB
		'%f', -- TotalLeftFromCapGB
		'%f', -- UsePerDayGB
		'%f', -- TotalSendOnPeakToday
		'%f', -- TotalRcvdOnPeakToday
		'%s', -- LastBilled
		'%s', -- NextBill
		'%d', -- iTalkMinutes
		'%d', -- PercentLeft
		'%d', -- PercentUsed
		'%d', -- TodaysUsage
		'%d', -- MissingData
		'%s')",#-- URLoutput
		
		 // CURRENT DATE
		 mysql_real_escape_string("$SSDateTime"),
		
	     mysql_real_escape_string("$array[Username]"),
	     mysql_real_escape_string("$array[Account]"),
	     mysql_real_escape_string("$array[AmountOwing]"),
	
	     mysql_real_escape_string("$array[DataQuotaGB]"),
	     mysql_real_escape_string("$array[DataUsedGB]"),
	     mysql_real_escape_string("$array[DataOffPeakGB]"),
	     mysql_real_escape_string("$array[TodayDataSentTotalGB]"),
	     mysql_real_escape_string("$array[TodayDataRcvdTotalGB]"),
	     mysql_real_escape_string("$array[TodayDataSentOffPeakGB]"),
	     mysql_real_escape_string("$array[TodayDataRcvdOffPeakGB]"),
	     mysql_real_escape_string("$DaysLeftTillReBill"),
	     mysql_real_escape_string("$TotalLeftGB"),
	     mysql_real_escape_string("$TotalLeftFromCapGB"),
	     mysql_real_escape_string("$UsePerDayGB"),
	     mysql_real_escape_string("$TotalSendOnPeakToday"),
	     mysql_real_escape_string("$TotalRcvdOnPeakToday"),
	     mysql_real_escape_string("$LastBilled"),
	     mysql_real_escape_string("$NextBill"),
	     mysql_real_escape_string("$array[iTalkMinutes]"),
	     mysql_real_escape_string("$PercentLeft"),
	     mysql_real_escape_string("$PercentUsed"),
	     mysql_real_escape_string("$TodaysUsage"),
	     mysql_real_escape_string("$MissingData"),
	     mysql_real_escape_string("$URLoutput"));



if ($con) {
	debugIs("Logged into MYSQL with host: $host and user: $user successfully");

	//Execute query to create table
	if (mysql_query($sqlDatabaseCreate,$con)) {
		debugIs("Created new Database '$db' successfully or one already exsits");
	
		//Select DATABASE
		if (mysql_select_db($db,$con)) {
			debugIs("Selected database '$db' successfully");
	
			//Execute query to create table
			if (mysql_query($sqlTableCreate,$con)) {
				debugIs("Created new table '$table' successfully or one already exsits");
				
				#Update table
				if (mysql_query($sqlTableUpdate,$con)) {debugIs("<b>Updated table '$table' successfully</b>");}
				else {debugIs("<font color=\"red\">Failed</font> updating table '$table': ".mysql_error()."");}
			}
			else {debugIs("<font color=\"red\">Failed</font> creating table '$table': ".mysql_error()."");}
		}
		else {debugIs("<font color=\"red\">Failed</font> connecting to Database '$db': ".mysql_error()."");}
	}
	else {debugIs("<font color=\"red\">Failed</font> creating new Database '$db': ".mysql_error()."");}		
}
else {debugIs("<font color=\"red\">Failed</font> logging in: ".mysql_error()."");}

// Close connection to MYSQL. 
mysql_close($con);





		//$filestatus = "<div id='statusok'>ELSE thing</div>";
		// }

?>