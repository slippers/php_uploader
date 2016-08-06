<?php
require_once './config/ConfigSettings.php';

function check_file_uploaded_length ($filename)
{
	return (bool) ((mb_strlen($filename,"UTF-8") > 225) ? true : false);
}

function check_file_uploaded_name ($filename)
{
	(bool) ((preg_match("`^[-0-9A-Z_\.]+$`i",$filename)) ? true : false);
}

function getMimetype($file){
	$finfo = new finfo;
	$fileinfo = $finfo->file($file, FILEINFO_MIME);
	return $fileinfo;
}

function UploadFile($destinationDirectory, $fileId, $mimeType){

	//print_r($_FILES);

	if($_FILES[$fileId]['error'] !== 0 ){
		return FALSE;
	}

	$tmp_name = $_FILES[$fileId]['tmp_name'];
	$name = $_FILES[$fileId]['name'];
	$type = $_FILES[$fileId]['type'];

	if(!is_dir($destinationDirectory)) {
		LogMsg("Destination directory does not exist $destinationDirectory");
		return FALSE;
	}

	if(check_file_uploaded_length($name)){
		LogMsg( "File length too long.");
		return FALSE;
	}

	if(check_file_uploaded_name($name)){
		LogMsg( "Bad chars in file name.");
		return FALSE;
	}

	if (!is_uploaded_file($tmp_name)) {
		LogMsg("Bang.$tmp_name");
		return FALSE;
	}

	if(!$type == $mimeType){
		LogMsg("Wrong mime type $type.");
		return FALSE;
	}

	if(pathinfo($name, PATHINFO_EXTENSION) !== ConfigSettings::adapterPattern){
		LogMsg("Incorrect file extention expected=" . ConfigSettings::adapterPattern);
		return FALSE;
	}

	if (strpos(getMimetype($tmp_name),$mimeType) !== 0) {
		LogMsg("Uploaded file has incorrect mime type.");
		return FALSE;
	}

	if(file_exists("$destinationDirectory$name")){
		LogMsg("That file is already uploaded.");
		return FALSE;
	}

	if(move_uploaded_file($tmp_name, "$destinationDirectory$name")){
		LogMsg("File ". $name ." uploaded successfully.");
		return TRUE;
	}

	return FALSE;
}


?>