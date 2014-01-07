<?php

require "backup-mysql.cfg.php";

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
	The MySQL Backup
***************************************** */
// start
error_reporting(0);
set_time_limit(0);
echo "MySQL Backup Started // ";

foreach($DBNAMES as $dbname){
	$name = $dbname;
	// create the mysql backup
	$conn = mysql_connect(DBHOST, DBUSER, DBPASS) or die(mysql_error());
	mysql_select_db($dbname);
	$f = fopen("mysql-".$name."-".date("Y_m_d-H_i").".sql", "w");
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
	fclose($f);
	echo "MySQL backup of ".$dbname." was successful // ";
	
	// put .sql in zip archive
	$zip = new ZipArchive();
	$zip->open("mysql-".$name."-".date("Y_m_d-H_i").".zip", ZIPARCHIVE::CREATE);
	$zip->addFile("mysql-".$name."-".date("Y_m_d-H_i").".sql");
	$zip->close();
	unlink("mysql-".$name."-".date("Y_m_d-H_i").".sql");
	echo " ZIP archive created // ";

	// upload to ftp
	ftpSend("mysql-".$name."-".date("Y_m_d-H_i").".zip");
}
echo " END MYSQLBACKUP // // ";



?>