<?php

// the configuration file
require "backup-files.cfg.php";

// check the security keys
if($_GET['KEY_GET1'] != KEY_GET1 || $_GET['KEY_GET2'] != KEY_GET2){
	die;
}

// set errors & time limit
error_reporting(E_ALL);
set_time_limit(240);

/* *****************************************
	FTP Upload Function
***************************************** */
function ftpSend($filename){
	echo "Start FTP Upload \n ";
	// connect to server
	$conn_id = ftp_connect(FTPSERVER, 21) or die ("Cannot connect to host \n ");
	$login_result = ftp_login($conn_id, FTPUSER, FTPPASS) or die("Cannot login \n ");
	// go to the directory
	if (ftp_chdir($conn_id, FTPDIR)) {
		echo "Changed directory to: ".FTPDIR." \n ";
	} else {
		die("Error while changing directory to ".FTPDIR." \n ");
	}
	// upload the file
	if (ftp_put($conn_id, date("Y-m-d_H-i_").$filename, $filename, FTP_BINARY)) {
		echo "$filename uploaded \n ";
	} else {
		die("Error while uploading $filename \n ");
		
	}
	ftp_close($conn_id);
	// delete the local file
	unlink($filename);
	echo "End FTP Upload \n ";
}


/* *****************************************
	Email Function
***************************************** */
function emailSend($filename){
	$mail = "";
	require_once 'phpmailer/PHPMailerAutoload.php';
	//Create a new PHPMailer instance
	$mail = new PHPMailer();
	//Set who the message is to be sent from
	$mail->setFrom(EMAILFROM, EMAILFROM);
	//Set who the message is to be sent to
	$mail->addAddress(EMAILTO, EMAILTO);
	//Set the subject line
	$mail->Subject = 'File Backup: '.$filename.' from: '.date("Y-m-d H:i");
	// Email Body
	$mail->Body = 'This is the File Backup \n  '.$filename.' ';
	$mail->AltBody = 'This is the File Backup \n '.$filename.' ';
	//Attachment (Backup)
	$mail->addAttachment($filename);
	//send the message, check for errors
	if (!$mail->send()) {
	    echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
	    echo "Message sent!";
	}
	$mail->ClearAddresses();
	// delete local file
	unlink($filename);
}



/* *****************************************
	The File Backup
***************************************** */
echo "START FILE BACKUP \n ";

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
	
	if(METHOD == "ftp"){
		// upload the .zip to ftp
		ftpSend($backupInfo[0].'.zip');
	}elseif(METHOD == "email"){
		// send the .zip file via mail
		emailSend($backupInfo[0].'.zip');
	}
}

?>