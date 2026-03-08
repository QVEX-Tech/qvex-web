<?php



if ($_SERVER['REQUEST_METHOD'] != 'GET')
{
	dieWithStatus(403,"Request method not supported");
}

if (!isset($_GET['deviceId']) || !isset($_GET['item']))
{
	dieWithStatus(403,"Missing mandatory parameters");
}

require "fetchFirmware.php";
fetchFirmware();

if ($_GET['item'] == "fw")
{
	
	if ($_GET['deviceId'] == "lynepad210")
	{
		$file = getLatestFirmware("lynepad210")["file"];
		if(!$file)
			dieWithStatus(404,"Firmware not found");
		
		sendFirmwareFile($file);
	}
	else if ($_GET['deviceId'] == "lynepad120")
	{
		$file = getLatestFirmware("lynepad120")["file"];
		if(!$file)
			dieWithStatus(404,"Firmware not found");
		
		sendFirmwareFile($file);
	}
	else
	{
		dieWithStatus(403,"Unknown deviceId ".$_GET['deviceId']);
	}
}
else if ($_GET['item'] == "fwver")
{
	if ($_GET['deviceId'] == "lynepad210")
	{
		$ver = getLatestFirmware("lynepad210")["version"];
		if(!$ver)
			dieWithStatus(404,"Firmware not found");

		echo json_encode(array("fwVersion" => $ver));
	}
	else if ($_GET['deviceId'] == "lynepad120")
	{
		$ver = getLatestFirmware("lynepad120")["version"];
		if(!$ver)
			dieWithStatus(404,"Firmware not found");

		echo json_encode(array("fwVersion" => $ver));
	}
	else
	{
		dieWithStatus(403,"Unknown deviceId ".$_GET['deviceId']);
	}
}
else
{
	dieWithStatus(403,"Unknown item ".$_GET['item']);
}




function sendFirmwareFile($filename)
{
    header("Content-type: application/octet-stream");
    header("Content-disposition: attachment;filename=$filename");
    readfile("firmware-files/".$filename);
}

function sendFile($filename)
{
    header("Content-type: application/octet-stream");
    header("Content-disposition: attachment;filename=$filename");
    readfile($filename);
}


function getLatestFirmware($deviceId) {
    $files = glob('firmware-files/'.$deviceId.'-*.hex');
    
    if (empty($files)) {
        return null;
    }
    
    $latestFile = null;
    $latestVersion = null;
    
    foreach ($files as $file) {
        $filename = basename($file);
        $version = substr($filename, strpos($filename, '-') + 1, -4);
        
        if ($latestVersion === null || version_compare($version, $latestVersion, '>')) {
            $latestFile = $filename;
            $latestVersion = intval($version);
        }
    }
    
    return array('file' => $latestFile, 'version' => $latestVersion);
}

function dieWithStatus($status_code,$human_msg,$log_msg="")
{
	http_response_code($status_code);	
	$array = array('readableMessage' => $human_msg);
	if($log_msg != "")$array['message'] = $log_msg;
	die(json_encode($array));
}



?>