<?php

require "backup-files.cfg.php";

if($_GET['KEY_GET1'] != KEY_GET1 || $_GET['KEY_GET2'] != KEY_GET2){
	die;
}

/* *****************************************
	FTP Upload Function
***************************************** */
function ftpSend($filename){
	echo "START FTP UPLOAD // ";
	$conn_id = ftp_connect(FTPSERVER, 21) or die ("Cannot connect to host // ");
	$login_result = ftp_login($conn_id, FTPUSER, FTPPASS) or die("Cannot login // ");
	if (ftp_chdir($conn_id, FTPDIR)) {
		echo "Changed directory to: ".FTPDIR." // ";
	} else {
		die("Error while changing directory to ".FTPDIR." // ");
	}
	if (ftp_put($conn_id, $filename, $filename, FTP_BINARY)) {
		echo "$filename uploaded // ";
	} else {
		die("Error while uploading $filename // ");
		
	}
	ftp_close($conn_id);
	unlink($filename);
	echo "END FTP UPLOAD // // ";
}



/* *****************************************
	The File Backup
***************************************** */
echo "START FILE BACKUP // ";

class Utils
{
  public static function listDirectory($dir)
  {
    $result = array();
    $root = scandir($dir);
    foreach($root as $value) {
      if($value === '.' || $value === '..') {
        continue;
      }
      if(is_file("$dir$value")) {
        $result[] = "$dir$value";
        continue;
      }
      if(is_dir("$dir$value")) {
        $result[] = "$dir$value/";
      }
      foreach(self::listDirectory("$dir$value/") as $value)
      {
        $result[] = $value;
      }
    }
    return $result;
  }
}

foreach($BACKUPDIRS as $backupInfo){
	// $backupInfo[0]; // the Name
	// $backupInfo[1]; // the Directory
	$source_dir = $backupInfo[1];
	$zip_file = $backupInfo[0].'.zip';
	$file_list = Utils::listDirectory($source_dir);
	$zip = new ZipArchive();
	if ($zip->open($zip_file, ZIPARCHIVE::CREATE) === true) {
	  foreach ($file_list as $file) {
	    if ($file !== $zip_file) {
	      $zip->addFile($file, substr($file, strlen($source_dir)));
	    }
	  }
	  $zip->close();
	}
	
	// upload to ftp
	ftpSend($backupInfo[0].'.zip');
}

?>