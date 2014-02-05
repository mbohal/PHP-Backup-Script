<?php
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
	$mail->Subject = 'Backup: '.$filename.' from: '.date("Y-m-d H:i");
	// Email Body
	$mail->Body = 	 "This is the Backup \n \n ".$filename." ";
	$mail->AltBody = "This is the Backup \n \n ".$filename." ";
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
?>