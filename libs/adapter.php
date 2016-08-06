<?php

require_once './config/ConfigSettings.php';
require_once 'user.php';
require_once 'upload.php';

function GetAdapterFileList(){
	$fileList = GetSortedFileNameArray(ConfigSettings::adapterDirectory, '*.' . ConfigSettings::adapterPattern);
	if (isset($fileList) && count($fileList) > 0) {
		return $fileList;
	} else {
		return array('No files found.');
	}
}

function PostCloneLastAdapterIssue(){
	$newFileName = $_POST['newFileName'];
	CloneLastAdapterIssue($newFileName);
}

function CloneLastAdapterIssue( $newFileName ){

	$ext = pathinfo($newFileName, PATHINFO_EXTENSION);
	if($ext !== ConfigSettings::adapterPattern){
		LogMsg("Incorrect file extention expected=" . ConfigSettings::adapterPattern);
		return FALSE;
	}

	$destinationFile = ConfigSettings::adapterDirectory . $newFileName;

	LogMsg("destinationFile=$destinationFile");

	$fileList = GetAdapterFileList();

	if(CopyFileIfNotExist(reset($fileList), $destinationFile)){
		return TRUE;
	}
}

function CopyFileIfNotExist($sourceFile, $destinationFile){

	if(file_exists($destinationFile)){
		LogMsg("The file $destinationFile already exists.");
		return FALSE;
	}
	else
		return copy($sourceFile, $destinationFile);
}

function GetSortedFileNameArray($dir, $pattern){

	if(is_dir($dir) == FALSE){
		LogMsg("dir is not a directory=$dir");
		return FALSE;
	}

	$fileList = array();

	$files = glob($dir . $pattern);

	foreach ($files as $file) {
		$fileList[filemtime($file)] = $file;
	}

	ksort($fileList);

	$fileList = array_reverse($fileList, TRUE);

	return $fileList;
}

function makeul($items, $valueFormatter= 'echo') {
	$out = "";
	if (isset($items) && count($items) > 0) {
		$out = "<ul>\n";
		foreach ($items as $key => $val) {
			$xray = $valueFormatter($val);
			$out .= "\t<li>$xray</li>\n";
		}
		$out .= "</ul>\n";
	}
	return $out;
}

function fileHyperLink($file){
	return "\n<a href=\"" . $file .  "\" target=\"_blank\">" . basename($file) . "</a>\n";
}

function LogMsg($message){
	//echo "$message";
	error_log($message);
}

//Main Page Program
function Main(){
	if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {

		if (Authenticated()) {
			//validate post function
			switch ($_POST['function']) {
				case "UploadFile" :
					LogMsg("UploadFile");
					UploadFile(ConfigSettings::adapterDirectory, ConfigSettings::adapterFile, ConfigSettings::mimeType);
					break;
				case "CloneLastAdapterIssue" :
					LogMsg("CloneLastAdapterIssue");
					PostCloneLastAdapterIssue();
					break;
				case "LogOut" :
					session_destroy();
					break;
				default:
					LogMsg("No function defined.");
			}
		} else {
			Authenticate();
		}

		//Redirect to the page we are currently on.  This will clear the post data.
		header("Location: " . $_SERVER['PHP_SELF']); /* Redirect browser */
		exit();

	}
}
