<?php

// 
// 
// 

// Check if data is missing by checking if say "Total Sent Today" doesn't exsit (ie didn't come though) (on hover over, over the "Fetched partial data" there should be a message saying "missing data from 'bka', 'bla2' " )

// 
// 
// 





// Get function to get input and say what file size format it is, and what you want outputed.
// Modes: auto or manual *auto detect, if an outSizeFormat is given then out in that format else auto set out size.
// FUNC($input,$inSizeFormat,$outSizeFormat,$mode)
// Usage: autoSizeFormat("5000","MB","GB"); should output "4.88 GB"
function autoSizeFormat($inputSize,$inSizeFormat,$outSizeFormat){
	if ("$inputSize") {
		
		// // Check what input size was choosen.
		// switch ($inSizeFormat){
		// 	case "Bytes":
		// 		$peakStatus = "On Peak";
		// 		break;
		// 	case "KB":
		// 		$peakStatus = "On Peak";
		// 		break;
		// 	case "MB":
		// 		$peakStatus = "On Peak";
		// 		break;
		// 	case "GB":
		// 		$peakStatus = "On Peak";
		// 		break;
		// 	case "TB":
		// 		$peakStatus = "On Peak";
		// 		break;
		// 	case "PB":
		// 		$peakStatus = "On Peak";
		// 		break;
		// 	}
		
			include 'config.php'; //incudes the main settings file
			// Check if autoSizeMode is enabled if yes then auto set size to human readable format.
			if ($autoSizeMode) {
				// Check if input size is choosen as GB
				if ($inSizeFormat == "GB") {


					// GB to Bytes
					$GBtoBytes = round($inputSize * 1024 * 1024 * 1024);

					
					$formatedSize = file_size($GBtoBytes);

					return "$formatedSize";

				}
			}
			else {
				return "$inputSize $inSizeFormat";
			}
			
			
		
	            
	
	
	}
}

// Converts any input in Bytes to a human readable format.
// Thanks to http://snipplr.com/view/4633/convert-size-in-kb-mb-gb-/ for the "file_size" function.
function file_size($inputSizeBytes)
{
    $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
	// I added number_format to make sure that the output will always have 2 decimal places (e.g. input: 0.2, output: 0.20)
    return $inputSizeBytes ? number_format(round($inputSizeBytes/pow(1024, ($i = floor(log($inputSizeBytes, 1024)))), 2), 2) . $filesizename[$i] : '0 Bytes';
}

// debugIs=Debug Info String
function debugIs($text){
	if ("$text") {
		include 'config.php'; //incudes the main settings file
	
		// If $debugInfo == 1 then show debug info (echo out extra info)
		if ($debugInfo) {
			echo "$text<br />\n";
		}
	}
}

// debugIs=Debug Info String
function debugIa($text){
	if ("$text") {
		include 'config.php'; //incudes the main settings file
	
		// If $debugInfo == 1 then show debug info (echo out extra info)
		if ($debugInfo) {
			print_r($text);
		}
	}
}

function offPeakStatus($startTime,$endTime){
	
		#######################################
		#### on-peak or off-peak checker... ###  (Thanks hellonearthisman, for the switch() case code)
		#######################################
		$todaysTime = date("g:i"); // 10:36 am
		$ampm = date("a"); // am/pm
	//	$TodaysTime = "1:17";
	//	$ampm = "am";
	//	echo $ampm;

	//  if ($TodaysTime > "1:15" And $TodaysTime < "6:45") { // if statement to check if it's inbetween ofpeak time!
	//	if ($ampm = "am") {

		switch ($ampm){
			case "am":
			if ($todaysTime > "$startTime" And $todaysTime < "$endTime"){
					$peakStatus = "<b>FREE</b>";
				}
				$peakStatus = "On Peak";
				break;
			case "pm":
				$peakStatus = "On Peak";
				break;
			}
		#	echo "Peak status: $PeakStatus";
		return $peakStatus;
}

#Get CSV type input and it returns an array with each title in an array.
function arrayCSV($input) {

/*
[Time] => 29 Nov 2009 10:51:35:623
[Username] => user
[Account] => accountNO
[AmountOwing] => 0.00
[DataQuotaGB] => 15
[DataUsedGB] => 8.42
[DataOffPeakGB] => 5.31
[TodayDataSentTotalGB] => 0.12
[TodayDataRcvdTotalGB] => 4.48
[TodayDataSentOffPeakGB] => 0.11
[TodayDataRcvdOffPeakGB] => 4.46
[LastBilled] => 13 Nov 2009
[NextBill] => 13 Dec 2009
[iTalkMinutes] => 0
*/
	//If no input then return FALSE
	if ( !"$input" ) { return FALSE; }
	
	//Split between ',' input CSV into an array
	$array = explode(",",trim($input));
	#print_r($array);
	
	//Loop though array and split array into seperate arrays for each title/value.	
	foreach ($array as &$value) {
		list ($title,$val) = explode('=',$value,2);
		$cleanDetailsArrary["$title"] = "$val";
		}
		if ($cleanDetailsArrary['Time']){
			return $cleanDetailsArrary;
		}
		else{
		  	return FALSE;
		}
	#	if (empty($ret['Time'])) {
	#	    echo '$var is either 0, empty, or not set at all';
	#	}
}

#Convert Slingshot time (10:51:35:623) to standard time (10:51:35) and or get the Date from the SS date/time.
#Options: date or time formate
#echo ssDateTimeFormate("29 Nov 2009 10:51:35:623","date");
function ssDateTimeFormate($input,$option) {
	if ("$input") {
		switch ($option){
		case "date":
			$pattern= '/^(3[0-1]|2[0-9]|1[0-9]|0[1-9])[\s{1}|\/|-](Jan|JAN|Feb|FEB|Mar|MAR|Apr|APR|May|MAY|Jun|JUN|Jul|JUL|Aug|AUG|Sep|SEP|Oct|OCT|Nov|NOV|Dec|DEC)[\s{1}|\/|-]\d{4}/';
			break;
		case "time":
			$pattern = '/([0-1]\d|2[0-3]):([0-5]\d):([0-5]\d)/';
			break;
		}
		preg_match($pattern, $input, $matches);
		#print_r($matches);
		return $matches[0];
	}
}

#Change the format of the inputdate
#$timeTest = "27 Nov 2009";
#echo dateTimeFormat("$timeTest","d/m/y h:i:s a");
function dateTimeFormat($input,$format) {
	if ("$input") {
			$formattedDate = date("$format", strtotime("$input"));
			return "$formattedDate";
	}	
}
?>