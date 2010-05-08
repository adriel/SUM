<?php
include 'config.php'; //incudes the main settings file
include 'names.php'; //includes the names.php file (where all the names are)

// The title in the CSV export
// Dev note: Need to add in the titles manuly.... or grab them from the array some how.... will see
$csvexporttitle = "$NoN,$TimeN,$CurrentTimeN,$TodaysDateN,$UsernameN,$AccountN,$AmountOwingN,$DataQuotaGBN,$DataUsedGBN,$DataOffPeakGBN,$TodayDataSentTotalGBN,$TodayDataRcvdTotalGBN,$TodayDataSentOffPeakGBN,$TodayDataRcvdOffPeakGBN,$DaysLeftTillReBillN,$TotalLeftGBN,$TotalLeftFrom15GBN,$UsePerDayGBN,$TotalSendOnPeakTodayN,$TotalRcvdOnPeakTodayN,$LastBilledN,$NextBillN,$PercentLeftN,$PercentUsedN \n";

	$con = mysql_connect($host, $user, $pass) or die("Can not connect." . mysql_error()); //Connects to Database
	mysql_select_db($db) or die("Can not connect."); //checks if it connected correctly

	$query = ("SELECT * FROM $table"); // query's what data that is going to be taken from the SQL database

		//Get the result of the query as a CSV file.
		    $sql_csv = mysql_query($query) or die("Error: " . mysql_error());
		    header("Content-type: text/octect-stream");
			header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		    header("Content-Disposition:attachment;filename=".$filename.".csv");
			print $csvexporttitle; // The title on the CSV export file
	
		    while($row = mysql_fetch_row($sql_csv)) {
		        print '"' . stripslashes(implode('","',$row)) . "\"\n";
		    }
		    exit;
		
			mysql_close($con); //Close connection to SQL DB
	
//header("Content-type: application/vnd.ms-excel"); //Excel content type
//OLD: header("Content-type: text/octect-stream");

//No idea what I done this for anymore, lol
include 'index.php'; //incudes the index file
$UsePerDayGBN = "UsePerDayGBN";
$TotalLeftGBN = "TotalLeftGBN";
?>