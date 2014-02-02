<?php

// the configuration file
require "backup-mysql.cfg.php";

// check the security keys
if($_GET['KEY_GET1'] != KEY_GET1 || $_GET['KEY_GET2'] != KEY_GET2){
	die;
}

// set errors & time limit
error_reporting(E_ALL);
set_time_limit(240);

/* *****************************************
	ZIP Creation Function
***************************************** */
function createZipFile($name){
	// create .zip archive
	$zip = new ZipArchive();
	$zip->open("mysql-".$name.".zip", ZIPARCHIVE::CREATE);
	// add the .sql file
	$zip->addFile("mysql-".$name.".sql");
	$zip->close();
	// delete the .sql file
	unlink("mysql-".$name.".sql");
	echo " ZIP archive created \n ";
}


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
	$mail->Subject = 'MySQL Backup: '.$filename.' from: '.date("Y-m-d H:i");
	// Email Body
	$mail->Body = 'This is the MySQL Backup \n  '.$filename.' ';
	$mail->AltBody = 'This is the MySQL Backup \n '.$filename.' ';
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
	The MySQL Backup
***************************************** */
// start
echo "MySQL Backup Started \n ";

foreach($DBNAMES as $dbname){
	$name = $dbname;
	// connect to db
	$conn = mysql_connect(DBHOST, DBUSER, DBPASS) or die(mysql_error());
	mysql_select_db($dbname);
	// create the backup file
	$f = fopen("mysql-".$name.".sql", "w");
	// create the MySQL backup
	$tables = mysql_list_tables($dbname);
	while ($cells = mysql_fetch_array($tables)){
	    $table = $cells[0];
	    fwrite($f,"DROP TABLE `".$table."`;\n"); 
	    $res = mysql_query("SHOW CREATE TABLE `".$table."`");
	    if ($res){
	        $create = mysql_fetch_array($res);
	        $create[1] .= ";";
	        $line = str_replace("\n", "", $create[1]);
	        fwrite($f, $line."\n");
	        $data = mysql_query("SELECT * FROM `".$table."`");
	        $num = mysql_num_fields($data);
	        while ($row = mysql_fetch_array($data)){
	            $line = "INSERT INTO `".$table."` VALUES(";
	            for ($i=1;$i<=$num;$i++){
	                $line .= "'".mysql_real_escape_string($row[$i-1])."', ";
	            }
	            $line = substr($line,0,-2);
	            fwrite($f, $line.");\n");
	        }
	    }
	}
	// finished
	fclose($f);
	echo "MySQL backup of ".$dbname." was successful \n ";
	
	// put .sql in zip archive
	createZipFile($name);

	if(METHOD == "ftp"){
		// upload the .zip to ftp
		ftpSend("mysql-".$name.".zip");
	}elseif(METHOD == "email"){
		// send the .zip file via mail
		emailSend("mysql-".$name.".zip");
	}
}
echo "End MySQL Backup \n ";



?>