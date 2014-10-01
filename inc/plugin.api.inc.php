<?PHP
//---------------------------------------------------------------------
// FILE: plugin.api.inc.php
// AUTHOR: Klemen Kenda
// DESCRIPTION: XML plugins file
// DATE: 20/11/2012
// HISTORY:
//---------------------------------------------------------------------

//---------------------------------------------------------------------
// FUNCTION: apiError
// Description: Function generates error message
//---------------------------------------------------------------------

function apiError($e) {
	ini_set('error_reporting', 1);
	
	header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1 
	header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1 
	header("Pragma: no-cache"); 
	header("Expires: 0"); 
	header("Content-Type: text/xml");
		
	$XML = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n<errors><error>" . $e . "</error></errors>";	
	
	return $XML;
}

//---------------------------------------------------------------------
// FUNCTION: getURL
// DESCRIPTION: complete a post request
//---------------------------------------------------------------------
function getURL ($url) {
  	//open connection
    $ch = curl_init();
    
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/x-www-form-urlencoded'));
	
	
	// $fields_string = urlencode($fields_string);
	//  print_r($fields_string);
	// exit();
	
    // curl_setopt($ch, CURLOPT_POST, count($fields));
	// curl_setopt($ch, CURLOPT_POST, 1);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
	curl_setopt($ch, CURLOPT_TIMEOUT, 45); //timeout in seconds
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
		
	//execute post
    $html = curl_exec($ch);
    
	if (curl_error($ch)) return -1;
		
    //close connection
    curl_close($ch);
	return $html;
}

//---------------------------------------------------------------------
// FUNCTION: getURLPost
// DESCRIPTION: complete a post request
//---------------------------------------------------------------------
function getURLPost ($url, $fields, $raw = 1) {
  	//url-ify the data for the POST
	$fields_string = $fields;
    if ($raw == 0) {
		$fields_string = "";
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		$fields_string = rtrim($fields_string, '&');
	}
    
    //open connection
    $ch = curl_init();
    
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/x-www-form-urlencoded'));
	
	
	// $fields_string = urlencode($fields_string);
	//  print_r($fields_string);
	// exit();
	
    curl_setopt($ch, CURLOPT_POST, count($fields));
	// curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
	curl_setopt($ch, CURLOPT_TIMEOUT, 45); //timeout in seconds
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
		
	//execute post
    $html = curl_exec($ch);
    
	if (curl_error($ch)) return -1;
		
    //close connection
    curl_close($ch);
	return $html;
}

//---------------------------------------------------------------------
// function: addJSON
// Description: Data adapter for data from standard JSON
//---------------------------------------------------------------------
function addJSON($log = TRUE) {
	global $data;
	global $miner;
	
	$JSON = $data;
	
	// debug - create a QMiner garbage collector flush
	$url = $miner["url"] . ":" . $miner["port"] . "/gs_gc";	
	$HTML = getURL($url);	
	
	// create request to EnStreaM
	$url = $miner["url"] . ":" . $miner["port"] . "/enstream/add-measurement?data=" . urlencode($JSON);
	// $HTML = passthruHTTP($url);
	$HTML = getURL($url);
	
	if ($log) {
		// add request to the log file
		date_default_timezone_set('UTC');
		$logname = "log-" . date("Ymd") . ".txt";
		$fp = fopen("logs/" . $logname, "a+");
		fwrite($fp, $data . "\n");
		fclose($fp);
	}
	
	// save to disk
	// sleep(1);
	return $HTML;	
}

//---------------------------------------------------------------------
// FUNCTION: getSensors
// Description: Get list of sensors in JSON
//---------------------------------------------------------------------

function getSensors() {
    global $miner;
	$url = $miner["url"] . ":" . $miner["port"] . "/qm_wordvoc?keyid=2";
	$HTML = getURL($url);
	return $HTML;
}

//---------------------------------------------------------------------
// FUNCTION: getNodes
// Description: Get list of nodes in JSON
//---------------------------------------------------------------------

function getNodes() {
    global $miner;
	$url = $miner["url"] . ":" . $miner["port"] . "/enstream/get-nodes";
	$HTML = getURL($url);
	return $HTML;
}

//---------------------------------------------------------------------
// FUNCTION: getMeasurements
// Description: Get list of measurements for a sensor in JSON
//---------------------------------------------------------------------

function getMeasurements($name, $date, $enddate) {
	global $miner;
	$url = $miner["url"] . ":" . $miner["port"] . "/enstream/get-measurement?name=" . urlencode($name) . "&startdate=" . $date . "&enddate=" . $enddate;
	$HTML = getURL($url);
	return $HTML;
}

//---------------------------------------------------------------------
// FUNCTION: getNMeasurements
// Description: Get list of N sensor measurements for a sensor in JSON
//---------------------------------------------------------------------

function getNMeasurements($name, $date, $enddate) {
	global $miner;
	$url = $miner["url"] . ":" . $miner["port"] . "/enstream/n-get-measurement?name=" . urlencode($name) . "&startdate=" . $date . "&enddate=" . $enddate;
	$HTML = getURL($url);
	return $HTML;
}

//---------------------------------------------------------------------
// FUNCTION: getAggregates
// Description: Get list of aggregates with filters in JSON
//---------------------------------------------------------------------

function getAggregates($name, $type, $window, $date, $enddate) {
	global $miner;
	$url = $miner["url"] . ":" . $miner["port"] . "/enstream/get-aggregate?name=" . urlencode($name) . "&type=" . $type . "&window=" . $window . "&startdate=" . $date . "&enddate=" . $enddate;
	$HTML = getURL($url);
	return $HTML;
}

//---------------------------------------------------------------------
// FUNCTION: getNAggregates
// Description: Get list of N sensor aggregates with filters in JSON
//---------------------------------------------------------------------

function getNAggregates($name, $type, $window, $date, $enddate) {
	global $miner;
	$url = $miner["url"] . ":" . $miner["port"] . "/enstream/n-get-aggregate?name=" . urlencode($name) . "&type=" . $type . "&window=" . $window . "&startdate=" . $date . "&enddate=" . $enddate;
	$HTML = getURL($url);
	return $HTML;
}

//---------------------------------------------------------------------
// FUNCTION: getAllAggregates
// Description: Get list of aggregates with filters in JSON
//---------------------------------------------------------------------

function getAllAggregates($name, $date, $enddate) {
	global $miner;
	$url = $miner["url"] . ":" . $miner["port"] . "/enstream/get-aggregates?name=" . urlencode($name) . "&startdate=" . $date . "&enddate=" . $enddate;
	$HTML = getURL($url);
	return $HTML;
}

//---------------------------------------------------------------------
// FUNCTION: getNAllAggregates
// Description: Get list of N sensor aggregates with filters in JSON
//---------------------------------------------------------------------

function getNAllAggregates($name, $date, $enddate) {
	global $miner;
	$url = $miner["url"] . ":" . $miner["port"] . "/enstream/n-get-aggregates?name=" . urlencode($name) . "&startdate=" . $date . "&enddate=" . $enddate;
	$HTML = getURL($url);
	return $HTML;
}

//---------------------------------------------------------------------
// FUNCTION: getCurrentAggregates
// Description: Get list of current aggregates
//---------------------------------------------------------------------

function getCurrentAggregates($name) {
	global $miner;
	$url = $miner["url"] . ":" . $miner["port"] . "/enstream/get-current-aggregates?sid=" . urlencode($name);
	$HTML = getURL($url);
	return $HTML;
}

//---------------------------------------------------------------------
// FUNCTION: getNCurrentAggregates
// Description: Get list of N sensor current aggregates
//---------------------------------------------------------------------

function getNCurrentAggregates($name) {
	global $miner;
	$url = $miner["url"] . ":" . $miner["port"] . "/enstream/n-get-current-aggregates?sid=" . urlencode($name);
	$HTML = getURL($url);
	return $HTML;
}

//---------------------------------------------------------------------
// FUNCTION: exportDataCleaning
// Description: Exports data for Data Cleaning NRG4Cast D2.3
//---------------------------------------------------------------------

function exportDataCleaning() {
	global $miner;
	global $sensorid;
	global $parameters;
	global $gap;
	
	// explode parameters
	$lines = explode(",", $parameters);
	$lines[count($lines)] = $gap;
	// write them into a file
	$fp = fopen("cleaning/parameters.txt", "w");
	for($i = 0; $i < count($lines); $i++) {
		fwrite($fp, $lines[$i] . "\n");
	}
	fclose($fp);
	
	// get data sample	
	$url = $miner["url"] . ":" . $miner["port"] . "/enstream/get-cleaning-sample?sensorid=" . $sensorid;
	$contents = getURL($url);
	
	$fp = fopen("cleaning/sample.csv", "w");
	fwrite($fp, $contents);
	fclose($fp);
	
	$HTML = "OK";
	
	return $HTML;
}

function exportAllMeasurements($sensorid) {
  global $miner;
  
  // get data sample	
	$url = $miner["url"] . ":" . $miner["port"] . "/enstream/get-all-measurements?sensorid=" . $sensorid;
	// return $url;
	
	$contents = getURL($url);
	
	return $contents;
}

function exportAllAggregates($sensorid, $aggrname, $windowlen) {
  global $miner;
  
  // get data sample	
	$url = $miner["url"] . ":" . $miner["port"] . "/enstream/get-all-aggregates?sensorid=" . $sensorid . "&aggrtype=" . $aggrname . "&windowlen=" . $windowlen;
	// return $url;
	
	$contents = getURL($url);
	
	return $contents;
}

//---------------------------------------------------------------------
// FUNCTION: executeDataCleaning
// Description: Executes script for Data Cleaning NRG4Cast D2.3
//---------------------------------------------------------------------

function executeDataCleaning() {
	$answer = shell_exec('cd cleaning && KalmanFilter.exe');
	
	$filename = "cleaning/output.csv";
	$fp = fopen($filename, "r");
	$contents = fread($fp, filesize($filename));
	fclose($fp);
	
	return $contents;
}

//---------------------------------------------------------------------
// PLUGIN: APIGET
// Description: Switch for API requests
//---------------------------------------------------------------------
function pluginAPIGET() {
  global $cmd; // command
	global $p; 	 // parameters
		
	$par = explode(":", $p);	// tokenize the parameters
  $pars = sizeof($par);			// get number of parameters

	// filter ":" is changed with "\colon;"
	for ($i = 0; $i < $pars; $i++) {
	  $par[$i] = str_replace("\colon;", ":", $par[$i]);
	}  	
			
	// cross site scripting
	header('Access-Control: allow <*>');	
	
	switch ($cmd) {
		// GET METADATA
		case "get-sensors":
			$HTML = getSensors();
			break;		
		case "get-nodes": 
			$HTML = getNodes();
			break;
			
		// GET MEASUEREMENTS AND AGGREGATES
		case "get-measurements": 
			if ($pars == 3) { 
				$HTML = getMeasurements($par[0], $par[1], $par[2]);				
			} else {
				$HTML = apiError("Wrong parameter count!");
			}
			break;
		case "n-get-measurements": 
			if ($pars == 3) { 
				$HTML = getNMeasurements($par[0], $par[1], $par[2]);				
			} else {
				$HTML = apiError("Wrong parameter count!");
			}
			break;
		case "get-aggregates": 
			if ($pars == 5) { 
				$HTML = getAggregates($par[0], $par[1], $par[2], $par[3], $par[4]);				
			} else {
				$HTML = apiError("Wrong parameter count!");
			}
			break;	
		case "n-get-aggregates": 
			if ($pars == 5) { 
				$HTML = getNAggregates($par[0], $par[1], $par[2], $par[3], $par[4]);				
			} else {
				$HTML = apiError("Wrong parameter count!");
			}
			break;				
		case "get-all-aggregates": 
			if ($pars == 3) { 
				$HTML = getAllAggregates($par[0], $par[1], $par[2]);				
			} else {
				$HTML = apiError("Wrong parameter count!");
			}
			break;		
		case "n-get-all-aggregates": 
			if ($pars == 3) { 
				$HTML = getNAllAggregates($par[0], $par[1], $par[2]);				
			} else {
				$HTML = apiError("Wrong parameter count!");
			}
			break;				
		case "get-current-aggregates":
			if ($pars == 1) {
			$HTML = getCurrentAggregates($par[0]);			
			} else {
			$HTML = apiError("Wrong parameter count!");
			}
			break;
		case "n-get-current-aggregates":
			if ($pars == 1) {
				$HTML = getNCurrentAggregates($par[0]);			
			} else {
				$HTML = apiError("Wrong parameter count!");
			}
			break;		  
			
		// ADDING DATA
		case "add-json":
		      $HTML = addJSON();
		      break;
		case "add-json-no-log":
		      $HTML = addJSON(FALSE);
		      break;		      		
	  	
		// EXPORTING DATA			
		case "export-all-measurements":
		  if ($pars == 1) {
		    $HTML = exportAllMeasurements($par[0]);
		    // $HTML = "test";
		  } else {
		    $HTML = apiError("Wrong parameter count!");
		  }
		  break;
		case "export-all-aggregates":
		  if ($pars == 3) {
			$HTML = exportAllAggregates($par[0], $par[1], $par[2]);			
		  } else {
			$HTML = apiError("Wrong parameter count!");
		  }
		  break;	
		  
		// DATA CLEANING SPECIAL FUNCTIONS
		case "export-data-cleaning":
			$HTML = exportDataCleaning();
			break;		
		case "save-data-cleaning":
			$HTML = "OK";
			break;
		case "reset-data-cleaning":
			$HTML = "OK";
			break;
		case "execute-data-cleaning":
			$HTML = executeDataCleaning();
			break;
		
		// DEFAULT RESPONSE
		default:
			$HTML = apiError("Command not correct!");
			break;		
	}
	
	return $HTML;
}

?>
