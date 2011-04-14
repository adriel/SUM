<?php
	header("Cache-Control: no-cache, must-revalidate");
	include 'config.php'; //incudes the main settings file 
	include 'func.php'; //incudes the func.php file (where all the core functions are)
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" /> 
		<title><?=$siteTitle?></title>
		<link rel="stylesheet" href="styles.css"/>
		<script src="js/jquery-1.1.2.pack.js"></script>
		<script src="js/jqueryprogressbar.js"></script>
		</head>
		<body>
		<div id="main">
			<div id="subtitle"><?="$siteTitle"?></div>
<?php							
$con = mysql_connect($host, $user, $pass) or die("Can not connect." . mysql_error()); //Connects to Database
mysql_select_db($db) or die("Can not connect."); //checks if it connected correctly

$query="SELECT * FROM $table ORDER BY `No` DESC LIMIT  0, 1";
$result = mysql_query($query);
if ($result) {
	// Dev note: do we even need the while loop...
	while ($array = mysql_fetch_assoc($result)) {
		$No                     = "$array[No]"; 
		$SSTime                 = "$array[SSDateTime]";
		 
		$Username               = "$array[Username]"; 
		$Account                = "$array[Account]"; 
		$AmountOwing            = "$array[AmountOwing]"; 
		
		$TodaysDate             = "$array[TodaysDate]"; 
		$DataQuotaGB            = "$array[DataQuotaGB]"; 
		$DataUsedGB             = "$array[DataUsedGB]"; 
		$DataOffPeakGB          = "$array[DataOffPeakGB]"; 
		$TodayDataSentTotalGB   = "$array[TodayDataSentTotalGB]"; 
		$TodayDataRcvdTotalGB   = "$array[TodayDataRcvdTotalGB]"; 
		$TodayDataSentOffPeakGB = "$array[TodayDataSentOffPeakGB]"; 
		$TodayDataRcvdOffPeakGB = "$array[TodayDataRcvdOffPeakGB]"; 
		$DaysLeftTillReBill     = "$array[DaysLeftTillReBill]"; 
		$TotalLeftGB            = "$array[TotalLeftGB]"; 
		$TotalLeftFromCapGB     = "$array[TotalLeftFromCapGB]"; 
		$UsePerDayGB            = "$array[UsePerDayGB]"; 
		$TotalSendOnPeakToday   = "$array[TotalSendOnPeakToday]"; 
		$TotalRcvdOnPeakToday   = "$array[TotalRcvdOnPeakToday]"; 
		$LastBilled             = "$array[LastBilled]"; 
		$NextBill               = "$array[NextBill]";                 
		$PercentLeft            = "$array[PercentLeft]";
		$PercentUsed            = "$array[PercentUsed]";
		$PeakStatus             = "$array[PeakStatus]";
		$TodaysUsage            = "$array[TodaysUsage]";
		$MissingData            = "$array[MissingData]";
	}
} 

// Gets last 5 updates
// Dev note: Check what is all needed and then change the * to that...
$last5 = mysql_query("SELECT * FROM $table ORDER BY `No` DESC LIMIT  0, $history");

mysql_close($con); //Close connection to SQL DB

$PeakStatus = offPeakStatus($startOffPeak,$endOffPeak);
         
$TodaysUsage .= " %";

// ::Todays date::
$TodaysDate = date("j M Y"); // 10 Dec 2008 

// ::Todays time::
$TodaysTime = date("g:i:s a"); // 10:36:12 pm

$AmountOwing = '$'.$AmountOwing ; // Adds the $ (dollar) sign. Option 2 (thanks to hellonearthisman for this)

if ($DaysLeftTillReBill == "1") { // If statement to check if it's 1 day or more and put down the right day/days word...
	$DaysLeftTillReBill .= " Day";
} 
else {
	$DaysLeftTillReBill .= " Days";
}

$PercentLeft .= " %";
$PercentUsed .= " %";

$cleanTotalRcvdOnPeakToday = "$TotalRcvdOnPeakToday";

// If statement to check if it's getting close to going over daily limit or over.
$TotalRcvdOnPeakTodayWARNING = $TotalRcvdOnPeakToday + "0.10 GB";
if ($TotalRcvdOnPeakToday > $UsePerDayGB) { 
	$TotalRcvdOnPeakToday = " <span id='overonpeak'>".autoSizeFormat("$TotalRcvdOnPeakToday","GB","")."</span>";
} 
	else if ($TotalRcvdOnPeakTodayWARNING > $UsePerDayGB){
		$TotalRcvdOnPeakToday = " <span id='warningonpeak'>".autoSizeFormat("$TotalRcvdOnPeakToday","GB","")."</span>";
	}

else {
	$TotalRcvdOnPeakToday = " <span id='clearonpeak'>".autoSizeFormat("$TotalRcvdOnPeakToday","GB","")."</span>";
}
	
	// $1 = size, $2 = input type (Bytes,KB,MB,GB,etc)
	// $UsePerDayGB = autoSizeFormat("$UsePerDayGB","GB","");	
	
	// If days left is <= 0 then it's past the billing months
	if ("$DaysLeftTillReBill" <= "0") {
		# code...
		$DaysLeftTillReBill = "past";
	}
	
	$displayresults = ' 
	<table id="mainTableResults">
		<tr>
			<td class="naming">Last Updated (SS Time)</td>
			<td>'.dateTimeFormat($SSTime,"h:m:s a").'</td>
		</tr>
		<tr>
			<td class="naming">Username</td>
			<td>'.$Username.'</td>
		</tr>
		<tr>
			<td class="naming">Account No.</td>
			<td>'.$Account.'</td>
		</tr>
		<tr>
			<td class="naming">Amount Owing</td>
			<td>'.$AmountOwing.'</td>
		</tr>
		<tr>
			<td class="naming">Data Quota</td>
			<td>'.autoSizeFormat("$DataQuotaGB","GB","").'</td>
		</tr>
		<tr>
			<td class="naming">Total On Peak Used</td>
			<td>'.autoSizeFormat("$DataUsedGB","GB","").'</td>
		</tr>
		<tr>
			<td class="naming">Total Off Peak Used</td>
			<td>'.autoSizeFormat("$DataOffPeakGB","GB","").'</td>
		</tr>
		<tr>
			<td class="naming">Total Sent Today</td>
			<td>'.autoSizeFormat("$TodayDataSentTotalGB","GB","").'</td>
		</tr>
		<tr>
			<td class="naming">Total Received Today</td>
			<td>'.autoSizeFormat("$TodayDataRcvdTotalGB","GB","").'</td>
		</tr>
		<tr>
			<td class="naming">Total Off Peak Sent Today</td>
			<td>'.autoSizeFormat("$TodayDataSentOffPeakGB","GB","").'</td>
		</tr>
		<tr>
			<td class="naming">Total Off Peak Received Today</td>
			<td>'.autoSizeFormat("$TodayDataRcvdOffPeakGB","GB","").'</td>
		</tr>                   
		<tr>
			<td class="naming">Last Billed On</td>
			<td>'.dateTimeFormat($LastBilled,"d M Y").'</td>
		</tr>
		<tr>
			<td class="naming">Next Billing On</td>
			<td>'.dateTimeFormat($NextBill,"d M Y").'</td>
		</tr>
		<tr>
			<td></td>
			<td class="space">&#160;</td>
		</tr>
		<tr>
			<td class="naming">Current Date</td>
			<td>'.$TodaysDate.'</td>
		</tr>
		<tr>
			<td class="naming">Current Time</td>
			<td>'.$TodaysTime.'</td>
		</tr>
		<tr>
			<td class="naming">Days Left Till Rebill</td>
			<td>'.$DaysLeftTillReBill.'</td>
		</tr>
		<tr>
			<td class="naming">Total left</td>
			<td>'.autoSizeFormat("$TotalLeftGB","GB","").'</td>
		</tr>
		<tr>
			<td class="naming">Total Left From '.$DataCap.' GB</td>
			<td>'.autoSizeFormat("$TotalLeftFromCapGB","GB","").'</td>
		</tr>
		<tr>
			<td class="naming">Use Per Day from '.$DataCap.' GB</td>
			<td>'.autoSizeFormat("$UsePerDayGB","GB","").'</td>
		</tr>
		<tr>
			<td class="naming">Total Sent On Peak Today</td>
			<td>'.autoSizeFormat("$TotalSendOnPeakToday","GB","").'</td>
		</tr>
		<tr>
			<td class="naming">Total Received On Peak Today</td>
			<td>'.$TotalRcvdOnPeakToday.'</td>
		</tr>
		<tr>
			<td class="naming">% left Form '.$DataCap.' GB</td>
			<td>'.$PercentLeft.'</td>
		</tr>
		<tr>
			<td class="naming">% USED From '.$DataCap.' GB</td>
			<td>'.$PercentUsed.'</td>
		</tr>
		<tr>
			<td class="naming">% of todays usage</td>
			<td>'.$TodaysUsage.'</td>
		</tr>
		<tr>
			<td class="naming">Peak status</td>
			<td>'.$PeakStatus.'</td>
		</tr>
	</table>
	';

// Time from 12:00AM-12:11AM to display message that data feed isn't working correctly
//$todaydemo = date("j M Y G:i:s  ::g:i a::   :::e T:::");   // 10 Dec 2008 22:36:56 ::10:36 pm:: :::Pacific/Auckland NZDT:::
$nowtime = date("G:i");
//$nowtime = "0:10";
//$Account = "";
//echo $nowtime;

// if statement to check to see if it's between 0:00-0:11 (the time where SS doens't give all the date (info)) + to check if data has come thou from mysql....
if ($nowtime > "0:00" And $nowtime < "0:11") 
	{ 
		//If statement to check if the cURL got anything.   
		if ($Account == "") { $filestatus = "<div id='statuserror'>ERROR receiving data (timed out?)</div>";} 
		else { $filestatus = "<div id='statuserrorfetch'>Fetched partial data</div>";}
		}
else {
		//If statement to check if the cURL got anything.   
		if ($Account == "") { $filestatus = "<div id='statuserror'>ERROR receiving data (timed out?)</div>";}
		else { $filestatus = "<div id='statusok'>Received data successfully</div>";}

	 }
	
	//$NextBill = "10";
	// if statement to check if enough data has arrived **NEW**
	if ("$MissingData" == "1") 
		{ 
		$filestatus = "<div id='statuserrorfetch'>Missing data</div>";
			}
	if ("$MissingData" == "2") {
		$filestatus = "<div id='statuserrorfetch'>Billing period is being reset...</div>";
	}
//echo $NextBill;
	echo "$filestatus";
	echo "$ifconfails";
	echo "$databasestatus"; // databasestatus #3 (currently disabled)
	echo "<div id ='data'>$displayresults</div>";
	
		echo '<div id="last">'; // Used for "p" in CSS
	
		//Last 5 updates below:
		echo "<p>Last $history updates</p>";
			?>
				<div id="last5">
		
				<table id='pastFewRefresh'>		
				<tr>
					<th class='thtime'>Date/Time</th>
					<th class='TSOPT'>Upload</th>
					<th class='TROPT'>Download</th>
				</tr>
				<?
				echo "\n"; // Keep code clean
				// Gets last 5 updates + displays it (needs to be changed to div tags or span or....?)
				if ("$MissingData" != "") {
					# If data has arrived then do the following

					while($row = mysql_fetch_array($last5)) 
					  {
					echo "<tr>".
					"<td class='thtime'>". $row['SSDateTime'] ."</td>".        
					"<td class='TSOPT'>". autoSizeFormat("$row[TotalSendOnPeakToday]","GB","") ."</td>".
					"<td class='TROPT'>". autoSizeFormat("$row[TotalRcvdOnPeakToday]","GB","") ."</td></tr>\n";
					  }
				}
				echo "</table>"; //Table ends

				if ("$MissingData" != "") {
					//Export entire SQL database to CSV
					echo '<a href="savecsv.php" title="Save the entire (data) SQL Database to a CSV file">Save data history to .csv file</a>';		
				}
		
				?>
				</div>
			</div>
		<script type="text/javascript">
		//<![CDATA[
			jQuery(function($){
				$("#progressbarCap").reportprogress(<?php echo round("$PercentUsed","2"); ?>);
				});
			
			jQuery(function($){
				$("#progressbarDay").reportprogress(<?php echo round("$TodaysUsage","2"); ?>);
				});
		//]]>
		</script>
			<div id="bottom">
				Used <?=autoSizeFormat("$DataUsedGB","GB","")?> from <?="$DataCap"?> GB cap<br />
				<div id="progressbarCap"></div>
				Used <?php echo autoSizeFormat("$cleanTotalRcvdOnPeakToday","GB","")?> today<br />
				<div id="progressbarDay"></div>
			</div>
		</div>
		<div id="footer">
			<span id="fotterleft">Created by Adriel (<?=$version?>)</span> <span id="footerright"> </span>
		</div>
	</body>
</html>
