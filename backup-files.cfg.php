<?php

/* *****************************************
	FTP Settings
***************************************** */
define("FTPSERVER", ""); // The FTP server
define("FTPUSER", ""); // Your FTP username
define("FTPPASS", ""); // Your FTP password
define("FTPDIR", ""); // The directory to upload the backup

/* *****************************************
	Directory Settings
***************************************** */
$BACKUPDIRS = array(
		array("BackupOneFilename", 	"../path/to/directory/to/backup"),
		array("BackupTwoFilename",	"../path/to/second/directory")
);

/* *****************************************
	Security Settings
***************************************** */
// Some random keys for GET requests
// Example:
// backup-files.php?KEY_GET1=randomStuff&KEY_GET2=otherStuff
define("KEY_GET1", "ba7816bf8f01cfea414140de5dae2223b00361a396177a9cb410ff61f20015ad"); 
define("KEY_GET2", "ab5df625bc76dbd4e163bed2dd888df828f90159bb93556525c31821b6541d46"); 

?>